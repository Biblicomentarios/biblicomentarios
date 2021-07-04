<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Editing;
use ACP;

class Wysiwyg extends Editing {

	public function get_view_settings() {
		$data = parent::get_view_settings();

		/* @var ACP\Editing\Settings\Content $setting */
		$setting = $this->column->get_setting( 'edit' );

		$data['type'] = $setting->get_editable_type();

		return $data;
	}


	public function register_settings() {
		$this->column->add_setting( new ACP\Editing\Settings\Content( $this->column ) );
	}

}