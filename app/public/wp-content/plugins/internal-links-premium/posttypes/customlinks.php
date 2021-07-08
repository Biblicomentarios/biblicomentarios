<?php
namespace ILJ\Posttypes;

use ILJ\Core\Options;
use ILJ\Database\Postmeta;
use ILJ\Helper\Capabilities;
use ILJ\Helper\Help;
use ILJ\Helper\Options as OptionsHelper;

/**
 * Custom links posttype
 *
 * Handles the custom post type for custom links
 *
 * @package ILJ\Posttypes
 * @since   1.0.1
 */
class CustomLinks
{
    const ILJ_POSTTYPE_CUSTOM_LINKS_SLUG           = 'ilj_customlinks';
    const ILJ_FIELD_CUSTOM_LINKS_URL               = 'ilj_custom_link_url';
    const ILJ_FIELD_CUSTOM_LINKS_OPTION_NOFOLLOW   = 'ilj_custom_link_option_nofollow';
    const ILJ_FIELD_CUSTOM_LINKS_OPTION_NEW_WINDOW = 'ilj_custom_link_option_new_window';

    protected function __construct()
    {
        $this->register();

        add_filter('manage_' . self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG . '_posts_columns', [$this, 'configureColumns']);

        add_filter('parent_file', [$this, 'keepMenu']);

        add_filter(
            'post_row_actions',
            function ($actions, $post) {
                if ($post->post_type != self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG) {
                    return $actions;
                }

                if (isset($actions['inline hide-if-no-js'])) {
                    unset($actions['inline hide-if-no-js']);
                }

                return $actions;
            },
            10,
            2
        );

        add_action('add_meta_boxes', [$this, 'addMetaboxes']);

        add_action('add_meta_boxes', [$this, 'cleanMetaboxes'], 99, 2);

        add_action('dbx_post_sidebar', [$this, 'nonce']);

        add_action(
            'save_post',
            [$this, 'savePost'], 10, 2
        );

        add_action(
            'manage_' . self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG . '_posts_custom_column',
            [$this, 'singleColumn'], 10, 2
        );

        add_filter(
            'post_row_actions',
            function ($actions, $post) {
                if ($post->post_type == self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG) {
                    $url = get_post_meta($post->ID, self::ILJ_FIELD_CUSTOM_LINKS_URL, true);
                    $actions['open'] = sprintf('<a href="%s" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-external"></span> %s</a>', esc_html($url), __('Open URL', 'internal-links'));
                }

                return $actions;
            }, 10, 2
        );

        add_action(
            'load-post-new.php',
            [get_class(), 'disableAutosave']
        );

        add_action(
            'load-post.php',
            [get_class(), 'disableAutosave']
        );

        add_action(
            'load-edit.php',
            [get_class(), 'customizeOverview']
        );

        add_action(
            'admin_enqueue_scripts', function () {
                $current_screen = get_current_screen();

                if ($current_screen->post_type !== self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG) {
                    return;
                }

                if ($current_screen->base == "edit") {
                    echo "
		            <style>
		            	th.column-new_window,
		            	th.column-nofollow {
  							width: 10%;
						}
						.dashicons.red {
							color: rgb(237, 73, 56);
						}
						.dashicons.green {
							color: rgb(173, 198, 7);
						}
					</style>
		        ";
                } elseif ($current_screen->base == "post") {
                    wp_enqueue_script('ilj_select2', ILJ_URL . 'admin/js/select2.js', [], ILJ_VERSION);
                    wp_enqueue_script(self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG, ILJ_URL . 'admin/js/ilj_custom_links.js', [], ILJ_VERSION);
                    wp_enqueue_style('ilj_menu_settings', ILJ_URL . 'admin/css/ilj_menu_settings.css', [], ILJ_VERSION);
                    wp_enqueue_style('ilj_select2', ILJ_URL . 'admin/css/select2.css', [], ILJ_VERSION);
                }
            }
        );

        add_action(
            'admin_footer', function () {
                $current_screen = get_current_screen();

                if (!in_array($current_screen->base, ['edit', 'post']) || $current_screen->post_type != self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG) {
                    return;
                }

                $url_manual = Help::getLinkUrl('custom-links/', null, 'edit', 'custom links');
                $after = "after('<a href=\"" . $url_manual . "\" target=\"_blank\" rel=\"noopener\" class=\"page-title-action\">" . __('Help', 'internal-links') . "</a>');";

                echo '<script type="text/javascript">';
                if ($current_screen->base == 'post' && $current_screen->action == 'add') {
                    echo "jQuery('h1.wp-heading-inline')." . $after;
                    echo '</script>';
                    return;
                }
                echo "jQuery('.page-title-action')." . $after;
                echo '</script>';
            }, 100
        );
    }

