<?php

namespace ACA\ACF\Search;

use AC;
use ACP\Helper\Select;
use ACP\Helper\Select\Formatter;
use ACP\Helper\Select\Group;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class User extends Meta
	implements SearchableValues {

	public function __construct( $meta_key, $meta_type ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $meta_type );
	}

	public function get_values( $search, $paged ) {
		$entities = new Select\Entities\User( compact( 'search', 'paged' ) );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Group\UserRole(
				new Formatter\UserName( $entities )
			)
		);
	}

}