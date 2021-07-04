<?php

namespace DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils;

// Simply check for defined constants, we do not need to `die` here
if (\defined('ABSPATH')) {
    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\UtilsProvider::setupConstants();
    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\Localization::instanceThis()->hooks();
}
