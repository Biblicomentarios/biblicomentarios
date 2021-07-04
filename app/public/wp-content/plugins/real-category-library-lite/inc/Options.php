<?php

namespace DevOwl\RealCategoryLibrary;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Singleton options class.
 */
class Options {
    use UtilsProvider;
    /**
     * Singleton instance.
     */
    private static $me;
    /**
     * Enable or disable the tree view for a post type.
     *
     * @param string $post_type
     * @param boolean $state
     */
    public function setActive($post_type, $state) {
        $isAvailable = new \DevOwl\RealCategoryLibrary\TaxTree($post_type);
        if ($isAvailable) {
            return update_option($this->getOptionName($post_type, 'active'), $state ? '1' : '0');
        }
        return \false;
    }
    /**
     * Enable or disable the fast mode for a post type.
     *
     * @param string $post_type
     * @param boolean $state
     */
    public function setFastMode($post_type, $state) {
        $isAvailable = new \DevOwl\RealCategoryLibrary\TaxTree($post_type);
        if ($isAvailable) {
            return update_option($this->getOptionName($post_type, 'fast-mode'), $state ? '1' : '0');
        }
        return \false;
    }
    /**
     * Returns if the tree is active for the given taxonomy tree.
     *
     * @param TaxTree $taxTree The taxonomy tree
     * @return boolean
     */
    public function isActive($taxTree) {
        // We can do the isPro check here because when it also returns true the CPT tree does not work
        if (!$this->isPro() && ($taxTree->getTypeNow() !== 'post' || $taxTree->getTaxNow()->objkey !== 'category')) {
            return \false;
        }
        return (bool) get_option($this->getOptionName($taxTree->getTypeNow(), 'active'), 1);
    }
    /**
     * Returns if the tree is fast mode for the given taxonomy tree.
     *
     * @param TaxTree $taxTree The taxonomy tree
     * @return boolean
     */
    public function isFastMode($taxTree) {
        // We can do the isPro check here because it is JS splitted
        return $this->isPro() && (bool) get_option($this->getOptionName($taxTree->getTypeNow(), 'fast-mode'), 1);
    }
    /**
     * Get the option name for a post type.
     *
     * @param string $post_type
     * @param string $type Can be `active` or `fast-mode`
     */
    protected function getOptionName($post_type, $type) {
        return \sprintf('%s-%s-%s', RCL_OPT_PREFIX, $type, $post_type);
    }
    /**
     * Get singleton core class.
     *
     * @return Options
     */
    public static function getInstance() {
        return !isset(self::$me) ? (self::$me = new \DevOwl\RealCategoryLibrary\Options()) : self::$me;
    }
}
