<?php

namespace ACA\ACF\Editing;

class Radio extends Options {

	public function get_view_settings() {
		$data = parent::get_view_settings();

		if ( ! $this->column->get_field()->get( 'multiple' ) ) {
			$data['type'] = 'select';
		}

		return $data;
	}

}