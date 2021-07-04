<?php

namespace DevOwl\RealCustomPostOrder\sortable;

use DevOwl\RealCustomPostOrder\view\PostScreenSettings;
use DevOwl\RealCustomPostOrder\Vendor\MatthiasWeb\WpdbBatch\Update;
use WP_Query;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Abstract sortable implementation. Each type of sortable should get an own subclass
 * of this abstract one. For example tags, media or posts.
 */
class PostSortable extends \DevOwl\RealCustomPostOrder\sortable\AbstractSortable {
    /**
     * get_post_statuses() is not enough for available post statuses because
     * it misses some statuses (e. g. future).
     */
    const SORTABLE_STATI = ['publish', 'pending', 'draft', 'private', 'future'];
    /**
     * Batch update processor.
     *
     * @var Update
     */
    private $batchUpdater;
    // Documented in AbstractSortable
    public function isAvailable($respectOption = \true) {
        $screen = get_current_screen();
        if (
            !is_admin() ||
            $screen === null ||
            $screen->base !== 'edit' ||
            empty($screen->post_type) ||
            $screen->post_type === 'attachment'
        ) {
            return \false;
        }
        return $respectOption
            ? \DevOwl\RealCustomPostOrder\view\PostScreenSettings::isActive($screen->post_type)
            : \true;
    }
    // Documented in AbstractSortable
    public function isSortable() {
        // Due to dynamic content (e. g. RCM) it is handled on frontend
        return \true;
    }
    // Documented in AbstractSortable
    public function localize() {
        $taxonomies = get_object_taxonomies(get_post_type(), 'objects');
        foreach ($taxonomies as $key => $tax) {
            $taxonomies[$key] = $tax->query_var;
        }
        return ['taxonomies' => $taxonomies, 'showPostFirstTimePointer' => $this->showFirstTimePointer()];
    }
    // Documented in AbstractSortable
    protected function updateByIntSequence($sequence) {
        global $wpdb;
        // Obtain initial menu_order from first sequence item
        $ids = \implode(',', \array_map('intval', $sequence));
        // phpcs:disable WordPress.DB.PreparedSQL
        $menu_order = \intval($wpdb->get_var("SELECT MIN(menu_order) FROM {$wpdb->posts} WHERE ID IN (" . $ids . ')'));
        // phpcs:enable WordPress.DB.PreparedSQL
        // Update in batch
        $batch = $this->getBatchUpdater();
        foreach ($sequence as $id) {
            $batch->add($id, ['menu_order' => $menu_order++]);
        }
        $batch->execute();
        return \true;
    }
    // Documented in AbstractSortable
    public function recreateIndex($sequence = null) {
        global $wpdb;
        // Get post type from either sequence or current screen
        $post_type = \is_array($sequence)
            ? $wpdb->get_var($wpdb->prepare("SELECT post_type FROM {$wpdb->posts} WHERE ID = %d", $sequence[0]))
            : get_current_screen()->post_type;
        $stati = '\'' . \implode('\',\'', self::SORTABLE_STATI) . '\'';
        // phpcs:disable WordPress.DB.PreparedSQL
        $sql = $wpdb->prepare(
            "UPDATE {$wpdb->posts} AS wpp2\n        LEFT JOIN (\n            SELECT @rownum := @rownum + 1 AS nr, wpp.ID\n            FROM {$wpdb->posts} AS wpp, (SELECT @rownum := 0) as r\n            WHERE wpp.post_type = %s AND wpp.post_status IN ({$stati})\n            ORDER BY wpp.menu_order ASC, wpp.post_date DESC\n        ) AS wppnew ON wpp2.ID = wppnew.ID\n        SET wpp2.menu_order = wppnew.nr\n        WHERE wpp2.post_type = %s AND wpp2.post_status IN ({$stati})",
            $post_type,
            $post_type
        );
        $wpdb->query($sql);
        // phpcs:enable WordPress.DB.PreparedSQL
    }
    /**
     * Modify query in WP_Query's depending on access type frontend or backend.
     *
     * @param WP_Query $query
     */
    public function pre_get_posts($query) {
        if ($query->get('skip_custom_order')) {
            return;
        }
        if (is_admin()) {
            $this->applyBackendQuery($query);
        } else {
            $this->applyFrontendQuery($query);
        }
    }
    /**
     * Apply backend query.
     *
     * @param WP_Query $query
     */
    protected function applyBackendQuery($query) {
        $post_type = $query->get('post_type');
        if (
            empty($post_type) ||
            !empty($query->get('orderby')) ||
            \is_array($post_type) ||
            !\DevOwl\RealCustomPostOrder\view\PostScreenSettings::isActive($post_type)
        ) {
            return;
        }
        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');
    }
    /**
     * Apply frontend query.
     *
     * @param WP_Query $query
     */
    protected function applyFrontendQuery($query) {
        $post_type = $query->get('post_type', 'post');
        if (\is_array($post_type) || !\DevOwl\RealCustomPostOrder\view\PostScreenSettings::isActive($post_type)) {
            return;
        }
        if ($this->applyFrontendQueryGetPosts($query)) {
            $query->set('orderby', 'menu_order');
            $query->set('order', 'ASC');
        }
    }
    /**
     * get_posts() passes suppress_filters and predefined orderby, we need to check this
     * otherwise it's the usual WP_Query.
     *
     * @param WP_Query $query
     * @see https://developer.wordpress.org/reference/functions/get_posts/
     */
    protected function applyFrontendQueryGetPosts($query) {
        $isGetPostsFunction = \boolval($query->get('suppress_filters'));
        if ($isGetPostsFunction) {
            // If default behavior is given we can change ordering safely
            return \in_array($query->get('orderby'), ['date', 'menu_order'], \true);
        } else {
            // WP_Query does not set by default, so we can safely do this
            return empty($query->get('orderby')) && empty($query->get('order'));
        }
    }
    /**
     * Returns true if it should show the first-time-pointer.
     *
     * @param boolean $set
     */
    public function showFirstTimePointer($set = null) {
        $optionName = RCPO_OPT_PREFIX . '-post-first-pointer-dismissed';
        if ($set === \true) {
            update_user_meta(get_current_user_id(), $optionName, 1);
        }
        return !\boolval(get_user_meta(get_current_user_id(), $optionName, \true));
    }
    // Getter
    protected function getBatchUpdater() {
        global $wpdb;
        return $this->batchUpdater === null
            ? ($this->batchUpdater = new \DevOwl\RealCustomPostOrder\Vendor\MatthiasWeb\WpdbBatch\Update(
                $wpdb->posts,
                'ID',
                ['ID' => '%d', 'menu_order' => '%d']
            ))
            : $this->batchUpdater;
    }
    /**
     * Get singleton instance.
     *
     * @return PostSortable
     */
    public static function get() {
        return \DevOwl\RealCustomPostOrder\sortable\AbstractSortable::getTypeInstance('post');
    }
}
