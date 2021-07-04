<?php

namespace ACA\ACF\Filtering;

use ACA\ACF\Filtering;

class User extends Filtering {

	public function get_filtering_data() {
		$options = [];

		if ( $this->column->is_serialized() ) {
			$values = $this->get_meta_values_unserialized();
		} else {
			$values = $this->get_meta_values();
		}

		foreach ( $values as $value ) {
			$options[ $value ] = $this->column->get_formatted_value( $value );
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}