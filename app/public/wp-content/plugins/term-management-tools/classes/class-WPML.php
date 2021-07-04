<?php

namespace CNMD\TMT;

/**
 * Class WPML
 *
 * Adds WPML support.
 *
 * Currently supports "Change Taxonomy" only.
 *
 * @package CNMD\TMT
 */
class WPML extends Base {

	private $site_languages = array();
	private $translation_records;


	/**
	 * WPML constructor.
	 */
	public function __construct() { }


	/**
	 * If WPML is active, then set it up do the right thing..
	 *
	 * @codeCoverageIgnore
	 */
	public function init() {
		$this->set_hooks();
	}


	/**
	 * Adds the required hooks.
	 *
	 * @codeCoverageIgnore
	 */
	private function set_hooks() {
		/*
		 * Add the term IDs for the translations for the user-selected terms.
		 */
		add_filter(
			'term_management_tools_changed_taxonomy__terms_and_child_terms',
			array(
				$this,
				'add_translated_terms_to_supplied_list',
			),
			10,
			3
		);

		/*
		 * Adjust the parentage resetsRelink the translations
		 */
		add_action(
			'term_management_tools_changed_taxonomy__reset_parent_for',
			array( $this, 'adjust_parentage' ),
			10,
			3
		);

		/*
		 * Relink the translations
		 */
		add_action(
			'term_management_tools_term_changed_taxonomy',
			array( $this, 'relink_translations_after_move' ),
			10,
			3
		);
	}


	/**
	 * Adds the translations' term IDs to the list of terms to move, and creates the translation linkages record.
	 *
	 * When the list of terms is supplied to this method, we know that this list of terms contains two things.
	 * 1. The list of terms manually selected by the user
	 * 2. Any child terms of the manually selected ones
	 *
	 * What it doesn't have is any of the translations' term IDs, or their children. We'll do that here.
	 *
	 * @param        $term_ids
	 * @param string $new_taxonomy   The target taxonomy.
	 * @param string $old_taxonomy   The source taxonomy.
	 *
	 * @return array
	 */
	public function add_translated_terms_to_supplied_list( $term_ids, string $new_taxonomy, string $old_taxonomy ) : array {

		// If this is not a translatable taxonomy, we don't need to do any of this.
		if ( ! $this->is_taxonomy_translatable( $old_taxonomy ) ) {
			return $term_ids;
		}

		$this->store_translation_records( $old_taxonomy );

		global $sitepress;
		// The language active in the admin when the terms were manually selected
		$active_lang = $sitepress->get_current_language();

		$all_terms_including_translations = array();
		foreach ( $this->get_site_languages() as $current_lang ) {
			// This is the key to how i works. This switches the site into each of the registered languages, which
			// makes all calls relative to this lang only, which is what we want.
			$sitepress->switch_lang( $current_lang );

			// This is retrieved for the current lang after the switch.
			$term_hierarchy                                   = $this->get_term_hierarchy( $old_taxonomy );
			$current_term_and_all_child_terms_in_current_lang = array();

			foreach ( $term_ids as $current_term ) {
				// Since we are going through each language, and since $current_term is the term ID in a single language
				// that may or may not be the site default language, we first need to get this term in the current lang.
				$current_term_id_in_current_lang = (int) apply_filters( 'wpml_object_id', $current_term, $old_taxonomy, false, $current_lang );
				if ( 0 === $current_term_id_in_current_lang ) {
					// This happens when a $current_lang translation does not exist for $current_term.
					continue;
				}
				// Get all of the children for $current_term_id_in_current_lang. You might think that $term_ids would
				// contain all the children already and so we only need to store each $current_term_and_all_child_terms_in_current_lang,
				// but there are cases where a term exists as an untranslated child in a language other than the one
				// active in the admin at the time they were manually selected. So we have to get all children manually
				// for all terms in all languages,
				$current_term_and_all_child_terms_in_current_lang   = array_merge( $current_term_and_all_child_terms_in_current_lang, $this->get_all_child_terms_for( $current_term_id_in_current_lang, $old_taxonomy, $term_hierarchy ) );
				$current_term_and_all_child_terms_in_current_lang[] = $current_term_id_in_current_lang;
			}
			// Clean out any dupes, because there almost always is.
			$current_term_and_all_child_terms_in_current_lang = array_unique( $current_term_and_all_child_terms_in_current_lang );

			// With the translation linkages done, we build up the array of simple term IDs.
			$all_terms_including_translations = array_merge( $all_terms_including_translations, $current_term_and_all_child_terms_in_current_lang );
		}
		// With all that done, switch back to whatever language was active in the admin.
		$sitepress->switch_lang( $active_lang );
		// return a unique array of ints, which is the full list of all terms in all languages to be moved.
		return array_unique( array_map( 'absint', $all_terms_including_translations ) );
	}


