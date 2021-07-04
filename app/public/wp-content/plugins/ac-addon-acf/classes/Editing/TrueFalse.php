<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;

class TrueFalse extends Editing {

	public function get_view_settings() {
		$data = parent::get_view_settings();

		$data['type'] = 'togglable';
		$data['options'] = [
			'0' => __( 'False', 'codepress-admin-columns' ),
			'1' => __( 'True', 'codepress-admin-columns' ),
		];

		return $data;
	}

}