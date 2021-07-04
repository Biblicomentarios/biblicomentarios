<?php

namespace ACA\ACF\Column;

use ACA\ACF;

class User extends ACF\Column {

	public function get_formatted_id( $id ) {
		return 'user_' . $id;
	}

	public function register_settings() {
		$this->add_setting( $this->field_settings_factory->create( 'user', $this ) );
	}

}