<?php

namespace DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license;

use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\ClientUtils;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Utils;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\UtilsProvider;
use WP_Error;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Handle license information for a given plugin and associated blog id.
 */
class License {
    use UtilsProvider;
    const OPTION_NAME_CODE_PREFIX = RPM_WP_CLIENT_OPT_PREFIX . '-code_';
    const OPTION_NAME_UUID_PREFIX = RPM_WP_CLIENT_OPT_PREFIX . '-uuid_';
    const OPTION_NAME_HOST_NAME = RPM_WP_CLIENT_OPT_PREFIX . '-hostname_';
    const OPTION_NAME_TELEMETRY_PREFIX = RPM_WP_CLIENT_OPT_PREFIX . '-telemetry_';
    const OPTION_NAME_INSTALLATION_TYPE_PREFIX = RPM_WP_CLIENT_OPT_PREFIX . '-installationType_';
    const OPTION_NAME_HINT_PREFIX = RPM_WP_CLIENT_OPT_PREFIX . '-hint_';
    const INSTALLATION_TYPE_NONE = \false;
    const INSTALLATION_TYPE_DEVELOPMENT = 'development';
    const INSTALLATION_TYPE_PRODUCTION = 'production';
    const ERROR_CODE_NOT_ACTIVATED = 'rpm_wpc_not_activated';
    /**
     * Plugin slug.
     *
     * @var string
     */
    private $slug;
    /**
     * Blog id for this license.
     *
     * @var int
     */
    private $blogId;
    /**
     * License activation handler.
     *
     * @var LicenseActivation
     */
    private $activation;
    /**
     * Remote status of the activation.
     *
     * @var WP_Error|array
     */
    private $remoteStatus;
    /**
     * C'tor.
     *
     * @param string $slug
     * @param int $blogId
     * @codeCoverageIgnore
     */
    public function __construct($slug, $blogId) {
        $this->slug = $slug;
        $this->blogId = $blogId;
        $this->activation = new \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\LicenseActivation(
            $this
        );
        add_action('update_option_siteurl', [$this, 'update_option_siteurl']);
        if ($this->getInitiator()->isExternalUpdateEnabled()) {
            add_action('shutdown', [$this, 'validateNewHostName']);
        }
    }
    /**
     * Switch to this blog.
     *
     * @see https://developer.wordpress.org/reference/functions/switch_to_blog/
     */
    public function switch() {
        if (\function_exists('switch_to_blog')) {
            switch_to_blog($this->getBlogId());
        }
    }
    /**
     * Restore to previous blog.
     *
     * @see https://developer.wordpress.org/reference/functions/restore_current_blog/
     */
    public function restore() {
        if (\function_exists('restore_current_blog')) {
            restore_current_blog();
        }
    }
    /**
     * If given, read the old license key from the previous updater and give it back as hint.
     */
    public function probablyMigrate() {
        $activation = $this->getActivation();
        if ($activation->getCode() !== \false) {
            // We already have a license key, do nothing
            return;
        }
        $this->switch();
        $oldValue = $this->getInitiator()->getMigrationOption();
        $this->restore();
        if ($oldValue !== null) {
            $activation->deactivate(
                \false,
                'warning',
                \sprintf(
                    // translators:
                    __(
                        'The plugin has a new update server, so you need to reactivate your license (%s) to continue receiving updates.',
                        RPM_WP_CLIENT_TD
                    ),
                    $oldValue
                ) .
                    (is_multisite()
                        ? ' ' .
                            __(
                                'You are using a WordPress mulisite. According to the license agreement of the plugin you need one license per website. If you have used only one license for all websites in your WordPress mulisite, this was only possible because it was not technically prevented. We ask for your understanding if this causes any inconvenience!',
                                RPM_WP_CLIENT_TD
                            )
                        : '')
            );
        }
    }
    /**
     * Sync our plugin version, PHP version and WordPress version with our remote system.
     */
    public function syncWithRemote() {
        $activation = $this->getActivation();
        $code = $activation->getCode();
        if (empty($code)) {
            return new \WP_Error(
                \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::ERROR_CODE_NOT_ACTIVATED,
                __('You have not yet activated a license for this plugin on your website.', RPM_WP_CLIENT_TD),
                ['blog' => $this->getBlogId(), 'slug' => $this->getSlug()]
            );
        }
        $response = $this->getClient()->patch($code, $this->getUuid());
        $this->validateRemoteResponse($response);
        return $response;
    }
    /**
     * Fetch remote status from the Real Product Manager server. Automatically
     * validates with `#validateRemoteResponse`, too.
     *
     * @param boolean $force
     */
    public function fetchRemoteStatus($force = \false) {
        // Not yet activated, it's an error when asking for remote result
        $code = $this->getActivation()->getCode();
        if (empty($code)) {
            return new \WP_Error(
                self::ERROR_CODE_NOT_ACTIVATED,
                __('You have not yet activated a license for this plugin on your website.', RPM_WP_CLIENT_TD),
                ['blog' => $this->getBlogId(), 'slug' => $this->getSlug()]
            );
        }
        if ($this->remoteStatus === null || $force) {
            $this->remoteStatus = $this->getClient()->get($code, $this->getUuid());
            $this->validateRemoteResponse($this->remoteStatus);
        }
        return $this->remoteStatus;
    }
    /**
     * If the `site_url` got updated through e.g. the UI, persist the host name as known
     * so that `self::validateNewHost` does not automatically deactivate the license - its
     * still the same WordPress installation.
     */
    public function update_option_siteurl() {
        $currentHostname = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Utils::getCurrentHostname();
        update_option(self::OPTION_NAME_HOST_NAME . $this->getSlug(), \base64_encode($currentHostname));
    }
    /**
     * Check if the plugin got migrated to another host and deactivate the license automatically.
     */
    public function validateNewHostName() {
        $this->switch();
        $currentHostname = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Utils::getCurrentHostname();
        $persistedHostname = $this->getHostname();
        $code = $this->getActivation()->getCode();
        $isLicensed = !empty($code);
        $dynamic =
            \defined('RPM_WP_CLIENT_SKIP_DYNAMIC_HOST_CHECK') && \constant('RPM_WP_CLIENT_SKIP_DYNAMIC_HOST_CHECK');
        if (
            $isLicensed &&
            !empty($currentHostname) &&
            \filter_var(\preg_replace('/:[0-9]+/', '', $currentHostname), \FILTER_VALIDATE_IP) === \false &&
            \parse_url($currentHostname) !== \false &&
            !$dynamic
        ) {
            // Backwards-compatibility, save option of current host
            if (empty($persistedHostname)) {
                update_option(self::OPTION_NAME_HOST_NAME . $this->getSlug(), \base64_encode($currentHostname));
                $persistedHostname = $currentHostname;
            }
            // Automatically deactivate
            if ($currentHostname !== $persistedHostname) {
                $this->getActivation()->deactivate(
                    \false,
                    'warning',
                    __(
                        'The license has been automatically deactivated because your website is running on a new domain. Please activate the license again!',
                        RPM_WP_CLIENT_TD
                    ) .
                        \sprintf(' "%s" -> "%s"', $persistedHostname, $currentHostname) .
                        ($this->getInitiator()->isExternalUpdateEnabled()
                            ? \sprintf(' %s: %s', __('License key', RPM_WP_CLIENT_TD), $code)
                            : '')
                );
                // It might be a clone of the website, let's delete also the UUID
                update_option(
                    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::OPTION_NAME_UUID_PREFIX .
                        $this->getSlug(),
                    ''
                );
            }
        }
        $this->restore();
    }
    /**
     * Validate a remote response against their body and probably an error code.
     * It automatically revokes the license if expired/revoked remotely.
     *
     * @param WP_Error|array $response
     */
    public function validateRemoteResponse($response) {
        if (
            is_wp_error($response) &&
            $response->get_error_code() ===
                \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\ClientUtils::ERROR_CODE_REMOTE
        ) {
            $errorCodes = $response->get_error_codes();
            $errors = $response->get_error_messages();
            foreach ($errorCodes as $index => $errorCode) {
                switch ($errorCode) {
                    case 'ClientNotFound':
                    case 'LicenseActivationNotFound':
                    case 'LicenseHasBeenExpired':
                    case 'LicenseHasBeenRevoked':
                    case 'LicenseNotFound':
                        $this->getActivation()->deactivate(
                            \false,
                            'warning',
                            \sprintf('%s (%s)', $errors[$index], $this->getActivation()->getCode())
                        );
                        return \false;
                    default:
                        break;
                }
            }
        }
        return \true;
    }
    /**
     * Get initiator.
     */
    public function getInitiator() {
        return \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core::getInstance()->getInitiator(
            $this->getSlug()
        );
    }
    /**
     * Get blog name for this license.
     */
    public function getBlogName() {
        $this->switch();
        $result = \sprintf('%s (%s)', get_bloginfo('name'), \parse_url(site_url(), \PHP_URL_HOST));
        $this->restore();
        return $result;
    }
    /**
     * Get known UUID. Can be empty if none given. The UUID will be set with the first
     * license activation.
     *
     * @return string
     */
    public function getUuid() {
        $this->switch();
        $result = get_option(self::OPTION_NAME_UUID_PREFIX . $this->getSlug(), '');
        $this->restore();
        return $result;
    }
    /**
     * Get known hostname. Can be empty if none given. The hostname will be set with the first
     * license activation. The value itself is base64-encoded in database to avoid search & replace
     * mechanism to replace the persisted URL.
     *
     * @return string
     */
    public function getHostname() {
        $this->switch();
        $result = get_option(self::OPTION_NAME_HOST_NAME . $this->getSlug(), '');
        $this->restore();
        return empty($result) ? $result : \base64_decode($result);
    }
    /**
     * Get installation type from remote status. Can be `false` if none given.
     *
     * @return string|false
     */
    public function getInstallationType() {
        $status = $this->fetchRemoteStatus();
        if (is_wp_error($status)) {
            return \false;
        }
        return $status['licenseActivation']['type'];
    }
    /**
     * Get license activation handler.
     *
     * @codeCoverageIgnore
     */
    public function getActivation() {
        return $this->activation;
    }
    /**
     * Get plugin slug.
     *
     * @codeCoverageIgnore
     */
    public function getSlug() {
        return $this->slug;
    }
    /**
     * Get license client.
     */
    public function getClient() {
        return $this->getInitiator()
            ->getPluginUpdater()
            ->getLicenseActivationClient();
    }
    /**
     * Get the license as array, useful for frontend needs or REST API.
     */
    public function getAsArray() {
        $remote = $this->fetchRemoteStatus();
        return [
            'blog' => $this->getBlogId(),
            'blogName' => $this->getBlogName(),
            'installationType' => $this->getInstallationType(),
            'code' => $this->getActivation()->getCode(),
            'hint' => $this->getActivation()->getHint(),
            'remote' => is_wp_error($remote) ? null : $remote
        ];
    }
    /**
     * Make it work with `array_unique`.
     *
     * @see https://stackoverflow.com/a/2426579/5506547
     */
    public function __toString() {
        return \json_encode([$this->getSlug(), $this->getBlogId()]);
    }
    /**
     * Get blog id.
     *
     * @codeCoverageIgnore
     */
    public function getBlogId() {
        return $this->blogId;
    }
}
