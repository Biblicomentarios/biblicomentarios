<?php

namespace ACA\ACF\Search;

use ACP\Search\Comparison\Meta;
use ACP\Search\Labels\Date;
use ACP\Search\Operators;
use ACP\Search\Value;

class Datepicker extends Meta {

	public function __construct( $meta_key, $type ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
			Operators::FUTURE,
			Operators::PAST,
			Operators::LT_DAYS_AGO,
			Operators::GT_DAYS_AGO,
			Operators::WITHIN_DAYS,
			Operators::PAST,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $type, Value::DATE, new Date() );
	}

	private function map_value( Value $value, $operator ) {
		switch ( $operator ) {
			case Operators::GT_DAYS_AGO:
			case Operators::LT_DAYS_AGO:
			case Operators::WITHIN_DAYS:
				return $value;
			default:
				return new Value(
					$this->format_date( $value->get_value() ),
					Value::INT
				);
		}
	}

	protected function get_meta_query( $operator, Value $value ) {
		$value = $this->map_value( $value, $operator );

		if ( in_array( $operator, [ Operators::FUTURE, Operators::PAST ] ) ) {
			$compare = Operators::FUTURE === $operator ? '>' : '<';

			return [
				'key'     => $this->get_meta_key(),
				'compare' => $compare,
				'type'    => 'NUMERIC',
				'value'   => date( 'Ymd' ),
			];
		}

		return parent::get_meta_query( $operator, $value );
	}

	/**
	 * @param array|string $value
	 *
	 * @return array|string
	 */
	private function format_date( $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $key => $item ) {
				$value[ $key ] = $this->format_date( $item );
			}
		} else {
			$value = date( 'Ymd', strtotime( $value ) );
		}

		return $value;
	}

}