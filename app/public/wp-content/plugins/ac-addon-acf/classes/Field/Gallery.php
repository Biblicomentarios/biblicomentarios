<?php

namespace ACA\ACF\Field;

use AC;
use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACP;

class Gallery extends Field {

	public function editing() {
		return new Editing\Gallery( $this->column );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Disabled();
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\EmptyNotEmpty( $this->get_meta_key(), $this->get_meta_type() );
	}

	public function export() {
		return new ACP\Export\Model\CustomField\Image( $this->column );
	}

	public function get_dependent_settings() {
		return [
			new AC\Settings\Column\Images( $this->column ),
		];
	}

}