<?php

namespace MatthiasWeb\RealMediaLibrary\Vendor\DevOwl\RealUtils;

use MatthiasWeb\RealMediaLibrary\Vendor\MatthiasWeb\Utils\PackageLocalization;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Package localization for `real-utils` package.
 */
class Localization extends \MatthiasWeb\RealMediaLibrary\Vendor\MatthiasWeb\Utils\PackageLocalization {
    /**
     * C'tor.
     */
    protected function __construct() {
        parent::__construct(REAL_UTILS_ROOT_SLUG, \dirname(__DIR__));
    }
    /**
     * Put your language overrides here!
     *
     * @param string $locale
     * @return string
     * @codeCoverageIgnore
     */
    protected function override($locale) {
        switch ($locale) {
            // Put your overrides here!
            case 'de_AT':
            case 'de_CH':
            case 'de_CH_informal':
            case 'de_DE_formal':
                return 'de_DE';
                break;
            default:
                break;
        }
        return $locale;
    }
    /**
     * Create instance.
     *
     * @codeCoverageIgnore
     */
    public static function instanceThis() {
        return new \MatthiasWeb\RealMediaLibrary\Vendor\DevOwl\RealUtils\Localization();
    }
}
