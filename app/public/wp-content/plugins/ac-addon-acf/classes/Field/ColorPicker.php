<?php

namespace ACA\ACF\Field;

use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACA\ACF\Formattable;
use ACP;
use ACP\Sorting\Type\DataType;

class ColorPicker extends Field
	implements Formattable {

	public function get_value( $id ) {
		return $this->format( parent::get_value( $id ) );
	}

	public function format( $value ) {
		return ac_helper()->string->get_color_block( $value );
	}

	public function editing() {
		return new Editing\ColorPicker( $this->column );
	}

	public function filtering() {
		return new Filtering( $this->column );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Text( $this->get_meta_key(), $this->get_meta_type() );
	}

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key() );
	}

}