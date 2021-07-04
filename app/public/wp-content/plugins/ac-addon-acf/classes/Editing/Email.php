<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;

class Email extends Editing {

	public function get_view_settings() {
		$data = parent::get_view_settings();

		$data['type'] = 'email';

		return $data;
	}

}