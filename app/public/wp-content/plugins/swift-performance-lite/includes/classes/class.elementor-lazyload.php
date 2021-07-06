<?php

class Elementor_Lazyload_Column extends \Elementor\Control_Switcher {

	public function get_type() {
            return __('Lazyload', 'swift-performance');
      }

	public function get_default_value() {
            return flase;
      }

}