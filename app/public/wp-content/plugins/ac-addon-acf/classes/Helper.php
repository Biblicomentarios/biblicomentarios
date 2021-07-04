<?php

namespace ACA\ACF;

class Helper {

	/**
	 * @param string $field_key
	 *
	 * @return string|null
	 */
	public function get_field_edit_link( $field_key ) {
		$field = acf_get_field( $field_key );

		if ( empty( $field['parent'] ) ) {
			return null;
		}

		if ( ! function_exists( 'acf_get_raw_field_group' ) ) {
			return null;
		}

		$group = acf_get_raw_field_group( $field['parent'] );

		if ( empty( $group['ID'] ) ) {
			return null;
		}

		return acf_get_field_group_edit_link( $group['ID'] );
	}
}