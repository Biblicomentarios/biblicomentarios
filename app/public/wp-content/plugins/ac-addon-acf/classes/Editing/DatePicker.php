<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;
use DateTime;

class DatePicker extends Editing {

	public function get_edit_value( $id ) {
		$value = parent::get_edit_value( $id );
		$date = DateTime::createFromFormat( $this->get_stored_date_format(), $value );

		if ( ! $date ) {
			return false;
		}

		return $date->format( 'Y-m-d' );
	}

	public function get_view_settings() {
		$data = parent::get_view_settings();
		$field = $this->column->get_field();

		$data['type'] = 'date';
		$data['weekstart'] = $field->get( 'first_day' );

		if ( ! $field->get( 'required' ) ) {
			$data['clear_button'] = true;
		}

		return $data;
	}

	public function save( $id, $value ) {
		if ( $value ) {
			$date = DateTime::createFromFormat( 'Y-m-d', $value );
			$value = $date->format( $this->get_stored_date_format() );
		}

		return parent::save( $id, $value );
	}

	/**
	 * Support for legacy settings in ACF Free version 5
	 * @return string
	 */
	private function get_stored_date_format() {
		$field = $this->column->get_field();
		$save_format = $field->get( 'save_format' );

		if ( ! $save_format ) {
			return 'Ymd';
		}

		return ac_helper()->date->parse_jquery_dateformat( $save_format );
	}

}