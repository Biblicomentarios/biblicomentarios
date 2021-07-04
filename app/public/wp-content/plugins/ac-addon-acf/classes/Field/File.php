<?php

namespace ACA\ACF\Field;

use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACA\ACF\Formattable;
use ACA\ACF\Sorting;
use ACP;
use ACP\Sorting\Model\MetaFormatFactory;

class File extends Field
	implements Formattable {

	public function get_value( $id ) {
		$attachment_id = parent::get_value( $id );

		return $this->format( $attachment_id );
	}

	public function format( $attachment_id ) {
		$value = false;

		if ( $attachment_id ) {
			$attachment = get_attached_file( $attachment_id );

			if ( $attachment ) {
				$value = ac_helper()->html->link( wp_get_attachment_url( $attachment_id ), esc_html( basename( $attachment ) ), [ 'target' => '_blank' ] );
			} else {
				$value = '<em>' . __( 'Invalid attachment', 'codepress-admin-columns' ) . '</em>';
			}
		}

		return $value;
	}

	public function editing() {
		return new Editing\File( $this->column );
	}

	public function sorting() {
		return ( new MetaFormatFactory() )->create( $this->get_meta_type(), $this->get_meta_key(), new Sorting\FormatValue\File() );
	}

	public function filtering() {
		return new Filtering\File( $this->column );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Media( $this->get_meta_key(), $this->get_meta_type() );
	}

}