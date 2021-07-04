<?php
/**
 * trim and remove empty element
 *
 * @param string $element
 *
 * @return string
 */
function _delete_empty_element( &$element ) {
	$element = stripslashes( $element );
	$element = trim( $element );
	if ( ! empty( $element ) ) {
		return $element;
	}

	return false;
}

/**
 * Test if page have tags or not...
 *
 * @return boolean
 * @author WebFactory Ltd
 */
function is_page_have_tags() {
	$taxonomies = get_object_taxonomies( 'page' );

	return in_array( 'post_tag', $taxonomies, true );
}

/**
 * Register widget on WP
 */
function st_register_widget() {
	register_widget( 'SimpleTags_Widget' );
	register_widget( 'SimpleTags_Shortcode_Widget' );
	register_widget( 'SimpleTags_PostTags_Widget' );
}

/**
 * Change menu item order
 */
add_action('custom_menu_order', 'taxopress_re_order_menu');	
function taxopress_re_order_menu()	{	    
    global $submenu;	    
    $newSubmenu = [];	    
    foreach ($submenu as $menuName => $menuItems) {	        
        if ('st_options' === $menuName) {
            $taxopress_settings = $taxopress_taxonomies = false;

            $taxopress_submenus = $submenu['st_options'];
            foreach($taxopress_submenus  as $key => $taxopress_submenu){
                if($taxopress_submenu[2] === 'st_options'){//settings
                    $taxopress_settings = $taxopress_submenu;
                    $taxopress_settings_key= $key;
                    unset($taxopress_submenus[$key]);
                }
                if($taxopress_submenu[2] === 'st_taxonomies'){//taxonomies
                    $taxopress_taxonomies = $taxopress_submenu;
                    $taxopress_taxonomies_key= $key;
                    unset($taxopress_submenus[$key]);
                }
            }
            if($taxopress_settings && $taxopress_taxonomies ){
            //swicth position
            $taxopress_submenus[$taxopress_settings_key] = $taxopress_taxonomies;
            $taxopress_submenus[$taxopress_taxonomies_key] = $taxopress_settings;
            }

            //resort array
            ksort($taxopress_submenus);

            $submenu['st_options'] = $taxopress_submenus;	            
           break;	        
        }	    
    }	
}

// Init TaxoPress
function init_simple_tags()
{
    new SimpleTags_Client();
    new SimpleTags_Client_TagCloud();

    // Admin and XML-RPC
    if (is_admin()) {
        require STAGS_DIR . '/inc/class.admin.php';
        new SimpleTags_Admin();
    }
    
	if (is_admin() && !defined('TAXOPRESS_PRO_VERSION')) {
		require_once(TAXOPRESS_ABSPATH . '/includes-core/TaxopressCoreAdmin.php');
		new \PublishPress\Taxopress\TaxopressCoreAdmin();
	}

    add_action('widgets_init', 'st_register_widget');
}