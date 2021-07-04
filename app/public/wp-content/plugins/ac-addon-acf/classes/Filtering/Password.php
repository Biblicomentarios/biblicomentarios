<?php

namespace ACA\ACF\Filtering;

use ACA\ACF\Filtering;

class Password extends Filtering {

	public function get_filtering_data() {
		return [
			'empty_option' => true,
			'options'      => [],
		];
	}

}