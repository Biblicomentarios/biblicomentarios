<?php

namespace ACA\ACF\Free\Setting\Field;

use ACA\ACF\Free\Setting\Field;

class User extends Field {

	public function get_grouped_field_options() {

		add_filter( 'acf/location/rule_match/user_type', '__return_true', 16 );
		add_filter( 'acf/location/rule_match/page_type', '__return_true', 16 );

		add_filter( 'acf/location/rule_match/ef_user', '__return_true', 16 );

		$group_ids = apply_filters( 'acf/location/match_field_groups', [], [] );

		remove_filter( 'acf/location/rule_match/user_type', '__return_true', 16 );
		remove_filter( 'acf/location/rule_match/page_type', '__return_true', 16 );

		remove_filter( 'acf/location/rule_match/ef_user', '__return_true', 16 );

		return $this->get_option_groups( $group_ids );
	}

}