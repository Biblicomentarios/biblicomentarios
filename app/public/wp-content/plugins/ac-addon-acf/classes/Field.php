<?php

namespace ACA\ACF;

use AC;
use ACP;

class Field
	implements ACP\Editing\Editable, ACP\Filtering\Filterable, ACP\Sorting\Sortable, ACP\Search\Searchable {

	/**
	 * @var Column
	 */
	protected $column;

	/**
	 * @param Column $column
	 */
	public function __construct( Column $column ) {
		$this->column = $column;

		// ACF multiple data is stored serialized
		$this->column->set_serialized( $this->get( 'multiple' ) );
	}

	/**
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function get_ajax_value( $id ) {
		return null;
	}

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_value( $id ) {
		return $this->column->get_formatted_value( $this->get_raw_value( $id ), $id );
	}

	public function get_raw_value( $id ) {
		return get_field( $this->column->get_meta_key(), $this->column->get_formatted_id( $id ), false );
	}

	public function get_separator() {
		return $this->column->get_separator();
	}

	public function search() {
		return false;
	}

	public function editing() {
		return new Editing\Disabled( $this->column );
	}

	public function filtering() {
		return new ACP\Filtering\Model\Disabled( $this->column );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Disabled();
	}

	public function export() {
		return new ACP\Export\Model\RawValue( $this->column );
	}

	/**
	 * @return AC\Settings\Column[]
	 */
	public function get_dependent_settings() {
		return [];
	}

	/**
	 * Get ACF field property
	 *
	 * @param string $property
	 *
	 * @return string|array|false
	 */
	public function get( $property ) {
		return $this->column->get_acf_field_option( $property );
	}

	protected function is_serialized() {
		return $this->column->is_serialized();
	}

	protected function get_meta_key() {
		return $this->column->get_meta_key();
	}

	protected function get_meta_type() {
		return $this->column->get_meta_type();
	}

}