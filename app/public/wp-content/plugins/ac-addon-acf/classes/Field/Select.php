<?php

namespace ACA\ACF\Field;

use AC\Settings\Column\NumberOfItems;
use ACA\ACF\Editing;
use ACA\ACF\Export;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACA\ACF\Formattable;
use ACA\ACF\Search;
use ACA\ACF\Sorting;
use ACP;
use ACP\Sorting\Model\MetaFormatFactory;

class Select extends Field
	implements Formattable {

	public function get_value( $id ) {
		$value = parent::get_value( $id );
		$choices = $this->get_choices();

		$options = [];
		foreach ( (array) $value as $value ) {
			if ( isset( $choices[ $value ] ) ) {
				$options[] = $choices[ $value ];
			}
		}

		$setting_limit = $this->column->get_setting( 'number_of_items' );

		return ac_helper()->html->more( $options, $setting_limit ? $setting_limit->get_value() : false );
	}

	public function format( $values ) {
		if ( empty( $values ) ) {
			return false;
		}

		return implode( ', ', (array) $values );
	}

	public function get_dependent_settings() {
		if ( ! $this->column->get_field()->get( 'multiple' ) ) {
			return [];
		}

		return [
			new NumberOfItems( $this->column ),
		];
	}

	public function editing() {
		if ( $this->column->get_field()->get( 'multiple' ) ) {
			return new Editing\MultipleSelect( $this->column );
		}

		return new Editing\Select( $this->column );
	}

	public function filtering() {
		return new Filtering\Options( $this->column );
	}

	public function sorting() {
		if ( $this->column->get_field()->get( 'multiple' ) ) {
			return ( new MetaFormatFactory )->create( $this->column->get_meta_type(), $this->column->get_meta_key(), new Sorting\FormatValue\Select( $this->get_choices() ) );
		}

		$choices = $this->get_choices();
		natcasesort( $choices );
		$sorted_choices = array_keys( $choices );

		return ( new ACP\Sorting\Model\MetaMappingFactory )->create( $this->get_meta_type(), $this->get_meta_key(), $sorted_choices );
	}

	public function search() {
		if ( $this->is_serialized() ) {
			return new Search\MultiSelect( $this->get_meta_key(), $this->get_meta_type(), $this->get_choices() );
		}

		return new Search\Select( $this->get_meta_key(), $this->get_meta_type(), $this->get_choices() );
	}

	public function export() {
		return new Export\Select( $this->column );
	}

	protected function get_choices() {
		return $this->column->get_acf_field_option( 'choices' );
	}

}