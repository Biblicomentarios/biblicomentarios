<?php

namespace CNMD\TMT;

/**
 * Class HTML
 *
 * Handles the creation and display of required HTML. The elements inserted ad re-positioned by javascript.
 *
 * @package CNMD\TMT
 */
class HTML extends Base {


	/**
	 * Create and insert the HTML required by each valid action. They are put into the footer of the page and
	 * moved to the correct spot via JS.
	 */
	public function insert() {
		global $taxonomy;
		foreach ( array_keys( $this->get_actions( $taxonomy ) ) as $key ) {
			if ( ! method_exists( $this, $key ) ) {
				// @codeCoverageIgnoreStart
				continue;
				// @codeCoverageIgnoreEnd
			}
			/*
			 * I realize this is less elegant than using something like
			 * $this->{$key}( $taxonomy )
			 * but it is also a lot clearer, and clearer > clever every time IMHO.
			 */
			echo '<div id="tmt-input-' . esc_attr( $key ) . '" style="display:none">';
			switch ( $key ) {
				case 'merge':
					$success = $this->merge( $taxonomy );
					break;
				case 'set_parent':
					$success = $this->set_parent( $taxonomy );
					break;
				case 'change_tax':
					$success = $this->change_tax( $taxonomy );
					break;
			}
			echo '</div>';
		}
	}


	/**
	 * Create and echo the HTML for the "Merge" required extra info.
	 *
	 * @param string $taxonomy
	 */
	private function merge( string $taxonomy ) {
		esc_html_e( 'into:', 'term-management-tools' );
		?>
		<input name="bulk_to_tag" type="text" size="20" />
		<?php
	}


	/**
	 * Create and echo the HTML for the "Set Parent" required extra info.
	 *
	 * @param string $taxonomy
	 *
	 * @codeCoverageIgnore  This contains only a WP core function, which we don't need to test.
	 */
	private function set_parent( string $taxonomy ) {
		wp_dropdown_categories(
			array(
				'echo'             => true,
				'hide_empty'       => 0,
				'hide_if_empty'    => false,
				'name'             => 'parent',
				'orderby'          => 'name',
				'taxonomy'         => $taxonomy,
				'hierarchical'     => true,
				'show_option_none' => __( 'None', 'term-management-tools' ),
			)
		);
	}


	/**
	 * Create and echo the HTML for the "Change Parent" required extra info. This is a list of valid taxonomies,
	 * excluding the current one.
	 *
	 * @param string $taxonomy
	 */
	private function change_tax( string $taxonomy ) {
		$tax_list = get_taxonomies(
			array(
				'show_ui' => true,
				'public'  => true,
			),
			'objects'
		);
		?>
		<select class="postform" name="new_tax">
			<?php
			foreach ( $tax_list as $new_tax => $tax_obj ) {
				if ( $new_tax === $taxonomy ) {
					continue;
				}
				echo '<option value="' . esc_attr( $new_tax ) . '">' . esc_html( $tax_obj->label ) . '</option>';
			}
			?>
		</select>
		<?php
	}

}

