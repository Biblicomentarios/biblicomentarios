<?php

namespace ACA\ACF\Field;

use AC;
use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACP;
use ACP\Sorting\Type\DataType;

class Image extends Field {

	public function editing() {
		return new Editing\Image( $this->column );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Image( $this->get_meta_key(), $this->get_meta_type(), $this->column->get_post_type() );
	}

	public function filtering() {
		return new Filtering\Image( $this->column );
	}

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key() );
	}

	public function export() {
		return new ACP\Export\Model\CustomField\Image( $this->column );
	}

	public function get_dependent_settings() {
		return [
			new AC\Settings\Column\Image( $this->column ),
		];
	}

}