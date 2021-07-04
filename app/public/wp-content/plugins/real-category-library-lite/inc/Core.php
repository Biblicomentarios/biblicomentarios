<?php

namespace DevOwl\RealCategoryLibrary;

use DevOwl\RealCategoryLibrary\base\Core as BaseCore;
use DevOwl\RealCategoryLibrary\comp\TablePress;
use DevOwl\RealCategoryLibrary\lite\Core as LiteCore;
use DevOwl\RealCategoryLibrary\overrides\interfce\IOverrideCore;
use DevOwl\RealCategoryLibrary\rest\Options;
use DevOwl\RealCategoryLibrary\rest\Post;
use DevOwl\RealCategoryLibrary\rest\Service;
use DevOwl\RealCategoryLibrary\rest\Term;
use DevOwl\RealCategoryLibrary\view\ConfigPage;
use DevOwl\RealCategoryLibrary\view\WooCommerce;
use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service as UtilsService;
use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\ServiceNoStore;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// Just for development purposes, you can ignore this!
if (\defined('RCL_DEVELOPMENT_ENV') && \constant('RCL_DEVELOPMENT_ENV')) {
    require_once RCL_INC . 'base/others/development-env.php';
}
// @codeCoverageIgnoreEnd
/**
 * Singleton core class which handles the main system for plugin. It includes
 * registering of the autoload, all hooks (actions & filters) (see BaseCore class).
 */
