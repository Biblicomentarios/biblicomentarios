<?php

namespace DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license;

use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\LicenseActivation as ClientLicenseActivation;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\PluginUpdate;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Utils;
use WP_Error;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Use this trait together in `PluginUpdate`.
 */
trait PluginUpdateLicensePool {
    /**
     * License instances.
     *
     * @var License[]
     */
    private $licenses;
    /**
     * License activation client.
     *
     * @var ClientLicenseActivation
     */
    private $licenseActivationClient;
    /**
     * C'tor.
     */
    protected function constructPluginUpdateLicensePool() {
        $this->licenseActivationClient = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\LicenseActivation::instance(
            $this
        );
    }
    /**
     * Unfortunately, `wp_update_plugins()` does not provide any hook but we want to let
     * our remote server know about installed WP version, PHP version, ...
     *
     * We misuse this hook to transfer them to our remote server.
     *
     * @param array $locales
     */
    public function plugins_update_check_locales($locales) {
        $backtrace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);
        if (\count($backtrace) >= 4 && $backtrace[3]['function'] === 'wp_update_plugins') {
            // Iterate all available licenses and let our remote server know the updated versions
            foreach ($this->getUniqueLicenses() as $license) {
                $license->syncWithRemote();
            }
        }
        return $locales;
    }
    /**
     * Update license settings for this plugin.
     *
     * @param array $licenses Pass `null` to activate all unlicensed, free sites
     * @param boolean $telemetry
     * @param boolean $newsletterOptIn
     * @param string $firstName
     * @param string $email
     */
    public function updateLicenseSettings(
        $licenses,
        $telemetry = \false,
        $newsletterOptIn = \false,
        $firstName = '',
        $email = ''
    ) {
        // Validate free products
        if ($licenses === null) {
            if ($this->getInitiator()->isExternalUpdateEnabled()) {
                return new \WP_Error(
                    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\PluginUpdate::ERROR_CODE_INVALID_LICENSES,
                    __('You need to provide at least one license!', RPM_WP_CLIENT_TD),
                    ['status' => 400]
                );
            }
            // Fallback to use free licenses
            $licenses = [];
            foreach ($this->getUniqueLicenses() as $license) {
                if (empty($license->getActivation()->getCode())) {
                    $licenses[] = [
                        'blog' => $license->getBlogId(),
                        'installationType' =>
                            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::INSTALLATION_TYPE_PRODUCTION,
                        'code' => $license->getActivation()->getCode()
                    ];
                }
            }
        }
        // Validate newsletter input
        if ($newsletterOptIn && (empty($firstName) || empty($email))) {
            return new \WP_Error(
                \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\PluginUpdate::ERROR_CODE_INVALID_NEWSLETTER,
                __(
                    'You need to provide an email and name if you want to subscribe to the newsletter!',
                    RPM_WP_CLIENT_TD
                ),
                ['status' => 400]
            );
        }
        $validateKeys = $this->validateLicenseCodes($licenses, $telemetry, $newsletterOptIn, $firstName, $email);
        if (is_wp_error($validateKeys)) {
            return $validateKeys;
        }
        // Synchronize announcements
        $this->getAnnouncementPool()->sync(\true);
        return \true;
    }
    /**
     * Validate license codes.
     *
     * @param array $licenses
     * @param boolean $telemetry
     * @param boolean $newsletterOptIn
     * @param string $firstName
     * @param string $email
     */
    protected function validateLicenseCodes($licenses, $telemetry, $newsletterOptIn, $firstName, $email) {
        $invalidKeys = [];
        $currentLicenses = $this->getLicenses(\true);
        $provider = $this->getInitiator()->getPrivacyProvider();
        // Validate license keys
        foreach ($licenses as $value) {
            $blogId = $value['blog'];
            $installationType = $value['installationType'];
            $code = $value['code'];
            if (isset($currentLicenses[$blogId])) {
                $result = $currentLicenses[$blogId]
                    ->getActivation()
                    ->activate($code, $installationType, $telemetry, $newsletterOptIn, $firstName, $email);
                // Ignore already existing activations as they should not lead to UI errors
                if (
                    is_wp_error($result) &&
                    $result->get_error_code() !==
                        \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\LicenseActivation::ERROR_CODE_ALREADY_ACTIVATED
                ) {
                    $errorText = \join(' ', $result->get_error_messages());
                    switch ($result->get_error_code()) {
                        case 'http_request_failed':
                            $errorText = \sprintf(
                                // translators:
                                __(
                                    'The license server for checking the license cannot be reached. Please check if you are blocking access to %s e.g. by a firewall.',
                                    RPM_WP_CLIENT_TD
                                ),
                                $provider
                            );
                            break;
                        default:
                            break;
                    }
                    $invalidKeys[$blogId] = [
                        'validateStatus' => 'error',
                        'hasFeedback' => \true,
                        'help' => $errorText,
                        'debug' => $result
                    ];
                }
            } else {
                return new \WP_Error(
                    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\PluginUpdate::ERROR_CODE_BLOG_NOT_FOUND,
                    '',
                    ['blog' => $blogId]
                );
            }
        }
        return empty($invalidKeys)
            ? \true
            : new \WP_Error(
                \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\PluginUpdate::ERROR_CODE_INVALID_KEYS,
                $invalidKeys[\array_keys($invalidKeys)[0]]['help'],
                ['invalidKeys' => $invalidKeys]
            );
    }
    /**
     * Get the license for the current blog id.
     *
     * @return License
     */
    public function getCurrentBlogLicense() {
        $blogId = get_current_blog_id();
        $licenses = $this->getLicenses();
        // Fallback to first found license (can be the case, when per-site multisite-licensing is disabled)
        return isset($licenses[$blogId]) ? $licenses[$blogId] : \array_shift($licenses);
    }
    /**
     * Check if plugin is fully licensed.
     */
    public function isLicensed() {
        foreach ($this->getUniqueLicenses() as $license) {
            if (empty($license->getActivation()->getCode())) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * Check if plugin is only partial licensed (e.g. missing sites in multisite).
     */
    public function isPartialLicensed() {
        // If fully licensed, it can never be partial
        if ($this->isLicensed()) {
            return \false;
        }
        foreach ($this->getUniqueLicenses() as $license) {
            if (!empty($license->getActivation()->getCode())) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Get first found license as we can not update per-site in multisite (?).
     */
    public function getFirstFoundLicense() {
        foreach ($this->getUniqueLicenses() as $license) {
            $code = $license->getActivation()->getCode();
            if ($code !== \false) {
                return $license;
            }
        }
        return \false;
    }
    /**
     * Get all licenses for each blog (when multisite is enabled). Attention: If a blog
     * uses the same hostname as a previous known blog, they share the same `License` instance.
     *
     * @param boolean $checkRemoteStatus
     * @return License[]
     */
    public function getLicenses($checkRemoteStatus = \false) {
        if ($this->licenses === null || $checkRemoteStatus) {
            $blogIds = $this->getPotentialBlogIds();
            $hostnames = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Utils::mapBlogsToHosts(
                $blogIds
            );
            // Create licenses per hostname
            $hostLicenses = [];
            foreach ($hostnames['host'] as $host => $hostBlogIds) {
                $hostLicenses[
                    $host
                ] = new \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License(
                    $this->getInitiator()->getPluginSlug(),
                    $hostBlogIds[0]
                );
            }
            // Create licenses per blog ID and point to hostname-license
            $this->licenses = [];
            foreach ($blogIds as $blogId) {
                $host = $hostnames['blog'][$blogId];
                $license = $hostLicenses[$host];
                if ($checkRemoteStatus) {
                    $license->fetchRemoteStatus(\true);
                }
                $license->probablyMigrate();
                $this->licenses[$blogId] = $license;
            }
        }
        return $this->licenses;
    }
    /**
     * The same as `getLicenses`, but only get unique `License` instances.
     *
     * @return License[]
     */
    public function getUniqueLicenses() {
        $result = [];
        foreach ($this->getLicenses() as $license) {
            $result[$license->getBlogId()] = $license;
        }
        return \array_values($result);
    }
    /**
     * Get license activation client.
     *
     * @codeCoverageIgnore
     */
    public function getLicenseActivationClient() {
        return $this->licenseActivationClient;
    }
}
