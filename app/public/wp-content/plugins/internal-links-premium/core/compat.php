<?php

namespace ILJ\Core;

use ILJ\Backend\MenuPage\Dashboard;
use ILJ\Backend\MenuPage\Tools;
use ILJ\Core\IndexStrategy\PolylangStrategy;
use ILJ\Core\IndexStrategy\WPMLStrategy;
use ILJ\Helper\Ajax;
use ILJ\Helper\IndexAsset;
use ILJ\Type\KeywordList;

/**
 * Compatibility handler
 *
 * Responsible for managing compatibility with other 3rd party plugins
 *
 * @package ILJ\Core
 *
 * @since 1.2.0
 */
class Compat
{

    /**
     * Initializes the Compat module
     *
     * @static
     * @since  1.2.0
     *
     * @return void
     */
    public static function init()
    {
        self::enableWpml();
        self::enableYoast();
        self::enableRankMath();
        self::enablePolylang();
    }

    /**
     * Responsible for handling Polylang integration
     *
     * @static
     * @since  1.2.2
     *
     * @return void
     */
    public static function enablePolylang()
    {
        if (!defined('POLYLANG_BASENAME')) {
            return;
        }

        add_filter(
            IndexBuilder::ILJ_FILTER_INDEX_STRATEGY, function ($strategy) {
                return new PolylangStrategy();
            }
        );

        add_filter(
            Ajax::ILJ_FILTER_AJAX_SEARCH_POSTS, function ($data, $args) {
                for($i=0; $i < count($data); $i++) {
                    $data[$i]['text'] = $data[$i]['text'] . ' (' . pll_get_post_language($data[$i]['id']) . ')';
                }

                return $data;
            }, 10, 2 
        );

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            add_filter(
                Ajax::ILJ_FILTER_AJAX_SEARCH_TERMS, function ( $data, $args ) {
                    for($i = 0; $i < count($data); $i++) {
                        $data[$i]['text'] = $data[$i]['text'] . ' (' . pll_get_term_language($data[$i]['id']) . ')';
                    }

                    return $data;
                }, 10, 2
            );
        }

