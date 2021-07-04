<?php

namespace ACA\ACF\Field;

use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACP;

class Email extends Field {

	public function get_value( $id ) {
		$email = parent::get_value( $id );

		if ( ! $email ) {
			return false;
		}

		return ac_helper()->html->link( 'mailto:' . $email, $email );
	}

	public function editing() {
		return new Editing\Email( $this->column );
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