<?php

namespace ACA\ACF;

use ACP;

/**
 * @property Column $column
 */
class Filtering extends ACP\Filtering\Model\Meta {

	public function __construct( Column $column ) {
		parent::__construct( $column );
	}

}