<?php

namespace ACA\ACF\Field;

use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Setting;
use ACP;

class TimePicker extends Field {

	public function editing() {
		return new Editing\Text( $this->column );
	}

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key() );
	}

	public function get_dependent_settings() {
		return [
			new Setting\Time( $this->column ),
		];
	}
}
