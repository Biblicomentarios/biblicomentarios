<?php

namespace ACA\ACF\Setting;

use AC;
use AC\View;
use ACA\ACF\Column;

/**
 * @property Column $column
 */
class RepeaterDisplay extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $repeater_display;

	protected function define_options() {
		return [ 'repeater_display' => 'subfield' ];
	}

	public function get_dependent_settings() {
		$settings = [];

		if ( 'subfield' === $this->get_repeater_display() ) {
			$settings[] = new Subfield( $this->column );
			$settings[] = new AC\Settings\Column\BeforeAfter( $this->column );
			$settings[] = new AC\Settings\Column\Separator( $this->column );
		}

		return $settings;
	}

	public function create_view() {
		$setting = $this->create_element( 'select' );

		$setting
			->set_attribute( 'data-refresh', 'column' )
			->set_options( [
				'subfield' => __( 'Subfield', 'codepress-admin-columns' ),
				'count'    => __( 'Number of Rows', 'codepress-admin-columns' ),
			] );

		$view = new View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $setting,
		] );

		return $view;
	}

	public function get_repeater_display() {
		return $this->repeater_display;
	}

	public function set_repeater_display( $repeater_display ) {
		$this->repeater_display = $repeater_display;

		return $this;
	}

}