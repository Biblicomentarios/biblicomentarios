<?php

namespace ACA\ACF\Field;

use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACP;
use ACP\Sorting\Type\DataType;

class Range extends Field {

	public function editing() {
		return new Editing\Range( $this->column );
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