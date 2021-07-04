<?php

namespace ACA\ACF\Editing;

use AC;
use ACA\ACF\Editing;
use ACP;
use ACP\Helper\Select;
use ACP\Helper\Select\Formatter;

class Taxonomy extends Editing
	implements ACP\Editing\PaginatedOptions {

	public function get_view_settings() {
		$data = parent::get_view_settings();

		$data['type'] = 'acf_select2';
		$data['ajax_populate'] = true;
		$data['store_values'] = false;
		$data['disable_revisioning'] = false;
		$data['clear_button'] = empty( $data['required'] );

		switch ( $this->column->get_acf_field_option( 'field_type' ) ) {
			case 'checkbox' :
			case 'multi_select' :
				$data['multiple'] = true;
				break;
		}

		return $data;
	}

	/**
	 * @return array|string
	 */
	private function get_taxonomy() {
		return $this->column->get_acf_field_option( 'taxonomy' );
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		$entities = new Select\Entities\Taxonomy( [
			'search'   => $search,
			'page'     => $page,
			'taxonomy' => $this->get_taxonomy(),
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Formatter\TermName( $entities )
		);
	}

	/**
	 * @param int $term_id
	 *
	 * @return array
	 */
	public function get_edit_value( $term_id ) {
		$term_ids = parent::get_edit_value( $term_id );

		$taxonomy = $this->get_taxonomy();

		$values = [];
		foreach ( ac_helper()->taxonomy->get_terms_by_ids( $term_ids, $taxonomy ) as $term ) {
			$values[ $term->term_id ] = $term->name;
		}

		return $values;
	}

	/**
	 * @param $id
	 * @param $value
	 *
	 * @return bool
	 */
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
		$values = array_keys( $this->get_edit_value( $id ) );
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
		$values = array_keys( $this->get_edit_value( $id ) );

		foreach ( $values as $key => $term ) {
			if ( in_array( $term, $terms ) ) {
				unset( $values[ $key ] );
			}
		}

		return $values;
	}

}