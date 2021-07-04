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
 * Helper class to PostSortable which handles post adjacent queries.
 *
 * @see https://stackoverflow.com/a/16497879/5506547
 */
class PostAdjacentActions {
    /**
     * C'tor.
     *
     * @codeCoverageIgnore C'tor
     */
    private function __construct() {
        // Silence is golden.
    }
    // See modifyWhere()
    public function get_previous_post_where($sql) {
        return $this->modifyWhere($sql, '<', '>');
    }
    // See modifyOrderBy()
    public function get_previous_post_sort($sql) {
        return $this->modifyOrderBy($sql, 'ASC');
    }
    // See modifyWhere()
    public function get_next_post_where($sql) {
        return $this->modifyWhere($sql, '>', '<');
    }
    // See modifyOrderBy()
    public function get_next_post_sort($sql) {
        return $this->modifyOrderBy($sql, 'DESC');
    }
    /**
     * Modify WHERE with given operators.
     *
     * @param string $sql
     * @param string $post_date_operator
     * @param string $menu_order_operator
     * @return string
     */
    public function modifyWhere($sql, $post_date_operator, $menu_order_operator) {
        if (!\DevOwl\RealCustomPostOrder\view\PostScreenSettings::isActive(get_post_type())) {
            return $sql;
        }
        // Validate parameters
        if (!\in_array($post_date_operator, ['>', '<'], \true) || !\in_array($menu_order_operator, ['>', '<'], \true)) {
            return $sql;
        }
        $current = get_post();
        return \str_replace(
            \sprintf('p.post_date ' . $post_date_operator . ' \'%s\'', $current->post_date),
            \sprintf('p.menu_order ' . $menu_order_operator . ' %d', $current->menu_order),
            $sql
        );
    }
    /**
     * Modify ORDER BY with given order.
     *
     * @param string $sql
     * @param string $order
     */
    public function modifyOrderBy($sql, $order) {
        if (!\DevOwl\RealCustomPostOrder\view\PostScreenSettings::isActive(get_post_type())) {
            return $sql;
        }
        // Validate parameters
        if (!\in_array($order, ['DESC', 'ASC'], \true)) {
            return $sql;
        }
        return 'ORDER BY p.menu_order ' . $order . ' LIMIT 1';
    }
    /**
     * New instance.
     *
     * @codeCoverageIgnore
     */
    public static function instance() {
        return new \DevOwl\RealCustomPostOrder\sortable\PostAdjacentActions();
    }
}
