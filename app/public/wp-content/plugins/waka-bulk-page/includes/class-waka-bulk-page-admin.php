<?php
/**
 * The admin area specific functionality of the plugin.
 *
 * @since      1.0.0
 */
class Waka_Bulk_Page_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;


    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the CSS for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/css/waka-bulk-page.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/js/waka-bulk-page.js', array('jquery'), $this->version, false);
    }


    /**
     * Loads language files for i18n
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain('waka-bulk-page', false, $this->plugin_name . '/languages/');
    }


    /**
     * Adds page in Pages admin menu
     *
     * @since    1.0.0
     */
    public function menu()
    {
        add_pages_page('Waka Bulk Page', __('Add multiple pages', 'waka-bulk-page'), 'manage_options', $this->plugin_name, array($this, 'menu_page'));
    }

    /**
     * Show form and handle form submit
     *
     * @since    1.0.0
     */
    public function menu_page()
    {
        if (!current_user_can('manage_options'))
            exit('Restricted area');

        $message_ok = '';
        $message_ko = '';

        if (isset($_POST["update_settings"])) {

            $verifiy_nonce = wp_verify_nonce($_REQUEST['_wpnonce'], 'wbp_submit');

            if (false === $verifiy_nonce) {

                $message_ko = __('Error while processing the request. Please try again.', 'waka-bulk-page');

            } else {

                $menu = false;
                // Create or replace a menu if the option is selected
                if (!empty($_POST['create_menu'])) {

                    $menus = get_registered_nav_menus();

                    $theme_location = $_POST['create_menu'];

                    $menuName = !empty($_POST['menu_name']) ? $_POST['menu_name'] : $menus[$theme_location]; // we get the name of the menu

                    $menu = wp_create_nav_menu($menuName);

                    if (is_wp_error($menu)) {
                        $menu = wp_create_nav_menu($menuName . ' ' . uniqid()); // Can be a conflict with the menu's name, so we generate a random new menu name
                    }

                    // We get the current nav menu locations so we don't overwrite the old associations menus <=> locations
                    $new_nav_menu_locations = array_merge((array)get_nav_menu_locations(), array($theme_location => $menu));

                    // ... and finally assign the newly created menu to the right location.
                    set_theme_mod('nav_menu_locations', $new_nav_menu_locations);
                }


                if(!empty($_POST['wbp-status'])){
                    update_option('wbp_preferred_status', $_POST['wbp-status']);
                }
                if (!empty($_POST['wbp-page'])) {

                    $arrInserted = array();
                    $arrMenuInserted = array();
                    $menu_order = 0;

                    foreach ($_POST['wbp-page'] as $id => $page) {

                        $menu_order += 10;

                        $parent = '';

                        $params = array(
                            'post_type' => 'page',
                            'post_parent' => $parent,
                            'post_title' => trim($page['name']),
                            'post_content' => '',
                            'menu_order' => $menu_order,
                            'post_status' => $page['status'],
                        );


                        // Template
                        if (!empty($page['template']))
                            $params['page_template'] = $page['template'];

                        // Parent
                        if (!empty($page['parent']) && !empty($arrInserted[$page['parent']])) // Parent is a new page just created
                            $params['post_parent'] = $arrInserted[$page['parent']];
                        elseif (!empty($page['parent']) && empty($arrInserted[$page['parent']])) { // Parent is an existing page
                            $parent = get_post($page['parent']);
                            if (!empty($parent))
                                $params['post_parent'] = $parent->ID;
                        }

                        $arrInserted[$id] = wp_insert_post($params);

                        if (0 == $arrInserted[$id]) {

                            $message_ko .= __('Error while inserting this page ' . $page['name'], 'waka-bulk-page') . '<br/>';

                        } else {

                            $menuItemParentIid = !empty($page['parent']) && !empty($arrMenuInserted[$page['parent']]) ? $arrMenuInserted[$page['parent']] : null;

                            if (!empty($menuItemParentIid)) {
                                $arrMenuInserted[$id] = !empty($menu) ? wp_update_nav_menu_item($menu, 0, array('menu-item-object' => 'page', 'menu-item-object-id' => $arrInserted[$id], 'menu-item-status' => 'publish', 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $menuItemParentIid)) : null;
                            } else {
                                $arrMenuInserted[$id] = !empty($menu) ? wp_update_nav_menu_item($menu, 0, array('menu-item-object' => 'page', 'menu-item-object-id' => $arrInserted[$id], 'menu-item-status' => 'publish', 'menu-item-type' => 'post_type')) : null;
                            }
                        }
                    }
                }
                $message_ok = '<br/>' . __('Updated successfully!', 'waka-bulk-page') . '<br/><br/>';
            }
        }
        ?>


        <div class="wrap" id="wbp">

            <form method="POST" action="">

                <h2><?php _e('Waka Bulk Page', 'waka-bulk-page') ?></h2>
                <?php if (!empty($message_ok)) : ?>
                    <div class="notice notice-success is-dismissible"><?php echo $message_ok; ?></div>
                <?php endif; ?>

                <?php if (!empty($message_ko)) : ?>
                    <div class="notice notice-error is-dismissible"><?php echo $message_ko; ?></div>
                <?php endif; ?>

                <hr/>

                <h3><?php _e('Existing pages', 'waka-bulk-page') ?></h3>

                <div class="wbp-row">

                    <div id="wbp-pages" class="wbp-col w50">
                        <ul>
                            <?php // Show existing pages with the plugin Page Walker class ?>
                            <?php echo strip_tags(wp_list_pages(array('title_li' => '', 'echo' => 0, 'post_status' => 'draft,pending,publish,private', 'walker' => new Waka_Bulk_Page_Walker())), '<ul><li><span>'); ?>
                        </ul>
                        <p><input type="submit" value="<?php esc_attr_e(__('Save')) ?>" class="button-primary wbp-btn-submit" disabled/></p>
                    </div>

                    <hr/>

                    <div id="wbp-form-wrapper" class="wbp-col w50">

                        <h2><?php _e('Create new pages', 'waka-bulk-page') ?></h2>
                        <p>
                            <?php _e('Fill the following form to create new pages.', 'waka-bulk-page') ?><br/>
                            <?php _e('One page per line. You can manage parent pages by starting the line with "-". You can manage as many levels as you want.', 'waka-bulk-page') ?>
                        </p>

                        <p>
                            <strong><?php _e('Example', 'waka-bulk-page') ?>:</strong><br/>
                            <em><?php _e('Home', 'waka-bulk-page') ?><br/>
                                <?php _e('About us', 'waka-bulk-page') ?> <br/>
                                - <?php _e('Our Team', 'waka-bulk-page') ?><br/>
                                - <?php _e('Our Services', 'waka-bulk-page') ?><br/>
                                -- <?php _e('Our first product', 'waka-bulk-page') ?><br/>
                                -- <?php _e('Our second product', 'waka-bulk-page') ?><br/>
                                <?php _e('Contact us', 'waka-bulk-page') ?><br/>
                                - <?php _e('Where to find us', 'waka-bulk-page') ?></em>
                        </p>
                        <br/>

                        <label for="wbp-new-pages"><?php _e('List the new pages below', 'waka-bulk-page') ?>:</label>

                        <textarea name="wbp-new-pages" id="wbp-new-pages"></textarea>

                        <p><label for="wbp-parent"><?php _e('Parent', 'waka-bulk-page') ?>:</label> <?php wp_dropdown_pages(array('sort_column' => 'menu_order', 'id' => 'wbp-parent', 'post_status' => 'draft,publish', 'show_option_none' => __('(No Parent)', 'waka-bulk-page'))); ?></p>

                        <p><?php
                            $wbp_preferred_status = get_option('wbp_preferred_status', 'publish');
                            $statuses = get_post_statuses();
                            if (!empty($statuses)): ?>

                                <label for="wbp-status"><?php _e('Status', 'waka-bulk-page') ?>:</label>
                                <select name="wbp-status" id="wbp-status">
                                <?php foreach ($statuses as $k => $v): ?>
                                    <option value="<?= $k ?>" <?php selected($wbp_preferred_status, $k) ?>><?= $v ?></option>
                                <?php endforeach; ?>
                                </select>
                                <?php
                            endif; ?>
                        </p>

                        <p>
                            <?php
                            $templates = get_page_templates();
                            if (!empty($templates)):
                                ?>
                                <label for="wbp-template"><?php _e('Template', 'waka-bulk-page') ?>:</label>
                                <select name="wbp-template" id="wbp-template">
                                <option value="">(<?php _e('default tpl', 'waka-bulk-page') ?>)</option>
                                <?php foreach ($templates as $k => $v) { ?>
                                    <option value="<?= $v ?>"><?= $k ?></option><?php
                                } ?>
                                </select>
                                <?php
                            endif;
                            ?>
                        </p>

                        <p><input class="button-secondary" id="wbp-add-pages" value="<?php esc_attr_e(__('Add Pages', 'waka-bulk-page')) ?>" type="button" disabled></p>

                        <?php
                        $navMenuLocations = get_nav_menu_locations();
                        $navMenus = get_registered_nav_menus();
                        if (!empty($navMenus)): ?>

                            <label for="create_menu"><?php _e('Create a menu for these pages', 'waka-bulk-page') ?></label>
                            <select name="create_menu" id="create_menu">
                                <option value=""><?php _e('No', 'waka-bulk-page') ?></option>
                                <?php foreach ($navMenus as $k => $v): ?>
                                    <?php $menuExists = !empty($navMenuLocations[$k]) ?>
                                    <option value="<?= $k ?>"><?= $v ?><?php if ($menuExists): ?> *<?php endif; ?></option>
                                <?php endforeach; ?>
                            </select>

                            <p id="menu_name"><input type="text" name="menu_name" placeholder="<?php esc_attr_e(__('New menu name', 'waka-bulk-page')) ?>"/><br/><em><?php _e('* This menu already exists, it will be replaced by the new menu.', 'waka-bulk-page') ?></em>
                            </p>

                        <?php endif; ?>

                        <p>
                            <input type="hidden" name="update_settings" value="Y"/>
                            <?php wp_nonce_field('wbp_submit') ?>
                            <input type="submit" value="<?php esc_attr_e(__('Save')) ?>" class="button-primary wbp-btn-submit" disabled/>
                        </p>

                    </div>
                </div>
            </form>

            <div id="wbp-footer">
                <p>
                    <a href="https://wordpress.org/plugins/waka-bulk-page/" target="_blank">Waka Bulk Page</a>
                </p>
            </div>
        </div>

        <?php
    }


}