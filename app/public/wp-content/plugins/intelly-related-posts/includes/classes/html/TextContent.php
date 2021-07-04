<?php
if (!defined('ABSPATH')) exit;

class IRP_TextContent extends IRP_HTMLTag {
    var $body;
    public function __construct() {
        parent::__construct();
        $this->body='';
    }

    public function hasTagContent() {
        return FALSE;
    }
    public function append($text) {
        $this->body.=$text;
    }
    public function write(IRP_HTMLContext $context) {
        global $irp;
        if($context->isUncuttable() || trim($this->body)=='') {
            //we dont want to insert nothing inside iframe or table
            $context->write($this->body);
            $context->incCounters($this->body);
            if(defined('IRP_DEBUG_BLOCK') && IRP_DEBUG_BLOCK) {
                $context->write(sprintf('<sup style="color:red;">&nbsp;{%s/%s,%s}</sup>'
                    , $context->currentWords, $context->wordsThreshold, $context->lastBoxWords));
            }
        } else {
            //here is all text so we have to decide when cut it
            $text=explode("\n", $this->body);
            for($i=0; $i<count($text); $i++) {
                $line=$text[$i];
                if(trim($line)=='') {
                    $context->writeRelatedBoxIfNeeded();
                }

                if($i<(count($text)-1)) {
                    $line.="\n";
                }
                $context->write($line);
                $context->incCounters($line);
                if(trim($line)!='') {
                    if (defined('IRP_DEBUG_BLOCK') && IRP_DEBUG_BLOCK) {
                        $context->write(sprintf('<sup style="color:#008000">&nbsp;{%s/%s,%s}</sup>'
                            , $context->currentWords, $context->wordsThreshold, $context->lastBoxWords));
                    }
                }
            }
        }
    }
    public function analyseText(IRP_HTMLContext $context) {
        $context->incCounters($this->body);
    }
}
