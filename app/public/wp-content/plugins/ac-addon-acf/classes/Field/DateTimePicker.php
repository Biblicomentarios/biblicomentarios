<?php

namespace ACA\ACF\Field;

use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACA\ACF\Search;
use ACA\ACF\Setting;
use ACP;
use ACP\Sorting\Type\DataType;

class DateTimePicker extends Field {

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key(), new DataType( DataType::DATETIME ) );
	}

	public function search() {
		return new Search\DateTimePicker( $this->get_meta_key(), $this->get_meta_type() );
	}

	public function filtering() {
		return new Filtering\DateTimePicker( $this->column );
	}

	public function editing() {
		return new Editing\DateTimePicker( $this->column );
	}

	public function get_dependent_settings() {
		return [
			new Setting\Date( $this->column ),
		];
	}

}