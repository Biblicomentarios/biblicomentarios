<?php

namespace ACA\ACF\Field;

use ACA\ACF\Column;
use ACA\ACF\Editing;
use ACA\ACF\Filtering;
use ACA\ACF\Search;
use ACA\ACF\Sorting;
use ACP\Sorting\Model\MetaFormatFactory;

/**
 * @property Column $column
 */
class Checkbox extends Select {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->column->set_serialized( true );
	}

	public function editing() {
		return new Editing\Checkbox( $this->column );
	}

	public function filtering() {
		return new Filtering\Checkbox( $this->column );
	}

	public function search() {
		return new Search\MultiSelect( $this->get_meta_key(), $this->get_meta_type(), $this->get_choices() );
	}

	public function sorting() {
		return ( new MetaFormatFactory )->create( $this->column->get_meta_type(), $this->column->get_meta_key(), new Sorting\FormatValue\Select( $this->get_choices() ) );
	}

}