    /**
     * Init the post type
     *
     * @since  1.0.1
     * @return CustomLinks
     */
    public static function init()
    {
        new self;
    }

    /**
     * Registers the post type
     *
     * @since  1.0.1
     * @return void
     */
    protected function register()
    {
        $labels = [
            'name'               => __('Custom Links', 'internal-links'),
            'singular_name'      => __('Custom link', 'internal-links'),
            'add_new'            => __('Add link', 'internal-links'),
            'all_items'          => __('All links', 'internal-links'),
            'add_new_item'       => __('Add link', 'internal-links'),
            'edit_item'          => __('Edit link', 'internal-links'),
            'new_item'           => __('Add link', 'internal-links'),
            'search_items'       => __('Search links', 'internal-links'),
            'not_found'          => __('No Links Found', 'internal-links'),
            'not_found_in_trash' => __('No Links Found', 'internal-links'),
            'menu_name'          => __('Custom Links', 'internal-links')
        ];

        $args = [
            'label'               => __('Custom Links', 'internal-links'),
            'labels'              => $labels,
            'description'         => __('Custom links for automatic linking.', 'internal-links'),
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'menu_position'       => 110,
            'supports'            => ['title', 'ilj_editor'],
            'exclude_from_search' => true,
            'has_archive'         => false
        ];

        register_post_type(
            self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG,
            $args
        );
    }

    /**
     * Takes care of keeping navigation activated if editing a custom link
     *
     * @since  1.0.1
     * @param  string $parent_file The parent file
     * @return string
     */
    public function keepMenu($parent_file)
    {
        global $plugin_page, $post_type, $submenu_file;

        if (self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG == $post_type) {
            $plugin_page = $submenu_file = 'edit.php?post_type=' . self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG;
        }

        return $parent_file;
    }

    /**
     * Sets the columns in the custom links overview
     *
     * @since  1.0.1
     * @param  array $columns An array of column name => label
     * @return array
     */
    public function configureColumns($columns)
    {
        unset($columns['author']);

        $columns['url']      = __('Link target', 'internal-links');
        $columns['nofollow'] = __('Nofollow', 'internal-links');
        $columns['new_window'] = __('New window', 'internal-links');

        return $columns;
    }

    /**
     * Renders the single value within an row/column entry within the custom link overview
     *
     * @since  1.0.1
     * @param  string $column  The name of the column to display
     * @param  int    $post_id The ID of the current post
     * @return void
     */
    public static function singleColumn($column, $post_id)
    {
        switch ($column) {
        case 'url':
            $url = get_post_meta($post_id, self::ILJ_FIELD_CUSTOM_LINKS_URL, true);
            if ($url) {
                echo esc_html($url);
            } else {
                echo __('No URL set.', 'internal-links');
            }
            break;
        case 'nofollow':
            $nofollow = get_post_meta($post_id, \ILJ\Posttypes\CustomLinks::ILJ_FIELD_CUSTOM_LINKS_OPTION_NOFOLLOW, true);
            if ($nofollow) {
                echo '<span class="dashicons dashicons-yes-alt green"></span>';
            } else {
                echo '<span class="dashicons dashicons-dismiss red"></span>';
            }
            break;
        case 'new_window':
            $new_window = get_post_meta($post_id, \ILJ\Posttypes\CustomLinks::ILJ_FIELD_CUSTOM_LINKS_OPTION_NEW_WINDOW, true);
            if ($new_window) {
                echo '<span class="dashicons dashicons-yes-alt green"></span>';
            } else {
                echo '<span class="dashicons dashicons-dismiss red"></span>';
            }
            break;
        }
    }

    /**
     * Adds all the meta boxes to the custom links editor interface
     *
     * @since  1.0.1
     * @return void
     */
    public function addMetaboxes()
    {
        add_meta_box(
            'ilj-url',
            __('Target', 'internal-links'),
            [$this, 'renderUrlBox'],
            self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG,
            'normal',
            'high'
        );

        add_meta_box(
            'ilj-link-options',
            __('Link options', 'internal-links'),
            [$this, 'renderLinkOptions'],
            self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG,
            'normal',
            'high'
        );

        add_meta_box(
            'ilj-submitdiv',
            __('Save', 'internal-links'),
            [$this, 'renderSubmitDiv'],
            self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG,
            'side',
            'low'
        );
    }

