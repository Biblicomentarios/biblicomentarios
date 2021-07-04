<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;
use ACP;
use ACP\Helper\Select;
use ACP\Helper\Select\Formatter;
use ACP\Helper\Select\Group;

class User extends Editing
	implements ACP\Editing\PaginatedOptions {

	public function get_edit_value( $id ) {
		$user_ids = parent::get_edit_value( $id );

		if ( empty( $user_ids ) ) {
			return false;
		}

		$values = [];

		foreach ( (array) $user_ids as $k => $user_id ) {
			$values[ $user_id ] = ac_helper()->user->get_display_name( $user_id );
		}

		return $values;
	}

	public function get_view_settings() {
		$data = [
			'type'          => 'select2_dropdown',
			'ajax_populate' => true,
		];

		$field = $this->column->get_field();

		if ( $field->get( 'allow_null' ) ) {
			$data['clear_button'] = true;
		}

		return $data;
	}

	public function get_paginated_options( $search, $paged, $id = null ) {

		$entities = new Select\Entities\User( [
			'search' => $search,
			'paged'  => $paged,
			'role'   => $this->column->get_field()->get( 'role' ),
		] );

		return new Select\Options\Paginated(
			$entities,
			new Group\UserRole(
				new Formatter\UserName( $entities )
			)
		);
	}

}