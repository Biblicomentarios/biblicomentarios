<?php

namespace ACA\ACF\Field;

use ACA\ACF\Field;
use ACA\ACF\Setting;

class Unsupported extends Field {

	public function get_dependent_settings() {
		return [
			new Setting\Unsupported( $this->column ),
		];
	}

}