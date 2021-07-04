<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;

class Password extends Editing {

	public function get_view_settings() {
		$data = parent::get_view_settings();

		$data['type'] = 'text' == $this->column->get_option( 'password_display' ) ? 'text' : 'password';

		return $data;
	}

}