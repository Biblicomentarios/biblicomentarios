<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class IRP_Logger {
    var $name;
    var $context=array();

    var $allTime;
    var $time;
    var $profile;

    public function __construct($name='IRPP') {
        if($name=='') $name='IRPP';
        $this->name=$name;
        $this->time=-1;
        $this->allTime=-1;
        $this->profile='';
    }

    public function startTime($profile) {
        $this->info('TIME [startTime]=%s', $profile);
        $this->profile=$profile;
        $this->time=microtime(TRUE);
    }
    public function pauseTime() {
        if($this->time>0 && $this->profile!='') {
            $diff=round(microtime(TRUE)-$this->time, 3)*1000;
            if($diff!='' && $diff>0) {
                if($this->allTime<0) {
                    $this->allTime=0;
                }
                $this->allTime+=$diff;
                $this->info('TIME pauseTime [%s]=%s', $this->profile, $diff);
            }
        }
        $this->time=-1;
        $this->profile='';
    }
    public function stopTime() {
        global $irp;
        $this->info('TIME [stopTime]=%s', $this->allTime);
        if($this->allTime>0) {
            $irp->Options->updateMaxExecutionTime($this->allTime);
        }
    }

    public function pushContext($context) {
        array_push($this->context, $context);
    }
    public function popContext() {
        array_pop($this->context);
    }

    public function exception(Exception $ex) {
        $this->write('[EXCEPTION]', 'FILE=%s, LINE=%s, CODE=%s, MESSAGE=%s'
            , $ex->getFile(), $ex->getLine(), $ex->getCode(), $ex->getMessage());
    }
    public function fatal($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL, $v6=NULL) {
        $what=$this->write('[FATAL]', $message, $v1, $v2, $v3, $v4, $v5, $v6);
        die($what);
    }
    public function debug($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL, $v6=NULL) {
        $this->write('[DEBUG]', $message, $v1, $v2, $v3, $v4, $v5, $v6);
    }
    public function info($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL, $v6=NULL) {
        $this->write('[INFO] ', $message, $v1, $v2, $v3, $v4, $v5, $v6);
    }
    public function error($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL, $v6=NULL) {
        $this->write('[ERROR]', $message, $v1, $v2, $v3, $v4, $v5, $v6);
    }
    private function dump($v) {
        if(is_array($v) && count($v)==0) {
            $v='[]';
        }
        if($v!=NULL) {
            if(is_array($v) || is_object($v)) {
                $v=print_r($v, TRUE);
            }
        }
        if(is_bool($v)) {
            $v=($v ? 'TRUE' : 'FALSE');
        }
        return $v;
    }
    private function write($verbosity, $message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL, $v6=NULL) {
        global $irp;

        $text=sprintf($message
            , $this->dump($v1), $this->dump($v2), $this->dump($v3)
            , $this->dump($v4), $this->dump($v5), $this->dump($v6));
        $m=microtime(TRUE)%1000;
        $m=":".str_pad(''.$m, 3, "0", STR_PAD_LEFT);
        $message=date("d/m/Y H:i:s").$m." ".$verbosity." ";
        if(count($this->context)>0) {
            $message.='{'.$this->context[count($this->context)-1].'} ';
        }
        $message="\n".$message.$text;
        if(!$irp->Options->isLoggerEnable()) {
            return $message;
        }

        $hasErrors=false;
        $filename=IRP_PLUGIN_ROOT."logs/".$this->name."_".date("Ym").".txt";
        if (!$handle = fopen($filename, 'a')) {
            $hasErrors=true;
        }

        if(!$hasErrors && fwrite($handle, $message)===FALSE) {
            $hasErrors=true;
        }

        if(!$hasErrors) {
            fclose($handle);
        }
        return $message;
    }
}