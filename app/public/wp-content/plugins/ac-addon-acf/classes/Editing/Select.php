<?php

namespace ACA\ACF\Editing;

class Select extends Options {

	public function get_view_settings() {
		$data = parent::get_view_settings();
		$data['type'] = 'select';

		return $data;
	}

}