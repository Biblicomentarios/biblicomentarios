<?php

namespace DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient;

use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\rest\PluginUpdate as RestPluginUpdate;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\rest\Announcement as RestAnnouncement;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\rest\Feedback as RestFeedback;
use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service;
use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\ServiceNoStore;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Core for real-product-manager-wp-client. It is only initialized once and holds all available initiators.
 */
class Core {
    use UtilsProvider;
    /**
     * The initiators need to be declared globally, due to the fact the class instance
     * itself is scoped and can exist more than once.
     */
    const GLOBAL_INITIATORS = 'real_product_manager_wp_client_initiators';
    /**
     * Singleton instance.
     */
    private static $me;
    /**
     * Assets handler.
     *
     * @var Assets
     */
    private $assets;
    /**
     * C'tor.
     */
    private function __construct() {
        $this->assets = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Assets::instance();
        // Enable `no-store` for our relevant WP REST API endpoints
        \DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\ServiceNoStore::hook(
            '/' . \DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service::getNamespace($this)
        );
        add_action('admin_enqueue_scripts', [$this->getAssets(), 'admin_enqueue_scripts'], 9);
        add_action('rest_api_init', [
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\rest\PluginUpdate::instance(),
            'rest_api_init'
        ]);
        add_action('rest_api_init', [
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\rest\Announcement::instance(),
            'rest_api_init'
        ]);
        add_action('rest_api_init', [
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\rest\Feedback::instance(),
            'rest_api_init'
        ]);
    }
    /**
     * Add an initiator.
     *
     * @param AbstractInitiator $initiator
     * @codeCoverageIgnore
     */
    public function addInitiator($initiator) {
        $slug = $initiator->getPluginSlug();
        $this->getInitiators();
        // Initialize global once
        $GLOBALS[self::GLOBAL_INITIATORS][$slug] = $initiator;
        // Initialize plugin updater once
        $initiator->getPluginUpdater()->initialize();
    }
    /**
     * Get initiator by slug.
     *
     * @param string $slug
     * @codeCoverageIgnore
     */
    public function getInitiator($slug) {
        $initiators = $this->getInitiators();
        return isset($initiators[$slug]) ? $initiators[$slug] : null;
    }
    /**
     * Get all initiators.
     *
     * @codeCoverageIgnore
     * @return AbstractInitiator[]
     */
    public function getInitiators() {
        if (!isset($GLOBALS[self::GLOBAL_INITIATORS])) {
            $GLOBALS[self::GLOBAL_INITIATORS] = [];
        }
        return $GLOBALS[self::GLOBAL_INITIATORS];
    }
    /**
     * Get assets handler.
     *
     * @codeCoverageIgnore
     */
    public function getAssets() {
        return $this->assets;
    }
    /**
     * Get singleton core class.
     *
     * @codeCoverageIgnore
     * @return Core
     */
    public static function getInstance() {
        return !isset(self::$me)
            ? (self::$me = new \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core())
            : self::$me;
    }
}
