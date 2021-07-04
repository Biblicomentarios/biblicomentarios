<?php

namespace DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\rest;

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
 * Create plugin update REST service.
 */
class PluginUpdate {
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
        register_rest_route($namespace, '/plugin-update/(?P<slug>[a-zA-Z0-9_-]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'routeGet'],
            'permission_callback' => [$this, 'permission_callback']
        ]);
        register_rest_route($namespace, '/plugin-update/(?P<slug>[a-zA-Z0-9_-]+)/skip', [
            'methods' => 'POST',
            'callback' => [$this, 'routeSkip'],
            'permission_callback' => [$this, 'permission_callback']
        ]);
        register_rest_route($namespace, '/plugin-update/(?P<slug>[a-zA-Z0-9_-]+)', [
            'methods' => 'PATCH',
            'callback' => [$this, 'routePatch'],
            'permission_callback' => [$this, 'permission_callback'],
            'args' => [
                'licenses' => [
                    'type' => 'string',
                    'validate_callback' => function ($param) {
                        return $param === null || \json_decode($param) !== null;
                    }
                ],
                'terms' => [
                    'type' => 'boolean',
                    'required' => \true,
                    'validate_callback' => function ($param) {
                        return \boolval($param);
                    }
                ],
                'autoUpdates' => ['type' => 'boolean', 'default' => \true],
                'telemetry' => ['type' => 'boolean', 'default' => \false],
                'newsletter' => ['type' => 'boolean', 'default' => \false],
                'firstName' => ['type' => 'string'],
                'email' => ['type' => 'string', 'validate_callback' => 'is_email']
            ]
        ]);
        register_rest_route($namespace, '/plugin-update/(?P<slug>[a-zA-Z0-9_-]+)/license/(?P<blogId>[0-9_-]+)', [
            'methods' => 'DELETE',
            'callback' => [$this, 'routeDeleteLicense'],
            'permission_callback' => [$this, 'permission_callback']
        ]);
        register_rest_route($namespace, '/plugin-update/(?P<slug>[a-zA-Z0-9_-]+)/license-notice', [
            'methods' => 'DELETE',
            'callback' => [$this, 'routeDismissLicenseNotice'],
            'permission_callback' => [$this, 'permission_callback']
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
     * @api {get} /real-product-manager-wp-client/v1/plugin-update/:slug Get the license for a given plugin slug
     * @apiHeader {string} X-WP-Nonce
     * @apiParam {string} slug
     * @apiName GetPluginUpdate
     * @apiPermission activate_plugins
     * @apiGroup PluginUpdate
     * @apiVersion 1.0.0
     */
    public function routeGet($request) {
        $slug = $request->get_param('slug');
        $initiator = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core::getInstance()->getInitiator(
            $slug
        );
        if ($initiator === null) {
            return new \WP_Error('rest_not_found', 'Not found', ['status' => 404]);
        } else {
            return new \WP_REST_Response($this->getResult($slug));
        }
    }
    /**
     * See API docs.
     *
     * @param WP_REST_Request $request
     * @api {post} /real-product-manager-wp-client/v1/plugin-update/:slug/skip Skip the license form for the current blog id
     * @apiHeader {string} X-WP-Nonce
     * @apiParam {string} slug
     * @apiName SkipPluginUpdate
     * @apiPermission activate_plugins
     * @apiGroup PluginUpdate
     * @apiVersion 1.0.0
     */
    public function routeSkip($request) {
        $slug = $request->get_param('slug');
        $initiator = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core::getInstance()->getInitiator(
            $slug
        );
        if ($initiator === null) {
            return new \WP_Error('rest_not_found', 'Not found', ['status' => 404]);
        } else {
            $skip = $initiator
                ->getPluginUpdater()
                ->getCurrentBlogLicense()
                ->getActivation()
                ->skip();
            return $skip === \false
                ? new \WP_Error('rest_error', __('Something went wrong. Please try again later!', RPM_WP_CLIENT_TD), [
                    'status' => 500
                ])
                : new \WP_REST_Response(['success' => $skip]);
        }
    }
    /**
     * See API docs.
     *
     * @param WP_REST_Request $request
     * @api {patch} /real-product-manager-wp-client/v1/plugin-update/:slug Update the license settings for a given plugin slug
     * @apiHeader {string} X-WP-Nonce
     * @apiParam {string} slug
     * @apiParam {string} licenses JSON string: `Array<{ blog: number; installationType: "development" | "production"; code: string; }>`
     * @apiParam {boolean} terms
     * @apiParam {boolean} [telemetry]
     * @apiParam {boolean} [newsletter]
     * @apiParam {string} [firstName]
     * @apiParam {string} [email]
     * @apiName PatchPluginUpdate
     * @apiPermission activate_plugins
     * @apiGroup PluginUpdate
     * @apiVersion 1.0.0
     */
    public function routePatch($request) {
        $slug = $request->get_param('slug');
        $initiator = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core::getInstance()->getInitiator(
            $slug
        );
        if ($initiator === null) {
            return new \WP_Error('rest_not_found', 'Not found', ['status' => 404]);
        } else {
            $licenses = $request->get_param('licenses');
            $autoUpdates = $request->get_param('autoUpdates');
            $telemetry = $request->get_param('telemetry');
            $newsletter = $request->get_param('newsletter');
            $firstName = $request->get_param('firstName');
            $email = $request->get_param('email');
            $pluginUpdater = $initiator->getPluginUpdater();
            $result = $pluginUpdater->updateLicenseSettings(
                $licenses === null ? null : \json_decode($licenses, ARRAY_A),
                $telemetry,
                $newsletter,
                $firstName,
                $email
            );
            // Enable auto updates
            if ($autoUpdates) {
                $pluginUpdater->enableAutoUpdates();
            }
            if (is_wp_error($result)) {
                return $result;
            }
            return new \WP_REST_Response($this->getResult($slug));
        }
    }
    /**
     * See API docs.
     *
     * @param WP_REST_Request $request
     * @api {delete} /real-product-manager-wp-client/v1/plugin-update/:slug/license/:blogId Delete the entered license code for a given plugin slug and blog id
     * @apiHeader {string} X-WP-Nonce
     * @apiParam {string} slug
     * @apiParam {number} blogId
     * @apiName DeleteLicense
     * @apiPermission activate_plugins
     * @apiGroup PluginUpdate
     * @apiVersion 1.0.0
     */
    public function routeDeleteLicense($request) {
        $slug = $request->get_param('slug');
        $blogId = \intval($request->get_param('blogId'));
        $initiator = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core::getInstance()->getInitiator(
            $slug
        );
        if ($initiator === null) {
            return new \WP_Error('rest_not_found', 'Not found', ['status' => 404]);
        } else {
            $licenses = $initiator->getPluginUpdater()->getLicenses(\true);
            if (!isset($licenses[$blogId])) {
                return new \WP_Error('rest_not_found', 'Not found', ['status' => 404]);
            }
            $license = $licenses[$blogId];
            $deactivate = $license->getActivation()->deactivate(\true);
            if (is_wp_error($deactivate)) {
                return $deactivate;
            }
            return new \WP_REST_Response($license->getAsArray());
        }
    }
    /**
     * See API docs.
     *
     * @param WP_REST_Request $request
     * @api {delete} /real-product-manager-wp-client/v1/plugin-update/:slug/license-notice Dismiss the current day in the license admin notice
     * @apiHeader {string} X-WP-Nonce
     * @apiParam {string} slug
     * @apiName DeleteLicenseNotice
     * @apiPermission activate_plugins
     * @apiGroup PluginUpdate
     * @apiVersion 1.0.0
     */
    public function routeDismissLicenseNotice($request) {
        $slug = $request->get_param('slug');
        $initiator = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core::getInstance()->getInitiator(
            $slug
        );
        if ($initiator === null) {
            return new \WP_Error('rest_not_found', 'Not found', ['status' => 404]);
        } else {
            return new \WP_REST_Response([
                'success' => $initiator
                    ->getPluginUpdater()
                    ->getView()
                    ->dismissLicenseAdminNotice()
            ]);
        }
    }
    /**
     * Get result for a given slug.
     *
     * @param string $slug
     */
    protected function getResult($slug) {
        $initiator = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core::getInstance()->getInitiator(
            $slug
        );
        // Current license activation properties (e.g. if license form should be shown for a plugin)
        $licenseActivation = $initiator
            ->getPluginUpdater()
            ->getCurrentBlogLicense()
            ->getActivation();
        $licenses = [];
        foreach ($initiator->getPluginUpdater()->getUniqueLicenses(\true) as $license) {
            $licenses[] = $license->getAsArray();
        }
        $user = get_userdata(get_current_user_id());
        return [
            'slug' => $slug,
            'licenses' => $licenses,
            'hasInteractedWithFormOnce' => $licenseActivation->hasInteractedWithFormOnce(),
            'name' => $initiator->getPluginName(),
            'needsLicenseKeys' => $initiator->isExternalUpdateEnabled(),
            'privacyProvider' => $initiator->getPrivacyProvider(),
            'privacyPolicy' => $initiator->getPrivacyPolicy(),
            'accountSiteUrl' => $initiator->getAccountSiteUrl(),
            'allowsAutoUpdates' => $initiator->isAutoUpdatesEnabled(),
            'allowsTelemetry' => $initiator->isTelemetryEnabled(),
            'allowsNewsletter' => $initiator->isNewsletterEnabled(),
            'announcementsActive' => $initiator
                ->getPluginUpdater()
                ->getAnnouncementPool()
                ->isActive(),
            'potentialNewsletterUser' => ['firstName' => $user->first_name, 'email' => $user->user_email],
            'checkUpdateLink' => $initiator
                ->getPluginUpdater()
                ->getPluginUpdateChecker()
                ->getCheckUpdateLink()
        ];
    }
    /**
     * New instance.
     *
     * @codeCoverageIgnore
     */
    public static function instance() {
        return new \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\rest\PluginUpdate();
    }
}
