<?php

namespace DevOwl\RealCategoryLibrary\rest;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
use DevOwl\RealCategoryLibrary\Core;
use DevOwl\RealCategoryLibrary\Options as RealCategoryLibraryOptions;
use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service as UtilsService;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * REST service for post type options.
 */
class Options {
    use UtilsProvider;
    /**
     * C'tor.
     */
    private function __construct() {
        // Silence is golden.
    }
    /**
     * Register endpoints.
     */
    public function rest_api_init() {
        $namespace = \DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service::getNamespace($this);
        register_rest_route($namespace, '/options/(?P<post_type>[a-zA-Z0-9_-]+)', [
            'methods' => 'PATCH',
            'callback' => [$this, 'routePostTypePatch'],
            'permission_callback' => [$this, 'permission_callback'],
            'args' => ['active' => ['type' => 'boolean'], 'fastMode' => ['type' => 'boolean']]
        ]);
    }
    /**
     * See API docs.
     *
     * @param WP_REST_Request $request
     * @api {patch} /real-category-library/v1/options/:post_type Change options for a given post type
     * @apiParam {string} post_type The post type
     * @apiParam {boolean} [active] Activate tree view for this post type
     * @apiParam {boolean} [fastMode] Activate fast-mode for this post type
     * @apiName PatchPostType
     * @apiGroup Options
     * @apiVersion 3.6.0
     * @since 3.6.0
     * @apiPermission manage_categories
     */
    public function routePostTypePatch($request) {
        $result = [];
        $post_type = $request->get_param('post_type');
        $active = $request->get_param('active');
        $fastMode = $request->get_param('fastMode');
        if ($active !== null) {
            $result['active'] = \DevOwl\RealCategoryLibrary\Options::getInstance()->setActive($post_type, $active);
        }
        if ($fastMode !== null) {
            $result['fastMode'] = \DevOwl\RealCategoryLibrary\Options::getInstance()->setFastMode(
                $post_type,
                $fastMode
            );
        }
        return new \WP_REST_Response($result);
    }
    /**
     * Check if user is allowed to call this service requests.
     */
    public function permission_callback() {
        $permit = \DevOwl\RealCategoryLibrary\rest\Service::permit(
            \DevOwl\RealCategoryLibrary\Core::MANAGE_MIN_CAPABILITY
        );
        return $permit === null ? \true : $permit;
    }
    /**
     * New instance.
     */
    public static function instance() {
        return new \DevOwl\RealCategoryLibrary\rest\Options();
    }
}
