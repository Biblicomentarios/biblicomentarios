<?php

namespace ACA\ACF\Free\Setting;

use ACA\ACF;
use ACA\ACF\Column;

class FieldFactory {

	/**
	 * @param string $type
	 * @param Column $column
	 *
	 * @return ACF\Setting\Field
	 */
	public function create( $type, Column $column ) {

		switch ( $type ) {
			case 'user' :
				return new Field\User( $column );
			case 'post' :
				return new Field\Post( $column );
			case 'taxonomy' :
				return new Field\Taxonomy( $column );
			case 'comment' :
				return new Field\Comment( $column );
			case 'media' :
				return new Field\Media( $column );
			default :
				return null;
		}
	}

}