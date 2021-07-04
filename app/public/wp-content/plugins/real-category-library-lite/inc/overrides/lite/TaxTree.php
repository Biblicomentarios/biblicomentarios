<?php

namespace DevOwl\RealCategoryLibrary\lite;

\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
trait TaxTree {
    // Documented in IOverrideTaxTree
    public function buildQueryUrl($slug) {
        return ['post_type' => 'post', 'category_name' => $slug];
    }
    // Documented in IOverrideTaxTree
    public function isActive($category) {
        $query = $this->getQueryArgs();
        $slug = $category->editableSlug;
        return isset($query['category_name']) && $query['category_name'] === $slug;
    }
    // Documented in IOverrideTaxTree
    public function isCurrentAllPosts() {
        $query = $this->getQueryArgs();
        $catId = isset($query['cat']) ? $query['cat'] : null;
        return empty(isset($query['category_name']) ? $query['category_name'] : '');
    }
}
