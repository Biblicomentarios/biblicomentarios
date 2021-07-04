<?php

namespace ACA\ACF\Filtering;

use ACA\ACF\Filtering;

class Number extends Filtering {

	public function get_data_type() {
		return 'numeric';
	}

	public function is_ranged() {
		return true;
	}

}