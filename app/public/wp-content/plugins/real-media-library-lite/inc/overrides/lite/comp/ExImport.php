<?php

namespace MatthiasWeb\RealMediaLibrary\lite\comp;

use MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
trait ExImport {
    // Documented in IOverrideExImport
    public function importTaxonomy($tax) {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IOverrideExImport
    public function importMlf() {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IOverrideExImport
    public function importFileBird() {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IOverrideExImport
    public function importShortcuts() {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IOverrideExImport
    public function import($tree) {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
}
