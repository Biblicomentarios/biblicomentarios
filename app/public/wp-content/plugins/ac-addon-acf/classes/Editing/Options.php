<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;

class Options extends Editing {

	public function get_view_settings() {
		$data = parent::get_view_settings();

		$field = $this->column->get_field();

		$data['type'] = 'checklist';

		if ( $field->get( 'multiple' ) ) {
			$data['type'] = 'select2_dropdown';
			$data['multiple'] = true;
		}

		$data['options'] = $field->get( 'choices' );

		if ( $field->get( 'allow_null' ) ) {
			$data['clear_button'] = true;
		}

		return $data;
	}

}