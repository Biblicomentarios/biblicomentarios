<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;

class Gallery extends Editing {

	public function get_view_settings() {
		$data = [
			'type'         => 'media',
			'attachment'   => [
				'library' => [
					'type' => 'image',
				],
			],
			'multiple'     => true,
			'store_values' => true,
		];

		if ( $this->column->get_field()->get( 'required' ) ) {
			$data['clear_button'] = true;
		}

		if ( 'uploadedTo' === $this->column->get_field()->get( 'library' ) ) {
			$data['attachment']['library']['uploaded_to_post'] = true;
		}

		return $data;
	}

}