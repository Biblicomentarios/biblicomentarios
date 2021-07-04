<?php

namespace ACA\ACF\Column;

use ACA\ACF;

class Post extends ACF\Column {

	public function register_settings() {
		$this->add_setting( $this->field_settings_factory->create( 'post', $this ) );
	}

	public function get_formatted_id( $id ) {
		return $id;
	}

}