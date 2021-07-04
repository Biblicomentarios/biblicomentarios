<?php

namespace ACA\ACF\Field;

use AC;
use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACP;
use ACP\Sorting\Type\DataType;

class Number extends Field {

	public function get_dependent_settings() {
		$settings = parent::get_dependent_settings();
		$settings[] = new AC\Settings\Column\NumberFormat( $this->column );

		return $settings;
	}

	public function editing() {
		return new Editing\Number( $this->column );
	}

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key(), new DataType( DataType::NUMERIC ) );
	}

	public function filtering() {
		return new Filtering\Number( $this->column );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Number( $this->get_meta_key(), $this->get_meta_type() );
	}

}