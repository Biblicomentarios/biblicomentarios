<?php

namespace DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license;

use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Utils;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\UtilsProvider;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\view\PluginUpdateView;
use WP_Error;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Handle license activation.
 */
class LicenseActivation {
    use UtilsProvider;
    const ERROR_CODE_ALREADY_ACTIVATED = 'rpm_wpc_already_exists';
    /**
     * License instance.
     *
     * @var License
     */
    private $license;
    /**
     * C'tor.
     *
     * @param License $license
     * @codeCoverageIgnore
     */
    public function __construct($license) {
        $this->license = $license;
    }
    /**
     * Mark the license as "ever seen form once", see also `#hasInteractedWithFormOnce`.
     * You should use the REST API endpoint `plugin-update/:slug/skip` for this.
     */
    public function skip() {
        $slug = $this->getLicense()->getSlug();
        update_option(
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_TELEMETRY_PREFIX .
                $slug,
            \false
        );
        update_option(
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_INSTALLATION_TYPE_PREFIX .
                $slug,
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::INSTALLATION_TYPE_PRODUCTION
        );
        return update_option(
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_CODE_PREFIX .
                $slug,
            ''
        );
    }
    /**
     * Activate this license with a given code. It returns a `WP_Error` if a code is already active.
     *
     * @param string $code
     * @param string $installationType
     * @param boolean $telemetry
     * @param boolean $newsletterOptIn
     * @param string $firstName
     * @param string $email
     */
    public function activate($code, $installationType, $telemetry, $newsletterOptIn, $firstName, $email) {
        $license = $this->getLicense();
        $license->switch();
        $slug = $license->getSlug();
        if (!empty($this->getCode())) {
            $result = new \WP_Error(
                self::ERROR_CODE_ALREADY_ACTIVATED,
                __(
                    'You have already activated a license for this plugin. Please deactivate it first!',
                    RPM_WP_CLIENT_TD
                ),
                ['blog' => $license->getBlogId(), 'slug' => $slug]
            );
        } else {
            $uuid = $license->getUuid();
            $result = $license
                ->getClient()
                ->post($code, $uuid, $installationType, $telemetry, $newsletterOptIn, $firstName, $email);
            if (!is_wp_error($result)) {
                // No error occurred, let's save the license key and UUID
                $licenseKey = $result['licenseActivation']['license']['licenseKey'];
                $uuid = $result['licenseActivation']['client']['uuid'];
                update_option(
                    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_CODE_PREFIX .
                        $slug,
                    $licenseKey
                );
                update_option(
                    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_UUID_PREFIX .
                        $slug,
                    $uuid
                );
                update_option(
                    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_HOST_NAME .
                        $slug,
                    \base64_encode(
                        \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Utils::getCurrentHostname()
                    )
                );
                // base64 encoded to avoid search & replace of migration tools
                update_option(
                    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_TELEMETRY_PREFIX .
                        $slug,
                    $telemetry
                );
                update_option(
                    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_INSTALLATION_TYPE_PREFIX .
                        $slug,
                    $installationType
                );
                delete_option(
                    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_HINT_PREFIX .
                        $slug
                );
                // The notice for license activation should never be shown again
                $initiator = $this->getLicense()->getInitiator();
                if ($initiator->isExternalUpdateEnabled()) {
                    update_option(
                        \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\view\PluginUpdateView::OPTION_NAME_ADMIN_NOTICE_LICENSE_DISMISSED_DAY_PREFIX .
                            $initiator->getPluginSlug(),
                        \PHP_INT_MAX
                    );
                }
            }
        }
        /**
         * License Activation for a given plugin got changed.
         *
         * Note: You are running in the context of the activated blog if you are in a multisite!
         *
         * @hook DevOwl/RealProductManager/LicenseActivation/StatusChanged/$slug
         * @param {boolean} $status
         * @param {string} $licenseKey
         * @param {string} $uuid
         * @since 1.6.4
         */
        do_action('DevOwl/RealProductManager/LicenseActivation/StatusChanged/' . $slug, \true, $licenseKey, $uuid);
        $license->restore();
        return $result;
    }
    /**
     * Deactivate the license for this blog and plugin.
     *
     * @param boolean $remote If `true`, the license is also deactivate remotely
     * @param string $validateStatus
     * @param string $help
     * @return WP_Error|true
     */
    public function deactivate($remote = \false, $validateStatus = null, $help = '') {
        $license = $this->getLicense();
        $license->switch();
        $code = $this->getCode();
        $uuid = $this->getLicense()->getUuid();
        // We need to ensure, the license activation is removed from remote server (only when not already detected remotely)
        if ($remote) {
            $delete = $this->getLicense()
                ->getClient()
                ->delete($code, $uuid);
            if (is_wp_error($delete)) {
                return $delete;
            }
        }
        // Let's remove locally...
        $slug = $license->getSlug();
        update_option(
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_CODE_PREFIX .
                $slug,
            ''
        );
        update_option(
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_HOST_NAME .
                $slug,
            ''
        );
        update_option(
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_TELEMETRY_PREFIX .
                $slug,
            \false
        );
        update_option(
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_INSTALLATION_TYPE_PREFIX .
                $slug,
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::INSTALLATION_TYPE_NONE
        );
        if ($validateStatus !== null) {
            update_option(
                \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_HINT_PREFIX .
                    $slug,
                ['validateStatus' => $validateStatus, 'hasFeedback' => \true, 'help' => $help]
            );
        }
        // Documented in `activate`
        do_action('DevOwl/RealProductManager/LicenseActivation/StatusChanged/' . $slug, \false, '', $uuid);
        $license->restore();
        return \true;
    }
    /**
     * Check if the form for this license was shown the user once. This allows you e.g.
     * show a form of the license activation directly after using the plugin for the first time.
     */
    public function hasInteractedWithFormOnce() {
        return $this->getCode() !== \false;
    }
    /**
     * Get a hint for this license activation. This can happen e.g. the remote status changed (revoked, expired)
     * and we want to user show a notice for this. Can be `false` if none given.
     *
     * @return string
     */
    public function getHint() {
        $license = $this->getLicense();
        $license->switch();
        $result = get_option(
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_HINT_PREFIX .
                $license->getSlug(),
            \false
        );
        $license->restore();
        return $result;
    }
    /**
     * Get entered license code for this activation. Can be `false` if none given. If it is
     * an empty string, the form got skipped through `#skip()`.
     *
     * @return string|false
     */
    public function getCode() {
        $license = $this->getLicense();
        $license->switch();
        $result = get_option(
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_CODE_PREFIX .
                $license->getSlug()
        );
        $license->restore();
        return $result;
    }
    /**
     * See `License#INSTALLATION_TYPE_*` constants.
     */
    public function getInstallationType() {
        $license = $this->getLicense();
        $license->switch();
        $result = get_option(
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_INSTALLATION_TYPE_PREFIX .
                $license->getSlug()
        );
        $license->restore();
        return empty($result)
            ? \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::INSTALLATION_TYPE_NONE
            : $result;
    }
    /**
     * Get license instance.
     *
     * @codeCoverageIgnore
     */
    public function getLicense() {
        return $this->license;
    }
}
