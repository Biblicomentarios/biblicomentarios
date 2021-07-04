<?php

namespace DevOwl\RealCategoryLibrary\rest;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service as UtilsService;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
/**
 * Enables the /posts REST.
 */
class Post {
    use UtilsProvider;
    /**
     * Register endpoints.
     */
    public function rest_api_init() {
        $namespace = \DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service::getNamespace($this);
        register_rest_route($namespace, '/posts/bulk/move', [
            'methods' => 'PUT',
            'callback' => [$this, 'updateItem'],
            'permission_callback' => [$this, 'permission_callback']
        ]);
    }
    /**
     * Check if user is allowed to call this service requests.
     */
    public function permission_callback() {
        $permit = \DevOwl\RealCategoryLibrary\rest\Service::permit();
        return $permit === null ? \true : $permit;
    }
    /**
     * See API docs.
     *
     * @param WP_REST_Request $request
     * @api {put} /real-category-library/v1/posts/bulk/move Move/Copy multiple posts
     * @apiParam {int[]} ids The post ids to move / copy
     * @apiParam {int} to The term id
     * @apiParam {boolean} isCopy If true the post is appended to the category
     * @apiParam {string} taxonomy The taxonomy
     * @apiName UpdatePostBulkMove
     * @apiGroup Post
     * @apiVersion 1.0.0
     * @apiPermission manage_categories
     */
    public function updateItem($request) {
        $ids = $request->get_param('ids');
        $to = \intval($request->get_param('to'));
        $isCopy = $request->get_param('isCopy');
        $isCopy = \gettype($isCopy) === 'string' ? $isCopy === 'true' : $isCopy;
        $taxonomy = $request->get_param('taxonomy');
        if (!\is_array($ids) || \count($ids) === 0 || $to === null) {
            return new \WP_Error('rest_rcl_posts_bulk_move_failed', __('Something went wrong.', RCL_TD), [
                'status' => 500
            ]);
        }
        foreach ($ids as $value) {
            wp_set_object_terms(\intval($value), $to, $taxonomy, $isCopy);
        }
        return new \WP_REST_Response(\true);
    }
}
