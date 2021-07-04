<?php

namespace DevOwl\RealCategoryLibrary\rest;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
use DevOwl\RealCategoryLibrary\TaxTree;
use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Service as UtilsService;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Create an example REST Service.
 *
 * @codeCoverageIgnore Example implementations gets deleted the most time after plugin creation!
 */
class Service {
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
        register_rest_route($namespace, '/notice/lite', [
            'methods' => 'DELETE',
            'callback' => [$this, 'routeNoticeDismissLite'],
            'permission_callback' => [$this, 'permission_callback_activate_plugins']
        ]);
        register_rest_route($namespace, '/tree', [
            'methods' => 'GET',
            'callback' => [$this, 'routeTree'],
            'permission_callback' => [$this, 'permission_callback']
        ]);
        register_rest_route($namespace, '/hierarchy/(?P<id>\\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'routeHierarchy'],
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
     * Check if user is allowed to call this service requests with `activate_plugins` cap.
     */
    public function permission_callback_activate_plugins() {
        $permit = \DevOwl\RealCategoryLibrary\rest\Service::permit('activate_plugins');
        return $permit === null ? \true : $permit;
    }
    /**
     * See API docs.
     *
     * @api {delete} /real-category-library/v1/notice/lite Dismiss the lite notice for a given time (transient)
     * @apiName DismissLiteNotice
     * @apiGroup Tree
     * @apiVersion 3.2.6
     * @since 3.2.6
     * @apiPermission activate_plugins
     */
    public function routeNoticeDismissLite() {
        $this->getCore()->isLiteNoticeDismissed(\true);
        return new \WP_REST_Response(\true);
    }
    /**
     * See API docs.
     *
     * @param WP_REST_Request $request
     * @api {put} /real-category-library/v1/hierarchy/:id Change a folder position within the hierarchy
     * @apiParam {int} id The folder id
     * @apiParam {int} parent The parent
     * @apiParam {int} totalNextId The total next id to the folder
     * @apiParam {string} type The post type
     * @apiParam {string} taxonomy The taxonomy
     * @apiName PutHierarchy
     * @apiGroup Tree
     * @apiVersion 3.1.0
     * @since 3.1.0 The hierarchy is now single relocate, that means you send a term id with next id
     * @apiPermission manage_categories
     */
    public function routeHierarchy($request) {
        $id = \intval($request->get_param('id'));
        $type = $request->get_param('type');
        $taxonomy = $request->get_param('taxonomy');
        $parent = \intval($request->get_param('parent'));
        $nextId = \intval($request->get_param('nextId'));
        // Check type and taxonomy
        if (!$type || !$taxonomy || !is_taxonomy_hierarchical($taxonomy)) {
            return new \WP_Error('rest_rcl_hierarchy_no_type', __('No type or valid taxonomy provided.', RCL_TD), [
                'status' => 500
            ]);
        }
        $taxOrder = $this->getCore()->getTaxOrder();
        $hierarchy = $taxOrder->relocate($type, $taxonomy, $id, $parent, $nextId);
        if (is_wp_error($hierarchy)) {
            return $hierarchy;
        }
        return new \WP_REST_Response(\true);
    }
    /**
     * See API docs.
     *
     * @param WP_REST_Request $request
     * @api {get} /real-category-library/v1/tree Get the full categories tree
     * @apiParam {string} type The post type
     * @apiParam {string} taxonomy The taxonomy
     * @apiParam {integer} [id] Only return this term id as result
     * @apiParam {string} [currentUrl] The current url to detect the active item
     * @apiParam {boolean} [remember] If true the selected taxonomy is saved to the user option
     * @apiName GetTree
     * @apiGroup Tree
     * @apiVersion 1.0.0
     * @apiPermission manage_categories
     */
    public function routeTree($request) {
        $type = $request->get_param('type');
        $taxonomy = $request->get_param('taxonomy');
        $id = $request->get_param('id');
        $currentUrl = $request->get_param('currentUrl');
        $remember = (bool) $request->get_param('remember');
        if (empty($type) || empty($taxonomy)) {
            return new \WP_Error('rest_rcl_tree_no_type', __('No type or taxonomy provided.', RCL_TD), [
                'status' => 500
            ]);
        }
        // Receive tree
        $taxTree = new \DevOwl\RealCategoryLibrary\TaxTree($type, $taxonomy, $currentUrl);
        // Save taxonomy to the user
        if ($remember === \true) {
            update_user_option(get_current_user_id(), 'rcl_tax_' . $type, $taxonomy);
        }
        if (\is_numeric($id)) {
            $term = get_term((int) $id);
            if ($term) {
                return new \WP_REST_Response($taxTree->enrichTerm($term));
            } else {
                return new \WP_Error('rest_rcl_tree_term_not_found', __('The passed term id was not found.', RCL_TD), [
                    'status' => 404
                ]);
            }
        } else {
            return new \WP_REST_Response(['tree' => $taxTree->getCats(), 'selectedId' => $taxTree->getSelectedId()]);
        }
    }
    /**
     * Checks if the current user has a given capability and throws an error if not.
     *
     * @param string $cap The capability
     * @throws \WP_Error
     */
    public static function permit($cap = 'manage_categories') {
        if (!current_user_can($cap)) {
            return new \WP_Error('rest_rcl_forbidden', __('Forbidden'), ['status' => 403]);
        }
        if (!wp_rcl_active()) {
            return new \WP_Error(
                'rest_rcl_not_activated',
                __('Real Category Management is not active for the current user.', RCL_TD),
                ['status' => 500]
            );
        }
        return null;
    }
    /**
     * New instance.
     */
    public static function instance() {
        return new \DevOwl\RealCategoryLibrary\rest\Service();
    }
}
