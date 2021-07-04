<?php

namespace ACA\ACF\Editing;

class Users extends User {

	public function get_view_settings() {
		$field = $this->column->get_field();
		$data = [
			'type'                => 'acf_select2',
			'ajax_populate'       => true,
			'multiple'            => true,
			'disable_revisioning' => 'true',
		];

		if ( $field->get( 'allow_null' ) ) {
			$data['clear_button'] = true;
		}

		return $data;
	}

	public function save( $id, $value ) {
		if ( ! isset( $value['values'] ) ) {
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

	private function extend_value( $id, $values ) {
		$old_values = array_keys( $this->get_edit_value( $id ) );
		$new_values = array_merge( $old_values, $values );

		return array_unique( $new_values );
	}

	private function reduce_value( $id, $remove_values ) {
		$values = array_keys( $this->get_edit_value( $id ) );

		foreach ( $values as $key => $value ) {
			if ( in_array( $value, $remove_values ) ) {
				unset( $values[ $key ] );
			}
		}

		return $values;
	}

}