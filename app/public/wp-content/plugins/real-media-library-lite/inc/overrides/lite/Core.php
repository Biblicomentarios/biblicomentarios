<?php

namespace MatthiasWeb\RealMediaLibrary\lite;

use MatthiasWeb\RealMediaLibrary\Vendor\DevOwl\Freemium\CoreLite;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
trait Core {
    use CoreLite;
    // Documented in IOverrideCore
    public function overrideConstruct() {
        // Silence is golden.
    }
    // Documented in IOverrideCore
    public function overrideInit() {
        // Silence is golden.
    }
    // Documented in IOverrideCore
    public function isLicenseNoticeDismissed($set = null) {
        return \true;
        // Always dismissed in Lite version
    }
    // Documented in IOverrideCore
    public function getUpdater() {
        return null;
        // wordpress.org updater is used
    }
}
