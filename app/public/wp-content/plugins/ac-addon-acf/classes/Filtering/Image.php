<?php

namespace ACA\ACF\Filtering;

use ACA\ACF\Filtering;
use ACP\Filtering\Helper;

class Image extends Filtering {

	public function get_filtering_data() {
		return [
			'options'      => ( new Helper() )->get_post_titles( $this->get_meta_values() ),
			'empty_option' => true,
		];
	}

}