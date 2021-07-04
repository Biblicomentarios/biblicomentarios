<?php

namespace DevOwl\RealCategoryLibrary;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\WpdbBatch\Update;
use WP_Error;
use WP_Term;
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
/**
 * Adds a custom order to the taxonomies.
 */
class TaxOrder {
    use UtilsProvider;
    const IGNORE_TAXONOMIES = [
        /**
         * WP File Download. They provide an own interface for drag & drop functionality.
         */
        'wpfd-category'
    ];
    /**
     * This function is called when a new term is created.
     *
     * @param int $term_id
     * @param int $tt_id
     * @param string $taxonomy
     */
    public function created_term($term_id, $tt_id, $taxonomy) {
        if (is_taxonomy_hierarchical($taxonomy)) {
            $nextOrder = wp_count_terms($taxonomy, ['hide_empty' => 0]);
            $this->update($nextOrder, $term_id, $taxonomy);
        }
    }
    /**
     * Updates the order for our categories. It also respects WooCommerce attribute taxonomies.
     *
     * You should also check with is_taxonomy_hierarchical before
     * using this function. WooCommerce counts the $ord parameter one
     * up because the index starts with 1.
     *
     * @param int $ord
     * @param int $cid
     * @param string $taxonomy
     */
    public function update($ord, $cid, $taxonomy) {
        global $wpdb;
        if (
            $this->getCore()
                ->getWooCommerce()
                ->isWooCommerceTaxonomy($taxonomy)
        ) {
            wc_set_term_order($cid, $ord + 1, $taxonomy);
        } else {
            $wpdb->query($wpdb->prepare("UPDATE {$wpdb->terms} SET term_order = %d WHERE term_id =%d", $ord, $cid));
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->term_relationships} SET term_order = %d WHERE term_taxonomy_id =%d",
                    $ord,
                    $cid
                )
            );
        }
    }
    /**
     * Update hierarchy with the batch method.
     *
     * @param array $hierarchy
     * @param string $taxonomy
     */
    public function batchUpdate($hierarchy, $taxonomy) {
        global $wpdb;
        if (
            $this->getCore()
                ->getWooCommerce()
                ->isWooCommerceTaxonomy($taxonomy)
        ) {
            // Meta name
            if (taxonomy_is_product_attribute($taxonomy)) {
                $meta_name = 'order_' . esc_attr($taxonomy);
            } else {
                $meta_name = 'order';
            }
            // Update wp_termmeta, get all meta ids...
            $keyValueOrder = [];
            for ($i = 0; $i < \count($hierarchy); $i++) {
                $nodeId = \intval($hierarchy[$i]->term_id);
                $keyValueOrder[$nodeId] = $i;
            }
            if (\count($keyValueOrder) > 0) {
                // phpcs:disable WordPress.DB.PreparedSQL
                $metas = $wpdb->get_results(
                    'SELECT meta_id, term_id FROM ' .
                        $wpdb->termmeta .
                        ' WHERE meta_key = "' .
                        $meta_name .
                        '" AND term_id IN (' .
                        \implode(',', \array_keys($keyValueOrder)) .
                        ')'
                );
                // phpcs:enable WordPress.DB.PreparedSQL
                // Create batch update if metas exists
                if (\count($metas) > 0) {
                    $wpbu = new \DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\WpdbBatch\Update(
                        $wpdb->termmeta,
                        'meta_id'
                    );
                    foreach ($metas as $meta) {
                        $wpbu->add($meta->meta_id, ['meta_value' => $keyValueOrder[$meta->term_id]]);
                    }
                    $wpbu->execute(500);
                }
            }
        } else {
            $wpbu_terms = new \DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\WpdbBatch\Update($wpdb->terms, 'term_id', [
                'term_id' => '%d',
                'term_order' => '%d'
            ]);
            $wpbu_rels = new \DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\WpdbBatch\Update(
                $wpdb->term_relationships,
                'term_taxonomy_id',
                ['term_taxonomy_id' => '%d', 'term_order' => '%d']
            );
            for ($i = 0; $i < \count($hierarchy); $i++) {
                $id = $hierarchy[$i]->term_id;
                $arr = ['term_order' => $i];
                $wpbu_terms->add($id, $arr);
                $wpbu_rels->add($id, $arr);
            }
            // Execute
            $wpbu_terms->execute(500);
            $wpbu_rels->execute(500);
        }
    }
    /**
     * Relocate a category within the tree.
     *
     * @param string $type The post type
     * @param string $taxonomy The taxonomy
     * @param int $id The term id we want to move
     * @param int $parent The parent where the movement should take place
     * @param int $nextId The next id relative to id
     * @param boolean $persist If true the new order gets persisted to database
     * @return WP_Error|array If successful you will get an array with old and new hierarchy
     * @since 3.1.0
     */
    public function relocate($type, $taxonomy, $id, $parent, $nextId, $persist = \true) {
        if (!taxonomy_exists($taxonomy) || !post_type_exists($type)) {
            return new \WP_Error(
                'rcl_build_hierarchy',
                \sprintf(
                    // translators:
                    __('No categories found for this post type (%1$s) with the taxonomy (%2$s).', RCL_TD),
                    $type,
                    $taxonomy
                ),
                ['status' => 500]
            );
        }
        // Receive tree
        $taxTree = new \DevOwl\RealCategoryLibrary\TaxTree($type, $taxonomy, null, \true, $parent);
        $tree = $taxTree->getCats();
        $cnt = \count($tree);
        $hierarchy = [];
        $current = null;
        // Get current
        foreach ($tree as $node) {
            if ($node->term_id === $id) {
                $current = $node;
                break;
            }
        }
        // The moved item comes from another hierarchical level
        if ($current === null) {
            $cats = get_categories([
                // Get single categorie with multi-function
                'hide_empty' => 0,
                'type' => $type,
                'taxonomy' => $taxonomy,
                'include' => $id
            ]);
            $taxTree->enrichTerm($current);
            // Update parent
            if (isset($cats[0])) {
                $current = $cats[0];
                $current->parent = $parent;
                $current->category_parent = $parent;
                if ($persist) {
                    wp_update_term($id, $taxonomy, ['parent' => $parent]);
                }
            }
        }
        if ($current === null) {
            return new \WP_Error('rcl_build_hierarchy', __('No item found.', RCL_TD), ['status' => 500]);
        }
        // Create order
        for ($i = 0; $i < \count($tree); $i++) {
            $node = $tree[$i];
            $next = isset($tree[$i + 1]) ? $tree[$i + 1] : null;
            // Next is at first position
            if ($i === 0 && $node->term_id === $nextId) {
                if (!\in_array($current, $hierarchy, \true)) {
                    $hierarchy[] = $current;
                }
                if (!\in_array($node, $hierarchy, \true)) {
                    $hierarchy[] = $node;
                }
            }
            // Next, but not first
            if ($next !== null && $next->term_id === $nextId) {
                if (!\in_array($node, $hierarchy, \true)) {
                    $hierarchy[] = $node;
                }
                if (!\in_array($current, $hierarchy, \true)) {
                    $hierarchy[] = $current;
                }
            }
            // Skip current
            if ($node->term_id === $id) {
                continue;
            }
            if (!\in_array($node, $hierarchy, \true)) {
                $hierarchy[] = $node;
            }
        }
        // Add the moved node if it should be at the end
        if ($cnt !== \count($hierarchy) && $current !== null && !\in_array($current, $hierarchy, \true)) {
            $hierarchy[] = $current;
        }
        // Persist
        if ($persist) {
            /*for ($i = 0; $i < count($hierarchy); $i++) {
                  $this->update($i, $hierarchy[$i]->term_id, $taxonomy);
              }*/
            $this->batchUpdate($hierarchy, $taxonomy);
        }
        return ['old' => $tree, 'new' => $hierarchy];
    }
    /**
     * Change the WordPress filter to get the orderby
     * SQL attribute for terms. Now use it in different filters.
     *
     * @param WP_Term[] $terms
     */
    public function wp_get_object_terms($terms) {
        /* Filter documentated below */
        if (\is_array($terms) && \count($terms) > 0) {
            // Get the first object and taxonomy name
            $first = \reset($terms);
            if (\is_object($first) && isset($first->taxonomy) && !isset($_GET['orderby'])) {
                $taxonomy = $first->taxonomy;
                if (
                    apply_filters('RCL/Sorting', \true, $taxonomy) &&
                    is_taxonomy_hierarchical($taxonomy) &&
                    !$this->getCore()
                        ->getWooCommerce()
                        ->isWooCommerceTaxonomy($taxonomy)
                ) {
                    // only for hierarchical taxonomies
                    \usort($terms, [\DevOwl\RealCategoryLibrary\TaxOrder::class, 'compare']);
                }
            }
        }
        return $terms;
    }
    /**
     * Change the WordPress filter to get the orderby
     * SQL attribute for terms.
     *
     * @param string $orderby
     * @param array $args
     */
    public function get_terms_orderby($orderby, $args) {
        $taxonomy = isset($args['taxonomy']) ? $args['taxonomy'] : 'category';
        /**
         * This filter allows you to disable the custom order functionality. This
         * can be useful if you have another plugin which handles your sorting.
         *
         * @param {boolean} $enabled=true - If true the custom order of RCL is available
         * @param {string} $taxonomy - Since 3.4.9: Disable sorting functionality by taxonomy
         * @return {boolean}
         * @hook RCL/Sorting
         */
        if (!apply_filters('RCL/Sorting', \true, $taxonomy)) {
            return $orderby;
        }
        if (
            \is_array($taxonomy) ||
            !is_taxonomy_hierarchical($taxonomy) ||
            $this->getCore()
                ->getWooCommerce()
                ->isWooCommerceTaxonomy($taxonomy)
        ) {
            return $orderby;
        }
        if ($args['orderby'] === 'term_order') {
            return 't.term_order';
        } elseif ($args['orderby'] === 'name') {
            return 't.name';
        } elseif (!isset($_GET['orderby'])) {
            return 't.term_order';
        } else {
            return $orderby;
        }
    }
    /**
     * Disable RCL's sorting functionality by a given taxonomy.
     *
     * @param boolean $sorting
     * @param string $taxonomy
     */
    public function disable_by_taxonomy($sorting, $taxonomy) {
        if (\in_array($taxonomy, self::IGNORE_TAXONOMIES, \true)) {
            return \false;
        }
        return $sorting;
    }
    /**
     * usort the terms.
     *
     * @param WP_Term $a
     * @param WP_Term $b
     */
    public static function compare($a, $b) {
        if (\intval($a->term_order) === \intval($b->term_order)) {
            return 0;
        } elseif (\intval($a->term_order) < \intval($b->term_order)) {
            return -1;
        } else {
            return 1;
        }
    }
}
