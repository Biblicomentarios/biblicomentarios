<?php

namespace ACA\ACF\Filtering;

class DateTimePicker extends DatePicker {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_date_format( 'Y-m-d H:i:s' );
	}

}