	/**
	 * If the from and to tax are both hierarchical, then we want to reset the parentage for terms, but only the
	 * top-most terms in our list. So if any term has a parent, and that parent is not also being moved, it should
	 * be reset to 0 (none).
	 *
	 * This is called via term_management_tools_changed_taxonomy__reset_parent_for filter
	 *
	 * @param array  $all_term_ids
	 * @param string $hierarchy
	 * @param array  $terms_to_reset_parentage_for
	 *
	 * @return array
	 */
	public function adjust_parentage( array $terms_to_reset_parentage_for, array $all_term_ids, string $hierarchy ) : array {
		global $sitepress;
		// The language active in the admin when the terms were manually selected
		$active_lang = $sitepress->get_current_language();

		// We build these up from scratch, so no need to keep what was sent
		$terms_to_reset_parentage_for = array();
		foreach ( $this->get_site_languages() as $current_lang ) {
			// This is the key to how i works. This switches the site into each of the registered languages, which
			// makes all calls relative to this lang only, which is what we want.
			$sitepress->switch_lang( $current_lang );

			// This is retrieved for the current lang after the switch.
			$term_hierarchy                                   = $this->get_term_hierarchy( $hierarchy );
			$current_term_and_all_child_terms_in_current_lang = array();

			foreach ( $all_term_ids as $term_id ) {
				$parent_term_id = (int) $this->get_parent_term_id( $term_id, $term_hierarchy );
				if ( $parent_term_id && ! in_array( $parent_term_id, $all_term_ids, true ) ) {
					$terms_to_reset_parentage_for[] = $term_id;

				}
			}
		}
		$sitepress->switch_lang( $active_lang );
		return $terms_to_reset_parentage_for;
	}


	/**
	 * Relink the moved terms to their translations.
	 *
	 * @param string $term_ids       CSV list of term IDs to move.
	 * @param string $new_taxonomy   The target taxonomy.
	 * @param string $old_taxonomy   The source taxonomy.
	 */
	public function relink_translations_after_move( $term_ids, string $new_taxonomy, string $old_taxonomy ) {
		$wpml_element_type     = apply_filters( 'wpml_element_type', $new_taxonomy );
		$wpml_parent_term_info = array();
		$terms_to_relink       = explode( ',', $term_ids );
		foreach ( $terms_to_relink as $term_to_relink ) {
			$translation_record = $this->get_translation_record( (int) $term_to_relink );
			if ( isset( $translation_record ) ) {
				// We have a translation
				$set_translation_args = array(
					'element_id'           => $translation_record->element_id,
					'element_type'         => $wpml_element_type,
					'trid'                 => $translation_record->trid,
					'language_code'        => $translation_record->language_code,
					'source_language_code' => $translation_record->source_language_code,
				);
				do_action( 'wpml_set_element_language_details', $set_translation_args );
			}
		}
	}


	/**
	 * Get an array of all active site languages.
	 *
	 * @return array
	 */
	private function get_site_languages() : array {
		if ( empty( $this->site_languages ) ) {
			$langs = apply_filters( 'wpml_active_languages', null, 'orderby=id&order=desc' );
			if ( isset( $langs ) ) {
				$this->site_languages = $langs;
			}
		}
		return wp_list_pluck( $this->site_languages, 'code' );
	}


	/**
	 * Build the stored translation records by pulling them directly from the database.
	 *
	 * @param string $taxonomy
	 *
	 * @return bool
	 */
	private function store_translation_records( string $taxonomy ) : bool {
		global $wpdb;
		$wpml_taxonomy             = apply_filters( 'wpml_element_type', $taxonomy );
		$this->translation_records = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}icl_translations WHERE element_type = %s",
				$wpml_taxonomy
			)
		);
		if ( $this->translation_records ) {
			return true;
		}
		return false;
	}

	/**
	 * Get the translation record for the supplied term.
	 *
	 * @param int $term_id
	 *
	 * @return object|null
	 */
	private function get_translation_record( int $term_id ) :? object {
		foreach ( $this->translation_records as $link_record ) {
			if ( $term_id === (int) $link_record->element_id ) {
				return $link_record;
			}
		}
		return null;
	}


	/**
	 * Determine if the supplied taxonomy has been set to be translatable.
	 *
	 * @src https://wpml.org/wpml-hook/wpml_sub_setting/
	 *
	 * @param string $taxonomy  Taxonomy id.
	 *
	 * @return bool             true if it is translatable, false otherwise.
	 */
	private function is_taxonomy_translatable( string $taxonomy ) : bool {
		return (bool) apply_filters( 'wpml_sub_setting', false, 'taxonomies_sync_option', $taxonomy );
	}


}
