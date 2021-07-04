<?php

namespace ACA\ACF\Field;

use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACA\ACF\Setting;
use ACP;

class Oembed extends Field {

	public function editing() {
		return new Editing\Text( $this->column );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Text( $this->get_meta_key(), $this->get_meta_type() );
	}

	public function filtering() {
		return new Filtering( $this->column );
	}

	public function get_dependent_settings() {
		return [
			new Setting\Oembed( $this->column ),
		];
	}

}