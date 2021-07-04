<?php

namespace DevOwl\RealCategoryLibrary;

use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Localization as UtilsLocalization;
use DevOwl\RealCategoryLibrary\base\UtilsProvider;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * i18n management for backend and frontend.
 */
class Localization {
    use UtilsProvider;
    use UtilsLocalization;
    /**
     * Put your language overrides here!
     *
     * @param string $locale
     * @return string
     */
    protected function override($locale) {
        switch ($locale) {
            case 'de_AT':
            case 'de_CH':
            case 'de_CH_informal':
            case 'de_DE_formal':
                return 'de_DE';
            default:
                break;
        }
        return $locale;
    }
    /**
     * Get the directory where the languages folder exists.
     *
     * @param string $type
     * @return string[]
     */
    protected function getPackageInfo($type) {
        if ($type === \DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Localization::$PACKAGE_INFO_BACKEND) {
            return [path_join(RCL_PATH, 'languages'), RCL_TD];
        } else {
            return [path_join(RCL_PATH, \DevOwl\RealCategoryLibrary\Assets::$PUBLIC_JSON_I18N), RCL_TD];
        }
    }
}
