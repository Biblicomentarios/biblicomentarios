<?php

namespace ACA\ACF\Filtering;

use ACA\ACF\Filtering;

class Repeater extends Filtering {

	public function get_data_type() {
		return 'numeric';
	}

	public function is_ranged() {
		return true;
	}

	public function get_filtering_vars( $vars ) {
		$field = $this->column->get_acf_field();
		$args = $this->get_filter_value();
		$args['key'] = $field['name'];

		return $this->get_filtering_vars_ranged( $vars, $args );
	}

}