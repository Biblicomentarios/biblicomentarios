<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;

class Image extends Editing {

	public function get_view_settings() {
		$data = [
			'type' => 'media',
		];

		$data['attachment']['library']['type'] = 'image';

		if ( ! $this->column->get_field()->get( 'required' ) ) {
			$data['clear_button'] = true;
		}

		if ( 'uploadedTo' === $this->column->get_field()->get( 'library' ) ) {
			$data['attachment']['library']['uploaded_to_post'] = true;
		}

		return $data;
	}

}