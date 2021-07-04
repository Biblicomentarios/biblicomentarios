<?php

namespace ACA\ACF\Field;

use AC;
use ACA\ACF\Field;

/**
 * Third party field
 */
class ImageCrop extends Field {

	public function get_value( $id ) {
		$value = get_field( $this->get_meta_key(), $this->column->get_formatted_id( $id ), true );

		if ( 'object' == $this->column->get_acf_field_option( 'save_format' ) ) {
			$value = $value['url'];
		}

		return $this->column->get_formatted_value( $value );
	}

	public function get_dependent_settings() {
		return [
			new AC\Settings\Column\Image( $this->column ),
		];
	}

}