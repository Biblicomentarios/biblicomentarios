<?php
if (!defined('ABSPATH')) exit;

class IRP_MainTag extends IRP_HTMLTag {
    public function __construct() {
        parent::__construct();
    }

    public function write(IRP_HTMLContext $context) {
        global $irp;
        foreach ($this->tags as $tag) {
            $tag->write($context);
        }

        //try to write the last box only if no boxes are written before
        //this prevent from inserting at the end a box due to tipically
        //marketer insert a CTA box (for instance download free book)
        if($irp->Options->isRewriteAtEnd() && $irp->Options->getRewriteBoxesWritten()==0) {
            $context->clearSkipNext();
            $context->setUncuttable(FALSE);
            $context->writeRelatedBox(TRUE);
        } elseif(!$irp->Options->isRewriteAtEnd()) {
            $args=array('last'=>TRUE);
            $context->popRelatedBox($args);
        }
    }
}
