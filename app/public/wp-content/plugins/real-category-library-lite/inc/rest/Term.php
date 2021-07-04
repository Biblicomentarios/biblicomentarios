<?php

namespace DevOwl\RealCategoryLibrary\rest;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
use DevOwl\RealCategoryLibrary\TaxTree;
use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service as UtilsService;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
/**
 * Enables the /terms REST for all hierarchical categories.
 */
class Term {
    use UtilsProvider;
    /**
     * Register endpoints.
     */
    public function rest_api_init() {
        require_once ABSPATH . '/wp-admin/includes/taxonomy.php';
        $namespace = \DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service::getNamespace($this);
        register_rest_route($namespace, '/terms/(?P<id>\\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'updateItem'],
            'permission_callback' => [$this, 'permission_callback']
        ]);
        register_rest_route($namespace, '/terms/(?P<id>\\d+)', [
            'methods' => 'DELETE',
            'callback' => [$this, 'deleteItem'],
            'permission_callback' => [$this, 'permission_callback']
        ]);
        register_rest_route($namespace, '/terms', [
            'methods' => 'POST',
            'callback' => [$this, 'createItem'],
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
     * @api {post} /real-category-library/v1/terms Create a new term
     * @apiParam {string} name The new name for the term
     * @apiParam {integer} parent The parent (0 for no parent)
     * @apiParam {string} type The post type
     * @apiParam {string} taxonomy The taxonomy
     * @apiName DeleteTerm
     * @apiGroup Term
     * @apiVersion 1.0.0
     * @apiPermission manage_categories
     */
    public function createItem($request) {
        if (($permit = \DevOwl\RealCategoryLibrary\rest\Service::permit()) !== null) {
            return $permit;
        }
        $name = $request->get_param('name');
        $parent = $request->get_param('parent');
        $taxonomy = $request->get_param('taxonomy');
        $type = $request->get_param('type');
        $insert = wp_insert_category(
            ['cat_name' => $name, 'category_parent' => $parent, 'taxonomy' => $taxonomy],
            \true
        );
        if (is_wp_error($insert)) {
            return new \WP_Error(
                'rest_rcl_term_' . $insert->get_error_code(),
                \html_entity_decode($insert->get_error_message()),
                ['status' => 500]
            );
        } elseif ($insert === 0) {
            return new \WP_Error('rest_rcl_term_failed', __('Unknown error: The term could not be created.', RCL_TD), [
                'status' => 500
            ]);
        } else {
            $taxTree = new \DevOwl\RealCategoryLibrary\TaxTree($type, $taxonomy);
            return new \WP_REST_Response($taxTree->enrichTerm(get_term($insert)));
        }
    }
    /**
     * See API docs.
     *
     * @param WP_REST_Request $request
     * @api {delete} /real-category-library/v1/terms/:id Delete a term by id
     * @apiParam {string} taxonomy The taxonomy
     * @apiName DeleteTerm
     * @apiGroup Term
     * @apiVersion 1.0.0
     * @apiPermission manage_categories
     */
    public function deleteItem($request) {
        if (($permit = \DevOwl\RealCategoryLibrary\rest\Service::permit()) !== null) {
            return $permit;
        }
        $taxonomy = $request->get_param('taxonomy');
        $id = $request->get_param('id');
        $delete = wp_delete_term($id, $taxonomy);
        if (is_wp_error($delete)) {
            return new \WP_Error(
                'rest_rcl_term_' . $delete->get_error_code(),
                \html_entity_decode($delete->get_error_message()),
                ['status' => 500]
            );
        } elseif ($delete === \false) {
            return new \WP_Error('rest_rcl_term_invalid', __('Category does not exist.', RCL_TD), ['status' => 500]);
        } elseif ($delete === 0) {
            return new \WP_Error('rest_rcl_term_default', __('You can not delete the default category.', RCL_TD), [
                'status' => 500
            ]);
        } else {
            return new \WP_REST_Response($delete);
        }
    }
    /**
     * See API docs.
     *
     * @param WP_REST_Request $request
     * @api {put} /real-category-library/v1/terms/:id Update a term by id
     * @apiParam {string} name The new name for the term
     * @apiParam {string} taxonomy The taxonomy
     * @apiName UpdateTerm
     * @apiGroup Term
     * @apiVersion 1.0.0
     * @apiPermission manage_categories
     */
    public function updateItem($request) {
        if (($permit = \DevOwl\RealCategoryLibrary\rest\Service::permit()) !== null) {
            return $permit;
        }
        $name = $request->get_param('name');
        $taxonomy = $request->get_param('taxonomy');
        $id = $request->get_param('id');
        $slug = sanitize_title($name);
        $update = wp_update_term($id, $taxonomy, ['name' => $name, 'slug' => $slug]);
        if (is_wp_error($update)) {
            return new \WP_Error(
                'rest_rcl_term_' . $update->get_error_code(),
                \html_entity_decode($update->get_error_message()),
                ['status' => 500]
            );
        } else {
            return new \WP_REST_Response($update);
        }
    }
}
