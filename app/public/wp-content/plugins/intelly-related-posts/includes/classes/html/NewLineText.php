<?php
if (!defined('ABSPATH')) exit;

class IRP_NewLineText extends IRP_TextContent {

    public function __construct() {
        parent::__construct();
    }
    public function hasTagContent() {
        return FALSE;
    }
    public function append($text) {
        $this->body.=$text;
    }
    public function write(IRP_HTMLContext $context) {
        $context->write($this->body);
    }
}
