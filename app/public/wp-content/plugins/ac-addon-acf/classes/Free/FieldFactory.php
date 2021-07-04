<?php

namespace ACA\ACF\Free;

use ACA\ACF;
use ACA\ACF\Column;
use ACA\ACF\Field;

class FieldFactory {

	/**
	 * @var ACF\FieldFactory
	 */
	private $factory;

	public function __construct() {
		$this->factory = new ACF\FieldFactory();
	}

	/**
	 * @param string $type
	 * @param Column $column
	 *
	 * @return Field
	 */
	public function create( $type, Column $column ) {
		switch ( $type ) {
			case 'date_picker' :
				return new Field\DatePicker( $column );
			default :
				return $this->factory->create( $type, $column );
		}
	}

}