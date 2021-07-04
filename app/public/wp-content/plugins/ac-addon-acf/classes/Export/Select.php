<?php

namespace ACA\ACF\Export;

use ACA\ACF\Column;
use ACP;

/**
 * @property Column $column
 */
class Select extends ACP\Export\Model {

	public function __construct( Column $column ) {
		parent::__construct( $column );
	}

	public function get_value( $id ) {
		$value = $this->column->get_raw_value( $id );
		$choices = $this->column->get_field()->get( 'choices' );

		$options = [];
		foreach ( (array) $value as $value ) {
			if ( isset( $choices[ $value ] ) ) {
				$options[] = $choices[ $value ];
			}
		}

		return ac_helper()->html->implode( $options, ', ' );
	}

}