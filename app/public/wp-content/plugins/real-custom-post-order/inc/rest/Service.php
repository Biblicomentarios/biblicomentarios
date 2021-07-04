<?php

namespace DevOwl\RealCustomPostOrder\rest;

use DevOwl\RealCustomPostOrder\Vendor\MatthiasWeb\Utils\Service as UtilsService;
use DevOwl\RealCustomPostOrder\base\UtilsProvider;
use DevOwl\RealCustomPostOrder\sortable\AbstractSortable;
use DevOwl\RealCustomPostOrder\sortable\PostSortable;
use WP_REST_Request;
use WP_REST_Response;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Create an example REST Service.
 */
class Service {
    use UtilsProvider;
    /**
     * C'tor.
     *
     * @codeCoverageIgnore
     */
    private function __construct() {
        // Silence is golden.
    }
    /**
     * Register endpoints.
     */
    public function rest_api_init() {
        $namespace = \DevOwl\RealCustomPostOrder\Vendor\MatthiasWeb\Utils\Service::getNamespace($this);
        register_rest_route($namespace, '/move', [
            'methods' => 'POST',
            'callback' => [$this, 'routeMove'],
            'permission_callback' => [$this, 'permission_callback'],
            'args' => [
                'type' => [
                    'type' => 'string',
                    'required' => \true,
                    'enum' => \DevOwl\RealCustomPostOrder\sortable\AbstractSortable::getTypes()
                ],
                'sequence' => [
                    // sequence is not validated here, because it is done by AbstractSortable
                    'required' => \true
                ]
            ]
        ]);
        register_rest_route($namespace, '/post/firstTimePointer', [
            'methods' => 'DELETE',
            'callback' => [$this, 'routePostFirstTimePointer'],
            'permission_callback' => [$this, 'permission_callback']
        ]);
    }
    /**
     * Check if user is allowed to call this service requests.
     */
    public function permission_callback() {
        return current_user_can('edit_posts');
    }
    /**
     * See API docs.
     *
     * @param WP_REST_Request $request
     * @api {post} /real-custom-post-order/v1/move Move an item by type and sequence
     * @apiHeader {string} X-WP-Nonce
     * @apiParam {string} type
     * @apiParam {int[]} sequence The ordered entries sequence (does not need to be the complete entries sequence, only affected!)
     * @apiName Move
     * @apiGroup Service
     * @apiVersion 1.0.0
     * @apiPermission edit_posts
     */
    public function routeMove($request) {
        // @codeCoverageIgnoreStart
        if (!\defined('PHPUNIT_FILE')) {
            require_once ABSPATH . '/wp-admin/includes/screen.php';
        }
        // @codeCoverageIgnoreEnd
        $result = \DevOwl\RealCustomPostOrder\sortable\AbstractSortable::getTypeInstance(
            $request->get_param('type')
        )->doUpdateByIntSequence($request->get_param('sequence'));
        if (!is_wp_error($result)) {
            return new \WP_REST_Response([]);
        }
        return $result;
    }
    /**
     * See API docs.
     *
     * @api {delete} /real-custom-post-order/v1/post/firstTimePointer Dismiss first-time-pointer
     * @apiHeader {string} X-WP-Nonce
     * @apiName DeleteFirstTimePointer
     * @apiGroup Service
     * @apiVersion 1.0.0
     * @apiPermission edit_posts
     */
    public function routePostFirstTimePointer() {
        \DevOwl\RealCustomPostOrder\sortable\PostSortable::get()->showFirstTimePointer(\true);
        return new \WP_REST_Response([]);
    }
    /**
     * New instance.
     *
     * @codeCoverageIgnore
     */
    public static function instance() {
        return new \DevOwl\RealCustomPostOrder\rest\Service();
    }
}
