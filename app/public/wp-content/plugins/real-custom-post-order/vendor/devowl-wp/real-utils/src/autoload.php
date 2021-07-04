<?php

namespace DevOwl\RealCustomPostOrder\Vendor\DevOwl\RealUtils;

// Simply check for defined constants, we do not need to `die` here
if (\defined('ABSPATH')) {
    \DevOwl\RealCustomPostOrder\Vendor\DevOwl\RealUtils\UtilsProvider::setupConstants();
    \DevOwl\RealCustomPostOrder\Vendor\DevOwl\RealUtils\Localization::instanceThis()->hooks();
}
