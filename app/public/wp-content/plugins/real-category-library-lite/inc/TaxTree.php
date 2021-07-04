<?php

namespace DevOwl\RealCategoryLibrary;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
use DevOwl\RealCategoryLibrary\lite\TaxTree as LiteTaxTree;
use DevOwl\RealCategoryLibrary\overrides\interfce\IOverrideTaxTree;
use WP_Term;
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
/**
 * Tree structure management.
 */
class TaxTree implements \DevOwl\RealCategoryLibrary\overrides\interfce\IOverrideTaxTree {
    use UtilsProvider;
    use LiteTaxTree;
    private $typenow;
    private $taxnow;
    private $query_var;
    private $query_args = [];
    const SKIP_POST_TYPES = ['wp_block', 'attachment'];
    /**
     * The categories for the taxonomy. The categories are lazy loaded.
     */
    private $cats = null;
    /**
     * Array of available taxonomies for the post type.
     */
    private $taxos = null;
    /**
     * The count of the categories.
     */
    private $cnt = 0;
    /**
     * The count of all posts.
     */
    private $cntPosts = 0;
    /**
     * Determines if the current taxonomy is a WC taxonomy.
     */
    private $isWcTaxonomy = \false;
    /**
     * The selected id if currentUrl is set.
     */
    private $selectedId = 'ALL';
    /**
     * Read the category tree flat (getCats()
     *
     * @since 3.1.0
     */
    private $flat = \false;
    /**
     * Read the category tree within this parent, this does not include child nodes (getCats()
     *
     * @since 3.1.0
     */
    private $parent = null;
    /**
     * Localize this variable to your frontend so the posts can be dragged (also for custom UI's).
     *
     * @since 4.0.0
     * @var string
     */
    private $tableCheckboxName;
    /**
     * Create a new tree instance.
     *
     * @param string $typecustom Use this post type instead of current
     * @param string $taxcustom Use this taxonomy instead of current
     * @param string $currentUrl The current url to detect the active category
     * @param boolean $flat Read the category tree flat (getCats(), since 3.1.0)
     * @param int $parent Read the category tree within this parent, this does not include child nodes (getCats(), since 3.1.0)
     */
    public function __construct(
        $typecustom = \false,
        $taxcustom = \false,
        $currentUrl = null,
        $flat = \false,
        $parent = null
    ) {
        global $typenow;
        /**
         * If a tax tree got requested, we can override the `global $typenow` so we can enable
         * the category tree also for custom UIs.
         *
         * @param {string} $typenow The currently active post type
         * @return {string}
         * @since 4.0.0
         * @hook RCL/Typenow
         */
        $this->typenow = $typecustom !== \false ? $typecustom : apply_filters('RCL/Typenow', $typenow);
        $this->flat = $flat;
        $this->parent = $parent;
        // Parse current url arguments
        if (\gettype($currentUrl) === 'string') {
            $args = [];
            // TODO: we could remove `urldecode` as this is no longer needed
            $parsedUrl = \parse_url(\urldecode($currentUrl));
            if (isset($parsedUrl['query'])) {
                \parse_str($parsedUrl['query'], $args);
                $this->query_args = $args;
            }
        }
        // Fetch the taxonomies which are able to filter
        $taxonomy_objects = get_object_taxonomies($this->typenow, 'objects');
        $this->taxos = [];
        foreach ($taxonomy_objects as $key => $value) {
            if (\boolval($value->hierarchical)) {
                $this->taxos[$key] = $value;
                $this->taxos[$key]->objkey = $key;
            }
        }
        // Check if taxonomy is ready and set it to users' current
        if (\count($this->taxos) > 0) {
            \reset($this->taxos);
            // Get current taxonomy
            $taxnow = $taxcustom !== \false ? $taxcustom : get_user_option('rcl_tax_' . $this->typenow);
            if ($taxnow !== \false && isset($this->taxos[$taxnow])) {
                $this->taxnow = $this->taxos[$taxnow];
            } else {
                $this->taxnow = \current($this->taxos);
            }
            $this->init();
        }
        /**
         * Localize this variable to your frontend so the posts can be dragged (also for custom UI's).
         *
         * @param {string} $tableCheckboxName By default `post[]`
         * @return {string}
         * @since 4.0.0
         * @hook RCL/TableCheckboxName
         */
        $this->tableCheckboxName = apply_filters('RCL/TableCheckboxName', 'post[]');
    }
    /**
     * Initialize the tree.
     */
    private function init() {
        // Initialize post count
        $num_posts = wp_count_posts($this->typenow, 'readable');
        $total_posts = \array_sum((array) $num_posts);
        foreach (get_post_stati(['show_in_admin_all_list' => \false]) as $state) {
            $total_posts -= $num_posts->{$state};
        }
        $this->cntPosts = $total_posts;
        // Get query var of current tax
        $category_args = get_taxonomy($this->taxnow->objkey);
        if ($category_args->query_var === \false || $category_args === \true) {
            $this->query_var = $this->taxnow->objkey;
        } else {
            $this->query_var = $category_args->query_var;
        }
        // Save if WC attribute
        $this->isWcTaxonomy = $this->getCore()
            ->getWooCommerce()
            ->isWoocommerceTaxonomy($this->query_var);
    }
    /**
     * Enrich the category object with additional data.
     *
     * @param {WP_Term} $category The category
     */
    public function enrichTerm($category) {
        if (\is_object($category)) {
            $category->queryArgs = $this->buildQueryUrl($category->slug);
            $category->editableSlug = apply_filters('editable_slug', $category->slug);
            $category->name = \htmlspecialchars_decode($category->name, \ENT_NOQUOTES);
        }
        return $category;
    }
    /**
     * Minify a WP_Term object.
     *
     * @param WP_Term $term
     */
    private function minify($term) {
        $result = [
            'term_id' => $term->term_id,
            'name' => $term->name,
            'count' => $term->count,
            'taxonomy' => $term->taxonomy,
            'slug' => $term->slug
        ];
        return (object) $result;
    }
    /**
     * Get the categories tree for the current post type and taxonomy. This is a
     * rescursive method.
     *
     * @param int $parent The parent
     * @param array $categories All available categories
     * @param boolean $flat Read the category flat (since 3.1.0)
     * @param boolean $readChildNodes Read child nodes (since 3.1.0)
     * @return array
     */
    public function getCategoryTree($parent, $categories, $flat = \false, $readChildNodes = \true) {
        // Initially build a parent -> children tree (performance)
        if (!isset($categories['parents']) && $readChildNodes) {
            $parents = [];
            foreach ($categories as &$category) {
                /**
                 * Should a given category / term be visible in the category tree?
                 *
                 * @param {boolean} $show True for show and false for hide
                 * @param {WP_Term} $term
                 * @return {boolean}
                 * @hook RCL/Node/Visible
                 * @since 3.2.22
                 */
                $show = apply_filters('RCL/Node/Visible', \true, $category);
                if (!$show) {
                    continue;
                }
                $parentIdx = 'p' . $category->category_parent;
                if (!isset($parents[$parentIdx])) {
                    $parents[$parentIdx] = [];
                }
                $parents[$parentIdx][] = $this->minify($category);
            }
            $categories = ['parents' => $parents];
        }
        $result = [];
        if ($readChildNodes) {
            // This is the usual way to read children
            $parentIdx = 'p' . $parent;
            if (isset($categories['parents'][$parentIdx])) {
                foreach ($categories['parents'][$parentIdx] as $category) {
                    $children = $this->getCategoryTree($category->term_id, $categories, $flat);
                    if (!$flat) {
                        $category->childNodes = $children;
                    }
                    $this->enrichTerm($category);
                    unset($category->slug);
                    // Check if active
                    if ($this->isActive($category)) {
                        $this->selectedId = $category->term_id;
                    }
                    $result[] = $category;
                    if ($flat) {
                        foreach ($children as $child) {
                            $result[] = $child;
                        }
                    }
                }
            }
        } else {
            // Read all children as flat array
            foreach ($categories as $category) {
                $category = $this->minify($category);
                $this->enrichTerm($category);
                // Check if active
                if ($this->isActive($category)) {
                    $this->selectedId = $category->cat_ID;
                }
                $result[] = $category;
            }
        }
        return $result;
    }
    /**
     * Check if this post type and taxonomy is available for the category view.
     *
     * @param boolean $checkScreen If `true`, the current screen will be checked, too (must be `edit`)
     * @return boolean
     */
    public function isAvailable($checkScreen = \false) {
        $available =
            $this->getTypeNow() !== null &&
            $this->getTaxNow() !== null &&
            !\in_array($this->getTypeNow(), self::SKIP_POST_TYPES, \true) &&
            ($checkScreen ? \in_array(get_current_screen()->base, ['edit'], \true) : \true);
        /**
         * Allows you to disable the taxonomy tree (i.e. the whole plugin) for a
         * type or taxonomy.
         *
         * @param {boolean} $enabled If true the plugin is available for this post type / taxonomy
         * @param {string} $type The post type
         * @param {object} $taxonomy The taxonomy
         * @param {boolean} $checkScreen If `true`, the current screen will be checked, too (must be `edit`) (since 4.0.0)
         * @return {boolean}
         * @hook RCL/Available
         */
        return apply_filters('RCL/Available', $available, $this->getTypeNow(), $this->getTaxNow(), $checkScreen);
    }
    /**
     * Get the count for the available categories for the post type and taxonomy.
     *
     * @return int
     */
    public function getCnt() {
        return $this->cnt;
    }
    /**
     * Get the posts count for all posts in this post type and taxonomy.
     *
     * @return int
     */
    public function getPostCnt() {
        return $this->cntPosts;
    }
    /**
     * Get all available categories for this post type and taxonomy.
     *
     * @return array
     */
    public function getCats() {
        if ($this->cats === null) {
            // Fix counts (currently, WooCommerce does have different behavior in `get_term` and `get_terms`, see also https://stackoverflow.com/a/64385326/5506547)
            add_filter('wp_doing_ajax', '__return_true');
            // New Initialization of categories so lazy loading is allowed
            $cats = get_categories([
                // Compatibility with "Password Protected Categories"; do not exclude private categories
                'ppc_check' => \true,
                'hide_empty' => 0,
                //'number' => 100,
                'type' => $this->typenow,
                'taxonomy' => $this->taxnow->objkey,
                'pad_counts' => 1,
                'parent' => $this->parent !== null ? $this->parent : ''
            ]);
            /**
             * Allow to modify the read categories for the current taxonomy / post type.
             *
             * @param {object[]} $categories List of categories (result of `get_categories`)
             * @param {string} $taxonomy
             * @param {string} $post_type
             * @return {array}
             * @hook RCL/Categories
             * @since 4.0.4
             */
            $cats = apply_filters('RCL/Categories', $cats, $this->getTaxNow(), $this->getTypeNow());
            $this->cnt = \count($cats);
            remove_filter('wp_doing_ajax', '__return_true');
            $this->cats = $this->getCategoryTree(0, $cats, $this->flat, $this->parent === null);
        }
        return $this->cats;
    }
    /**
     * Get the post type.
     *
     * @return string
     */
    public function getTypeNow() {
        return $this->typenow;
    }
    /**
     * Get the taxonomy.
     *
     * @return mixed
     */
    public function getTaxNow() {
        return $this->taxnow;
    }
    /**
     * Get all available taxonomies.
     *
     * @param boolean $prepared If true only key -> label is returned
     * @return array
     */
    public function getTaxos($prepared = \false) {
        $result = $this->taxos;
        if ($prepared) {
            $result = [];
            foreach ($this->taxos as $key => $value) {
                $result[$key] = $value->label;
            }
        }
        return $result;
    }
    /**
     * Get the query variable name.
     *
     * @return string
     */
    public function getQueryVar() {
        return $this->query_var;
    }
    /**
     * Get the query arguments.
     *
     * @return mixed
     */
    public function getQueryArgs() {
        return $this->query_args;
    }
    /**
     * Get the selected id. You have to pass a currentUrl for this attribute.
     *
     * @return string
     */
    public function getSelectedId() {
        return $this->isCurrentAllPosts() || $this->selectedId === 'ALL' ? 'ALL' : $this->selectedId;
    }
    /**
     * Getter.
     *
     * @codeCoverageIgnore
     */
    public function getTableCheckboxName() {
        return $this->tableCheckboxName;
    }
    /**
     * Get all available post types ready for the taxonomy tree.
     */
    public static function getAvailablePostTypes() {
        $all_post_types = get_post_types([], 'objects');
        $post_types = get_post_types(['show_ui' => \true], 'objects');
        /**
         * Currently, Real Category Management can only handle post types with `show_ui=true`.
         *
         * @param {string[]} $post_types
         * @return {string[]}
         * @hook RCL/ForcePostTypes
         * @since 4.0.0
         */
        $specialPostTypes = apply_filters('RCL/ForcePostTypes', []);
        foreach ($specialPostTypes as $specialPostType) {
            if (isset($all_post_types[$specialPostType])) {
                $post_types[$specialPostType] = $all_post_types[$specialPostType];
            }
        }
        foreach ($post_types as &$post_type) {
            $name = $post_type->name;
            if (\in_array($name, self::SKIP_POST_TYPES, \true)) {
                unset($post_types[$name]);
                continue;
            }
            $taxTree = new \DevOwl\RealCategoryLibrary\TaxTree($name);
            $post_type = [
                'link' => admin_url('edit.php?post_type=' . $name),
                'label' => $post_type->label,
                'available' => $taxTree->isAvailable(),
                'active' => \DevOwl\RealCategoryLibrary\Options::getInstance()->isActive($taxTree),
                'fastMode' => \DevOwl\RealCategoryLibrary\Options::getInstance()->isFastMode($taxTree)
            ];
        }
        return $post_types;
    }
}
