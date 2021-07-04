<?php

namespace DevOwl\RealCategoryLibrary\overrides\interfce\view;

interface IOverrideWooCommerce {
    /**
     * Initialize WooCommerce support and check for user option.
     */
    public function init();
    /**
     * Get WC attributes and add filter for each one.
     *
     * @see https://support.yithemes.com/hc/en-us/articles/115000123814-Ajax-Product-filter-Attributes-are-no-longer-hierarchical-than-version-3-0-x-of-WooCommerce
     */
    public function applyHierarchicalAttributes();
    /**
     * Check if the passed taxonomy is a WooCommerce taxonomy.
     *
     * @param string $taxonomy The taxonomy
     * @return boolean
     */
    public function isWooCommerceTaxonomy($taxonomy);
}
