<?php

namespace ACA\ACF\Field;

use AC;
use AC\Collection;
use AC\Settings\Column\Post;
use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\Filtering;
use ACP;
use ACP\Sorting\FormatValue\SerializedSettingFormatter;
use ACP\Sorting\FormatValue\SettingFormatter;
use ACP\Sorting\Model\MetaFormatFactory;
use ACP\Sorting\Model\MetaRelatedPostFactory;

class PostObject extends Field {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->column->set_serialized( $this->column->get_acf_field_option( 'multiple' ) );
	}

	public function get_value( $id ) {
		$value = $this->column->get_formatted_value( new Collection( $this->get_raw_value( $id ) ) );
		$setting_limit = $this->column->get_setting( 'number_of_items' );

		return ac_helper()->html->more( $value->all(), $setting_limit ? $setting_limit->get_value() : false );
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public function get_raw_value( $id ) {
		return array_filter( (array) parent::get_raw_value( $id ) );
	}

	public function editing() {
		if ( $this->is_serialized() ) {
			return new Editing\PostObjects( $this->column );
		}

		return new Editing\PostObject( $this->column );
	}

	public function sorting() {
		$setting = $this->column->get_setting( Post::NAME );

		if ( ! $this->is_serialized() ) {
			$model = ( new MetaRelatedPostFactory() )->create( $this->get_meta_type(), $setting->get_value(), $this->get_meta_key() );

			if ( $model ) {
				return $model;
			}
		}

		$formatter = $this->is_serialized()
			? new SerializedSettingFormatter( new SettingFormatter( $setting ) )
			: new SettingFormatter( $setting );

		return ( new MetaFormatFactory() )->create( $this->get_meta_type(), $this->get_meta_key(), $formatter );
	}

	/**
	 * @return array|string
	 */
	private function get_post_type() {
		$post_type = $this->column->get_acf_field_option( 'post_type' );

		if ( is_array( $post_type ) && ( in_array( 'all', $post_type ) || in_array( 'any', $post_type ) ) ) {
			$post_type = 'any';
		}

		return $post_type;
	}

	private function get_terms() {
		$taxonomy = $this->column->get_acf_field_option( 'taxonomy' );

		$array_terms = acf_decode_taxonomy_terms( $taxonomy );

		if ( ! $array_terms ) {
			return [];
		}

		$terms = [];
		foreach ( $array_terms as $taxonomy => $term_slugs ) {
			foreach ( $term_slugs as $term_slug ) {
				$terms[] = get_term_by( 'slug', $term_slug, $taxonomy );
			}
		}

		return array_filter( $terms );
	}

	public function search() {
		if ( $this->is_serialized() ) {
			return new ACP\Search\Comparison\Meta\Posts( $this->get_meta_key(), $this->get_meta_type(), $this->get_post_type(), $this->get_terms() );
		}

		return new ACP\Search\Comparison\Meta\Post( $this->get_meta_key(), $this->get_meta_type(), $this->get_post_type(), $this->get_terms() );
	}

	public function filtering() {
		return new Filtering\PostObject( $this->column );
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this->column );
	}

	public function get_dependent_settings() {
		$settings = [
			new AC\Settings\Column\Post( $this->column ),
		];

		if ( $this->is_serialized() ) {
			$settings[] = new AC\Settings\Column\NumberOfItems( $this->column );
		}

		return $settings;
	}

}