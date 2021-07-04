<?php

namespace DevOwl\RealCategoryLibrary\overrides\interfce;

interface IOverrideTaxTree {
    /**
     * Build the href query url for the category slug.
     *
     * @param string $slug The category slug
     * @return string
     */
    public function buildQueryUrl($slug);
    /**
     * Checks if the passed slug and category id is the current url.
     *
     * @param string $category
     * @return boolean
     */
    public function isActive($category);
    /**
     * Check if the current selected category type is "All posts". You have to pass a currentUrl for this attribute.
     *
     * @return boolean
     */
    public function isCurrentAllPosts();
}
