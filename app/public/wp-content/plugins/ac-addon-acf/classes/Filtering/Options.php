<?php

namespace ACA\ACF\Filtering;

use ACA\ACF\Filtering;

class Options extends Filtering {

	public function get_filtering_data() {

		if ( $this->column->is_serialized() ) {
			$values = $this->get_meta_values_unserialized();
		} else {
			$values = $this->get_meta_values();
		}

		$options = [];

		$choices = (array) $this->column->get_field()->get( 'choices' );

		foreach ( $values as $value ) {
			if ( $choices && isset( $choices[ $value ] ) ) {
				$options[ $value ] = $choices[ $value ];
			}
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}