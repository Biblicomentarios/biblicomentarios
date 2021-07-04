<?php
if (!defined('ABSPATH')) exit;

class IRP_BehaviourTag extends IRP_HTMLTag {

    var $allowBoxBefore=FALSE;
    var $allowBoxAfter=FALSE;
    var $skipWordsCount=FALSE;
    var $ensureUncuttable=FALSE;
    var $ensureWithoutPreviousBox=FALSE;
    var $ensureWithoutNextBox=FALSE;

    public function write(IRP_HTMLContext $context) {
        $w=$context->skipWordsCount;
        $context->skipWordsCount=$this->skipWordsCount;

        if($this->ensureWithoutPreviousBox) {
            //if present remove the last related box
            $args=array('last'=>TRUE);
            $context->popRelatedBox($args);
        }
        if($this->allowBoxBefore) {
            $context->writeRelatedBoxIfNeeded();
        }

        if($this->ensureUncuttable) {
            $previous=$context->isUncuttable();
            $context->setUncuttable(TRUE);
        }
        parent::write($context);
        if($this->ensureUncuttable) {
            $context->setUncuttable($previous);
        }

        if($this->allowBoxAfter) {
            $context->writeRelatedBoxIfNeeded();
        }
        if($this->ensureWithoutNextBox) {
            $context->setWithoutNextBox();
        }

        $context->skipWordsCount=$w;
    }
    public function analyseText(IRP_HTMLContext $context) {
        if($this->skipWordsCount) {
            return;
        }

        parent::analyseText($context);
    }
}
