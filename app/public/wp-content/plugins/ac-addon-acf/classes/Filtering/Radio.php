<?php

namespace ACA\ACF\Filtering;

use ACA\ACF\Filtering;

class Radio extends Filtering {

	public function get_filtering_data() {
		$options = [];

		$choices = (array) $this->column->get_field()->get( 'choices' );

		foreach ( $this->get_meta_values() as $value ) {
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