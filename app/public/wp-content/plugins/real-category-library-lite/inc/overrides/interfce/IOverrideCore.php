<?php

namespace DevOwl\RealCategoryLibrary\overrides\interfce;

use DevOwl\RealCategoryLibrary\Vendor\DevOwl\Freemium\ICore;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
interface IOverrideCore extends \DevOwl\RealCategoryLibrary\Vendor\DevOwl\Freemium\ICore {
    /**
     * Additional constructor.
     */
    public function overrideConstruct();
    /**
     * Additional init.
     */
    public function overrideInit();
}
