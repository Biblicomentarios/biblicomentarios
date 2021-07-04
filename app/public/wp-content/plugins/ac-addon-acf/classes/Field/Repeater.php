<?php

namespace ACA\ACF\Field;

use AC\Collection;
use ACA\ACF\API;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACA\ACF\Formattable;
use ACA\ACF\Setting;
use ACA\ACF\Sorting;
use ACP;

class Repeater extends Field {

	public function get_value( $id ) {
		if ( 'count' === $this->get_repeater_display() ) {
			$count = $this->get_row_count( $id );

			return $count ? $count : $this->column->get_empty_char();
		}

		return $this->get_sub_field_value( $id );
	}

	public function get_row_count( $id ) {
		$value = $this->get_raw_value( $id );

		if ( ! $value || empty( $value ) ) {
			return 0;
		}

		return count( $this->get_raw_value( $id ) );
	}

	public function get_sub_field_value( $id ) {
		$sub_field = $this->get_acf_sub_field();

		if ( empty( $sub_field ) ) {
			return false;
		}

		$raw_values = $this->get_raw_value( $id );

		if ( empty( $raw_values ) ) {
			return $this->column->get_empty_char();
		}

		$values = new Collection();
		$field = $this->column->get_field_by_type( $sub_field['type'] );

		foreach ( $raw_values as $raw_value ) {
			if ( isset( $raw_value[ $sub_field['key'] ] ) ) {
				$value = $raw_value[ $sub_field['key'] ];

				if ( $field instanceof Formattable ) {
					$value = $field->format( $value );
				}

				$values->push( $value );
			}
		}

		return $this->column->get_formatted_value( $values );
	}

	public function get_dependent_settings() {
		return [
			new Setting\RepeaterDisplay( $this->column ),
		];
	}

	/**
	 * @return false|string
	 */
	private function get_repeater_display() {
		$setting = $this->column->get_setting( 'repeater_display' );

		if ( ! $setting instanceof Setting\RepeaterDisplay ) {
			return false;
		}

		return $setting->get_repeater_display();
	}

	private function get_acf_sub_field() {
		if ( 'count' === $this->get_repeater_display() ) {
			return false;
		}

		return API::get_field( $this->column->get_setting( 'sub_field' )->get_value() );
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this->column );
	}

	public function sorting() {
		if ( 'count' === $this->get_repeater_display() ) {
			return new Sorting\Repeater( $this->column->get_acf_field_option( 'name' ) );
		}

		return new ACP\Sorting\Model\Disabled();
	}

	public function filtering() {
		if ( 'count' === $this->get_repeater_display() ) {
			return new Filtering\Repeater( $this->column );
		}

		return new ACP\Filtering\Model\Disabled( $this->column );
	}
}