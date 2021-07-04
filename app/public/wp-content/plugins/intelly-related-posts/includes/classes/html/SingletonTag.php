<?php
if (!defined('ABSPATH')) exit;

class IRP_SingletonTag extends IRP_HTMLTag {
    public function __construct() {
        parent::__construct();
    }

    public function hasTagContent() {
        return FALSE;
    }
    public function write(IRP_HTMLContext $context) {
        $context->write($this->openTag);
    }
}
