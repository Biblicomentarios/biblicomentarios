<?php

namespace DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\rest;

use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\Feedback as ClientFeedback;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\UtilsProvider;
use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service as UtilsService;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Create feedback REST service.
 */
class Feedback {
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
        $namespace = \DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service::getNamespace($this);
        register_rest_route($namespace, '/feedback/(?P<slug>[a-zA-Z0-9_-]+)', [
            'methods' => 'POST',
            'callback' => [$this, 'routePost'],
            'permission_callback' => [$this, 'permission_callback'],
            'args' => [
                'reason' => ['type' => 'string', 'required' => \true],
                'note' => ['type' => 'string', 'default' => ''],
                'email' => [
                    'type' => 'string',
                    'default' => '',
                    'validate_callback' => function ($param) {
                        return empty($param) || is_email($param);
                    }
                ]
            ]
        ]);
    }
    /**
     * Check if user is allowed to call this service requests.
     */
    public function permission_callback() {
        return current_user_can('activate_plugins');
    }
    /**
     * See API docs.
     *
     * @param WP_REST_Request $request
     * @api {post} /real-product-manager-wp-client/v1/feedback/:slug Create a feedback
     * @apiHeader {string} X-WP-Nonce
     * @apiParam {string} slug
     * @apiParam {string} reason
     * @apiParam {string} [note]
     * @apiParam {string} [email]
     * @apiName Create
     * @apiPermission activate_plugins
     * @apiGroup Feedback
     * @apiVersion 1.0.0
     */
    public function routePost($request) {
        $slug = $request->get_param('slug');
        $initiator = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core::getInstance()->getInitiator(
            $slug
        );
        if ($initiator === null) {
            return new \WP_Error('rest_not_found', 'Not found', ['status' => 404]);
        } else {
            $reason = $request->get_param('reason');
            $note = $request->get_param('note');
            $email = $request->get_param('email');
            $result = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\Feedback::instance(
                $initiator->getPluginUpdater()
            )->post($reason, $note, $email);
            if (is_wp_error($result)) {
                return $result;
            }
            return new \WP_REST_Response(['success' => \true]);
        }
    }
    /**
     * New instance.
     *
     * @codeCoverageIgnore
     */
    public static function instance() {
        return new \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\rest\Feedback();
    }
}
