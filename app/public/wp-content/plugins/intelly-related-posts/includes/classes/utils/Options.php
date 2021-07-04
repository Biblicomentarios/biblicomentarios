<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class IRP_Options {
    var $vars;
    public function __construct() {
        $this->vars=array();
    }

    //always add a prefix to avoid conflicts with other plugins
    protected function getKey($key) {
        return 'IRP_'.$key;
    }
    //option
    protected function removeOption($key) {
        $key=$this->getKey($key);
        delete_option($key);
    }
    protected function getOption($key, $default=FALSE) {
        $key=$this->getKey($key);
        $result=get_option($key, $default);
        if(is_string($result)) {
            $result=trim($result);
        }
        return $result;
    }
    protected function setOption($key, $value) {
        $key=$this->getKey($key);
        if(is_bool($value)) {
            $value=($value ? 1 : 0);
        }
        update_option($key, $value);
    }

    //$_SESSION
    protected function removeSession($key) {
        global $wp_session;

        $key=$this->getKey($key);
        if(isset($wp_session[$key])) {
            unset($wp_session[$key]);
        }
    }
    protected function getSession($key, $default=FALSE) {
        global $wp_session;

        $key=$this->getKey($key);
        $result=$default;
        if(isset($wp_session[$key])) {
            $result=$wp_session[$key];
        }
        if(is_string($result)) {
            $result=trim($result);
        }
        return $result;
    }
    protected function setSession($key, $value) {
        global $wp_session;

        $key=$this->getKey($key);
        $wp_session[$key]=$value;
    }

    //$_REQUEST
    //However WP enforces its own logic - during load process wp_magic_quotes() processes variables to emulate magic quotes setting and enforces $_REQUEST to contain combination of $_GET and $_POST, no matter what PHP configuration says.
    protected function removeRequest($key) {
        $key=$this->getKey($key);
        if(isset($this->vars[$key])) {
            unset($this->vars[$key]);
        }
    }
    protected function getRequest($key, $default=FALSE) {
        $key=$this->getKey($key);
        $result=$default;
        if(isset($this->vars[$key])) {
            $result=$this->vars[$key];
        }
        return $result;
    }
    protected function setRequest($key, $value) {
        $key=$this->getKey($key);
        $this->vars[$key]=$value;
    }

    //ShowWhatsNew
    public function isShowWhatsNew() {
        return $this->getOption('ShowWhatsNew', FALSE);
    }
    public function setShowWhatsNew($value) {
        $this->setOption('ShowWhatsNew', $value);
    }

    //TrackingEnable
    public function isTrackingEnable() {
        return $this->getOption('TrackingEnable', 0);
    }
    public function setTrackingEnable($value) {
        $this->setOption('TrackingEnable', $value);
    }
    //TrackingNotice
    public function isTrackingNotice() {
        return $this->getOption('TrackingNotice', 1);
    }
    public function setTrackingNotice($value) {
        $this->setOption('TrackingNotice', $value);
    }

    public function isActive() {
        return $this->getOption('Active', 0);
    }
    public function setActive($value) {
        $this->setOption('Active', $value);
    }

    public function getTrackingLastSend() {
        return $this->getOption('TrackingLastSend['.IRP_PLUGIN_SLUG.']', 0);
    }
    public function setTrackingLastSend($value) {
        $this->setOption('TrackingLastSend['.IRP_PLUGIN_SLUG.']', $value);
    }
    public function getPluginInstallDate() {
        return $this->getOption('PluginInstallDate['.IRP_PLUGIN_SLUG.']', 0);
    }
    public function setPluginInstallDate($value) {
        $this->setOption('PluginInstallDate['.IRP_PLUGIN_SLUG.']', $value);
    }
    public function getPluginUpdateDate() {
        return $this->getOption('PluginUpdateDate['.IRP_PLUGIN_SLUG.']', 0);
    }
    public function setPluginUpdateDate($value) {
        $this->setOption('PluginUpdateDate['.IRP_PLUGIN_SLUG.']', $value);
    }

    public function isPluginFirstInstall() {
        return $this->getOption('PluginFirstInstall', FALSE);
    }
    public function setPluginFirstInstall($value) {
        $this->setOption('PluginFirstInstall', $value);
    }
    public function isShowActivationNotice() {
        return $this->getOption('ShowActivationNotice', FALSE);
    }
    public function setShowActivationNotice($value) {
        $this->setOption('ShowActivationNotice', $value);
    }

    //LoggerEnable
    public function isLoggerEnable() {
        return ($this->getOption('LoggerEnable', FALSE) || (defined('IRP_LOGGER') && IRP_LOGGER));
    }
    public function setLoggerEnable($value) {
        $this->setOption('LoggerEnable', $value);
    }

    //Cache
    public function getCache($name, $id) {
        return $this->getRequest('Cache_'.$name.'_'.$id);
    }
    public function setCache($name, $id, $value) {
        $this->setRequest('Cache_'.$name.'_'.$id, $value);
    }

    public function getFeedbackEmail() {
        return $this->getOption('FeedbackEmail', get_bloginfo('admin_email'));
    }
    public function setFeedbackEmail($value) {
        $this->setOption('FeedbackEmail', $value);
    }

    private function hasGenericMessages($type) {
        $result=$this->getRequest($type.'Messages', NULL);
        return (is_array($result) && count($result)>0);
    }

    private function pushGenericMessage($type, $message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL) {
        global $irp;
        $array=$this->getRequest($type.'Messages', array());
        $array[]=$irp->Lang->L($message, $v1, $v2, $v3, $v4, $v5);
        $this->setRequest($type.'Messages', $array);
    }
    private function writeGenericMessages($type, $clean=TRUE) {
        $result=FALSE;
        $array=$this->getRequest($type.'Messages', array());
        if(is_array($array) && count($array)>0) {
            $result=TRUE;
            ?>
            <div class="irp-box-<?php echo strtolower($type)?>"><?php echo wpautop(implode("\n", $array)); ?></div>
        <?php }
        if($clean) {
            $this->removeRequest($type.'Messages');
        }
        return $result;
    }
    //WarningMessages
    public function hasWarningMessages() {
        return $this->hasGenericMessages('Warning');
    }
    public function pushWarningMessage($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL) {
        return $this->pushGenericMessage('Warning', $message, $v1, $v2, $v3, $v4, $v5);
    }
    public function writeWarningMessages($clean=TRUE) {
        return $this->writeGenericMessages('Warning', $clean);
    }
    //SuccessMessages
    public function hasSuccessMessages() {
        return $this->hasGenericMessages('Success');
    }
    public function pushSuccessMessage($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL) {
        return $this->pushGenericMessage('Success', $message, $v1, $v2, $v3, $v4, $v5);
    }
    public function writeSuccessMessages($clean=TRUE) {
        return $this->writeGenericMessages('Success', $clean);
    }
    //InfoMessages
    public function hasInfoMessages() {
        return $this->hasGenericMessages('Info');
    }
    public function pushInfoMessage($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL) {
        return $this->pushGenericMessage('Info', $message, $v1, $v2, $v3, $v4, $v5);
    }
    public function writeInfoMessages($clean=TRUE) {
        return $this->writeGenericMessages('Info', $clean);
    }
    //ErrorMessages
    public function hasErrorMessages() {
        return $this->hasGenericMessages('Error');
    }
    public function pushErrorMessage($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL) {
        return $this->pushGenericMessage('Error', $message, $v1, $v2, $v3, $v4, $v5);
    }
    public function writeErrorMessages($clean=TRUE) {
        return $this->writeGenericMessages('Error', $clean);
    }

    public function writeMessages($clean=TRUE) {
        $result=FALSE;
        if($this->writeInfoMessages($clean)) {
            $result=TRUE;
        }
        if($this->writeSuccessMessages($clean)) {
            $result=TRUE;
        }
        if($this->writeWarningMessages($clean)) {
            $result=TRUE;
        }
        if($this->writeErrorMessages($clean)) {
            $result=TRUE;
        }

        return $result;
    }
    public function pushMessage($success, $message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL) {
        if($success) {
            $this->pushSuccessMessage($message.'Success', $v1, $v2, $v3, $v4, $v5);
        } else {
            $this->pushErrorMessage($message.'Error', $v1, $v2, $v3, $v4, $v5);
        }
    }
}