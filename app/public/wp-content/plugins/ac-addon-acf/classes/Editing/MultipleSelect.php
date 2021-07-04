<?php

namespace ACA\ACF\Editing;

class MultipleSelect extends Options {

	public function get_view_settings() {
		$data = parent::get_view_settings();
		$data['type'] = 'acf_select2';
		$data['multiple'] = true;
		$data['disable_revisioning'] = true;

		return $data;
	}

	public function save( $id, $value ) {
		if ( ! isset( $value['save_strategy'] ) ) {
			return parent::save( $id, $value );
		}

		switch ( $value['save_strategy'] ) {
			case 'add':
				return parent::save( $id, $this->extend_value( $id, $value['values'] ) );

			case 'remove':
				return parent::save( $id, $this->reduce_value( $id, $value['values'] ) );

			default:
				return parent::save( $id, $value['values'] );

		}

	}

	/**
	 * @param int   $id
	 * @param array $terms
	 *
	 * @return array
	 */
	private function extend_value( $id, $terms ) {
		$values = array_values( (array) $this->get_edit_value( $id ) );
		$new_values = array_merge( $values, $terms );

		return array_unique( $new_values );
	}

	/**
	 * @param int   $id
	 * @param array $terms
	 *
	 * @return array
	 */
	private function reduce_value( $id, $terms ) {
		$values = array_values( (array) $this->get_edit_value( $id ) );

		foreach ( $values as $key => $term ) {
			if ( in_array( $term, $terms ) ) {
				unset( $values[ $key ] );
			}
		}

		return array_values( $values );
	}

}