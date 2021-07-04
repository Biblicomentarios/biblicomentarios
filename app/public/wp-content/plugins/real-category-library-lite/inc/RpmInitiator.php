<?php

namespace DevOwl\RealCategoryLibrary;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\AbstractInitiator;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Initiate real-product-manager-wp-client functionality.
 */
class RpmInitiator extends \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\AbstractInitiator {
    use UtilsProvider;
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function getPluginBase() {
        return $this;
    }
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function getProductAndVariant() {
        return [4, $this->isPro() ? 6 : 7];
    }
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function getPluginAssets() {
        return $this->getCore()->getAssets();
    }
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function getPrivacyPolicy() {
        return 'https://devowl.io/privacy-policy';
    }
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function getMigrationOption() {
        if ($this->isPro()) {
            $optionName = \sprintf('wpls_license_%s', $this->getPluginSlug());
            $old = get_site_option($optionName);
            if (empty($old)) {
                return null;
            } else {
                delete_site_option($optionName);
                return $old;
            }
        }
        return null;
    }
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function isExternalUpdateEnabled() {
        return $this->isPro();
    }
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function isAdminNoticeLicenseVisible() {
        return \DevOwl\RealCategoryLibrary\Core::getInstance()
            ->getDefaultTaxTree()
            ->isAvailable(\true) ||
            \DevOwl\RealCategoryLibrary\Core::getInstance()
                ->getConfigPage()
                ->isVisible();
    }
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function isLocalAnnouncementVisible() {
        return $this->isAdminNoticeLicenseVisible();
    }
}
