<?php

namespace CNMD\TMT;

abstract class Base {

	/**
	 * Tracks the presence of WPML.
	 *
	 * @var bool
	 */
	protected $wpml_is_active = false;


	private $term_hierarchy = null;

	/**
	 * Get the actions available for term manipulation. Accounts for hierarchical taxonomies.
	 *
	 * @todo: rename to get_selectbox_actions
	 *
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	protected function get_actions( string $taxonomy ) : array {
		$actions = array(
			'merge'      => __( 'Merge', 'term-management-tools' ),
			'change_tax' => __( 'Change taxonomy', 'term-management-tools' ),
		);

		if ( is_taxonomy_hierarchical( $taxonomy ) ) {
			$actions = array_merge(
				array(
					'set_parent' => __( 'Set parent', 'term-management-tools' ),
				),
				$actions
			);
		}

		return $actions;
	}


	/**
	 * Returns the list of all child terms for a supplied term ID. The returns list is itself not hierarchichal; it
	 * is a simple array.
	 *
	 * @param int    $term_id
	 * @param string $taxonomy_from
	 * @param array  $term_hierarchy
	 *
	 * @return array
	 */
	protected function get_all_child_terms_for( int $term_id, string $taxonomy_from, array $term_hierarchy ) : array {
		$terms_found = array();
		if ( empty( $term_hierarchy ) || ! isset( $term_hierarchy[ $term_id ] ) ) {
			return array();
		} else {
			foreach ( $term_hierarchy[ $term_id ] as $child_term_id ) {
				$terms_found = array_merge( $terms_found, $this->get_all_child_terms_for__recursive( (int) $child_term_id, $taxonomy_from, $term_hierarchy ) );
			}
		}
		return $terms_found;
	}

	/**
	 * Recursive part of the get_all_child_terms_for() method.
	 *
	 * @param int    $term_id
	 * @param string $taxonomy_from
	 * @param array  $term_hierarchy
	 *
	 * @return int[]
	 */
	protected function get_all_child_terms_for__recursive( int $term_id, string $taxonomy_from, array $term_hierarchy ) : array {
		$terms_found = array();
		if ( empty( $term_hierarchy ) || ! isset( $term_hierarchy[ $term_id ] ) ) {
			// end of the line
			return array( (int) $term_id );
		} else {
			$terms_found = array_merge( $terms_found, $term_hierarchy[ $term_id ] );
			foreach ( $term_hierarchy[ $term_id ] as $child_term_id ) {
				$this->get_all_child_terms_for__recursive( (int) $child_term_id, $taxonomy_from, $term_hierarchy );
			}
			$terms_found[] = $term_id;
		}
		return $terms_found;
	}

	/**
	 * Return the results of _get_term_hierarchy() for the current language.
	 *
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	protected function get_term_hierarchy( string $taxonomy ) {
		return _get_term_hierarchy( $taxonomy );
	}


	/**
	 * Return a supplied term's parent term ID, or null if not found.
	 *
	 * @param $term_id
	 * @param $term_hierarchy
	 *
	 * @return int|null
	 */
	protected function get_parent_term_id( $term_id, $term_hierarchy ): ?int {
		foreach ( $term_hierarchy as $haystack_term => $haystack ) {
			if ( in_array( $term_id, $haystack ) ) {
				return $haystack_term;
			}
		}
		return null;
	}


	/**
	 * Build the SQL placeholder for use in teh $wpdb commands.
	 *
	 * @param array $term_ids
	 *
	 * @return string|null
	 */
	protected function build_sql_placeholder( array $term_ids ) : ?string {
		if ( empty( $term_ids ) ) {
			return null;
		}
		$placeholder = '';
		$limit       = count( $term_ids );
		for ( $i = 0; $i < $limit; $i++ ) {
			if ( $placeholder ) {
				$placeholder .= ',';
			}
			$placeholder .= '%d';
		}
		return $placeholder;
	}

}
