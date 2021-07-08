<?php

namespace ILJ\Core\IndexStrategy;

use ILJ\Backend\Editor;
use ILJ\Core\Options;
use ILJ\Database\Linkindex;
use ILJ\Database\Postmeta;
use ILJ\Enumeration\KeywordOrder;
use ILJ\Helper\Encoding;
use ILJ\Helper\IndexAsset;
use ILJ\Helper\Keyword;
use ILJ\Helper\Regex;
use ILJ\Helper\Replacement;
use ILJ\Helper\Url;
use ILJ\Helper\Blacklist;
use ILJ\Type\Ruleset;

/**
 * Default indexbuilder
 *
 * The default index builder strategy
 *
 * @package ILJ\Core\Indexbuilder
 *
 * @since 1.2.0
 */
class DefaultStrategy implements StrategyInterface
{
    /**
     * @var   Ruleset
     * @since 1.0.0
     */
    protected $link_rules;

    /**
     * @var   array
     * @since 1.0.1
     */
    protected $link_options = [];
    
    /**
     * 
     * @var array
     * @since 1.2.15
     */
    protected $blacklisted_posts = [];

    /**
     * 
     * @var array
     * @since 1.2.15
     */
    protected $blacklisted_terms = [];

    public function __construct()
    {
        $this->link_rules = new Ruleset();

        $this->blacklisted_posts = Blacklist::getBlacklistedList("post");
        
        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            $this->blacklisted_terms = Blacklist::getBlacklistedList("term");
        }        
    }

    /**
     * @inheritdoc
     */
    public function setIndices()
    {
        $index_count = 0;

        $this->loadLinkConfigurations();

        $posts = IndexAsset::getPosts();
        
        $this->writeToIndex(
            $posts, 'post', [
            'id'      => 'ID',
            'content' => 'post_content'
            ], $index_count
        );

        if (\ILJ\ilj_fs()->is__premium_only()) {
            if (\ILJ\ilj_fs()->can_use_premium_code()) {
                $terms = IndexAsset::getTerms__premium_only();

                $this->writeToIndex(
                    $terms, 'term', [
                    'id'      => 'term_id',
                    'content' => 'description'
                    ], $index_count
                );
            }
        }

        return $index_count;
    }

    /**
     * @inheritdoc
     */
    public function setLinkOptions(array $link_options)
    {
        $this->link_options = $link_options;
    }

    /**
     * Picks up all meta definitions for configured keywords and adds them to internal ruleset
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function loadLinkConfigurations()
    {
        $post_definitions = Postmeta::getAllLinkDefinitions();

        foreach ($post_definitions as $definition) {
            $type = 'post';

            if (\ILJ\ilj_fs()->is__premium_only()) {
                if (\ILJ\ilj_fs()->can_use_premium_code()) {
                    if (get_post_type($definition->post_id) == \ILJ\Posttypes\CustomLinks::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG) {
                        $type = 'custom';
                    }
                }
            }

            $anchors = unserialize($definition->meta_value);

            if (!$anchors) {
                continue;
            }

            $anchors = $this->applyKeywordOrder($anchors);

            $this->addAnchorsToLinkRules(
                $anchors, [
                'id' => $definition->post_id,
                'type' => $type
                ]
            );
        }

        if (\ILJ\ilj_fs()->is__premium_only()) {
            if (\ILJ\ilj_fs()->can_use_premium_code()) {
                $term_definitions = \ILJ\Database\Termmeta::getAllLinkDefinitions();

                foreach ($term_definitions as $definition) {
                    $anchors = unserialize($definition->meta_value);

                    if (!$anchors) {
                        continue;
                    }

                    $this->addAnchorsToLinkRules(
                        $anchors, [
                        'id' => $definition->term_id,
                        'type' => 'term'
                        ]
                    );
                }
            }
        }

        return;
    }

    /**
     * Adds anchors to link_rules
     *
     * @since 1.2.0
     * @param array $anchors The bag of anchor texts
     * @param array $params  The params
     *
     * @return void
     */
    protected function addAnchorsToLinkRules(array $anchors, array $params)
    {
        foreach ($anchors as $anchor) {
            $anchor = Encoding::unmaskSlashes($anchor);

            if (!Regex::isValid($anchor)) {
                continue;
            }

            $pattern = Regex::escapeDot($anchor);
            $this->link_rules->addRule($pattern, $params['id'], $params['type']);
        }
        return;
    }

    /**
     * Reorders the configured anchors depending on the plugin settings
     *
     * @since 1.2.0
     * @param array $anchors The bag of anchor texts
     *
     * @return array
     */
    protected function applyKeywordOrder(array $anchors)
    {
        $keyword_order = Options::getOption(\ILJ\Core\Options\KeywordOrder::getKey());

        switch ($keyword_order) {
        case KeywordOrder::HIGH_WORDCOUNT_FIRST:
            usort(
                $anchors, function ($a, $b) {
                    return Keyword::gapWordCount($b) - Keyword::gapWordCount($a);
                }
            );
            break;
        case KeywordOrder::LOW_WORDCOUNT_FIRST:
            usort(
                $anchors, function ($a, $b) {
                    return Keyword::gapWordCount($a) - Keyword::gapWordCount($b);
                }
            );
            break;
        }

        return $anchors;
    }

    /**
     * Writes a set of data to the linkindex
     *
     * @since 1.0.1
     *
     * @param  array  $data      The data container
     * @param  string $data_type Type of the data inside the container
     * @param  array  $fields    Field settings for the container objects
     * @param  int    &$counter  Counts the written operations
     * @return void
     */
    protected function writeToIndex($data, $data_type, array $fields, &$counter)
    {
        if (!is_array($data) || !count($data)) {
            return;
        }

        $multi_keyword_mode = $this->link_options['multi_keyword_mode'];
        $links_per_page     = $this->link_options['links_per_page'];
        $links_per_target   = $this->link_options['links_per_target'];
        
        $fields = wp_parse_args(
            $fields, [
            'id'      => '',
            'content' => ''
            ]
        );

        foreach ($data as $item) {
            $linked_urls         = [];
            $linked_anchors      = [];
            $post_outlinks_count = 0;

            if (!property_exists($item, $fields['content']) || !property_exists($item, $fields['id'])) {
                continue;
            }

            $content = $item->{$fields['content']};

            if ($data_type == 'post') {
                $this->filterTheContentWithoutTexturize($content);
            }

            if (\ILJ\ilj_fs()->is__premium_only()) {
                if (\ILJ\ilj_fs()->can_use_premium_code()) {
                    if (Options::getOption(\ILJ\Core\Options\RespectExistingLinks::getKey())) {
                        preg_match_all('/<a.*href="(.*)".*>.*<\/a>/sU', $content, $existing_link_targets);
                        $existing_link_targets = Url::convertRelativePathsToAbsolute($existing_link_targets[1]);
                    }
                }
            }

            Replacement::mask($content);
            
            while ($this->link_rules->hasRule()) {
                $link_rule = $this->link_rules->getRule();
                
                if (!isset($linked_urls[$link_rule->value])) {
                    $linked_urls[$link_rule->value] = 0;
                }
                if (!isset($incoming_link[$link_rule->value])) {
                    $incoming_link[$link_rule->value] = IndexAsset::getIncomingLinksCount($link_rule->value , $link_rule->type);
                }

                if (!$multi_keyword_mode
                    && (($links_per_page > 0 && $post_outlinks_count >= $links_per_page)
                    || ($links_per_target > 0 && $linked_urls[$link_rule->value] >= $links_per_target))
                ) {
                    $this->link_rules->nextRule();
                    continue;
                }

                if($link_rule->type == "post"){
                    $is_blacklisted_post = Blacklist::checkIfBlacklisted($link_rule->type ,$link_rule->value , $this->blacklisted_posts);
                    if($is_blacklisted_post){
                        $this->link_rules->nextRule();
                        continue;
                    }
                }
                if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
                    if($link_rule->type == "term"){
                        $is_blacklisted_term = Blacklist::checkIfBlacklisted($link_rule->type ,$link_rule->value , $this->blacklisted_terms);
                        if($is_blacklisted_term){
                            $this->link_rules->nextRule();
                            continue;
                        }
                    }
                }
                
                if ($link_rule->value != $item->{$fields['id']}) {
                    preg_match('/'. Encoding::maskPattern($link_rule->pattern) .'/ui', $item->{$fields['content']}, $rule_match);

                    if (isset($rule_match['phrase'])) {
                        $phrase = trim($rule_match['phrase']);

                        if (!$multi_keyword_mode && in_array($phrase, $linked_anchors)) {
                               $this->link_rules->nextRule();
                               continue;
                        }
                        $is_blacklisted_keyword = IndexAsset::checkIfBlacklistedKeyword($item->{$fields['id']}, $phrase , $data_type);
                        if( $is_blacklisted_keyword ){
                            $this->link_rules->nextRule();
                            continue;
                        }
                        

                        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
                            $limit_incoming_links = IndexAsset::getLimitLinksMetas__premium_only($link_rule->value , $link_rule->type);
                            if($limit_incoming_links[Editor::ILJ_META_KEY_LIMITINCOMINGLINKS] == true && $incoming_link[$link_rule->value] >= $limit_incoming_links[Editor::ILJ_META_KEY_MAXINCOMINGLINKS] && $limit_incoming_links[Editor::ILJ_META_KEY_MAXINCOMINGLINKS] != 0 && $limit_incoming_links[Editor::ILJ_META_KEY_MAXINCOMINGLINKS] != ""){
                                $this->link_rules->nextRule();
                                continue;
                            }
                            
                        }

	                    if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()  && isset($existing_link_targets) ) {
	                    	$asset_data = IndexAsset::getMeta($link_rule->value, $link_rule->type);

	                    	if ($asset_data && in_array($asset_data->url, $existing_link_targets)) {
	                    		$this->link_rules->nextRule();
	                    		continue;
	                    	}
                        }

                        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
                            $in_same_tax = IndexAsset::postsInSameTaxonomy__premium_only($item->{$fields['id']}, $link_rule->value,  $data_type);
                            $has_same_term = IndexAsset::postsInSameTerm__premium_only($item->{$fields['id']}, $link_rule->value,  $data_type);
                            if($in_same_tax && !$has_same_term){
                                $this->link_rules->nextRule();
                                continue;
                            }
                        }

                        Linkindex::addRule($item->{$fields['id']}, $link_rule->value, $phrase, $data_type, $link_rule->type);
                        $counter++;
                        $post_outlinks_count++;
                        $linked_urls[$link_rule->value]++;
                        $incoming_link[$link_rule->value]++;
                        $linked_anchors[] = $phrase;
                    }
                }
                
                $this->link_rules->nextRule();
            }
            $this->link_rules->reset();
        }
    }

    /**
     * Applies content filters to a given piece of content without calling
     * WordPress' texturize method (that escapes special chars like apostrophes)
     *
     * @since  1.2.9
     * @param  $content The content that gets filtered
     * @return void
     */
    protected function filterTheContentWithoutTexturize(&$content)
    {
        remove_filter('the_content', 'wptexturize');
        $content = apply_filters('the_content', $content);
        add_filter('the_content', 'wptexturize');
    }
}
