<?php

namespace ACA\ACF;

use AC;
use ACP;

/**
 * ACF Field for Advanced Custom Fields
 * @since 1.1
 * @abstract
 */
abstract class Column extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Filtering\Filterable, ACP\Sorting\Sortable, ACP\Export\Exportable, ACP\Search\Searchable, AC\Column\AjaxValue {

	/**
	 * @var Free\FieldFactory|FieldFactory
	 */
	protected $field_factory;

	/**
	 * @var Free\FieldFactory|FieldFactory
	 */
	protected $field_settings_factory;

	public function __construct() {
		$this
			->set_type( 'column-acf_field' )
			->set_label( __( 'Advanced Custom Fields', 'codepress-admin-columns' ) )
			->set_group( 'acf' );

		$this->field_factory = API::is_free()
			? new Free\FieldFactory()
			: new FieldFactory();

		$this->field_settings_factory = API::is_free()
			? new Free\Setting\FieldFactory()
			: new Setting\FieldFactory();

	}

	public function get_meta_key() {
		return $this->get_field()->get( 'name' );
	}

	public function get_ajax_value( $id ) {
		return $this->get_field()->get_ajax_value( $id );
	}

	public function get_value( $id ) {
		$value = $this->get_field()->get_value( $id );

		if ( $value instanceof AC\Collection ) {
			$value = $value->filter()->implode( $this->get_separator() );
		}

		// Wrap in ACF Append Prepend
		if ( $value ) {
			$prepend = $this->get_field()->get( 'prepend' );
			$append = $this->get_field()->get( 'append' );

			// remove &nbsp; characters
			$prepend = str_replace( chr( 194 ) . chr( 160 ), ' ', $prepend );
			$append = str_replace( chr( 194 ) . chr( 160 ), ' ', $append );

			$value = $prepend . $value . $append;
		}

		if ( ! $value && ! in_array( $value, [ 0, '0' ], true ) ) {
			return $this->get_empty_char();
		}

		return $value;
	}

	public function get_raw_value( $id ) {
		return $this->get_field()->get_raw_value( $id );
	}

	public function editing() {
		return $this->get_field()->editing();
	}

	public function filtering() {
		return $this->get_field()->filtering();
	}

	public function search() {
		return $this->get_field()->search();
	}

	public function sorting() {
		return $this->get_field()->sorting();
	}

	public function export() {
		return $this->get_field()->export();
	}

	/**
	 * @return array|false ACF Field settings
	 */
	public function get_acf_field() {
		return API::get_field( $this->get_field_hash() );
	}

	/**
	 * @param string $property
	 *
	 * @return string|array|false
	 */
	public function get_acf_field_option( $property ) {
		$field = $this->get_acf_field();

		if ( ! $field || ! array_key_exists( $property, $field ) ) {
			return false;
		}

		return $field[ $property ];
	}

	/**
	 * @return Field
	 */
	public function get_field() {
		return $this->field_factory->create( $this->get_acf_field_option( 'type' ), $this );
	}

	/**
	 * Returns Field. By default it will return a Pro version Field, but when available this returns a Free version Field.
	 *
	 * @param string $field_type ACF field type
	 *
	 * @return Field
	 */
	public function get_field_by_type( $field_type ) {
		return $this->field_factory->create( $field_type, $this );
	}

	/**
	 * Get Field hash
	 * @return string ACF field Hash (key)
	 * @since 1.1
	 */
	public function get_field_hash() {
		if ( ! $this->get_setting( 'field' ) ) {
			return false;
		}

		return $this->get_setting( 'field' )->get_value();
	}

	/**
	 * Get formatted ID for ACF
	 *
	 * @param int $id
	 *
	 * @since 1.2.2
	 */
	public abstract function get_formatted_id( $id );

}