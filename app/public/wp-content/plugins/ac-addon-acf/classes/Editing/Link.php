<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;

class Link extends Editing {

	public function get_view_settings() {
		$data = parent::get_view_settings();

		$data['type'] = 'acf_link';

		return $data;
	}

}