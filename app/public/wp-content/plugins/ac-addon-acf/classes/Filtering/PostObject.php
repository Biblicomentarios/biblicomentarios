<?php

namespace ACA\ACF\Filtering;

use ACA\ACF\Filtering;
use ACP\Filtering\Helper;

class PostObject extends Filtering {

	public function get_filtering_data() {

		if ( $this->column->is_serialized() ) {
			$values = $this->get_meta_values_unserialized();
		} else {
			$values = $this->get_meta_values();
		}

		return [
			'empty_option' => true,
			'options'      => ( new Helper() )->get_post_titles( $values ),
		];
	}

}