<?php
namespace ILJ\Helper;

use ILJ\Core\Options as CoreOptions;
use ILJ\Database\Postmeta;
use ILJ\Type\KeywordList;

/**
 * Import toolset
 *
 * Methods for data import
 *
 * @since   1.2.10
 * @package ILJ\Helper
 */
class Import
{

    /**
     * Imports all configured sources on configured post types as keywords for linking
     *
     * @since  1.2.10
     * @param  array $taxonomy_types The types where import gets applied (e. g. "post" or "page")
     * @param  array $source_import  The source of import (e. g. "title")
     * @return void
     */
    public static function internalPost__premium_only(array $post_types, array $source_import)
    {
        $query = new \WP_Query(
            [
	            'post_type' => $post_types,
	            'post__not_in'     => CoreOptions::getOption(\ILJ\Core\Options\Blacklist::getKey()),
	            'posts_per_page' => -1
            ]
        );

        $posts = $query->get_posts();

        foreach ($posts as $post) {
            $keywords = KeywordList::fromMeta($post->ID, 'post');

            foreach($source_import as $import) {
                /**
                 * Filters / collects keywords from all subscribed keyword generators
                 *
                 * @since 1.2.0
                 *
                 * @param string $import The submitted import type as filter
                 * @param \WP_Post  $post The current post object
                 */
                $import_keywords = apply_filters($import, $post);

                if ($import_keywords instanceof KeywordList) {
                    $keywords->merge($import_keywords);
                }
            }

            update_post_meta($post->ID, Postmeta::ILJ_META_KEY_LINKDEFINITION, $keywords->getKeywords());
        }
    }

    /**
     * Imports all configured sources on configured term types as keywords for linking
     *
     * @since  1.2.10
     * @param  array $taxonomy_types The types where import gets applied (e. g. "category" or "post_tag")
     * @param  array $source_import  The source of import (e. g. "title")
     * @return void
     */
    public static function internalTerm__premium_only(array $taxonomy_types, array $source_import)
    {
        $args = [
	        'taxonomy'  => $taxonomy_types,
	        'exclude'  => CoreOptions::getOption(\ILJ\Core\Options\TermBlacklist::getKey()),
	        'hide_empty' => false
        ];

        $query = new \WP_Term_Query($args);

        if (!$query->terms || empty($query) || is_wp_error($query)) {
            return;
        }

        foreach ($query->terms as $term) {
            $keywords = KeywordList::fromMeta($term->term_id, 'term');

            foreach($source_import as $import) {
                /**
                 * Filters / collects keywords from all subscribed keyword generators
                 *
                 * @since 1.2.0
                 *
                 * @param string $import The submitted import type as filter
                 * @param \WP_Term  $term The current term object
                 */
                $import_keywords = apply_filters($import, $term);

                if ($import_keywords instanceof KeywordList) {
                    $keywords->merge($import_keywords);
                }

                if ($import != 'ilj-import-intern-term-title') {
                    continue;
                }

                $title = strtolower($term->name);
                $keywords->addKeyword($title);
            }

            update_term_meta($term->term_id, Postmeta::ILJ_META_KEY_LINKDEFINITION, $keywords->getKeywords());
        }
    }
}