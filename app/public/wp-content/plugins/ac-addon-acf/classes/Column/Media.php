<?php

namespace ACA\ACF\Column;

use ACA\ACF;

class Media extends ACF\Column {

	public function register_settings() {
		$this->add_setting( $this->field_settings_factory->create( 'media', $this ) );
	}

	public function get_formatted_id( $id ) {
		return $id;
	}

}