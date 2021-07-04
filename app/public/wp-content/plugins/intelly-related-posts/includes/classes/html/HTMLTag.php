<?php
if (!defined('ABSPATH')) exit;

class IRP_HTMLTag {
    var $tags;
    var $tag;
    var $openTag;
    var $closeTag;

    public function __construct() {
        $this->tags = array();
        $this->tag='';
        $this->openTag='';
        $this->closeTag='';
    }

    public function hasTagContent() {
        return TRUE;
    }
    public function pushTag(IRP_HTMLTag $child) {
        if(count($this->tags)>0) {
            //replace the TextContent tag that only contains \r\n with NewLineText
            //due to now we use the substrln is impossible that we enter here
            $last=$this->tags[count($this->tags)-1];
            if(isset($last->body)) {
                $newline=TRUE;
                if($last->body=="\n" || $last->body=="\r\n") {
                    //we dont have to do nothing... sure this is a newlinetext!
                } else {
                    for($i=0; $i<strlen($last->body); $i++) {
                        $c=substr($last->body, $i, 1);
                        if($c!="\n" && $c!="\n") {
                            $newline=FALSE;
                            break;
                        }
                    }
                }

                if($newline) {
                    $tag=new IRP_NewLineText();
                    $tag->body=$last->body;
                    $this->tags[count($this->tags)-1]=$tag;
                }
            }
        }
        $this->tags[] = $child;
    }

    public function write(IRP_HTMLContext $context) {
        $context->write($this->openTag);
        foreach($this->tags as $tag) {
            $tag->write($context);
        }
        $context->write($this->closeTag);
    }
    public function analyseText(IRP_HTMLContext $context) {
        foreach ($this->tags as $tag) {
            $tag->analyseText($context);
        }
    }
}