class Core extends \DevOwl\RealCategoryLibrary\base\Core implements
    \DevOwl\RealCategoryLibrary\overrides\interfce\IOverrideCore {
    use LiteCore;
    const TERMS_PRIORITY = 9999999;
    /**
     * The minimal required capability so a user can manage options for RCL.
     */
    const MANAGE_MIN_CAPABILITY = 'manage_options';
    /**
     * Singleton instance.
     */
    private static $me;
    /**
     * The tax order.
     *
     * @var TaxOrder
     */
    private $taxOrder;
    /**
     * The WooCommerce handler.
     *
     * @var WooCommerce
     */
    private $wooCommerce;
    /**
     * The taxonomy tree.
     *
     * @var TaxTree
     */
    private $taxTree;
    /**
     * The config page.
     *
     * @var ConfigPage
     */
    private $configPage;
    /**
     * See RpmInitiator.
     *
     * @var RpmInitiator
     */
    private $rpmInitiator;
    /**
     * Application core constructor.
     */
    protected function __construct() {
        parent::__construct();
        // Load no-namespace API functions
        foreach (['general'] as $apiInclude) {
            require_once RCL_INC . 'api/' . $apiInclude . '.php';
        }
        // Enable `no-store` for our relevant WP REST API endpoints
        \DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\ServiceNoStore::hook(
            '/' . \DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service::getNamespace($this)
        );
        $this->wooCommerce = new \DevOwl\RealCategoryLibrary\view\WooCommerce();
        add_action('init', [$this->getWooCommerce(), 'init'], 2);
        $this->rpmInitiator = new \DevOwl\RealCategoryLibrary\RpmInitiator();
        $this->rpmInitiator->start();
        $this->overrideConstruct();
        $this->overrideConstructFreemium();
        (new \DevOwl\RealCategoryLibrary\AdInitiator())->start();
    }
    /**
     * The init function is fired even the init hook of WordPress. If possible
     * it should register all hooks to have them in one place.
     */
    public function init() {
        $this->taxOrder = new \DevOwl\RealCategoryLibrary\TaxOrder();
        $this->configPage = \DevOwl\RealCategoryLibrary\view\ConfigPage::instance();
        // Compatibility
        $tablePressComp = \DevOwl\RealCategoryLibrary\comp\TablePress::instance();
        if ($tablePressComp->isActive()) {
            /* https://github.com/TobiasBg/TablePress/issues/148
               add_filter('RCL/Available', [$tablePressComp, 'available'], 10, 4);
               add_filter('RCL/Typenow', [$tablePressComp, 'typenow']);
               add_filter('RCL/TableCheckboxName', [$tablePressComp, 'tableCheckboxName']);
               add_filter('RCL/ForcePostTypes', [$tablePressComp, 'forcePostType']);*/
        }
        // Register all your hooks here
        add_action('rest_api_init', [\DevOwl\RealCategoryLibrary\rest\Service::instance(), 'rest_api_init']);
        add_action('rest_api_init', [new \DevOwl\RealCategoryLibrary\rest\Term(), 'rest_api_init']);
        add_action('rest_api_init', [new \DevOwl\RealCategoryLibrary\rest\Post(), 'rest_api_init']);
        add_action('rest_api_init', [\DevOwl\RealCategoryLibrary\rest\Options::instance(), 'rest_api_init']);
        add_action('created_term', [$this->getTaxOrder(), 'created_term'], 10, 3);
        add_action('admin_enqueue_scripts', [$this->getAssets(), 'admin_enqueue_scripts']);
        add_action('wp_enqueue_scripts', [$this->getAssets(), 'wp_enqueue_scripts']);
        add_action('admin_menu', [$this->getConfigPage(), 'admin_menu']);
        add_filter(
            'plugin_action_links_' . plugin_basename(RCL_FILE),
            [$this->getConfigPage(), 'plugin_action_links'],
            10,
            2
        );
        add_filter('get_terms_orderby', [$this->getTaxOrder(), 'get_terms_orderby'], self::TERMS_PRIORITY, 2);
        add_filter('wp_get_object_terms', [$this->getTaxOrder(), 'wp_get_object_terms'], self::TERMS_PRIORITY, 3);
        add_filter('get_the_terms', [$this->getTaxOrder(), 'wp_get_object_terms'], self::TERMS_PRIORITY, 3);
        add_filter('get_terms', [$this->getTaxOrder(), 'wp_get_object_terms'], self::TERMS_PRIORITY, 3);
        add_filter('tag_cloud_sort', [$this->getTaxOrder(), 'wp_get_object_terms'], self::TERMS_PRIORITY, 3);
        add_filter('acf/format_value_for_api', [$this->getTaxOrder(), 'wp_get_object_terms'], self::TERMS_PRIORITY);
        add_filter('get_the_categories', [$this->getTaxOrder(), 'wp_get_object_terms'], self::TERMS_PRIORITY, 3);
        add_filter('RCL/Sorting', [$this->getTaxOrder(), 'disable_by_taxonomy'], 10, 2);
        add_filter('woocommerce_products_general_settings', [
            $this->getWooCommerce(),
            'woocommerce_products_general_settings'
        ]);
        $this->overrideInit();
    }
    /**
     * Get the tax order.
     */
    public function getTaxOrder() {
        return $this->taxOrder;
    }
    /**
     * Get the WooCommerce handler.
     */
    public function getWooCommerce() {
        return $this->wooCommerce;
    }
    /**
     * Get the current taxonomy tree.
     */
    public function getDefaultTaxTree() {
        return $this->taxTree === null ? ($this->taxTree = new \DevOwl\RealCategoryLibrary\TaxTree()) : $this->taxTree;
    }
    /**
     * Get config page.
     *
     * @codeCoverageIgnore
     */
    public function getConfigPage() {
        return $this->configPage;
    }
    /**
     * Get ad initiator from `real-product-manager-wp-client`.
     *
     * @codeCoverageIgnore
     */
    public function getRpmInitiator() {
        return $this->rpmInitiator;
    }
    /**
     * Get singleton core class.
     *
     * @return Core
     */
    public static function getInstance() {
        return !isset(self::$me) ? (self::$me = new \DevOwl\RealCategoryLibrary\Core()) : self::$me;
    }
}
// Inherited from packages/utils/src/Service
/**
 * See API docs.
 *
 * @api {get} /real-category-library/v1/plugin Get plugin information
 * @apiHeader {string} X-WP-Nonce
 * @apiName GetPlugin
 * @apiGroup Plugin
 *
 * @apiSuccessExample {json} Success-Response:
 * {
 *     Name: "My plugin",
 *     PluginURI: "https://example.com/my-plugin",
 *     Version: "0.1.0",
 *     Description: "This plugin is doing something.",
 *     Author: "<a href="https://example.com">John Smith</a>",
 *     AuthorURI: "https://example.com",
 *     TextDomain: "my-plugin",
 *     DomainPath: "/languages",
 *     Network: false,
 *     Title: "<a href="https://example.com">My plugin</a>",
 *     AuthorName: "John Smith"
 * }
 * @apiVersion 0.1.0
 */
