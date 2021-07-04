<?php

namespace DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient;

// Simply check for defined constants, we do not need to `die` here
if (\defined('ABSPATH')) {
    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\UtilsProvider::setupConstants();
    \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Localization::instanceThis()->hooks();
}
