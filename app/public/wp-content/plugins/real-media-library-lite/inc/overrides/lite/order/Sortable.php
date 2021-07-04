<?php

namespace MatthiasWeb\RealMediaLibrary\lite\order;

use MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
trait Sortable {
    // Documented in IFolderContent
    public function contentDeleteOrder() {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IFolderContent
    public function contentRestoreOldCustomNr() {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IFolderContent
    public function contentOrder($attachmentId, $nextId, $lastIdInView = \false) {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IFolderContent
    public function contentOrderBy($orderby, $writeMetadata = \true) {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IFolderContent
    public function contentIndex($delete = \true) {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IFolderContent
    public function contentReindex() {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IFolderContent
    public function contentEnableOrder() {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IFolderContent
    public function getAttachmentNextTo($attachmentId) {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IFolderContent
    public function getContentAggregationNr($function = 'MAX') {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IFolderContent
    public function getContentNrOf($attachmentId) {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
    // Documented in IFolderContent
    public function getContentOldCustomNrCount() {
        throw new \MatthiasWeb\RealMediaLibrary\exception\OnlyInProVersionException(__METHOD__);
    }
}
