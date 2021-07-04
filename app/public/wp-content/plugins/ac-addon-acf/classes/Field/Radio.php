<?php

namespace ACA\ACF\Field;

use ACA\ACF\Editing;
use ACA\ACF\Filtering;

class Radio extends Select {

	public function editing() {
		return new Editing\Radio( $this->column );
	}

	public function filtering() {
		return new Filtering\Radio( $this->column );
	}

}