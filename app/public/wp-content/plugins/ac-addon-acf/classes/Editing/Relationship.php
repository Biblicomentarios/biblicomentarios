<?php

namespace ACA\ACF\Editing;

class Relationship extends PostObject {

	public function get_view_settings() {
		$data = parent::get_view_settings();

		$data['multiple'] = true;

		return $data;
	}

}