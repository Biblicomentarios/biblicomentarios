<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;

class ColorPicker extends Editing {

	public function get_view_settings() {
		$data = parent::get_view_settings();

		$data['type'] = 'color';

		return $data;
	}

}