    /**
     * Derigisters all unused stuff from the custom link interface
     *
     * @since  1.0.1
     * @param  string  $post_type The current post type
     * @param  WP_Post $post      The post object
     * @return void
     */
    public function cleanMetaboxes($post_type, $post)
    {
        $slug = self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG;

        $exceptions = [$slug, Postmeta::ILJ_META_KEY_LINKDEFINITION, 'ilj-url', 'ilj-link-options', 'ilj-submitdiv'];

        if ($post_type == $slug) {
            global $wp_meta_boxes;
            /**
             * Loop through each page key of the '$wp_meta_boxes' global...
             */
            if (!empty($wp_meta_boxes)) : foreach ($wp_meta_boxes as $page => $page_boxes):
                    /**
                     * Loop through each contect...
                     */
                    if (!empty($page_boxes)) : foreach ($page_boxes as $context => $box_context):
                            /**
                             * Loop through each type of meta box...
                             */
                            if (!empty($box_context)) : foreach ($box_context as $box_type):
                                    /**
                                     * Loop through each individual box...
                                     */
                                    if (!empty($box_type)) : foreach ($box_type as $id => $box):
                                            /**
                                             * Check to see if the meta box should be removed...
                                             */
                                            if (!in_array($id, $exceptions)) :

                                                /**
                                                 * Remove the meta box
                                                 */
                                                remove_meta_box($id, $page, $context);
                                            endif;
                                    endforeach;
                                    endif;
                            endforeach;
                            endif;
                    endforeach;
                    endif;
            endforeach;
            endif;
        }
    }

    /**
     * Generates the nonce field for the interface
     *
     * @since  1.0.1
     * @param  WP_Post $post The post object
     * @return void
     */
    public static function nonce($post)
    {
        wp_nonce_field(
            self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG,
            self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG,
            false
        );
    }

    /**
     * Renders the submit box within the interface
     *
     * @since  1.0.1
     * @param  WP_Post $post The post object
     * @return void
     */
    public function renderSubmitDiv($post)
    {
        $editor_capability = Capabilities::mapRoleToCapability(Options::getOption(\ILJ\Core\Options\EditorRole::getKey()));

        if (!current_user_can($editor_capability)) {
            echo '<p>' . __('No permission to edit links.', 'internal-links') . '</p>';
            return;
        }

        echo '<div id="major-publishing-action" style="margin:15px 0 10px 0;">';
        echo '<div id="save-action" style="display:inline-block;margin-right:15px;">';
        echo '<input type="submit" name="save" id="save-post" class="button button-primary" value="' . __('Save', 'internal-links') . '">&nbsp;';
        echo '<div class="spinner"></div>';
        echo '</div>';

        $current_screen = get_current_screen();
        if ($current_screen->action != 'add') {
            echo '<div style="display:inline-block;">';
            echo '<a class="button button-secondary" onclick="return confirm(\'' . __('Are you sure?', 'internal-links') . '\');" href="' . get_delete_post_link($post->ID) . '" >' . __('Delete', 'internal-links') . '</a>';
            echo '</div>';
        }

        echo '</div>';
    }

