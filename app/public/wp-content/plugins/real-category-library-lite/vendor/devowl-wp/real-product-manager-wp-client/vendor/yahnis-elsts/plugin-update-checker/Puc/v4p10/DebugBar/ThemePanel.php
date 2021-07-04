<?php

namespace DevOwl\RealCategoryLibrary\Vendor;

if (!\class_exists('DevOwl\\RealCategoryLibrary\\Vendor\\Puc_v4p10_DebugBar_ThemePanel', \false)) {
    class Puc_v4p10_DebugBar_ThemePanel extends \DevOwl\RealCategoryLibrary\Vendor\Puc_v4p10_DebugBar_Panel {
        /**
         * @var Puc_v4p10_Theme_UpdateChecker
         */
        protected $updateChecker;
        protected function displayConfigHeader() {
            $this->row('Theme directory', \htmlentities($this->updateChecker->directoryName));
            parent::displayConfigHeader();
        }
        protected function getUpdateFields() {
            return \array_merge(parent::getUpdateFields(), ['details_url']);
        }
    }
}
