<?php
if (!defined('ABSPATH')) exit;

class IRP_Stack {
    var $array;

    public function __construct() {
        $this->array=array();
    }

    public function push($v) {
        array_push($this->array, $v);
    }
    public function pop() {
        $v=array_pop($this->array);
        return $v;
    }
    public function peek() {
        return $this->array[count($this->array)-1];
    }
    public function size() {
        return count($this->array);
    }
    public function isEmpty() {
        return (count($this->array)==0);
    }
}