    /**
     * Renders the url box within the interface
     *
     * @since  1.0.1
     * @return void
     */
    public function renderUrlBox($post)
    {
        $url = get_post_meta(get_the_ID(), self::ILJ_FIELD_CUSTOM_LINKS_URL, true);

        echo '<div class="ilj-menu-settings">';
        echo '<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="draft">';
        echo '<table class="form-table">';
        echo '<tbody>';

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label for="' . self::ILJ_FIELD_CUSTOM_LINKS_URL . '">' . __('Target URL', 'internal-links') . '</label>';
        echo '</th>';
        echo '<td>';
        echo '<input type="text" style="width:70%;" class="widefat" name="' . self::ILJ_FIELD_CUSTOM_LINKS_URL . '" value="' . $url . '" required="required" placeholder="https://..." />';
        echo '<p class="description">' . __('The URL that should get linked.', 'internal-links') . '</p>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label for="post_status">' . __('Link status', 'internal-links') . '</label>';
        echo '</th>';
        echo '<td>';
        echo '<select name="post_status" id="post_status">';
        echo '<option value="publish"' . selected($post->post_status, 'publish') . '>' . __('Active', 'internal-links') . '</option>';
        echo '<option value="draft"' . selected($post->post_status, 'draft') . '>' . __('Inactive', 'internal-links') . '</option>';
        echo '</select>';
        echo '</td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }

    /**
     * Renders the link options box within the interface
     *
     * @since  1.0.1
     * @return void
     */
    public function renderLinkOptions($post)
    {
        $nofollow = get_post_meta($post->ID, self::ILJ_FIELD_CUSTOM_LINKS_OPTION_NOFOLLOW, true);
        $new_window = get_post_meta($post->ID, self::ILJ_FIELD_CUSTOM_LINKS_OPTION_NEW_WINDOW, true);

        echo '<div class="ilj-menu-settings">';
        echo '<table class="form-table">';
        echo '<tbody>';

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label for="' . self::ILJ_FIELD_CUSTOM_LINKS_OPTION_NOFOLLOW . '">' . __('Add nofollow attribute', 'internal-links') . '</label>';
        echo '</th>';
        echo '<td>';
        echo OptionsHelper::getToggleField(self::ILJ_FIELD_CUSTOM_LINKS_OPTION_NOFOLLOW, checked(1, $nofollow, false));
        echo '<p class="description">' . __('The custom URL gets linked with <code>rel="nofollow"</code> attribute.', 'internal-links') . '</p>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label for="' . self::ILJ_FIELD_CUSTOM_LINKS_OPTION_NEW_WINDOW . '">' . __('Open in new window', 'internal-links') . '</label>';
        echo '</th>';
        echo '<td>';
        echo OptionsHelper::getToggleField(self::ILJ_FIELD_CUSTOM_LINKS_OPTION_NEW_WINDOW, checked(1, $new_window, false));
        echo '<p class="description">' . __('The link to the custom URL gets opened in a new tab.', 'internal-links') . '</p>';
        echo '</td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }

    /**
     * Handles the saving process of a custom link
     *
     * @since  1.0.1
     * @param  int     $post_id The post id of the custom link
     * @param  WP_Post $post    The post object of the custom link
     * @return void
     */
    public function savePost($post_id, $post)
    {
        $editor_capability = Capabilities::mapRoleToCapability(Options::getOption(\ILJ\Core\Options\EditorRole::getKey()));

        if (($post->post_type != self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG) 
            || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
            || (!current_user_can($editor_capability)) 
            || (!isset($_POST[self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG]) || !wp_verify_nonce($_POST[self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG], self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG))
        ) {
            return;
        }

        //saving the url
        $url = (isset($_POST[self::ILJ_FIELD_CUSTOM_LINKS_URL])) ? esc_url($_POST[self::ILJ_FIELD_CUSTOM_LINKS_URL]) : '';
        $url = filter_var($url, FILTER_SANITIZE_URL);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return;
        }

        update_post_meta($post_id, self::ILJ_FIELD_CUSTOM_LINKS_URL, $url);

        //saving options
        $nofollow = (isset($_POST[self::ILJ_FIELD_CUSTOM_LINKS_OPTION_NOFOLLOW])) ? 1 : 0;
        update_post_meta($post_id, self::ILJ_FIELD_CUSTOM_LINKS_OPTION_NOFOLLOW, $nofollow);

        $new_window = (isset($_POST[self::ILJ_FIELD_CUSTOM_LINKS_OPTION_NEW_WINDOW])) ? 1 : 0;
        update_post_meta($post_id, self::ILJ_FIELD_CUSTOM_LINKS_OPTION_NEW_WINDOW, $new_window);
    }

    /**
     * Disables the autosave functionality for this post type
     *
     * @since  1.0.1
     * @return void
     */
    public static function disableAutosave()
    {
        global $typenow;

        if (self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG == $typenow) {
            wp_deregister_script('autosave');
        }
    }

    /**
     * Customizes the overview of all custom link posts
     *
     * @since  1.0.1
     * @return void
     */
    public static function customizeOverview()
    {
        $posttype = isset($_GET['post_type']) ? $_GET['post_type'] : false;

        if ($posttype && $posttype == self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG) {

            $current_screen = get_current_screen();

            add_filter(
                "views_{$current_screen->id}",
                function ($views) {
                    if (isset($views['draft'])) {
                        unset($views['draft']);
                    }

                    if (isset($views['publish'])) {
                        unset($views['publish']);
                    }

                    if (count($views) == 1) {
                        return [];
                    }

                    return $views;
                }
            );

            add_filter(
                "bulk_actions-{$current_screen->id}",
                function ($actions) {

                    if (isset($actions['edit'])) {
                        unset($actions['edit']);
                    }

                    return $actions;
                }
            );

            add_filter(
                'display_post_states',
                function ($states) {

                    if (isset($states['draft'])) {
                        $states['draft'] = __('Inactive', 'internal-links');
                    }

                    return $states;
                }
            );
        }
    }

	/**
	 * Removes all data related to the registered posttype (post, postmeta)
	 *
	 * @since 1.2.8
	 * @return void
	 */
    public static function removePostData()
    {
	    $posts = get_posts([
	    	'post_type' => self::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG,
		    'numberposts' => -1
	    ]);

	    foreach ($posts as $post) {
		    wp_delete_post($post->ID, true);
	    }
    }
}
