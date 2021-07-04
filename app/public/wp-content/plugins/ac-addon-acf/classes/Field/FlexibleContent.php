<?php

namespace ACA\ACF\Field;

use ACA\ACF\Field;
use ACA\ACF\Setting;
use ACP;

class FlexibleContent extends Field {

	public function get_ajax_value( $id ) {
		$results = [];
		$field = $this->column->get_acf_field();
		$labels = $this->get_layout_labels();

		$i = 0;
		while ( have_rows( $field['name'], $id ) ) {
			the_row();
			$title = $labels[ get_row_layout() ];
			$acf_layout = $this->get_layout_by_name( get_row_layout() );

			$title = apply_filters( 'acf/fields/flexible_content/layout_title', $title, $field, $acf_layout, $i );
			$title = apply_filters( "acf/fields/flexible_content/layout_title/key={$field['key']}", $title, $field, $acf_layout, $i );
			$title = apply_filters( "acf/fields/flexible_content/layout_title/name={$field['name']}", $title, $field, $acf_layout, $i );

			$results[] = '[ ' . $title . ' ]';
			$i++;
		}

		return implode( '<br>', $results );
	}

	public function get_value( $id ) {
		$setting = $this->column->get_setting( 'flex_display' );

		if ( 'structure' === $setting->get_value() ) {
			return $this->get_structure_value( $id );
		}

		return $this->get_count_value( $id );
	}

	public function get_structure_value( $id ) {
		$raw_value = $this->get_raw_value( $id );

		if ( ! $raw_value ) {
			return false;
		}

		return ac_helper()->html->get_ajax_toggle_box_link( $id, count( $raw_value ), $this->column->get_name() );
	}

	public function get_count_value( $id ) {
		$raw_value = $this->get_raw_value( $id );

		if ( ! $raw_value ) {
			return false;
		}

		$field = $this->column->get_acf_field();

		if ( empty( $field['layouts'] ) ) {
			return false;
		}

		$labels = $this->get_layout_labels();

		$layouts = [];
		foreach ( $raw_value as $values ) {
			$layouts[ $values['acf_fc_layout'] ] = [
				'count' => empty( $layouts[ $values['acf_fc_layout'] ] ) ? 1 : ++$layouts[ $values['acf_fc_layout'] ]['count'],
				'label' => $labels[ $values['acf_fc_layout'] ],
			];
		}

		$output = [];
		foreach ( $layouts as $layout ) {
			$label = $layout['label'];

			if ( $layout['count'] > 1 ) {
				$label .= '<span class="ac-rounded">' . $layout['count'] . '</span>';
			}

			$output[] = $label;
		}

		return implode( '<br/>', $output );
	}

	public function get_dependent_settings() {
		return [
			new Setting\FlexibleContent( $this->column ),
		];
	}

	public function export() {
		return new ACP\Export\Model\Disabled( $this->column );
	}

	/**
	 * @param $name
	 *
	 * @return string|false
	 */
	private function get_layout_by_name( $name ) {
		$field = $this->column->get_acf_field();

		foreach ( $field['layouts'] as $layout ) {
			if ( $name === $layout['name'] ) {
				return $layout;
			}
		}

		return false;
	}

	/**
	 * @return array
	 */
	private function get_layout_labels() {
		$field = $this->column->get_acf_field();
		$labels = [];

		foreach ( $field['layouts'] as $layout ) {
			$labels[ $layout['name'] ] = $layout['label'];
		}

		return $labels;
	}
}