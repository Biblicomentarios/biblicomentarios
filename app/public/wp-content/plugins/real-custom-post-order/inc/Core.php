<?php

namespace DevOwl\RealCustomPostOrder;

use DevOwl\RealCustomPostOrder\base\Core as BaseCore;
use DevOwl\RealCustomPostOrder\rest\Service;
use DevOwl\RealCustomPostOrder\sortable\AbstractSortable;
use DevOwl\RealCustomPostOrder\sortable\PostAdjacentActions;
use DevOwl\RealCustomPostOrder\sortable\PostSortable;
use DevOwl\RealCustomPostOrder\view\PostScreenSettings;
use DevOwl\RealCustomPostOrder\Vendor\MatthiasWeb\Utils\Service as UtilsService;
use DevOwl\RealCustomPostOrder\Vendor\MatthiasWeb\Utils\ServiceNoStore;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Singleton core class which handles the main system for plugin. It includes
 * registering of the autoload, all hooks (actions & filters) (see BaseCore class).
 */
class Core extends \DevOwl\RealCustomPostOrder\base\Core {
    /**
     * Singleton instance.
     */
    private static $me;
    /**
     * Application core constructor.
     */
    protected function __construct() {
        parent::__construct();
        \DevOwl\RealCustomPostOrder\sortable\AbstractSortable::register(
            'post',
            \DevOwl\RealCustomPostOrder\sortable\PostSortable::class
        );
        // Enable `no-store` for our relevant WP REST API endpoints
        \DevOwl\RealCustomPostOrder\Vendor\MatthiasWeb\Utils\ServiceNoStore::hook(
            '/' . \DevOwl\RealCustomPostOrder\Vendor\MatthiasWeb\Utils\Service::getNamespace($this)
        );
        (new \DevOwl\RealCustomPostOrder\AdInitiator())->start();
    }
    /**
     * The init function is fired even the init hook of WordPress. If possible
     * it should register all hooks to have them in one place.
     */
    public function init() {
        $postAdjacent = \DevOwl\RealCustomPostOrder\sortable\PostAdjacentActions::instance();
        $postScreenSettings = \DevOwl\RealCustomPostOrder\view\PostScreenSettings::instance();
        // Register all your hooks here
        add_action('pre_get_posts', [\DevOwl\RealCustomPostOrder\sortable\PostSortable::get(), 'pre_get_posts']);
        add_action('rest_api_init', [\DevOwl\RealCustomPostOrder\rest\Service::instance(), 'rest_api_init']);
        add_action('admin_enqueue_scripts', [$this->getAssets(), 'admin_enqueue_scripts']);
        add_filter('check_admin_referer', [$postScreenSettings, 'check_admin_referer'], 10, 2);
        add_filter('screen_settings', [$postScreenSettings, 'screen_settings'], 999, 2);
        add_filter('get_previous_post_where', [$postAdjacent, 'get_previous_post_where']);
        add_filter('get_previous_post_sort', [$postAdjacent, 'get_previous_post_sort']);
        add_filter('get_next_post_where', [$postAdjacent, 'get_next_post_where']);
        add_filter('get_next_post_sort', [$postAdjacent, 'get_next_post_sort']);
        // add_action('wp_enqueue_scripts', [$this->getAssets(), 'wp_enqueue_scripts']); // we never need assets in frontend
    }
    /**
     * Get singleton core class.
     *
     * @return Core
     */
    public static function getInstance() {
        return !isset(self::$me) ? (self::$me = new \DevOwl\RealCustomPostOrder\Core()) : self::$me;
    }
}
// Inherited from packages/utils/src/Service
/**
 * See API docs.
 *
 * @api {get} /real-custom-post-order/v1/plugin Get plugin information
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
 * @apiVersion 1.0.0
 */
