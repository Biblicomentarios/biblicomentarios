<?php

namespace ACA\ACF\Setting;

use AC;
use AC\View;
use ACA\ACF\API;
use ACA\ACF\Column;

/**
 * @property Column $column
 */
class Subfield extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $sub_field;

	protected function define_options() {
		return [ 'sub_field' ];
	}

	public function create_view() {
		$setting = $this->create_element( 'select' );

		$setting
			->set_no_result( sprintf( __( 'No %s subfields available.', 'codepress-admin-columns' ), 'ACF' ) )
			->set_attribute( 'data-refresh', 'column' )
			->set_options( $this->get_sub_fields() );

		$view = new View( [
			'label'   => __( 'Subfield', 'codepress-admin-columns' ),
			'tooltip' => __( 'Select a repeater sub field.', 'codepress-admin-columns' ),
			'setting' => $setting,
		] );

		return $view;
	}

	public function get_sub_fields() {
		$fields = [];

		$sub_fields = $this->column->get_field()->get( 'sub_fields' );

		if ( $sub_fields ) {
			foreach ( $sub_fields as $sub_field ) {
				if ( in_array( $sub_field['type'], [ 'repeater', 'group' ] ) ) {
					continue;
				}

				$fields[ $sub_field['key'] ] = $sub_field['label'];
			}
		}

		natcasesort( $fields );

		return $fields;
	}

	/**
	 * @return string
	 */
	public function get_first_sub_field() {
		$fields = $this->get_sub_fields();
		reset( $fields );

		return key( $fields );
	}

	public function get_sub_field() {
		if ( null === $this->sub_field ) {
			$this->sub_field = $this->get_first_sub_field();
		}

		return $this->sub_field;
	}

	public function set_sub_field( $sub_field ) {
		$this->sub_field = $sub_field;

		return $this;
	}

	public function get_dependent_settings() {
		$acf_field = API::get_field( $this->get_sub_field() );

		if ( ! $acf_field ) {
			return [];
		}

		return $this->column->get_field_by_type( $acf_field['type'] )->get_dependent_settings();
	}

}