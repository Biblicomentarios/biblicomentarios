<?php

namespace DevOwl\RealCustomPostOrder;

use DevOwl\RealCustomPostOrder\Vendor\MatthiasWeb\Utils\Localization as UtilsLocalization;
use DevOwl\RealCustomPostOrder\base\UtilsProvider;
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
            case 'es_CO':
            case 'es_UY':
            case 'es_CR':
            case 'es_CL':
            case 'es_GT':
            case 'es_PE':
            case 'es_MX':
            case 'es_AR':
            case 'es_VE':
                return 'es_ES';
            case 'fr_CA':
            case 'fr_BE':
                return 'fr_FR';
            case 'nl_NL_formal':
            case 'nl_BE':
                return 'nl_NL';
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
        if ($type === \DevOwl\RealCustomPostOrder\Vendor\MatthiasWeb\Utils\Localization::$PACKAGE_INFO_BACKEND) {
            return [path_join(RCPO_PATH, 'languages'), RCPO_TD];
        } else {
            return [path_join(RCPO_PATH, \DevOwl\RealCustomPostOrder\Assets::$PUBLIC_JSON_I18N), RCPO_TD];
        }
    }
}
