<?php

namespace ACA\ACF\Field;

use AC;
use ACA\ACF\API;
use ACA\ACF\Field;
use ACA\ACF\Formattable;
use ACA\ACF\Setting;

class Group extends Field {

	public function get_value( $id ) {
		$value = $this->get_raw_value( $id );
		$field = $this->get_field();

		if ( ! $value ) {
			return $this->column->get_empty_char();
		}

		if ( $field instanceof Formattable ) {
			$value = $field->format( $value );
		}

		return $this->column->get_formatted_value( $value );
	}

	public function get_raw_value( $id ) {
		$sub_field = $this->get_acf_sub_field();

		if ( empty( $sub_field ) ) {
			return false;
		}

		$raw_value = parent::get_raw_value( $id );

		if ( empty( $raw_value ) ) {
			return false;
		}

		if ( ! isset( $raw_value[ $sub_field['key'] ] ) ) {
			return false;
		}

		return $raw_value[ $sub_field['key'] ];
	}

	/**
	 * @return Field|false
	 */
	private function get_field() {
		$sub_field = $this->get_acf_sub_field();

		if ( ! $sub_field ) {
			return false;
		}

		return $this->column->get_field_by_type( $sub_field['type'] );
	}

	public function get_dependent_settings() {
		return [
			new Setting\Subfield( $this->column ),
			new AC\Settings\Column\BeforeAfter( $this->column ),
		];
	}

	/**
	 * @return array|false
	 */
	private function get_acf_sub_field() {
		return API::get_field( $this->column->get_setting( 'sub_field' )->get_value() );
	}

}