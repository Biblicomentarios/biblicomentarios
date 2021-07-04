<?php

namespace ACA\ACF\Field;

use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACA\ACF\Formattable;

class Link extends Field
	implements Formattable {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->column->set_serialized( true );
	}

	public function get_value( $id ) {
		$link = parent::get_value( $id );

		if ( empty( $link ) ) {
			return $this->column->get_empty_char();
		}

		return $this->format( $link );
	}

	public function format( $link ) {
		$label = $link['title'];

		if ( ! $label ) {
			$label = str_replace( [ 'http://', 'https://' ], '', $link['url'] );
		}

		if ( '_blank' === $link['target'] ) {
			$label .= '<span class="dashicons dashicons-external" style="font-size: 1em;"></span>';
		}

		return ac_helper()->html->link( $link['url'], $label );
	}

	public function filtering() {
		return new Filtering\Link( $this->column );
	}

	public function editing() {
		return new Editing\Link( $this->column );
	}

}