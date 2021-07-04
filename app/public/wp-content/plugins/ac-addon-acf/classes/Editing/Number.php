<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;

class Number extends Editing {

	public function get_view_settings() {
		$data = parent::get_view_settings();

		$data['type'] = 'number';

		if ( ! isset( $data['range_step'] ) ) {
			$data['range_step'] = 'any';
		}

		return $data;
	}

}