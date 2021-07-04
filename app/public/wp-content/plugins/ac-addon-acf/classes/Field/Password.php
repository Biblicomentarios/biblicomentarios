<?php

namespace ACA\ACF\Field;

use AC;
use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACP;
use ACP\Sorting\Type\DataType;

class Password extends Field {

	public function get_dependent_settings() {
		return [
			new AC\Settings\Column\Password( $this->column ),
		];
	}

	public function editing() {
		return new Editing\Password( $this->column );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Text( $this->get_meta_key(), $this->get_meta_type() );
	}

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key() );
	}

	public function filtering() {
		return new Filtering\Password( $this->column );
	}

}