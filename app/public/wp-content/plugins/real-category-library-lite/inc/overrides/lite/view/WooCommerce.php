<?php

namespace DevOwl\RealCategoryLibrary\lite\view;

\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
trait WooCommerce {
    // Documented in IOverrideWooCommerce
    public function init() {
        // Silence is golden.
    }
    // Documented in IOverrideWooCommerce
    public function applyHierarchicalAttributes() {
        // Silence is golden.
    }
    // Documented in IOverrideWooCommerce
    public function isWooCommerceTaxonomy($taxonomy) {
        return \false;
    }
}
