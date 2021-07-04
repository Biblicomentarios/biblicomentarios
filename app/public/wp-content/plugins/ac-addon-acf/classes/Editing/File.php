<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;

class File extends Editing {

	public function get_view_settings() {
		$data = [
			'type' => 'attachment',
		];

		if ( ! $this->column->get_field()->get( 'required' ) ) {
			$data['clear_button'] = true;
		}

		return $data;
	}

}