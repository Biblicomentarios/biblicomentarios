<?php

namespace ACA\ACF\Field;

use ACA\ACF\Field;

class GoogleMap extends Field {

	public function get_value( $id ) {
		$value = parent::get_value( $id );

		$map_data = [];
		if ( ! empty( $value['address'] ) ) {
			$map_data[] = $value['address'];
		}
		if ( ! empty( $value['lat'] ) ) {
			$map_data[] = $value['lat'];
		}
		if ( ! empty( $value['lng'] ) ) {
			$map_data[] = $value['lng'];
		}

		return implode( "<br/>\n", $map_data );
	}

}