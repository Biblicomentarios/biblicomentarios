<?php

namespace ACA\ACF\Filtering;

use ACA\ACF\Filtering;

class Link extends Filtering {

	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_meta_values() as $value ) {
			$value = unserialize( $value );
			$options[ $value['url'] ] = $value['url'];
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}