        add_filter(
            IndexAsset::ILJ_FILTER_INDEX_ASSET, function ($meta_data, $type, $id) {

                $asset_language = '';
                $language_container = [];

                if (\ILJ\ilj_fs()->is__premium_only()) {
                    if (\ILJ\ilj_fs()->can_use_premium_code() ) {
                        if ($type == 'term') {
                               $asset_language = pll_get_term_language($id);
                        }
                    }
                }

                $asset_language = ($asset_language == '') ? pll_get_post_language($id) : $asset_language;

                if (!$asset_language || $asset_language == '') {
                    return $meta_data;
                }

                if (!isset($language_container[$asset_language])) {
                    $language_container[$asset_language] = PLL()->model->get_language($asset_language);
                }

                $flag_url = $language_container[$asset_language]->flag_url;
                $flag_img = sprintf('<img class="tip" src="%s" title="%s" />', $flag_url, $language_container[$asset_language]->name);
                $meta_data->title = $flag_img . ' ' . $meta_data->title;

                return $meta_data;
            }, 10, 3
        );
    }

    /**
     * Responsible for handling WPML integration
     *
     * @static
     * @since  1.2.0
     *
     * @return void
     */
    protected static function enableWpml()
    {
        if (!function_exists('icl_object_id') || defined('POLYLANG_BASENAME')) {
            return;
        }

        add_filter(
            IndexBuilder::ILJ_FILTER_INDEX_STRATEGY, function ($strategy) {
                return new WPMLStrategy();
            } 
        );

        add_filter(
            Ajax::ILJ_FILTER_AJAX_SEARCH_POSTS, function ($data, $args) {
                global $sitepress;

                $languages = WPMLStrategy::getLanguages();
                $current_language = $sitepress->get_current_language();

                for($i=0; $i < count($data); $i++) {
                     $data[$i]['text'] = $data[$i]['text'] . ' (' . $current_language . ')';
                }

                foreach ($languages as $language) {
                    if ($language == $current_language) {
                          continue;
                    }

                    $sitepress->switch_lang($language, true);

                    $query = new \WP_Query($args);

                    foreach ($query->posts as $post) {
                        $data[] = [
                        "id"   => $post->ID,
                        "text" => $post->post_title . ' (' . $language . ')'
                        ];
                    }

                    $sitepress->switch_lang($current_language, true);
                }

                return $data;
            }, 10, 2 
        );

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            add_filter(
                Ajax::ILJ_FILTER_AJAX_SEARCH_TERMS, function ( $data, $args ) {
                    global $sitepress;

                    $data             = [];
                    $languages        = WPMLStrategy::getLanguages();
                    $current_language = $sitepress->get_current_language();

                    foreach ( $languages as $language ) {

                         $sitepress->switch_lang($language, true);

                         $query = new \WP_Term_Query($args);

                        if ($query->terms && ! empty($query) && ! is_wp_error($query) ) {
                            foreach ( $query->terms as $term ) {
                                $taxonomy = get_taxonomy($term->taxonomy);
                                $data[]   = [
                                 "id"   => $term->term_id,
                                 "text" => $term->name . ' [' . $taxonomy->label . ']' . ' (' . $language . ')'
                                ];
                            }
                        }

                        $sitepress->switch_lang($current_language, true);
                    }

                    return $data;
                }, 10, 2
            );
        }

        add_filter(
            IndexAsset::ILJ_FILTER_INDEX_ASSET, function ($meta_data, $type, $id) {
                global $sitepress;

                if (\ILJ\ilj_fs()->is__premium_only()) {
                    if (\ILJ\ilj_fs()->can_use_premium_code() ) {
                        if ($type == 'term') {

                            $term = get_term((int) $id);

                            $language_code = apply_filters(
                                'wpml_element_language_code', null, array(
                                'element_id'   => (int) $id,
                                'element_type' => $term->taxonomy
                                )
                            );

                            if ($language_code != "") {
                                       $language = $sitepress->get_language_for_element((int) $id, 'tax_' . $term->taxonomy);

                                       $current_language = $sitepress->get_current_language();

                                if ($current_language != $language_code) {
                                    $sitepress->switch_lang($language_code, true);

                                    $term = get_term((int) $id);

                                    $meta_data->title = $term->name;
                                    $meta_data->url = get_term_link($term);
                                    $meta_data->url_edit = get_edit_term_link($term->term_id);

                                    $sitepress->switch_lang($current_language, true);
                                }

                                 $language_information = $sitepress->get_language_details($language);

                                 $language_info = [
                                'language_code'      => $language,
                                'locale'             => $sitepress->get_locale($language),
                                'text_direction'     => $sitepress->is_rtl($language),
                                'display_name'       => $sitepress->get_display_language_name($language, $current_language),
                                'native_name'        => isset($language_information['display_name']) ? $language_information['display_name'] : '',
                                'different_language' => $language !== $current_language,
                                 ];
                            }

                        }
                    }
                }

                $language_info = !isset($language_info) ? wpml_get_language_information(null, (int) $id) : $language_info;

                if (!$language_info) {
                    return $meta_data;
                }

                $flag_url = $sitepress->get_flag_url($language_info['language_code']);
                $flag_img = sprintf('<img class="tip" src="%s" title="%s" />', $flag_url, $language_info['display_name']);

                $meta_data->title = $flag_img . ' ' . $meta_data->title;

                return $meta_data;
            }, 10, 3 
        );
    }

    /**
     * Responsible for handling Yoast-SEO integration
     *
     * @static
     * @since  1.2.0
     *
     * @return void
     */
    protected static function enableYoast()
    {
        if (!defined('WPSEO_VERSION')) {
            return;
        }

        add_filter(
            Tools::ILJ_FILTER_MENUPAGE_TOOLS_KEYWORD_IMPORT_POST, function ($keyword_import_source) {
                $import_source = [
                'title' => __('Yoast focus keywords', 'internal-links'),
                'class' => 'yoast-seo'
                ];

                $keyword_import_source[] = $import_source;
                return $keyword_import_source;
            }
        );

        add_filter(
            Tools::ILJ_FILTER_MENUPAGE_TOOLS_KEYWORD_IMPORT_TERM, function ($keyword_import_source) {
                $import_source = [
                'title' => __('Yoast focus keywords', 'internal-links'),
                'class' => 'yoast-seo'
                ];

                $keyword_import_source[] = $import_source;
                return $keyword_import_source;
            }
        );

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            add_action(
                Tools::ILJ_MENUPAGE_TOOLS_IMPORT_INTERN_POST . '-' . 'yoast-seo', function ($post) {
                    $keywords = new KeywordList();
                    $focus_keyword = \WPSEO_Meta::get_value('focuskw', $post->ID);
                    $keywords->addKeyword($focus_keyword);

                    $focus_keywords_additional = get_post_meta($post->ID, '_yoast_wpseo_focuskeywords', true);
                    $focus_keywords_additional = json_decode($focus_keywords_additional, true);

                    if (!empty($focus_keywords_additional)) {
                        $extra_keywords = new KeywordList($focus_keywords_additional);
                        $keywords->merge($extra_keywords);
                    }

                    return $keywords;
                } 
            );

            add_action(
                Tools::ILJ_MENUPAGE_TOOLS_IMPORT_INTERN_TERM . '-' . 'yoast-seo', function ($term) {
                    $keywords = new KeywordList();

                    $taxonomy_meta = get_option('wpseo_taxonomy_meta');

                    if (empty($taxonomy_meta)) {
                        return $keywords;
                    }

                    $taxonomies = [];

                    foreach($taxonomy_meta as $taxonomy => $meta)
                    {
                        $taxonomies = $taxonomies + $meta;
                    }

                    if (!isset($taxonomies[$term->term_id]) || !isset($taxonomies[$term->term_id]['wpseo_focuskw'])) {
                        return $keywords;
                    }

                    $focus_kw = strtolower($taxonomies[$term->term_id]['wpseo_focuskw']);
                    $keywords = KeywordList::fromInput($focus_kw);

                    return $keywords;
                } 
            );
        }
    }

    /**
     * Responsible for handling RankMath integration
     *
     * @static
     * @since  1.2.0
     *
     * @return void
     */
    protected static function enableRankMath()
    {
        if (!class_exists('RankMath')) {
            return;
        }

        add_filter(
            Tools::ILJ_FILTER_MENUPAGE_TOOLS_KEYWORD_IMPORT_POST, function ($keyword_import_source) {
                $import_source = [
                'title' => __('RankMath focus keywords', 'internal-links'),
                'class' => 'rankmath'
                ];

                $keyword_import_source[] = $import_source;
                return $keyword_import_source;
            }
        );

        add_filter(
            Tools::ILJ_FILTER_MENUPAGE_TOOLS_KEYWORD_IMPORT_TERM, function ($keyword_import_source) {
                $import_source = [
                'title' => __('RankMath focus keywords', 'internal-links'),
                'class' => 'rankmath'
                ];

                $keyword_import_source[] = $import_source;
                return $keyword_import_source;
            }
        );

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            add_action(
                Tools::ILJ_MENUPAGE_TOOLS_IMPORT_INTERN_POST . '-' . 'rankmath', function ($post) {
                    $focus_keyword_meta    = get_post_meta($post->ID, 'rank_math_focus_keyword', true);
                    $focus_keywords = KeywordList::fromInput($focus_keyword_meta);

                    return $focus_keywords;
                }
            );

            add_action(
                Tools::ILJ_MENUPAGE_TOOLS_IMPORT_INTERN_TERM . '-' . 'rankmath', function ($term) {
                    $focus_keyword_meta = get_term_meta($term->term_id, 'rank_math_focus_keyword', true);
                    $focus_keywords = KeywordList::fromInput($focus_keyword_meta);

                    return $focus_keywords;
                }
            );
        }
    }
}