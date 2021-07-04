<?php

namespace ACA\ACF\Field;

use AC;
use AC\Collection;
use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACA\ACF\Search;
use ACP;
use ACP\Sorting\FormatValue\SerializedSettingFormatter;
use ACP\Sorting\FormatValue\SettingFormatter;
use ACP\Sorting\Model\MetaFormatFactory;
use ACP\Sorting\Model\MetaRelatedUserFactory;

class User extends Field {

	public function get_value( $id ) {
		$value = $this->column->get_formatted_value( new Collection( $this->get_raw_value( $id ) ) );
		$setting_limit = $this->column->get_setting( 'number_of_items' );

		return ac_helper()->html->more( $value->all(), $setting_limit ? $setting_limit->get_value() : false );
	}

	public function get_raw_value( $id ) {
		return array_filter( (array) parent::get_raw_value( $id ) );
	}

	public function get_dependent_settings() {
		$settings = [
			new AC\Settings\Column\User( $this->column ),
		];

		if ( $this->is_serialized() ) {
			$settings[] = new AC\Settings\Column\NumberOfItems( $this->column );
		}

		return $settings;
	}

	public function editing() {
		if ( $this->is_serialized() ) {
			return new Editing\Users( $this->column );
		}

		return new Editing\User( $this->column );
	}

	public function filtering() {
		return new Filtering\User( $this->column );
	}

	public function sorting() {
		$setting = $this->column->get_setting( AC\Settings\Column\User::NAME );

		if ( ! $this->is_serialized() ) {
			$model = ( new MetaRelatedUserFactory() )->create( $this->get_meta_type(), $setting->get_value(), $this->get_meta_key() );

			if ( $model ) {
				return $model;
			}
		}

		$formatter = $this->is_serialized()
			? new SerializedSettingFormatter( new SettingFormatter( $setting ) )
			: new SettingFormatter( $setting );

		return ( new MetaFormatFactory() )->create( $this->get_meta_type(), $this->get_meta_key(), $formatter );
	}

	public function search() {
		if ( $this->is_serialized() ) {
			return new Search\Users( $this->get_meta_key(), $this->get_meta_type() );
		}

		return new Search\User( $this->get_meta_key(), $this->get_meta_type() );
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this->column );
	}

}