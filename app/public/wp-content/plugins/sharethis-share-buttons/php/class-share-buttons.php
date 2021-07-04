<?php
/**
 * Share Buttons.
 *
 * @package ShareThisShareButtons
 */

namespace ShareThisShareButtons;

/**
 * Share Buttons Class
 *
 * @package ShareThisShareButtons
 */
class Share_Buttons
{

    /**
     * Plugin instance.
     *
     * @var object
     */
    public $plugin;

    /**
     * Button Widget instance.
     *
     * @var object
     */
    public $button_widget;

    /**
     * Menu slug.
     *
     * @var string
     */
    public $menu_slug;

    /**
     * Menu hook suffix.
     *
     * @var string
     */
    private $hook_suffix;

    /**
     * Sub Menu hook suffix.
     *
     * @var string
     */
    private $general_hook_suffix;

    /**
     * Holds the settings sections.
     *
     * @var array
     */
    public $setting_sections;

    /**
     * Holds the settings fields.
     *
     * @var array
     */
    public $setting_fields;

    /**
     * Networks available for sharing.
     *
     * @var array
     */
    public $networks;

    /**
     * Languages available for sharing in.
     *
     * @var array
     */
    public $languages;

    /**
     * Class constructor.
     *
     * @param object $plugin Plugin class.
     * @param object $button_widget Button Widget class.
     */
    public function __construct($plugin, $button_widget)
    {
        $this->button_widget = $button_widget;
        $this->plugin        = $plugin;
        $this->menu_slug     = 'sharethis';
        $this->setSettings();
        $this->setNetworks();
        $this->setLanguages();

        // Configure your buttons notice on activation.
        register_activation_hook(
            "{$this->plugin->dir_path}/sharethis-share-buttons.php",
            [$this, 'stActivationHook']
        );

        // Clean up plugin information on deactivation.
        register_deactivation_hook(
            "{$this->plugin->dir_path}/sharethis-share-buttons.php",
            [$this, 'stDeactivationHook']
        );
    }

    /**
     * Set the settings sections and fields.
     *
     * @access private
     */
    private function setSettings()
    {
        // Sections config.
        $this->setting_sections = array(
            '<span id="Inline" class="st-arrow">&#9658;</span>' .
            esc_html__(
                'Inline Share Buttons',
                'sharethis-share-buttons'
            ),
            '<span id="Sticky" class="st-arrow">&#9658;</span>' .
            esc_html__(
                'Sticky Share Buttons',
                'sharethis-share-buttons'
            ),
            '<span id="GDPR" class="st-arrow">&#9658;</span>' .
            esc_html__(
                'GDPR Compliance Tool',
                'sharethis-share-buttons'
            ),
        );

        // Setting configs.
        $this->setting_fields = array(
            array(
                'id_suffix'   => 'inline',
                'description' => '',
                'callback'    => 'enableCb',
                'section'     => 'share_button_section_1',
                'arg'         => 'inline',
            ),
            array(
                'id_suffix'   => 'inline_settings',
                'description' => $this->getDescriptions('Inline'),
                'callback'    => 'configSettings',
                'section'     => 'share_button_section_1',
                'arg'         => 'inline',
            ),
            array(
                'id_suffix'   => 'sticky',
                'description' => '',
                'callback'    => 'enableCb',
                'section'     => 'share_button_section_2',
                'arg'         => 'sticky',
            ),
            array(
                'id_suffix'   => 'sticky_settings',
                'description' => $this->getDescriptions('Sticky'),
                'callback'    => 'configSettings',
                'section'     => 'share_button_section_2',
                'arg'         => 'sticky',
            ),
            array(
                'id_suffix'   => 'shortcode',
                'description' => $this->getDescriptions('', 'shortcode'),
                'callback'    => 'shortcodeTemplate',
                'section'     => 'share_button_section_1',
                'arg'         => array(
                    'type'  => 'shortcode',
                    'value' => '[sharethis-inline-buttons]',
                ),
            ),
            array(
                'id_suffix'   => 'template',
                'description' => $this->getDescriptions('', 'template'),
                'callback'    => 'shortcodeTemplate',
                'section'     => 'share_button_section_1',
                'arg'         => array(
                    'type'  => 'template',
                    'value' => '<?php echo sharethis_inline_buttons(); ?>',
                ),
            ),
            array(
                'id_suffix'   => 'gdpr',
                'description' => '',
                'callback'    => 'enableCb',
                'section'     => 'share_button_section_3',
                'arg'         => 'gdpr',
            ),
        );

        // Inline setting array.
        $this->inline_setting_fields = array(
            array(
                'id_suffix' => 'inline_post_top',
                'title'     => esc_html__('Top of post body', 'sharethis-share-buttons'),
                'callback'  => 'onoff_cb',
                'type'      => '',
                'default'   => array(
                    'true'   => 'checked="checked"',
                    'false'  => '',
                    'margin' => true,
                ),
            ),
            array(
                'id_suffix' => 'inline_post_bottom',
                'title'     => esc_html__('Bottom of post body', 'sharethis-share-buttons'),
                'callback'  => 'onoff_cb',
                'type'      => '',
                'default'   => array(
                    'true'   => '',
                    'false'  => 'checked="checked"',
                    'margin' => true,
                ),
            ),
            array(
                'id_suffix' => 'inline_page_top',
                'title'     => esc_html__('Top of page body', 'sharethis-share-buttons'),
                'callback'  => 'onoff_cb',
                'type'      => '',
                'default'   => array(
                    'true'   => '',
                    'false'  => 'checked="checked"',
                    'margin' => true,
                ),
            ),
            array(
                'id_suffix' => 'inline_page_bottom',
                'title'     => esc_html__('Bottom of page body', 'sharethis-share-buttons'),
                'callback'  => 'onoff_cb',
                'type'      => '',
                'default'   => array(
                    'true'   => '',
                    'false'  => 'checked="checked"',
                    'margin' => true,
                ),
            ),
            array(
                'id_suffix' => 'excerpt',
                'title'     => esc_html__('Include in excerpts', 'sharethis-share-buttons'),
                'callback'  => 'onoff_cb',
                'type'      => '',
                'default'   => array(
                    'true'   => '',
                    'false'  => 'checked="checked"',
                    'margin' => true,
                ),
            ),
        );

        // Sticky setting array.
        $this->sticky_setting_fields = array(
            array(
                'id_suffix' => 'sticky_home',
                'title'     => esc_html__('Home Page', 'sharethis-share-buttons'),
                'callback'  => 'onoff_cb',
                'type'      => '',
                'default'   => array(
                    'true'  => 'checked="checked"',
                    'false' => '',
                ),
            ),
            array(
                'id_suffix' => 'sticky_post',
                'title'     => esc_html__('Posts', 'sharethis-share-buttons'),
                'callback'  => 'onoff_cb',
                'type'      => '',
                'default'   => array(
                    'true'  => 'checked="checked"',
                    'false' => '',
                ),
            ),
            array(
                'id_suffix' => 'sticky_custom_posts',
                'title'     => esc_html__('Custom Post Types', 'sharethis-share-buttons'),
                'callback'  => 'onoff_cb',
                'type'      => '',
                'default'   => array(
                    'true'  => 'checked="checked"',
                    'false' => '',
                ),
            ),
            array(
                'id_suffix' => 'sticky_page',
                'title'     => esc_html__('Pages', 'sharethis-share-buttons'),
                'callback'  => 'onoff_cb',
                'type'      => '',
                'default'   => array(
                    'true'  => 'checked="checked"',
                    'false' => '',
                ),
            ),
            array(
                'id_suffix' => 'sticky_page_off',
                'title'     => esc_html__('Exclude specific pages:', 'sharethis-share-buttons'),
                'callback'  => 'listCb',
                'type'      => array(
                    'single' => 'page',
                    'multi'  => 'pages',
                ),
            ),
            array(
                'id_suffix' => 'sticky_category',
                'title'     => esc_html__('Category archive pages', 'sharethis-share-buttons'),
                'callback'  => 'onoff_cb',
                'type'      => '',
                'default'   => array(
                    'true'  => 'checked="checked"',
                    'false' => '',
                ),
            ),
            array(
                'id_suffix' => 'sticky_category_off',
                'title'     => esc_html__('Exclude specific category archives:', 'sharethis-share-buttons'),
                'callback'  => 'listCb',
                'type'      => array(
                    'single' => 'category',
                    'multi'  => 'categories',
                ),
            ),
            array(
                'id_suffix' => 'sticky_tags',
                'title'     => esc_html__('Tags Archives', 'sharethis-share-buttons'),
                'callback'  => 'onoff_cb',
                'type'      => '',
                'default'   => array(
                    'true'  => 'checked="checked"',
                    'false' => '',
                ),
            ),
            array(
                'id_suffix' => 'sticky_author',
                'title'     => esc_html__('Author pages', 'sharethis-share-buttons'),
                'callback'  => 'onoff_cb',
                'type'      => '',
                'default'   => array(
                    'true'  => 'checked="checked"',
                    'false' => '',
                ),
            ),
        );
    }

    /**
     * Add in ShareThis menu option.
     *
     * @action admin_menu
     */
    public function defineSharethisMenus()
    {
        $propertyid = get_option('sharethis_property_id');

        // Menu base64 Encoded icon.
        $icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDE2IDE2IiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPCEtLSBHZW5lcmF0b3I6IFNrZXRjaCA0NC4xICg0MTQ1NSkgLSBodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2ggLS0+CiAgICA8dGl0bGU+RmlsbCAzPC90aXRsZT4KICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPgogICAgPGRlZnM+PC9kZWZzPgogICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9IkRlc2t0b3AtSEQiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0xMC4wMDAwMDAsIC00MzguMDAwMDAwKSIgZmlsbD0iI0ZFRkVGRSI+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik0yMy4xNTE2NDMyLDQ0OS4xMDMwMTEgQzIyLjcyNjg4NzcsNDQ5LjEwMzAxMSAyMi4zMzM1MDYyLDQ0OS4yMjg5OSAyMS45OTcwODA2LDQ0OS40Mzc5ODkgQzIxLjk5NTE0OTksNDQ5LjQzNTA5MyAyMS45OTcwODA2LDQ0OS40Mzc5ODkgMjEuOTk3MDgwNiw0NDkuNDM3OTg5IEMyMS44ODA3NTU1LDQ0OS41MDg5NDMgMjEuNzM1NDY5OCw0NDkuNTQ1NjI2IDIxLjU4OTIxODgsNDQ5LjU0NTYyNiBDMjEuNDUzMTA0LDQ0OS41NDU2MjYgMjEuMzE5ODg1Miw0NDkuNTA3NDk0IDIxLjIwODg2OTYsNDQ5LjQ0NTIyOSBMMTQuODczNzM4Myw0NDYuMDM4OTggQzE0Ljc2NDE3MDcsNDQ1Ljk5MDIzIDE0LjY4NzkwNzgsNDQ1Ljg3ODczMSAxNC42ODc5MDc4LDQ0NS43NTEzMDUgQzE0LjY4NzkwNzgsNDQ1LjYyMzM5NSAxNC43NjUxMzYsNDQ1LjUxMTg5NyAxNC44NzQ3MDM2LDQ0NS40NjI2NjQgTDIxLjIwODg2OTYsNDQyLjA1Njg5NyBDMjEuMzE5ODg1Miw0NDEuOTk1MTE1IDIxLjQ1MzEwNCw0NDEuOTU2NTAxIDIxLjU4OTIxODgsNDQxLjk1NjUwMSBDMjEuNzM1NDY5OCw0NDEuOTU2NTAxIDIxLjg4MDc1NTUsNDQxLjk5MzY2NyAyMS45OTcwODA2LDQ0Mi4wNjQ2MiBDMjEuOTk3MDgwNiw0NDIuMDY0NjIgMjEuOTk1MTQ5OSw0NDIuMDY3MDM0IDIxLjk5NzA4MDYsNDQyLjA2NDYyIEMyMi4zMzM1MDYyLDQ0Mi4yNzMxMzcgMjIuNzI2ODg3Nyw0NDIuMzk5MTE1IDIzLjE1MTY0MzIsNDQyLjM5OTExNSBDMjQuMzY2NTQwMyw0NDIuMzk5MTE1IDI1LjM1MTY4MzQsNDQxLjQxNDQ1NSAyNS4zNTE2ODM0LDQ0MC4xOTk1NTggQzI1LjM1MTY4MzQsNDM4Ljk4NDY2IDI0LjM2NjU0MDMsNDM4IDIzLjE1MTY0MzIsNDM4IEMyMi4wMTYzODc2LDQzOCAyMS4wOTMwMjcyLDQzOC44NjMwMjYgMjAuOTc1MjU0MSw0MzkuOTY3MzkgQzIwLjk3MTM5MjYsNDM5Ljk2MzA0NiAyMC45NzUyNTQxLDQzOS45NjczOSAyMC45NzUyNTQxLDQzOS45NjczOSBDMjAuOTUwNjM3NSw0NDAuMjM5MTM3IDIwLjc2OTE1MTEsNDQwLjQ2NzkyNiAyMC41MzYwMTgzLDQ0MC41ODQyNTEgTDE0LjI3OTU2MzMsNDQzLjk0NzU0MiBDMTQuMTY0MjAzNiw0NDQuMDE3MDQ3IDE0LjAyNDIyNzMsNDQ0LjA1NjE0NCAxMy44Nzk0MjQzLDQ0NC4wNTYxNDQgQzEzLjcwODU1NjgsNDQ0LjA1NjE0NCAxMy41NDgzMDgxLDQ0NC4wMDQ0OTggMTMuNDIwODgxNSw0NDMuOTEwMzc2IEMxMy4wNzUyODUsNDQzLjY4NDk2NiAxMi42NjUwMDk4LDQ0My41NTEyNjQgMTIuMjIxOTEyNiw0NDMuNTUxMjY0IEMxMS4wMDcwMTU1LDQ0My41NTEyNjQgMTAuMDIyMzU1MSw0NDQuNTM2NDA3IDEwLjAyMjM1NTEsNDQ1Ljc1MTMwNSBDMTAuMDIyMzU1MSw0NDYuOTY2MjAyIDExLjAwNzAxNTUsNDQ3Ljk1MDg2MiAxMi4yMjE5MTI2LDQ0Ny45NTA4NjIgQzEyLjY2NTAwOTgsNDQ3Ljk1MDg2MiAxMy4wNzUyODUsNDQ3LjgxNzY0MyAxMy40MjA4ODE1LDQ0Ny41OTIyMzMgQzEzLjU0ODMwODEsNDQ3LjQ5NzYyOSAxMy43MDg1NTY4LDQ0Ny40NDY0NjUgMTMuODc5NDI0Myw0NDcuNDQ2NDY1IEMxNC4wMjQyMjczLDQ0Ny40NDY0NjUgMTQuMTY0MjAzNiw0NDcuNDg1MDc5IDE0LjI3OTU2MzMsNDQ3LjU1NDU4NSBMMjAuNTM2MDE4Myw0NTAuOTE4MzU4IEMyMC43Njg2Njg0LDQ1MS4wMzQyMDEgMjAuOTUwNjM3NSw0NTEuMjYzNDcyIDIwLjk3NTI1NDEsNDUxLjUzNTIxOSBDMjAuOTc1MjU0MSw0NTEuNTM1MjE5IDIwLjk3MTM5MjYsNDUxLjUzOTU2MyAyMC45NzUyNTQxLDQ1MS41MzUyMTkgQzIxLjA5MzAyNzIsNDUyLjYzOTEwMSAyMi4wMTYzODc2LDQ1My41MDI2MDkgMjMuMTUxNjQzMiw0NTMuNTAyNjA5IEMyNC4zNjY1NDAzLDQ1My41MDI2MDkgMjUuMzUxNjgzNCw0NTIuNTE3NDY2IDI1LjM1MTY4MzQsNDUxLjMwMjU2OSBDMjUuMzUxNjgzNCw0NTAuMDg3NjcyIDI0LjM2NjU0MDMsNDQ5LjEwMzAxMSAyMy4xNTE2NDMyLDQ0OS4xMDMwMTEiIGlkPSJGaWxsLTMiPjwvcGF0aD4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPg==';

        if (empty($GLOBALS['admin_page_hooks']['sharethis-general'])) {
            // Main sharethis menu.
            add_menu_page(
                __('Share Buttons by ShareThis', 'sharethis-share-buttons'),
                __('ShareThis', 'sharethis-share-buttons'),
                'manage_options',
                $this->menu_slug . '-general',
                null,
                $icon,
                26
            );

            // Create submenu to replace default submenu item. Set hook for enqueueing styles.
            $this->general_hook_suffix = add_submenu_page(
                $this->menu_slug . '-general',
                __('ShareThis General Settings', 'sharethis-share-buttons'),
                __('General Settings', 'sharethis-share-buttons'),
                'manage_options',
                $this->menu_slug . '-general',
                array($this, 'generalSettingsDisplay')
            );
        }

        // If the property ID is set then register the share buttons menu.
        if ($this->isPropertyIdSet('empty')) {
            $this->shareButtonsSettings();
        }
    }

    /**
     * Add Share Buttons settings page.
     */
    public function shareButtonsSettings()
    {
        $this->hook_suffix = add_submenu_page(
            $this->menu_slug . '-general',
            $this->getDescriptions('', 'share_buttons'),
            esc_html__('Share Buttons', 'sharethis-share-buttons'),
            'manage_options',
            $this->menu_slug . '-share-buttons',
            array($this, 'shareButtonDisplay')
        );
    }

    /**
     * Enqueue main MU script.
     *
     * @action wp_enqueue_scripts
     */
    public function enqueueMu()
    {
        wp_enqueue_script("{$this->plugin->assets_prefix}-mu");
    }

    /**
     * Enqueue admin assets.
     *
     * @action admin_enqueue_scripts
     *
     * @param string $hook_suffix The current admin page.
     */
    public function enqueueAdminAssets($hook_suffix)
    {
        // Are sticky and inline buttons enabled.
        $inline        = 'true' === get_option('sharethis_inline') || true === get_option('sharethis_inline') ?
            true :
            false;
        $sticky        = 'true' === get_option('sharethis_sticky') || true === get_option('sharethis_sticky') ?
            true :
            false;
        $gdpr          = 'true' === get_option('sharethis_gdpr') || true === get_option('sharethis_gdpr') ?
            true :
            false;
        $first_exists  = get_option('sharethis_first_product');
        $first_button  = false !== $first_exists && null !== $first_exists ? $first_exists : '';
        $first_exists  = false === $first_exists || null === $first_exists || '' === $first_exists ? true : false;
        $propertyid    = explode('-', get_option('sharethis_property_id'), 2);
        $token         = get_option('sharethis_token');
        $property_id   = isset($propertyid[0]) ? $propertyid[0] : '';
        $secret        = isset($propertyid[1]) ? $propertyid[1] : '';
        $admin_url     = str_replace('http://', '', str_replace('https://', '', site_url()));
        $button_config = get_option('sharethis_button_config', true);
        $button_config = false !== $button_config && null !== $button_config ? $button_config : '';

        // Only euqueue assets on this plugin admin menu.
        if ($hook_suffix !== $this->hook_suffix && $hook_suffix !== $this->general_hook_suffix) {
            return;
        }

        // Enqueue the styles globally throughout the ShareThis menus.
        wp_enqueue_style("{$this->plugin->assets_prefix}-admin");
        wp_enqueue_script("{$this->plugin->assets_prefix}-mua");

        // Only enqueue these scripts on share buttons plugin admin menu.
        if ($hook_suffix === $this->hook_suffix) {
            if ($first_exists && ($inline || $sticky)) {
                $first = $inline ? 'inline' : 'sticky';

                update_option('sharethis_first_product', $first);
            }

            wp_enqueue_script("{$this->plugin->assets_prefix}-admin");
            wp_add_inline_script(
                "{$this->plugin->assets_prefix}-admin",
                sprintf(
                    'ShareButtons.boot( %s );',
                    wp_json_encode(
                        [
                            'inlineEnabled' => $inline,
                            'stickyEnabled' => $sticky,
                            'gdprEnabled'   => $gdpr,
                            'propertyid'    => $property_id,
                            'token'         => $token,
                            'secret'        => $secret,
                            'buttonConfig'  => $button_config,
                            'nonce'         => wp_create_nonce($this->plugin->meta_prefix),
                        ]
                    )
                )
            );
        }

        // Only enqueue this script on the general settings page for credentials.
        if ($hook_suffix === $this->general_hook_suffix) {
            wp_enqueue_script("{$this->plugin->assets_prefix}-credentials");
            wp_add_inline_script(
                "{$this->plugin->assets_prefix}-credentials",
                sprintf(
                    'Credentials.boot( %s );',
                    wp_json_encode(
                        [
                            'nonce'        => wp_create_nonce($this->plugin->meta_prefix),
                            'url'          => $admin_url,
                            'propertyid'   => $property_id,
                            'secret'       => $secret,
                            'firstButton'  => $first_button,
                            'buttonConfig' => $button_config,
                        ]
                    )
                )
            );
        }
    }

    /**
     * Call back for displaying the General Settings page.
     */
    public function generalSettingsDisplay()
    {
        global $current_user;

        // Check user capabilities.
        if (! current_user_can('manage_options')) {
            return;
        }

        // If the property id is set then show the general settings template.
        if ($this->isPropertyIdSet()) {
            include_once "{$this->plugin->dir_path}/templates/general/general-settings.php";
        } else {
            // Get the current sites true url including sub directories.
            $admin_url   = str_replace('/wp-admin/', '', admin_url());
            $setup_steps = $this->getSetupSteps();
            $networks    = $this->networks;
            $languages   = $this->languages;
            $button     = isset($_GET['b']) && 'i' === $_GET['b'] ?
                'Inline' :
                'Sticky'; // WPCS: CSRF ok. // Input var okay.
            $page       = ! isset($_GET['s']) && ! isset($_GET['l']) && ! isset($_GET['p']) ?
                'first' :
                ''; // WPCS: CSRF ok. // Input var okay.
            $page       = isset($_GET['s']) && '' === $page && '2' === $_GET['s'] ?
                'second' :
                $page; // WPCS: CSRF ok. // Input var okay.
            $page       = isset($_GET['s']) && '' === $page && '3' === $_GET['s'] ?
                'third' :
                $page; // WPCS: CSRF ok. // Input var okay.
            $page       = isset($_GET['l']) && '' === $page && 't' === $_GET['l'] ?
                'login' :
                $page; // WPCS: CSRF ok. // Input var okay.
            $page       = isset($_GET['p']) && '' === $page && 't' === $_GET['p'] ?
                'property' :
                $page; // WPCS: CSRF ok. // Input var okay.
            $step_class = '';

            include_once "{$this->plugin->dir_path}/templates/general/connection-template.php";
        }
    }

    /**
     * Call back for property id setting view.
     */
    public function propertySetting()
    {
        // Check user capabilities.
        if (! current_user_can('manage_options')) {
            return;
        }

        $credential    = get_option('sharethis_property_id');
        $credential    = null !== $credential && false !== $credential ? $credential : '';
        $error_message = '' === $credential ?
            '<div class="st-error"><strong>' . esc_html__(
                'ERROR',
                'sharethis-share-buttons'
            ) . '</strong>:' . esc_html__(
                'Property ID is required.',
                'sharethis-share-buttons'
            ) . '</div>' :
            '';

        include_once "{$this->plugin->dir_path}/templates/general/property-setting.php";
    }

    /**
     * Call back for displaying Share Buttons settings page.
     */
    public function shareButtonDisplay()
    {
        // Check user capabilities.
        if (! current_user_can('manage_options')) {
            return;
        }

        $description = $this->getDescriptions('', 'share_buttons');

        include_once "{$this->plugin->dir_path}/templates/share-buttons/share-button-settings.php";
    }

    /**
     * Define general setting section and fields.
     *
     * @action admin_init
     */
    public function generalSettings()
    {
        // Add setting section.
        add_settings_section(
            'property_id_section',
            null,
            null,
            $this->menu_slug . '-general'
        );

        // Register Setting.
        register_setting($this->menu_slug . '-general', 'sharethis_property_id');

        // Property id field.
        add_settings_field(
            'property_id',
            $this->getDescriptions('', 'property'),
            array($this, 'propertySetting'),
            $this->menu_slug . '-general',
            'property_id_section'
        );
    }

    /**
     * Define share button setting sections and fields.
     *
     * @action admin_init
     */
    public function settingsApiInit()
    {
        // Register sections.
        foreach ($this->setting_sections as $index => $title) {
            // Since the index starts at 0, let's increment it by 1.
            $i       = $index + 1;
            $section = "share_button_section_{$i}";

            switch ($i) {
                case 1:
                    $arg = 'inline';
                    break;
                case 2:
                    $arg = 'sticky';
                    break;
                case 3:
                    $arg = 'gdpr';
                    break;
            }

            // Add setting section.
            add_settings_section(
                $section,
                $title,
                array($this, 'socialButtonLink'),
                $this->menu_slug . '-share-buttons',
                array($arg)
            );
        }

        // Register setting fields.
        foreach ($this->setting_fields as $setting_field) {
            register_setting($this->menu_slug . '-share-buttons', $this->menu_slug . '_' . $setting_field['id_suffix']);
            add_settings_field(
                $this->menu_slug . '_' . $setting_field['id_suffix'],
                $setting_field['description'],
                array($this, $setting_field['callback']),
                $this->menu_slug . '-share-buttons',
                $setting_field['section'],
                $setting_field['arg']
            );
        }

        // Register omit settings.
        register_setting($this->menu_slug . '-share-buttons', $this->menu_slug . '_sticky_page_off');
        register_setting($this->menu_slug . '-share-buttons', $this->menu_slug . '_sticky_category_off');
    }

    /**
     * Call back function for on / off buttons.
     *
     * @param string $type The setting type.
     */
    public function configSettings($type)
    {
        $config_array = 'inline' === $type ? $this->inline_setting_fields : $this->sticky_setting_fields;

        // Display on off template for inline settings.
        foreach ($config_array as $setting) {
            $option       = 'sharethis_' . $setting['id_suffix'];
            $title        = isset($setting['title']) ? $setting['title'] : '';
            $option_value = get_option('sharethis_' . $type . '_settings');
            $default      = isset($setting['default']) ? $setting['default'] : '';
            $allowed      = array(
                'li'    => array(
                    'class' => array(),
                ),
                'span'  => array(
                    'id'    => array(),
                    'class' => array(),
                ),
                'input' => array(
                    'id'    => array(),
                    'name'  => array(),
                    'type'  => array(),
                    'value' => array(),
                ),
            );

            // Margin control variables.
            $margin = isset($setting['default']['margin']) ? $setting['default']['margin'] : false;
            $mclass = isset($option_value[$option . '_margin_top']) &&
                      0 !== (int)$option_value[$option . '_margin_top'] ||
                      isset($option_value[$option . '_margin_bottom']) &&
                      0 !== (int)$option_value[$option . '_margin_bottom'] ?
                'active-margin' : '';
            $onoff  = '' !== $mclass ? __('On', 'sharethis-share-buttons') : __('Off', 'sharethis-share-buttons');
            $active = array(
                'class' => $mclass,
                'onoff' => esc_html($onoff),
            );

            if (isset($option_value[$option]) && false !== $option_value[$option] && null !== $option_value[$option]) {
                $default = array(
                    'true'  => '',
                    'false' => '',
                );
            }

            // Display the list call back if specified.
            if ('onoff_cb' === $setting['callback']) {
                include "{$this->plugin->dir_path}/templates/share-buttons/onoff-buttons.php";
            } else {
                $current_omit = $this->getOmit($setting['type']);

                $this->listCb($setting['type'], $current_omit, $allowed);
            }
        } // End foreach().
    }

    /**
     * Helper function to build the omit list html
     *
     * @access private
     *
     * @param array $setting the omit type.
     *
     * @return string The html for omit list.
     */
    private function getOmit($setting)
    {
        $current_omit = get_option('sharethis_sticky_' . $setting['single'] . '_off');
        $current_omit = isset($current_omit) ? $current_omit : '';
        $html         = '';

        if (is_array($current_omit)) {
            foreach ($current_omit as $title => $id) {
                $html .= '<li class="omit-item">';
                $html .= $title;
                $html .= '<span id="' . $id . '" class="remove-omit">X</span>';
                $html .= "<input
                            type='hidden'
                            name='sharethis_sticky_{$setting['single']}
                            _off[{$title}]'
                            value='{$id}'
                            id='sharethis_sticky_{$setting['single']}_off[{$title}]'
                        >";
                $html .= '</li>';
            }
        }

        // Add ommit ids to meta box option.
        $this->updateMetaboxList($current_omit);

        return $html;
    }

    /**
     * Helper function to update metabox list to sync with omit.
     *
     * @param array $current_omit The omit list.
     */
    private function updateMetaboxList($current_omit)
    {
        $current_on = get_option('sharethis_sticky_page_on');

        if (isset($current_on, $current_omit) && is_array($current_on) && is_array($current_omit)) {
            $new_on = array_diff($current_on, $current_omit);

            if (is_array($new_on)) {
                delete_option('sharethis_sticky_page_on');
                delete_option('sharethis_sticky_page_off');

                update_option('sharethis_sticky_page_off', $current_omit);
                update_option('sharethis_sticky_page_on', $new_on);
            }
        }
    }

    /**
     * Callback function for onoff buttons
     *
     * @param array $id The setting type.
     */
    public function enableCb($id)
    {
        include "{$this->plugin->dir_path}/templates/share-buttons/enable-buttons.php";
    }

    /**
     * Callback function for omitting fields.
     *
     * @param array $type The type of list to return for exlusion.
     * @param array $current_omit The currently omited items.
     * @param array $allowed The allowed html that an omit item can echo.
     */
    public function listCb($type, $current_omit, $allowed)
    {
        include "{$this->plugin->dir_path}/templates/share-buttons/list.php";
    }

    /**
     * Callback function for the shortcode and template code fields.
     *
     * @param string $type The type of template to pull.
     */
    public function shortcodeTemplate($type)
    {
        include "{$this->plugin->dir_path}/templates/share-buttons/shortcode-templatecode.php";
    }

    /**
     * Callback function for the login buttons.
     *
     * @param string $button The specific product to link to.
     */
    public function socialButtonLink($button)
    {
        $networks  = $this->networks;
        $languages = $this->languages;

        if ($button['id'] === 'share_button_section_3') {
            // User type options.
            $user_types = array(
                'eu'     => esc_html__('Only visitors in the EU', 'sharethis-custom'),
                'always' => esc_html__('All visitors globally', 'sharethis-custom'),
            );

            // Consent type options.
            $consent_types = array(
                'global'    => esc_html__(
                    'Global: Publisher consent = 1st party cookie; Vendors consent = 3rd party cookie',
                    'sharethis-custom'
                ),
                'publisher' => esc_html__(
                    'Service: publisher consent = 1st party cookie; Vendors consent = 1st party cookie',
                    'sharethis-custom'
                ),
            );

            $vendor_data = $this->getVendors();

            if ( $vendor_data ) {
				$vendors  = $vendor_data['vendors'];
				$purposes = array_column($vendor_data['purposes'], 'name', 'id');
			}

            // Template vars.
            $colors = [
                '#e31010',
                '#000000',
                '#ffffff',
                '#09cd18',
                '#ff6900',
                '#fcb900',
                '#7bdcb5',
                '#00d084',
                '#8ed1fc',
                '#0693e3',
                '#abb8c3',
                '#eb144c',
                '#f78da7',
                '#9900ef',
                '#b80000',
                '#db3e00',
                '#fccb00',
                '#008b02',
                '#006b76',
                '#1273de',
                '#004dcf',
                '#5300eb',
                '#eb9694',
                '#fad0c3',
                '#fef3bd',
                '#c1e1c5',
                '#bedadc',
                '#c4def6',
                '#bed3f3',
                '#d4c4fb'
            ];

            include "{$this->plugin->dir_path}/templates/general/gdpr/gdpr-config.php";
        } else {
            include "{$this->plugin->dir_path}/templates/share-buttons/button-config.php";
        }
    }

    /**
     * Callback function for random gif field.
     *
     * @access private
     * @return string
     */
    private function randomGif()
    {
        if (! is_wp_error(wp_safe_remote_get('http://api.giphy.com/v1/gifs/random?api_key=dc6zaTOxFJmzC&rating=g'))) {
            $content = wp_safe_remote_get('http://api.giphy.com/v1/gifs/random?api_key=dc6zaTOxFJmzC&rating=g')['body'];

            return '<div id="random-gif-container"><img src="' .
                   esc_url(
                       json_decode(
                           $content,
                           ARRAY_N
                       )['data']['image_url']
                   ) . '"/></div>';
        } else {
            return esc_html__(
                'Sorry we couldn\'t show you a funny gif.  Refresh if you can\'t live without it.',
                'sharethis-share-buttons'
            );
        }
    }

    /**
     * Define setting descriptions.
     *
     * @param string $type Type of button.
     * @param string $subtype Setting type.
     *
     * @access private
     * @return string|void
     */
    private function getDescriptions($type = '', $subtype = '')
    {
        global $current_user;

        switch ($subtype) {
            case '':
                $description = esc_html__('WordPress Display Settings', 'sharethis-share-buttons');
                $description .= '<span>';
                $description .= esc_html__(
                    'Use these settings to automatically include or restrict the display of ',
                    'sharethis-share-buttons'
                ) . esc_html($type) . esc_html__(
                    ' Share Buttons on specific pages of your site.',
                    'sharethis-share-buttons'
                );
                $description .= '</span>';
                break;
            case 'shortcode':
                $description = esc_html__('Shortcode', 'sharethis-share-buttons');
                $description .= '<span>';
                $description .= esc_html__(
                    'Use this shortcode to deploy your inline share buttons in a widget, or WYSIWYG editor.',
                    'sharethis-share-buttons'
                );
                $description .= '</span>';
                break;
            case 'template':
                $description = esc_html__('PHP', 'sharethis-share-buttons');
                $description .= '<span>';
                $description .= esc_html__(
                    'Use this PHP snippet to include your inline share buttons anywhere else in your template.',
                    'sharethis-share-buttons'
                );
                $description .= '</span>';
                break;
            case 'social':
                $description = esc_html__('Social networks and button styles', 'sharethis-share-buttons');
                $description .= '<span>';
                $description .= esc_html__(
                    'Login to ShareThis Platform to add, remove or re-order social networks in your ',
                    'sharethis-share-buttons'
                ) . esc_html($type) . esc_html__(
                    ' Share buttons.  You may also update the alignment, size, labels and count settings.',
                    'sharethis-share-buttons'
                );
                $description .= '</span>';
                break;
            case 'property':
                $description = esc_html__('Property ID', 'sharethis-share-buttons');
                $description .= '<span>';
                $description .= esc_html__(
                    'We use this unique ID to identify your property. Copy it from your ',
                    'sharethis-share-buttons'
                );
                $description .= '<a class="st-support" href="https://platform.sharethis.com/settings?utm_source=sharethis-plugin&utm_medium=sharethis-plugin-page&utm_campaign=property-settings" target="_blank">';
                $description .= esc_html__('ShareThis platform settings', 'sharethis-share-buttons');
                $description .= '</a></span>';
                break;
            case 'share_buttons':
                $description = '<h1>';
                $description .= esc_html__('Share Buttons by ShareThis', 'sharethis-share-buttons');
                $description .= '</h1>';
                $description .= '<h3>';
                $description .= esc_html__(
                    'Welcome aboard, ',
                    'sharethis-share-buttons'
                ) . esc_html($current_user->display_name) . '! ';
                $description .= esc_html__(
                    'Use the settings panels below for complete control over where and how share buttons appear on your site.',
                    'sharethis-share-buttons'
                );
                break;
        } // End switch().

        return wp_kses_post($description);
    }

    /**
     * Set the property id and secret key for the user's platform account if query params are present.
     *
     * @action wp_ajax_set_credentials
     */
    public function setCredentials()
    {
        check_ajax_referer($this->plugin->meta_prefix, 'nonce');

        if (! isset($_POST['data'], $_POST['token']) || '' === $_POST['data']) { // WPCS: input var ok.
            wp_send_json_error('Set credentials failed.');
        }

        $data  = sanitize_text_field(wp_unslash($_POST['data'])); // WPCS: input var ok.
        $token = sanitize_text_field(wp_unslash($_POST['token'])); // WPCS: input var ok.

        // If both variables exist add them to a database option.
        if (false === get_option('sharethis_property_id')) {
            update_option('sharethis_property_id', $data);
            update_option('sharethis_token', $token);
        }
    }

    /**
     * Helper function to determine if property ID is set.
     *
     * @param string $type Should empty count as false.
     *
     * @access private
     * @return bool
     */
    private function isPropertyIdSet($type = '')
    {
        $property_id = get_option('sharethis_property_id');

        // If the property id is set then show the general settings template.
        if (false !== $property_id && null !== $property_id) {
            if ('empty' === $type && '' === $property_id) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * AJAX Call back to update status of buttons
     *
     * @action wp_ajax_update_buttons
     */
    public function updateButtons()
    {
        check_ajax_referer($this->plugin->meta_prefix, 'nonce');

        if (! isset($_POST['type'], $_POST['onoff'])) { // WPCS: CSRF ok. input var ok.
            wp_send_json_error('Update buttons failed.');
        }

        // Set option type and button value.
        $type  = 'sharethis_' . sanitize_text_field(wp_unslash($_POST['type'])); // WPCS: input var ok.
        $onoff = sanitize_text_field(wp_unslash($_POST['onoff'])); // WPCS: input var ok.

        if ('On' === $onoff) {
            update_option($type, 'true');
        } elseif ('Off' === $onoff) {
            update_option($type, 'false');
        }
    }

    /**
     * AJAX Call back to set defaults when rest button is clicked.
     *
     * @action wp_ajax_set_default_settings
     */
    public function setDefaultSettings()
    {
        check_ajax_referer($this->plugin->meta_prefix, 'nonce');

        if (! isset($_POST['type'])) { // WPCS: CRSF ok. input var ok.
            wp_send_json_error('Update buttons failed.');
        }

        // Set option type and button value.
        $type = strtolower(sanitize_text_field(wp_unslash($_POST['type']))); // WPCS: input var ok.

        $this->setTheDefaults($type);
    }

    /**
     * Helper function to set the default button options.
     *
     * @param string $type The type of default to set.
     */
    private function setTheDefaults($type)
    {
        $default = array(
            'inline_settings'     => array(
                'sharethis_inline_post_top'                  => 'true',
                'sharethis_inline_post_bottom'               => 'false',
                'sharethis_inline_page_top'                  => 'false',
                'sharethis_inline_page_bottom'               => 'false',
                'sharethis_excerpt'                          => 'false',
                'sharethis_inline_post_top_margin_top'       => 0,
                'sharethis_inline_post_top_margin_bottom'    => 0,
                'sharethis_inline_post_bottom_margin_top'    => 0,
                'sharethis_inline_post_bottom_margin_bottom' => 0,
                'sharethis_inline_page_top_margin_top'       => 0,
                'sharethis_inline_page_top_margin_bottom'    => 0,
                'sharethis_inline_page_bottom_margin_top'    => 0,
                'sharethis_inline_page_bottom_margin_bottom' => 0,
                'sharethis_excerpt_margin_top'               => 0,
                'sharethis_excerpt_margin_bottom'            => 0,
            ),
            'sticky_settings'     => array(
                'sharethis_sticky_home'         => 'true',
                'sharethis_sticky_post'         => 'true',
                'sharethis_sticky_custom_posts' => 'true',
                'sharethis_sticky_page'         => 'true',
                'sharethis_sticky_category'     => 'true',
                'sharethis_sticky_tags'         => 'true',
                'sharethis_sticky_author'       => 'true',
            ),
            'sticky_page_off'     => '',
            'sticky_category_off' => '',
        );

        if ('both' !== $type) {
            update_option('sharethis_' . $type . '_settings', $default[$type . '_settings']);

            if ('sticky' === $type) {
                update_option('sharethis_sticky_page_off', '');
                update_option('sharethis_sticky_category_off', '');
            }
        } else {
            foreach ($default as $types => $settings) {
                update_option('sharethis_' . $types, $settings);
            }
        }
    }

    /**
     * AJAC Call back to return categories or pages based on input.
     *
     * @action wp_ajax_return_omit
     */
    public function returnOmit()
    {
        check_ajax_referer($this->plugin->meta_prefix, 'nonce');

        if (! isset($_POST['key'], $_POST['type']) || '' === $_POST['key']) { // WPCS: input var ok.
            wp_send_json_error('');
        }

        $key_input   = sanitize_text_field(wp_unslash($_POST['key'])); // WPCS: input var ok.
        $type        = sanitize_text_field(wp_unslash($_POST['type'])); // WPCS: input var ok.
        $current_cat = array_values(get_option('sharethis_sticky_category_off'));

        if ('category' === $type) {
            // Search category names LIKE $key_input.
            $categories = get_categories(array(
                'name__like' => $key_input,
                'exclude'    => $current_cat,
                'hide_empty' => false,
            ));

            foreach ($categories as $cats) {
                $related[] = array(
                    'id'    => $cats->term_id,
                    'title' => $cats->name,
                );
            }
        } else {
            // Search page names like $key_input.
            $pages = get_pages();

            foreach ($pages as $page) {
                if (false !== stripos($page->post_title, $key_input) && $this->notInList($page->ID)) {
                    $related[] = array(
                        'id'    => $page->ID,
                        'title' => $page->post_title,
                    );
                }
            }
        }

        // Create output list if any results exist.
        if (count($related) > 0) {
            foreach ($related as $items) {
                $item_option[] = sprintf(
                    '<li class="ta-' . $type . '-item" data-id="%1$d">%2$s</li>',
                    (int)$items['id'],
                    esc_html($items['title'])
                );
            }

            wp_send_json_success($item_option);
        } else {
            wp_send_json_error('no results');
        }
    }

    /**
     * Helper function to determine if page is in the list already.
     *
     * @param integer $id The page id.
     *
     * @return bool
     */
    private function notInList($id)
    {
        $current_pages = array_values(get_option('sharethis_sticky_page_off'));

        if (! is_array($current_pages) || array() === $current_pages || ! in_array(
            (string)$id,
            $current_pages,
            true
        )
        ) {
            return true;
        }

        return false;
    }

    /**
     * Display custom admin notice.
     *
     * @action admin_notices
     */
    public function connectionMadeAdminNotice()
    {
        $screen = get_current_screen();
        if ('sharethis_page_sharethis-share-buttons' === $screen->base) {
			settings_errors();
            if (isset($_GET['reset']) && '' !== $_GET['reset']) { // WPCS: CSRF ok. Input var ok.
                ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <?php
                        // translators: The type of button.
                        printf(
                            esc_html__(
                                'Successfully reset your %1$s share button options!',
                                'sharethis-share-buttons'
                            ),
                            esc_html(sanitize_text_field(wp_unslash($_GET['reset'])))
                        ); // WPCS: CSRF ok. Input var ok.
                        ?>
                    </p>
                </div>
                <?php
            };
        }
    }

    /**
     * Runs only when the plugin is activated.
     */
    public function stActivationHook()
    {
        // Create transient data.
        set_transient('st-activation', true, 5);
        set_transient('st-connection', true, 360);

        // Set the default optons.
        $this->setTheDefaults('both');
    }

    /**
     * Admin Notice on Activation.
     *
     * @action admin_notices
     */
    public function activationInformNotice()
    {
        $screen  = get_current_screen();
        $product = get_option('sharethis_first_product');
        $product = null !== $product && false !== $product ? ucfirst($product) : 'your';
        $gen_url = '<a href="' . esc_url(admin_url('admin.php?page=sharethis-share-buttons&nft')) . '">
                        configuration
                    </a>';

        if (! $this->isPropertyIdSet()) {
            $gen_url = '<a href="' . esc_url(admin_url('admin.php?page=sharethis-general')) . '">configuration</a>';
        }

        // Check transient, if available display notice.
        if (get_transient('st-activation')) {
            ?>
            <div class="updated notice is-dismissible">
                <p>
                    <?php
                    // translators: The general settings url.
                    printf(
                        esc_html__(
                            'Your ShareThis Share Button plugin requires %1$s',
                            'sharethis-share-button'
                        ),
                        wp_kses_post($gen_url)
                    );
                    ?>
                    .
                </p>
            </div>
            <?php
            // Delete transient, only display this notice once.
            delete_transient('st-activation');
        }

        if ('sharethis_page_sharethis-share-buttons' === $screen->base &&
            get_transient('st-connection') &&
            ! isset($_GET['nft'])
        ) { // WPCS: CSRF ok. input var ok.
            ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    <?php
                    // translators: The product type.
                    printf(
                        esc_html__(
                        'Congrats! You’ve activated %1$s Share Buttons. Sit tight, they’ll appear on your site in just a few minutes!',
                            'sharethis-share-buttons'
                        ),
                        esc_html($product)
                    );
                    ?>
                </p>
            </div>
            <?php
            delete_transient('st-connection');
        }
    }

    /**
     * Remove all database information when plugin is deactivated.
     */
    public function stDeactivationHook()
    {
        foreach (wp_load_alloptions() as $option => $value) {
            if (strpos($option, 'sharethis_') === 0) {
                delete_option($option);
            }
        }
    }

    /**
     * Register the button widget.
     *
     * @action widgets_init
     */
    public function registerWidgets()
    {
        register_widget($this->button_widget);
    }

    /**
     * Return the set up steps.
     */
    private function getSetupSteps()
    {
        $steps = array(
            1 => esc_html__('Choose button type', 'sharethis-share-buttons'),
            2 => esc_html__('Design your buttons', 'sharethis-share-buttons'),
            3 => esc_html__('Register with ShareThis', 'sharethis-share-buttons'),
            4 => esc_html__('Configure WordPress Settings', 'sharethis-share-buttons'),
        );

        return $steps;
    }

    /**
     * Set the languages array.
     */
    private function setLanguages()
    {
        $this->languages = array(
            'English'    => 'en',
            'German'     => 'de',
            'Spanish'    => 'es',
            'French'     => 'fr',
            'Italian'    => 'it',
            'Japanese'   => 'ja',
            'Korean'     => 'ko',
            'Portuguese' => 'pt',
            'Russian'    => 'ru',
            'Chinese'    => 'zh',
        );
    }


    /**
     * Set network array with info.
     */
    private function setNetworks()
    {
        $this->networks = [
            'facebook'        => array(
                'color'      => '#3B5998',
                'color-rgba' => '59, 89, 152',
                'path'       => 'm21.7 16.7h5v5h-5v11.6h-5v-11.6h-5v-5h5v-2.1c0-2 0.6-4.5 1.8-5.9 1.3-1.3 2.8-2 4.7-2h3.5v5h-3.5c-0.9 0-1.5 0.6-1.5 1.5v3.5z',
                'selected'   => 'true',
            ),
            'twitter'         => array(
                'color'      => '#55acee',
                'color-rgba' => '85, 172, 238',
                'path'       => 'm31.5 11.7c1.3-0.8 2.2-2 2.7-3.4-1.4 0.7-2.7 1.2-4 1.4-1.1-1.2-2.6-1.9-4.4-1.9-1.7 0-3.2 0.6-4.4 1.8-1.2 1.2-1.8 2.7-1.8 4.4 0 0.5 0.1 0.9 0.2 1.3-5.1-0.1-9.4-2.3-12.7-6.4-0.6 1-0.9 2.1-0.9 3.1 0 2.2 1 3.9 2.8 5.2-1.1-0.1-2-0.4-2.8-0.8 0 1.5 0.5 2.8 1.4 4 0.9 1.1 2.1 1.8 3.5 2.1-0.5 0.1-1 0.2-1.6 0.2-0.5 0-0.9 0-1.1-0.1 0.4 1.2 1.1 2.3 2.1 3 1.1 0.8 2.3 1.2 3.6 1.3-2.2 1.7-4.7 2.6-7.6 2.6-0.7 0-1.2 0-1.5-0.1 2.8 1.9 6 2.8 9.5 2.8 3.5 0 6.7-0.9 9.4-2.7 2.8-1.8 4.8-4.1 6.1-6.7 1.3-2.6 1.9-5.3 1.9-8.1v-0.8c1.3-0.9 2.3-2 3.1-3.2-1.1 0.5-2.3 0.8-3.5 1z',
                'selected'   => 'true',
            ),
            'pinterest'       => array(
                'color'      => '#CB2027',
                'color-rgba' => '203, 32, 39',
                'path'       => 'm37.3 20q0 4.7-2.3 8.6t-6.3 6.2-8.6 2.3q-2.4 0-4.8-0.7 1.3-2 1.7-3.6 0.2-0.8 1.2-4.7 0.5 0.8 1.7 1.5t2.5 0.6q2.7 0 4.8-1.5t3.3-4.2 1.2-6.1q0-2.5-1.4-4.7t-3.8-3.7-5.7-1.4q-2.4 0-4.4 0.7t-3.4 1.7-2.5 2.4-1.5 2.9-0.4 3q0 2.4 0.8 4.1t2.7 2.5q0.6 0.3 0.8-0.5 0.1-0.1 0.2-0.6t0.2-0.7q0.1-0.5-0.3-1-1.1-1.3-1.1-3.3 0-3.4 2.3-5.8t6.1-2.5q3.4 0 5.3 1.9t1.9 4.7q0 3.8-1.6 6.5t-3.9 2.6q-1.3 0-2.2-0.9t-0.5-2.4q0.2-0.8 0.6-2.1t0.7-2.3 0.2-1.6q0-1.2-0.6-1.9t-1.7-0.7q-1.4 0-2.3 1.2t-1 3.2q0 1.6 0.6 2.7l-2.2 9.4q-0.4 1.5-0.3 3.9-4.6-2-7.5-6.3t-2.8-9.4q0-4.7 2.3-8.6t6.2-6.2 8.6-2.3 8.6 2.3 6.3 6.2 2.3 8.6z',
                'selected'   => 'true',
            ),
            'email'           => array(
                'color'      => '#7d7d7d',
                'color-rgba' => '125, 125, 125',
                'path'       => 'm33.4 13.4v-3.4l-13.4 8.4-13.4-8.4v3.4l13.4 8.2z m0-6.8q1.3 0 2.3 1.1t0.9 2.3v20q0 1.3-0.9 2.3t-2.3 1.1h-26.8q-1.3 0-2.3-1.1t-0.9-2.3v-20q0-1.3 0.9-2.3t2.3-1.1h26.8z',
                'selected'   => 'true',
            ),
            'sms'             => array(
                'color'      => '#ffbd00',
                'color-rgba' => '255, 189, 0',
                'path'       => 'M29.577,23.563 C27.233,23.563 25.935,22.138 25.935,22.138 L27.22,20.283 C27.22,20.283 28.349,21.315 29.605,21.315 C30.108,21.315 30.652,21.12 30.652,20.52 C30.652,19.334 26.158,19.376 26.158,16.306 C26.158,14.464 27.707,13.25 29.688,13.25 C31.839,13.25 32.898,14.38 32.898,14.38 L31.866,16.376 C31.866,16.376 30.861,15.497 29.661,15.497 C29.159,15.497 28.6,15.72 28.6,16.278 C28.6,17.534 33.094,17.311 33.094,20.464 C33.094,22.125 31.824,23.563 29.577,23.563 L29.577,23.563 Z M23.027,23.394 L22.721,18.901 C22.665,18.147 22.721,17.227 22.721,17.227 L22.692,17.227 C22.692,17.227 22.356,18.273 22.134,18.901 L21.088,21.79 L18.994,21.79 L17.947,18.901 C17.724,18.273 17.389,17.227 17.389,17.227 L17.361,17.227 C17.361,17.227 17.417,18.147 17.361,18.901 L17.055,23.394 L14.598,23.394 L15.422,13.417 L18.073,13.417 L19.524,17.631 C19.748,18.273 20.026,19.278 20.026,19.278 L20.055,19.278 C20.055,19.278 20.334,18.273 20.557,17.631 L22.008,13.417 L24.66,13.417 L25.469,23.394 L23.027,23.394 Z M10.548,23.563 C8.204,23.563 6.906,22.138 6.906,22.138 L8.19,20.283 C8.19,20.283 9.32,21.315 10.576,21.315 C11.078,21.315 11.623,21.12 11.623,20.52 C11.623,19.334 7.129,19.376 7.129,16.306 C7.129,14.464 8.678,13.25 10.66,13.25 C12.808,13.25 13.869,14.38 13.869,14.38 L12.836,16.376 C12.836,16.376 11.832,15.497 10.632,15.497 C10.129,15.497 9.571,15.72 9.571,16.278 C9.571,17.534 14.064,17.311 14.064,20.464 C14.064,22.125 12.795,23.563 10.548,23.563 L10.548,23.563 Z M32.814,6 L7.185,6 C5.437,6 4,7.438 4,9.213 L4,28.99 C4,30.756 5.426,32.203 7.185,32.203 L10.61,32.203 L12.445,34.295 C13.086,34.952 14.117,34.949 14.755,34.295 L16.59,32.203 L32.814,32.203 C34.562,32.203 36,30.764 36,28.99 L36,9.213 C36,7.446 34.574,6 32.814,6 L32.814,6 Z',
                'selected'   => 'true',
            ),
            'messenger'       => array(
                'color'      => '#448AFF',
                'color-rgba' => '68, 138, 255',
                'path'       => 'M25,2C12.3,2,2,11.6,2,23.5c0,6.3,2.9,12.2,8,16.3v8.8l8.6-4.5c2.1,0.6,4.2,0.8,6.4,0.8c12.7,0,23-9.6,23-21.5 C48,11.6,37.7,2,25,2z M27.3,30.6l-5.8-6.2l-10.8,6.1l12-12.7l5.9,5.9l10.5-5.9L27.3,30.6z',
                'selected'   => 'false',
                'url'        => 'messenger.com/',
                'viewbox'    => '50',
            ),
            'sharethis'       => array(
                'color'      => '#95D03A',
                'color-rgba' => '149, 208, 58',
                'path'       => 'm30 26.8c2.7 0 4.8 2.2 4.8 4.8s-2.1 5-4.8 5-4.8-2.3-4.8-5c0-0.3 0-0.7 0-1.1l-11.8-6.8c-0.9 0.8-2.1 1.3-3.4 1.3-2.7 0-5-2.3-5-5s2.3-5 5-5c1.3 0 2.5 0.5 3.4 1.3l11.8-6.8c-0.1-0.4-0.2-0.8-0.2-1.1 0-2.8 2.3-5 5-5s5 2.2 5 5-2.3 5-5 5c-1.3 0-2.5-0.6-3.4-1.4l-11.8 6.8c0.1 0.4 0.2 0.8 0.2 1.2s-0.1 0.8-0.2 1.2l11.9 6.8c0.9-0.7 2.1-1.2 3.3-1.2z',
                'selected'   => 'true',
            ),
            'linkedin'        => array(
                'color'      => '#0077b5',
                'color-rgba' => '0, 119, 181',
                'path'       => 'm13.3 31.7h-5v-16.7h5v16.7z m18.4 0h-5v-8.9c0-2.4-0.9-3.5-2.5-3.5-1.3 0-2.1 0.6-2.5 1.9v10.5h-5s0-15 0-16.7h3.9l0.3 3.3h0.1c1-1.6 2.7-2.8 4.9-2.8 1.7 0 3.1 0.5 4.2 1.7 1 1.2 1.6 2.8 1.6 5.1v9.4z m-18.3-20.9c0 1.4-1.1 2.5-2.6 2.5s-2.5-1.1-2.5-2.5 1.1-2.5 2.5-2.5 2.6 1.2 2.6 2.5z',
                'selected'   => 'false',
            ),
            'reddit'          => array(
                'color'      => '#ff4500',
                'color-rgba' => '255, 69, 0',
                'path'       => 'm40 18.9q0 1.3-0.7 2.3t-1.7 1.7q0.2 1 0.2 2.1 0 3.5-2.3 6.4t-6.5 4.7-9 1.7-8.9-1.7-6.4-4.7-2.4-6.4q0-1.1 0.2-2.1-1.1-0.6-1.8-1.6t-0.7-2.4q0-1.8 1.3-3.2t3.1-1.3q1.9 0 3.3 1.4 4.8-3.3 11.5-3.6l2.6-11.6q0-0.3 0.3-0.5t0.6-0.1l8.2 1.8q0.4-0.8 1.2-1.3t1.8-0.5q1.4 0 2.4 1t0.9 2.3-0.9 2.4-2.4 1-2.4-1-0.9-2.4l-7.5-1.6-2.3 10.5q6.7 0.2 11.6 3.6 1.3-1.4 3.2-1.4 1.8 0 3.1 1.3t1.3 3.2z m-30.7 4.4q0 1.4 1 2.4t2.4 1 2.3-1 1-2.4-1-2.3-2.3-1q-1.4 0-2.4 1t-1 2.3z m18.1 8q0.3-0.3 0.3-0.6t-0.3-0.6q-0.2-0.2-0.5-0.2t-0.6 0.2q-0.9 0.9-2.7 1.4t-3.6 0.4-3.6-0.4-2.7-1.4q-0.2-0.2-0.5-0.2t-0.6 0.2q-0.3 0.2-0.3 0.6t0.3 0.6q1 0.9 2.6 1.5t2.8 0.6 2 0.1 2-0.1 2.8-0.6 2.6-1.6z m-0.1-4.6q1.4 0 2.4-1t1-2.4q0-1.3-1-2.3t-2.4-1q-1.3 0-2.3 1t-1 2.3 1 2.4 2.3 1z',
                'selected'   => 'false',
            ),
            'tumblr'          => array(
                'color'      => '#32506d',
                'color-rgba' => '50, 80, 109',
                'path'       => 'm25.9 29.9v-3.5c-1.1 0.8-2.2 1.1-3.3 1.1-0.5 0-1-0.1-1.6-0.4-0.4-0.3-0.6-0.5-0.7-0.9-0.2-0.3-0.3-1.1-0.3-2.4v-5.5h5v-3.3h-5v-5.6h-3c-0.2 1.3-0.5 2.2-0.7 2.8-0.3 0.7-0.8 1.3-1.5 1.9-0.7 0.5-1.4 0.9-2.1 1.2v3h2.3v7.6c0 0.8 0.1 1.6 0.4 2.2 0.2 0.5 0.5 1 1.1 1.5 0.4 0.4 1 0.8 1.8 1.1 1 0.3 1.9 0.4 2.7 0.4 0.8 0 1.6-0.1 2.4-0.3 0.8-0.2 1.7-0.5 2.5-0.9z',
                'selected'   => 'false',
            ),
            'digg'            => array(
                'color'      => '#262626',
                'color-rgba' => '38, 38, 38',
                'path'       => 'm6.4 8.1h3.9v19.1h-10.3v-13.6h6.4v-5.5z m0 15.9v-7.2h-2.4v7.2h2.4z m5.5-10.4v13.6h4v-13.6h-4z m0-5.5v3.9h4v-3.9h-4z m5.6 5.5h10.3v18.3h-10.3v-3.1h6.4v-1.6h-6.4v-13.6z m6.4 10.4v-7.2h-2.4v7.2h2.4z m5.5-10.4h10.4v18.3h-10.4v-3.1h6.4v-1.6h-6.4v-13.6z m6.4 10.4v-7.2h-2.4v7.2h2.4z',
                'selected'   => 'false',
            ),
            'stumbleupon'     => array(
                'color'      => '#eb4924',
                'color-rgba' => '235, 73, 36',
                'path'       => 'm22.1 16.2v-2.5q0-0.8-0.7-1.5t-1.5-0.6-1.5 0.6-0.6 1.5v12.7q0 3.7-2.6 6.2t-6.3 2.6q-3.7 0-6.3-2.6t-2.6-6.3v-5.5h6.8v5.4q0 0.9 0.6 1.5t1.5 0.6 1.5-0.6 0.6-1.5v-12.8q0-3.6 2.7-6.1t6.2-2.5q3.7 0 6.3 2.5t2.6 6.1v2.8l-4 1.2z m11 4.6h6.8v5.5q0 3.7-2.6 6.3t-6.3 2.6q-3.7 0-6.3-2.6t-2.6-6.2v-5.6l2.7 1.3 4-1.2v5.6q0 0.9 0.6 1.5t1.5 0.6 1.5-0.6 0.7-1.5v-5.7z',
                'selected'   => 'false',
            ),
            'whatsapp'        => array(
                'color'      => '#25d366',
                'color-rgba' => '37, 211, 102',
                'path'       => 'm25 21.7q0.3 0 2.2 1t2 1.2q0 0.1 0 0.3 0 0.8-0.4 1.7-0.3 0.9-1.6 1.5t-2.2 0.6q-1.3 0-4.3-1.4-2.2-1-3.8-2.6t-3.3-4.2q-1.6-2.3-1.6-4.3v-0.2q0.1-2 1.7-3.5 0.5-0.5 1.2-0.5 0.1 0 0.4 0t0.4 0.1q0.4 0 0.6 0.1t0.3 0.6q0.2 0.5 0.8 2t0.5 1.7q0 0.5-0.8 1.3t-0.7 1q0 0.2 0.1 0.3 0.7 1.7 2.3 3.1 1.2 1.2 3.3 2.2 0.3 0.2 0.5 0.2 0.4 0 1.2-1.1t1.2-1.1z m-4.5 11.9q2.8 0 5.4-1.1t4.5-3 3-4.5 1.1-5.4-1.1-5.5-3-4.5-4.5-2.9-5.4-1.2-5.5 1.2-4.5 2.9-2.9 4.5-1.2 5.5q0 4.5 2.7 8.2l-1.7 5.2 5.4-1.8q3.5 2.4 7.7 2.4z m0-30.9q3.4 0 6.5 1.4t5.4 3.6 3.5 5.3 1.4 6.6-1.4 6.5-3.5 5.3-5.4 3.6-6.5 1.4q-4.4 0-8.2-2.1l-9.3 3 3-9.1q-2.4-3.9-2.4-8.6 0-3.5 1.4-6.6t3.6-5.3 5.3-3.6 6.6-1.4z',
                'selected'   => 'false',
            ),
            'vk'              => array(
                'color'      => '#4c6c91',
                'color-rgba' => '76, 108, 145',
                'path'       => 'm39.8 12.2q0.5 1.3-3.1 6.1-0.5 0.7-1.4 1.8-1.6 2-1.8 2.7-0.4 0.8 0.3 1.7 0.3 0.4 1.6 1.7h0.1l0 0q3 2.8 4 4.6 0.1 0.1 0.1 0.3t0.2 0.5 0 0.8-0.5 0.5-1.3 0.3l-5.3 0.1q-0.5 0.1-1.1-0.1t-1.1-0.5l-0.4-0.2q-0.7-0.5-1.5-1.4t-1.4-1.6-1.3-1.2-1.1-0.3q-0.1 0-0.2 0.1t-0.4 0.3-0.4 0.6-0.4 1.1-0.1 1.6q0 0.3-0.1 0.5t-0.1 0.4l-0.1 0.1q-0.4 0.4-1.1 0.5h-2.4q-1.5 0.1-3-0.4t-2.8-1.1-2.1-1.3-1.5-1.2l-0.5-0.5q-0.2-0.2-0.6-0.6t-1.4-1.9-2.2-3.2-2.6-4.4-2.7-5.6q-0.1-0.3-0.1-0.6t0-0.3l0.1-0.1q0.3-0.4 1.2-0.4l5.7-0.1q0.2 0.1 0.5 0.2t0.3 0.2l0.1 0q0.3 0.2 0.5 0.7 0.4 1 1 2.1t0.8 1.7l0.3 0.6q0.6 1.3 1.2 2.2t1 1.4 0.9 0.8 0.7 0.3 0.5-0.1q0.1 0 0.1-0.1t0.3-0.5 0.3-0.9 0.2-1.7 0-2.6q-0.1-0.9-0.2-1.5t-0.3-1l-0.1-0.2q-0.5-0.7-1.8-0.9-0.3-0.1 0.1-0.5 0.4-0.4 0.8-0.7 1.1-0.5 5-0.5 1.7 0.1 2.8 0.3 0.4 0.1 0.7 0.3t0.4 0.5 0.2 0.7 0.1 0.9 0 1.1-0.1 1.5 0 1.7q0 0.3 0 0.9t-0.1 1 0.1 0.8 0.3 0.8 0.4 0.6q0.2 0 0.4 0t0.5-0.2 0.8-0.7 1.1-1.4 1.4-2.2q1.2-2.2 2.2-4.7 0.1-0.2 0.2-0.4t0.3-0.2l0 0 0.1-0.1 0.3-0.1 0.4 0 6 0q0.8-0.1 1.3 0t0.7 0.4z',
                'selected'   => 'false',
            ),
            'weibo'           => array(
                'color'      => '#ff9933',
                'color-rgba' => '255, 153, 51',
                'path'       => 'm15.1 28.7q0.4-0.8 0.2-1.6t-1-1.1q-0.8-0.3-1.6 0t-1.4 1q-0.5 0.8-0.3 1.5t1 1.2 1.7 0 1.4-1z m2.1-2.7q0.1-0.3 0-0.6t-0.3-0.4q-0.4-0.2-0.7 0t-0.5 0.4q-0.3 0.7 0.3 1 0.3 0.1 0.7 0t0.5-0.4z m3.8 2.3q-1 2.3-3.5 3.4t-5 0.3q-2.4-0.8-3.3-2.9t0.2-4.1q1-2.1 3.4-3.1t4.7-0.5q2.4 0.7 3.5 2.7t0 4.2z m7-3.5q-0.2-2.2-2-3.8t-4.6-2.5-6.2-0.4q-4.9 0.5-8.2 3.1t-3 5.9q0.2 2.2 2 3.8t4.7 2.5 6.1 0.4q5-0.5 8.3-3.1t2.9-5.9z m6.9 0.1q0 1.5-0.8 3.1t-2.5 3-3.7 2.7-5.1 1.8-6 0.7-6.2-0.7-5.3-2.1-3.9-3.4-1.4-4.4q0-2.6 1.6-5.5t4.4-5.8q3.7-3.7 7.6-5.2t5.5 0.1q1.4 1.4 0.4 4.7-0.1 0.3 0 0.4t0.2 0.2 0.4 0 0.3-0.1l0.1-0.1q3.1-1.3 5.5-1.3t3.4 1.4q1 1.4 0 4 0 0.3-0.1 0.4t0.1 0.3 0.3 0.2 0.3 0.1q1.3 0.4 2.3 1t1.8 1.9 0.8 2.6z m-1.7-14q1 1.1 1.3 2.5t-0.2 2.6q-0.2 0.5-0.7 0.7t-0.9 0.1q-0.6-0.1-0.8-0.6t-0.1-1q0.5-1.4-0.5-2.5t-2.4-0.8q-0.6 0.1-1-0.2t-0.6-0.8q-0.1-0.5 0.2-1t0.8-0.5q1.4-0.3 2.7 0.1t2.2 1.4z m4.1-3.6q1.9 2.1 2.5 5t-0.3 5.4q-0.2 0.6-0.8 0.8t-1.1 0.1-0.9-0.7-0.1-1.2q0.6-1.8 0.2-3.8t-1.8-3.5q-1.4-1.6-3.3-2.2t-3.9-0.2q-0.6 0.2-1.1-0.2t-0.7-1 0.2-1.1 1-0.7q2.7-0.5 5.4 0.3t4.7 3z',
                'selected'   => 'false',
            ),
            'odnoklassniki'   => array(
                'color'      => '#d7772d',
                'color-rgba' => '215, 119, 45',
                'path'       => 'm19.8 20.2q-4.2 0-7.2-2.9t-2.9-7.2q0-4.2 2.9-7.1t7.2-3 7.1 3 3 7.1q0 4.2-3 7.2t-7.1 2.9z m0-15.1q-2.1 0-3.5 1.5t-1.5 3.5q0 2.1 1.5 3.5t3.5 1.5 3.5-1.5 1.5-3.5q0-2-1.5-3.5t-3.5-1.5z m11.7 16.4q0.3 0.6 0.3 1.1t-0.1 0.9-0.6 0.8-0.9 0.9-1.4 0.9q-2.6 1.6-7 2.1l1.6 1.6 5.9 6q0.7 0.7 0.7 1.6t-0.7 1.6l-0.2 0.3q-0.7 0.7-1.7 0.7t-1.6-0.7q-1.5-1.5-6-6l-6 6q-0.6 0.7-1.6 0.7t-1.6-0.7l-0.3-0.3q-0.7-0.6-0.7-1.6t0.7-1.6l7.6-7.6q-4.6-0.5-7.1-2.1-0.9-0.6-1.4-0.9t-0.9-0.9-0.6-0.8-0.1-0.9 0.3-1.1q0.2-0.5 0.6-0.8t1-0.5 1.2 0 1.5 0.8q0.1 0.1 0.3 0.3t1 0.5 1.5 0.7 2.1 0.5 2.5 0.3q2 0 3.9-0.6t2.6-1.1l0.9-0.6q0.7-0.5 1.4-0.8t1.3 0 0.9 0.5 0.7 0.8z',
                'selected'   => 'false',
            ),
            'xing'            => array(
                'color'      => '#1a7576',
                'color-rgba' => '26, 117, 118',
                'path'       => 'm17.8 14.9q-0.2 0.4-5.7 10.2-0.6 1-1.5 1h-5.3q-0.5 0-0.7-0.4t0-0.8l5.7-10q0 0 0 0l-3.6-6.2q-0.3-0.5-0.1-0.9 0.2-0.3 0.8-0.3h5.3q0.9 0 1.5 1z m18-14.3q0.3 0.3 0 0.8l-11.8 20.8v0.1l7.5 13.7q0.3 0.4 0.1 0.8-0.3 0.3-0.8 0.3h-5.3q-0.9 0-1.5-1l-7.5-13.8 11.8-21.1q0.6-1 1.4-1h5.4q0.5 0 0.7 0.4z',
                'selected'   => 'false',
            ),
            'print'           => array(
                'color'      => '#222222',
                'color-rgba' => '34, 34, 34',
                'path'       => 'm30 5v6.6h-20v-6.6h20z m1.6 15c1 0 1.8-0.7 1.8-1.6s-0.8-1.8-1.8-1.8-1.6 0.8-1.6 1.8 0.7 1.6 1.6 1.6z m-5 11.6v-8.2h-13.2v8.2h13.2z m5-18.2c2.8 0 5 2.2 5 5v10h-6.6v6.6h-20v-6.6h-6.6v-10c0-2.8 2.2-5 5-5h23.2z',
                'selected'   => 'false',
            ),
            'blogger'         => array(
                'color'      => '#ff8000',
                'color-rgba' => '235, 73, 36',
                'path'       => 'M27.5,30 L12.5,30 C11.125,30 10,28.875 10,27.5 C10,26.125 11.125,25 12.5,25 L27.5,25 C28.875,25 30,26.125 30,27.5 C30,28.875 28.875,30 27.5,30 M12.5,10 L20,10 C21.375,10 22.5,11.125 22.5,12.5 C22.5,13.875 21.375,15 20,15 L12.5,15 C11.125,15 10,13.875 10,12.5 C10,11.125 11.125,10 12.5,10 M37.41375,15 L35.21875,15 L35.17125,15 C33.7975,15 32.59375,13.8375 32.5,12.5 C32.5,5.365 26.7475,0 19.5625,0 L13.0075,0 C5.8275,0 0.005,5.78125 0,12.91625 L0,27.08875 C0,34.22375 5.8275,40 13.0075,40 L27.0075,40 C34.1925,40 40,34.22375 40,27.08875 L40,17.93375 C40,16.5075 38.85,15 37.41375,15',
                'selected'   => 'false',
            ),
            'flipboard'       => array(
                'color'      => '#e12828',
                'color-rgba' => '37, 211, 102',
                'path'       => 'M0,0 L13.3333333,0 L13.3333333,13.3333333 L0,13.3333333 L0,0 Z M0,13.3333333 L13.3333333,13.3333333 L13.3333333,26.6666667 L0,26.6666667 L0,13.3333333 Z M13.3333333,13.3333333 L26.6666667,13.3333333 L26.6666667,26.6666667 L13.3333333,26.6666667 L13.3333333,13.3333333 Z M0,26.6666667 L13.3333333,26.6666667 L13.3333333,40 L0,40 L0,26.6666667 Z M13.3333333,0 L26.6666667,0 L26.6666667,13.3333333 L13.3333333,13.3333333 L13.3333333,0 Z M26.6666667,0 L40,0 L40,13.3333333 L26.6666667,13.3333333 L26.6666667,0 Z',
                'selected'   => 'false',
            ),
            'meneame'         => array(
                'color'      => '#ff6400',
                'color-rgba' => '255, 100, 0',
                'path'       => 'M37.6371624,10.0104081 C36.6268087,11.0735024 35.3851257,11.7323663 34.1607384,12.4190806 C33.0545379,13.0405452 31.9144669,13.5911899 30.8702425,14.343154 C29.735216,15.1635509 28.7926035,16.1645784 28.4798406,17.69397 C28.2268918,18.9376949 28.4322776,20.1686881 28.6600035,21.3837667 C29.1586946,24.0598043 30.1380603,26.5496412 31.1094988,29.042661 C31.6074692,30.3293553 32.1421928,31.6009307 32.5421545,32.93139 C33.1842552,35.0758805 32.648811,36.7294059 30.9206881,37.9357315 C29.6761225,38.7998935 28.2831027,39.1786606 26.852609,39.4309068 C26.0166529,39.5765253 25.1691665,39.6234733 24.305105,39.6855402 C24.1220595,39.6171075 23.7847945,39.8407074 23.727863,39.5614064 C23.6774173,39.2876755 24.0290954,39.1977581 24.2373638,39.1006792 C25.4934598,38.5261626 26.7574829,37.975518 28.0128583,37.4010014 C28.7954861,37.043719 29.5579356,36.6363056 30.2591298,36.1079413 C30.5012688,35.9273108 30.7232295,35.7188297 30.9178055,35.4801109 C31.2276857,35.0973651 31.3033542,34.6931347 31.1231912,34.1934167 C30.1661657,31.5539827 29.1017631,28.9583137 28.2629244,26.2671573 C27.5905563,24.0940206 27.0529501,21.8930335 26.9117024,19.5838271 C26.7430699,16.8608415 27.5992042,14.7410187 29.7272888,13.2832426 C30.7318773,12.5965283 31.8099723,12.0832829 32.8880674,11.5676503 C34.2227144,10.9302712 35.5429484,10.2777731 36.7140075,9.29266029 C38.4651913,7.81578675 39.0395507,5.83919521 38.8896551,3.5045255 C38.8226345,2.45416285 38.6619292,1.42210198 38.445013,0.399589849 C38.4197902,0.284209103 38.2763805,0.113127308 38.4651913,0.0232098993 C38.6367064,-0.0571587581 38.7267879,0.0852767832 38.8002943,0.234873888 C39.3710505,1.38151978 39.8164133,2.56556495 39.9569404,3.88408837 C40.2243022,6.41052884 39.2218756,8.33778516 37.6371624,10.0104081 M23.0864829,39.5518577 C23.0021667,39.6791743 22.9092026,39.8407074 22.7398494,39.7754576 C22.5877919,39.7165736 22.6079701,39.539126 22.6022049,39.3966905 C22.5596865,38.3829313 22.7088614,37.3882697 22.7938983,36.3872423 C22.9149678,34.9517467 23.1340459,33.5218212 23.0129764,32.067228 C22.9762232,31.6072965 22.7852505,31.3271998 22.4133942,31.1314504 C21.3216068,30.5529552 20.1505477,30.3635717 18.9650755,30.236255 C17.5151242,30.0810878 16.0601283,30.0962066 14.6072944,29.9999234 C14.5172129,29.9935575 14.4271315,29.9720728 14.33705,29.9593412 C17.1382234,29.4500744 19.9365141,29.1731607 22.7571451,29.5463577 C24.0968367,29.7230096 24.3533887,30.0277739 24.3843767,31.5229492 C24.4348223,33.8886524 24.1335899,36.2105904 23.5282424,38.4792146 C23.4266305,38.8587775 23.3005165,39.2287916 23.0864829,39.5518577 M21.7576011,39.7070249 C20.0035347,39.8629878 18.250189,39.9529052 16.4903574,39.9807557 C14.0192426,40.0213379 11.5358766,40.0396397 9.09574982,39.586074 C5.13216519,38.8492287 2.06074743,36.6705219 0.568998308,32.3711966 C-0.230925135,30.0651732 -0.129313238,27.6620707 0.512787472,25.318648 C1.47774017,21.803116 2.93922192,18.5772295 5.31809334,15.9377955 C5.93425059,15.2542641 6.62679692,14.6662201 7.30781283,14.0638531 C7.37267149,14.0041734 7.43753014,13.914256 7.57229202,13.961204 C7.53914204,14.1848039 7.42599972,14.3527028 7.31646065,14.5269675 C5.46726826,17.4393367 3.88471701,20.5068731 2.69131771,23.8202898 C1.8611269,26.127109 1.66078571,28.4832634 2.20992234,30.8919359 C2.88805564,33.8759207 4.68752306,35.7164425 7.22061397,36.7978386 C9.05899658,37.5840192 10.9759302,37.9603991 12.9180867,38.2150325 C15.6205308,38.5667448 18.3345053,38.8022807 21.0398319,39.0919261 C21.3014285,39.1197767 21.5579805,39.1977581 21.8109293,39.2749439 C21.9348814,39.3091602 22.0927041,39.365657 22.0754085,39.5486747 C22.0559509,39.7603387 21.8786706,39.6950889 21.7576011,39.7070249 M11.1424008,3.1782765 C12.8453009,1.61785138 14.8717736,0.862704291 17.0229191,0.440172043 C20.3977312,-0.219487531 23.5902185,0.505421844 26.6782112,1.97831674 C28.265807,2.73664675 29.797192,3.63184219 31.4359541,4.25967259 C31.79628,4.39653803 32.1623711,4.5206718 32.5421545,4.58353441 C33.2289356,4.68857067 33.7917647,4.43075439 34.242172,3.86817379 C34.8338271,3.13132847 35.0420955,2.24568178 35.0420955,1.2788707 C35.2078454,1.27011768 35.241716,1.41255322 35.2950442,1.52156814 C35.7987798,2.54408026 35.6553701,4.20715446 34.9714716,5.07131646 C34.2537024,5.97844784 33.3024421,6.14077661 32.2913677,6.01027701 C31.1426488,5.86386282 30.0508614,5.49384871 29.0037544,4.94399977 C27.0356545,3.90557306 25.0372872,2.94831074 22.8976721,2.41358066 C20.2204509,1.74516806 17.5634079,1.86373173 14.9647377,2.81781113 C11.1784334,4.20397154 9.1887139,8.35688266 10.2185252,12.618013 C10.8152249,15.0768164 12.0684383,17.0136215 14.0250078,18.3838673 C14.868891,18.9774814 15.8035763,19.1612948 16.7894279,19.1485632 C17.7853686,19.1334443 18.7568072,18.9185974 19.7282457,18.7132993 C19.8968783,18.6790829 20.0604662,18.6329306 20.3220628,18.7196651 C19.4716937,19.3013432 18.6242073,19.6307751 17.7291578,19.8384605 C16.4175716,20.1432248 15.1031028,20.1432248 13.790796,19.8639238 C12.6593727,19.6275922 11.7304526,18.9496308 10.9816955,18.011466 C9.4978735,16.1462766 8.4284263,14.0415726 8.11061888,11.5461656 C7.67678652,8.12373398 8.76352936,5.35380035 11.1424008,3.1782765',
                'selected'   => 'false',
            ),
            'mailru'          => array(
                'color'      => '#168de2',
                'color-rgba' => '22, 141, 226',
                'path'       => 'M26.9076184,19.8096616 C26.680007,15.199769 23.3977769,12.428026 19.4332382,12.428026 L19.2839504,12.428026 C14.7091187,12.428026 12.1717509,16.1777412 12.1717509,20.4369883 C12.1717509,25.2068628 15.2416138,28.2191267 19.2660779,28.2191267 C23.7536497,28.2191267 26.7047131,24.7932107 26.9181316,20.7410637 L26.9076184,19.8096616 Z M19.3065538,8.30410621 C22.3632752,8.30410621 25.2370663,9.71216703 27.347597,11.9168506 L27.347597,11.9256167 C27.347597,10.8665577 28.0309569,10.0688392 28.9803015,10.0688392 L29.2200031,10.0671956 C30.7049967,10.0671956 31.0093547,11.5316884 31.0093547,11.9951979 L31.016714,28.4558125 C30.9121073,29.5324037 32.0838067,30.0885055 32.7335243,29.3981722 C35.2693151,26.6817654 38.3029074,15.4348111 31.1570656,8.91773583 C24.496935,2.84225472 15.561216,3.84378592 10.8087108,7.25764856 C5.75657856,10.8895689 2.52376063,18.9264732 5.66406215,26.4752133 C9.08716952,34.7109994 18.8828707,37.1655178 24.7045713,34.7170261 C27.6524807,33.4766176 29.0144695,37.630671 25.9524915,38.9872308 C21.3261451,41.0423421 8.45006784,40.8352421 2.43492385,29.9750936 C-1.62896484,22.6416718 -1.41239232,9.73846544 9.36577009,3.05373779 C17.6112956,-2.05965974 28.4819745,-0.643380663 35.0369728,6.49225518 C41.8889698,13.9511423 41.4894671,27.9183387 34.8056817,33.3517002 C31.7778718,35.8177242 27.2803124,33.4163505 27.3092238,29.8211384 L27.2776841,28.6453802 C25.169256,30.8265047 22.3632752,32.0986904 19.3065538,32.0986904 C13.2661781,32.0986904 7.95174078,26.5590395 7.95174078,20.2655007 C7.95174078,13.9073117 13.2661781,8.30410621 19.3065538,8.30410621 L19.3065538,8.30410621 Z',
                'selected'   => 'false',
            ),
            'delicious'       => array(
                'color'      => '#205cc0',
                'color-rgba' => '32, 92, 192',
                'path'       => 'm35.9 30.7v-10.7h-15.8v-15.7h-10.7q-2 0-3.5 1.4t-1.5 3.6v10.7h15.7v15.7h10.8q2 0 3.5-1.4t1.5-3.6z m1.4-21.4v21.4q0 2.7-1.9 4.6t-4.5 1.8h-21.5q-2.6 0-4.5-1.8t-1.9-4.6v-21.4q0-2.7 1.9-4.6t4.5-1.8h21.5q2.6 0 4.5 1.8t1.9 4.6z',
                'selected'   => 'false',
            ),
            'buffer'          => [
                'color'      => '#323b43',
                'color-rgba' => '50, 59, 67',
                'path'       => [
                    'M0.496,10.1161254 L19.6484848,18.7835897 C19.8706667,18.8841026 20.1295758,18.8841026 20.3517576,18.7835897 L39.5042424,10.1160114 C40.1164848,9.83920228 40.1164848,9.01960114 39.5042424,8.74267806 L20.3515152,0.0754415954 C20.1294545,-0.0250712251 19.8705455,-0.0250712251 19.6483636,0.0754415954 L0.496,8.74279202 C-0.116242424,9.0197151 -0.116242424,9.83920228 0.496,10.1161254 Z',
                    'M0.496,20.6295157 L19.6484848,29.297094 C19.8706667,29.3976068 20.1295758,29.3976068 20.3517576,29.297094 L39.5042424,20.6296296 C40.1164848,20.3528205 40.1164848,19.5332194 39.5042424,19.2560684 L35.4635152,17.4276923 L22.6294545,23.2353276 C21.8141818,23.6041026 20.9048485,23.7989744 20.0001212,23.7989744 C19.0953939,23.7989744 18.186303,23.6041026 17.3695758,23.2347578 L4.5369697,17.4272365 L0.495878788,19.2559544 C-0.116242424,19.5332194 -0.116242424,20.3527066 0.496,20.6295157 Z',
                    'M39.5042424,29.7696866 L35.4635152,27.9409687 L22.6294545,33.7489459 C21.8141818,34.1177208 20.9048485,34.3125926 20.0001212,34.3125926 C19.0953939,34.3125926 18.186303,34.1177208 17.3695758,33.7483761 L4.5369697,27.9409687 L0.495878788,29.7698006 C-0.116363636,30.0469516 -0.116363636,30.8662108 0.495878788,31.1433618 L19.6483636,39.8108262 C19.8705455,39.911339 20.1294545,39.911339 20.3516364,39.8108262 L39.5041212,31.1433618 C40.1164848,30.8660969 40.1164848,30.0467236 39.5042424,29.7696866 Z'
                ],
                'selected'   => 'false',
            ],
            'diaspora'        => [
                'color'      => '#000000',
                'color-rgba' => '0, 0, 0',
                'path'       => 'M25.6574359,36.4933333 L21.8305641,31.0666667 C20.8098462,29.6166667 19.9811282,28.4883333 19.9368205,28.4883333 C19.8925128,28.4883333 18.2859487,30.6783333 16.1362051,33.6766667 C14.0882051,36.5333333 12.3946667,38.8666667 12.3700513,38.8666667 C12.3158974,38.8666667 4.98871795,33.6266667 4.96902564,33.5716667 C4.95917949,33.545 6.61825641,31.075 8.64820513,28.0833333 C10.6830769,25.0916667 12.3470769,22.6 12.3470769,22.54 C12.3470769,22.4483333 11.6775385,22.2183333 6.50994872,20.465 L0.615384615,18.4666667 C0.566153846,18.4366667 0.871384615,17.4 1.91671795,14.05 C2.67158974,11.64 3.30174359,9.65 3.31979487,9.625 C3.34276923,9.59833333 6.12594872,10.4983333 9.50974359,11.6333333 C12.8951795,12.7666667 15.6882051,13.69 15.7259487,13.69 C15.7620513,13.69 15.8014359,13.64 15.8129231,13.5733333 C15.8293333,13.5233333 15.8621538,10.5966667 15.8785641,7.07333333 C15.9113846,3.57333333 15.9442051,0.673333333 15.9606154,0.623333333 C15.9934359,0.573333333 16.9452308,0.573333333 20.4406154,0.573333333 C22.8758974,0.573333333 24.8927179,0.598333333 24.9255385,0.623333333 C24.9665641,0.65 25.0322051,2.6 25.1487179,6.92333333 C25.3292308,14.0483333 25.3292308,14.1483333 25.4441026,14.1483333 C25.4851282,14.1483333 28.1682051,13.2483333 31.4010256,12.115 C34.6387692,11.015 37.2955897,10.115 37.3169231,10.1383333 C37.3661538,10.2116667 40.0246154,18.9883333 40.0001699,19.0133333 C39.9721026,19.04 37.2644103,19.9883333 33.9938462,21.1133333 C29.4646154,22.6633333 28.0090256,23.1883333 28.0090256,23.2633333 C27.9860513,23.3133333 29.5351795,25.6883333 31.5306667,28.6133333 C33.473641,31.5133333 35.0473846,33.8883333 35.0473846,33.9133333 C35.0227692,33.99 27.7612308,39.4166667 27.6906667,39.4166667 C27.6660513,39.4166667 26.7306667,38.14 25.6475897,36.5916667 L25.6557949,36.48 L25.6574359,36.4933333 Z',
                'selected'   => 'false',
            ],
            'douban'          => [
                'color'         => '#2e963d',
                'color-rgba'    => '46, 150, 61',
                'path'          => [
                    'M13.506489,14.0556989 L15.1318414,14.0556989 L15.1318414,4.94654014 L1.37779154,4.94654014 L1.37779154,14.0552298 L2.82468638,14.0552298 L4.05216045,19.2499414 L0,19.2499414 L0,21.3127199 L16.4209385,21.3127199 L16.4209385,19.2499414 L12.3531051,19.2499414 L13.506489,14.0556989 Z M4.14797894,7.29220597 L12.4482112,7.29220597 L12.4482112,11.6462309 L4.14797894,11.6462309 L4.14797894,7.29220597 Z M10.0313768,19.2504105 L6.3738888,19.2504105 L5.14605854,14.0556989 L11.1851169,14.0556989 L10.0313768,19.2504105 Z M22.5668577,12.9316558 L21.3589747,13.2431603 L21.3589747,10.7084337 L23,10.7084337 L23,8.43923657 L22.3392442,8.43923657 L22.564008,3.62733766 L22.8564503,3.62733766 L22.8564503,1.7977183 L21.1847917,1.7977183 L21.1847917,0 L19.7959579,0 L19.7959579,1.7977183 L18.1392597,1.7977183 L18.1392597,3.62733766 L18.4220845,3.62733766 L18.6472046,8.43923657 L17.7691653,8.43923657 L17.7691653,10.7084337 L19.6356667,10.7084337 L19.6356667,13.5673313 L18.0576893,13.9097985 L18.0576893,16.1226997 L19.6139383,15.7525536 C19.4561406,18.0062693 18.4487997,21.0556349 18.4381136,21.0889433 L20.016091,22 C20.0698776,21.8376799 21.317299,18.0630344 21.3568375,15.3256424 L22.5665014,15.0788783 L22.5665014,12.9316558 L22.5668577,12.9316558 Z M20.9472045,8.43923657 L20.038888,8.43923657 L19.813768,3.62733766 L21.1716122,3.62733766 L20.9472045,8.43923657 Z',
                    'M29.5663749,13.8593243 L29.5671009,1.92420235 L29.9776935,1.79704828 L28.819975,0 L23.3366948,1.61077461 L23.3366948,15.2552039 C23.3366948,16.9448046 22.3717478,20.12741 22,21.2112694 L23.3762656,22 C23.4358033,21.8259256 24.8414748,17.703037 24.8414748,15.2552039 L24.8414748,3.17228288 L25.6481385,2.94096571 L25.6481385,19.113931 L24.5336211,19.1176847 L24.5336211,21.4003583 L24.5485056,21.573025 L28.1298456,21.0615936 L28.1298456,19.1040778 L27.1540076,19.1078314 L27.1540076,2.50836034 L28.061958,2.24748336 L28.061958,13.8048968 C28.051793,14.2018427 27.9661167,18.873699 29.4189827,21.854547 C29.9442943,21.6682733 30.471058,21.498891 31,21.3403003 L30.7930701,21.0160809 C29.4505668,18.5330575 29.5645597,13.9048371 29.5663749,13.8593243 Z',
                    'M35,10.8394923 L35,8.54252289 L34.2545706,8.54252289 L34.4745496,3.81799344 L34.9085887,3.81799344 L34.9085887,1.7171473 L33.1757202,1.7171473 L33.1757202,0 L31.8124425,0 L31.8124425,1.7171473 L30.0795739,1.7171473 L30.0795739,3.81799344 L30.4702091,3.81799344 L30.7815994,8.54252289 L30,8.54252289 L30,10.8394923 L31.7045903,10.8394923 L31.7045903,13.6293818 L30.2561489,13.6293818 L30.2561489,15.9287256 L31.7045903,15.9287256 L31.7045903,22 L33.2954098,22 L33.2954098,15.9287256 L34.74418,15.9287256 L34.74418,13.6293818 L33.2954098,13.6293818 L33.2954098,10.8394923 L35,10.8394923 Z M32.8817572,8.54299774 L32.1540839,8.54299774 L31.8436802,3.81846832 L33.102065,3.81846832 L32.8817572,8.54299774 Z',
                ],
                'selected'      => 'false',
                'shape'         => '<rect width="16" height="2"></rect>',
                'viewbox-total' => '0 -10 45 45',
            ],
            'evernote'        => [
                'color'      => '#5ba525',
                'color-rgba' => '91, 165, 37',
                'path'       => 'M16.0001116,1.59985116 C13.7938628,1.59985116 12.0001116,3.39360233 12.0001116,5.59985116 L12.0001116,9.59985116 C12.0001116,10.0404767 11.6407372,10.3998512 11.2001116,10.3998512 L7.20011162,10.3998512 C5.47198605,10.3998512 4.05011163,11.7936023 4.03136279,13.5092256 C3.99386279,17.0311 4.37823721,20.4248512 5.05011162,22.5842256 C5.82198605,25.0436 7.35948605,26.6373512 9.37511162,27.0717256 L15.0313628,28.3592256 C15.9001116,28.5436 16.7844884,28.3686 17.5157372,27.8654744 C18.2469884,27.3623488 18.7282372,26.6029744 18.8719884,25.6529744 L19.0001139,23.7842256 C19.9188651,24.9342256 21.4407395,25.5998512 23.2001139,25.5998512 L25.6001139,25.5998512 C27.3657395,25.5998512 28.8001139,27.0342256 28.8001139,28.7998512 L28.8001139,31.1998512 C28.8001139,32.5217256 27.7219884,33.5998512 26.4001139,33.5998512 L24.0844884,33.5998512 C23.6563628,33.5998512 23.2688628,33.3123512 23.2126139,32.9404767 C23.1719884,32.6967279 23.2344884,32.4654767 23.3876139,32.2842279 C23.5407395,32.1029767 23.7657395,31.9998512 24.0001139,31.9998512 L25.6001139,31.9998512 C26.0438651,31.9998512 26.4001139,31.640479 26.4001139,31.1998512 L26.4001139,27.9998512 C26.4001139,27.5592279 26.0438651,27.1998512 25.6001139,27.1998512 L23.2001139,27.1998512 C20.5532395,27.1998512 18.4001139,29.352979 18.4001139,31.9998512 L18.4001139,33.5998512 C18.4001139,36.2467279 20.5532395,38.3998512 23.2001139,38.3998512 L27.2001139,38.3998512 C35.2001139,38.3998512 35.2001139,27.052979 35.2001139,21.5998512 C35.2001139,16.0748535 35.0688651,12.4592279 34.3751139,9.17485349 C33.8251139,6.58110465 32.2594884,5.05610465 29.6813651,4.64360465 C29.4719907,4.62173023 24.9094907,4.15923023 22.2719907,4.02485349 C21.8282395,2.69985349 20.3126162,1.59985116 18.7251162,1.59985116 L16.0001116,1.59985116 Z M10.7626116,3.63735116 L5.11261162,9.28735116 C5.74386279,8.97797674 6.45323721,8.79985116 7.20011162,8.79985116 L10.4001116,8.79985116 L10.4001116,5.59985116 C10.4001116,4.90922558 10.5313628,4.24985116 10.7626116,3.63735116 Z M27.6001116,15.9998512 C28.7063628,15.9998512 29.6001116,16.8967256 29.6001116,17.9998512 C29.6001116,18.4748512 29.4282372,18.9029767 29.1532372,19.2467256 C28.5469884,18.7436 27.3688628,18.3998512 26.0001116,18.3998512 C25.8782372,18.3998512 25.7626116,18.4092256 25.6469861,18.4154767 C25.6157349,18.2811023 25.6001116,18.1436023 25.6001116,17.9998512 C25.6001116,16.8936023 26.4938628,15.9998512 27.6001116,15.9998512 L27.6001116,15.9998512 Z',
                'selected'   => 'false',
            ],
            'googlebookmarks' => [
                'color'         => '#4285F4',
                'color-rgba'    => '66, 133, 244',
                'path'          => [
                    'M33.0853326,13.4792117 L31.7441853,13.4792117 L31.7441853,13.4108566 L16.7441853,13.4108566 L16.7441853,20.0775233 L26.1647605,20.0775233 C24.787807,23.9577302 21.0964,26.7441899 16.7441853,26.7441899 C11.2200977,26.7441899 6.74418528,22.2682767 6.74418528,16.7441899 C6.74418528,11.2201 11.2200977,6.74418992 16.7441853,6.74418992 C19.2930139,6.74418992 21.6139768,7.70447442 23.3783,9.27674183 L28.0918418,4.5632 C25.1165814,1.78976279 21.1354605,0.0775232558 16.7441853,0.0775232558 C7.53845582,0.0775232558 0.0775186047,7.53846047 0.0775186047,16.7441899 C0.0775186047,25.9499209 7.53845582,33.4108566 16.7441853,33.4108566 C25.9499163,33.4108566 33.4108519,25.9499209 33.4108519,16.7441899 C33.4108519,15.6276535 33.2969209,14.5371605 33.0853326,13.4792162 L33.0853326,13.4792117 Z',
                    'M1.99809303,8.98702325 L7.47660698,13.0039512 C8.95772792,9.33533025 12.5449674,6.7441876 16.7441861,6.7441876 C19.2930139,6.7441876 21.6139767,7.70447209 23.3783,9.2767395 L28.0918418,4.56319767 C25.1165814,1.78976047 21.1354605,0.0775209302 16.7441861,0.0775209302 C10.3411907,0.0775209302 4.79106047,3.69080232 1.99809303,8.98702558 L1.99809303,8.98702325 Z M16.7441861,33.4108535 C21.0475721,33.4108535 24.9603326,31.7637186 27.9193163,29.0846814 L22.7598117,24.7194465 C21.0866349,25.9857232 19.0065558,26.7441861 16.7441861,26.7441861 C12.4082488,26.7441861 8.72986275,23.9805139 7.34314418,20.123093 L1.90694651,24.3092907 C4.66410697,29.7064263 10.2663209,33.4108535 16.7441861,33.4108535 L16.7441861,33.4108535 Z',
                    'M33.0853326,13.4792117 L31.7441861,13.4792117 L31.7441861,13.4108535 L16.7441861,13.4108535 L16.7441861,20.0775202 L26.1647605,20.0775202 C25.5039535,21.9427558 24.3027814,23.5475744 22.7565582,24.7194488 C22.7598139,24.7194488 22.7598139,24.7194488 22.7598139,24.7194488 L27.9193186,29.0846837 C27.5547349,29.4134605 33.4108527,25.0775202 33.4108527,16.7441868 C33.4108527,15.6276512 33.2969232,14.5371582 33.0853349,13.4792139 L33.0853326,13.4792117 Z',
                ],
                'selected'      => 'false',
                'viewbox-total' => '-2 -4 48 48',
            ],
            'gmail'           => [
                'color'      => '#D44638',
                'color-rgba' => '212, 70, 56',
                'path'       => 'M4.73110465,6.27906977 C2.66164441,6.27906977 0.976744186,8.09899684 0.976744186,10.3343023 L0.976744186,10.7180012 L21,26.1046512 L41.0232558,10.7180012 L41.0232558,10.3343023 C41.0232558,8.09899684 39.3383556,6.27906977 37.2688954,6.27906977 L4.73110465,6.27906977 Z M5.44156644,8.08139535 L36.5551764,8.08139535 L21,19.7965116 L5.44156644,8.08139535 Z M0.976744186,12.6118498 L0.976744186,34.6656977 C0.976744186,36.9010031 2.66164441,38.7209302 4.73110465,38.7209302 L37.2688954,38.7209302 C39.3383556,38.7209302 41.0232558,36.9010031 41.0232558,34.6656977 L41.0232558,12.6118498 L36.0174419,16.4593932 L36.0174419,36.9186047 L5.98255814,36.9186047 L5.98255814,16.4593932 L0.976744186,12.6118498 Z',
                'selected'   => 'false',
            ],
            'hackernews'      => [
                'color'      => '#ff4000',
                'color-rgba' => '255, 64, 0',
                'path'       => 'M0.212093023,0.212093023 L0.212093023,40.532093 L40.532093,40.532093 L40.532093,0.212093023 L0.212093023,0.212093023 Z M38.612093,38.612093 L2.13209302,38.612093 L2.13209302,2.13209302 L38.612093,2.13209302 L38.612093,38.612093 Z M36.692093,4.05209302 L4.05209302,4.05209302 L4.05209302,36.692093 L36.692093,36.692093 L36.692093,4.05209302 Z M22.292093,23.252093 L22.292093,30.932093 L18.452093,30.932093 L18.452093,23.252093 L11.732093,10.772093 L14.784893,10.772093 L20.372093,20.554493 L25.959293,10.772093 L29.012093,10.772093 L22.292093,23.252093 Z',
                'selected'   => 'false',
            ],
            'instapaper'      => [
                'color'      => '#000000',
                'color-rgba' => '0, 0, 0',
                'path'       => 'M34.4400814,3.3600814 L7.56008139,3.3600814 C5.24168139,3.3600814 3.3600814,5.24168139 3.3600814,7.56008139 L3.3600814,34.4400814 C3.3600814,36.7584814 5.24168139,38.6400814 7.56008139,38.6400814 L34.4400814,38.6400814 C36.7584814,38.6400814 38.6400814,36.7584814 38.6400814,34.4400814 L38.6400814,7.56008139 C38.6400814,5.24168139 36.7584814,3.3600814 34.4400814,3.3600814 Z M27.7116814,10.7184814 C27.7116814,10.8696814 27.4932814,11.0208814 27.3420814,11.0208814 C26.1912814,11.1804814 23.5200814,11.6844814 23.5200814,12.6000814 L23.5200814,29.4000814 C23.5200814,31.0044814 27.3252814,31.0044814 27.4008814,31.0044814 C27.5604814,31.0044814 27.7116814,31.1556814 27.7116814,31.3068814 L27.7116814,32.4576814 C27.7116814,32.6088814 27.5604814,32.7600814 27.4008814,32.7600814 L14.5992814,32.7600814 C14.4396814,32.7600814 14.2884814,32.6088814 14.2884814,32.4576814 L14.2884814,31.3068814 C14.2884814,31.1556814 14.4396814,31.0044814 14.5992814,31.0044814 C14.5992814,31.0044814 14.6244814,31.0044814 14.6748814,31.0044814 C15.2124814,31.0044814 18.4800814,30.9372814 18.4800814,29.4000814 L18.4800814,12.6000814 C18.4800814,11.8356814 15.9600814,11.2224814 14.5824814,10.9956814 C14.4312814,10.9956814 14.2800814,10.8444814 14.2800814,10.6932814 L14.2800814,9.54248139 C14.2800814,9.39128139 14.4312814,9.24008139 14.5824814,9.24008139 L27.4176814,9.26528139 C27.5688814,9.26528139 27.7200814,9.42488139 27.7200814,9.57608139 L27.7116814,10.7184814 Z',
                'selected'   => 'false',
            ],
            'line'            => [
                'color'      => '#00c300',
                'color-rgba' => '0, 195, 0',
                'path'       => 'M4.69883721,0.198837209 C2.21483721,0.198837209 0.198837209,2.21483721 0.198837209,4.69883721 L0.198837209,33.4988372 C0.198837209,35.9828372 2.21483721,37.9988372 4.69883721,37.9988372 L33.4988372,37.9988372 C35.9828372,37.9988372 37.9988372,35.9828372 37.9988372,33.4988372 L37.9988372,4.69883721 C37.9988372,2.21483721 35.9828372,0.198837209 33.4988372,0.198837209 L4.69883721,0.198837209 Z M19.0988372,6.49883721 C26.5418372,6.49883721 32.5988372,11.3221334 32.5988372,17.2531334 C32.5988372,19.6201334 31.6622041,21.7635384 29.7002041,23.8695384 C28.2872041,25.4715384 25.9933291,27.2349759 23.8783291,28.6929759 C21.7633291,30.1329759 19.8188372,31.266484 19.0988372,31.563484 C18.8108372,31.680484 18.5948372,31.7339922 18.4238372,31.7339922 C17.8298372,31.7339922 17.8831334,31.1055384 17.9281334,30.8445384 C17.9641334,30.6465384 18.1267666,29.7089922 18.1267666,29.7089922 C18.1717666,29.3759922 18.2160628,28.8466797 18.0810628,28.5136797 C17.9280628,28.1446797 17.3249922,27.9550465 16.8839922,27.8650465 C10.4039922,27.0190465 5.59883721,22.5631334 5.59883721,17.2531334 C5.59883721,11.3221334 11.6558372,6.49883721 19.0988372,6.49883721 L19.0988372,6.49883721 Z M18.1918073,13.6970791 C17.7383799,13.7054904 17.2988372,14.0515247 17.2988372,14.5988372 L17.2988372,19.9988372 C17.2988372,20.4956372 17.7020372,20.8988372 18.1988372,20.8988372 C18.6956372,20.8988372 19.0988372,20.4956372 19.0988372,19.9988372 L19.0988372,17.4078209 L21.0658291,20.5209052 C21.5752291,21.2337052 22.6988372,20.8745346 22.6988372,19.9988346 L22.6988372,14.5988346 C22.6988372,14.1020346 22.2956372,13.6988346 21.7988372,13.6988346 C21.3020372,13.6988346 20.8988372,14.1020346 20.8988372,14.5988346 L20.8988372,17.2988346 L18.9318453,14.076764 C18.7408203,13.809464 18.4638637,13.6920297 18.1918073,13.6970765 L18.1918073,13.6970791 Z M10.0988372,13.6988372 C9.60203721,13.6988372 9.19883721,14.1020372 9.19883721,14.5988372 L9.19883721,19.9988372 C9.19883721,20.4956372 9.60203721,20.8988372 10.0988372,20.8988372 L12.7988372,20.8988372 C13.2956372,20.8988372 13.6988372,20.4956372 13.6988372,19.9988372 C13.6988372,19.5020372 13.2956372,19.0988372 12.7988372,19.0988372 L10.9988372,19.0988372 L10.9988372,14.5988372 C10.9988372,14.1020372 10.5956372,13.6988372 10.0988372,13.6988372 Z M15.4988372,13.6988372 C15.0020372,13.6988372 14.5988372,14.1020372 14.5988372,14.5988372 L14.5988372,19.9988372 C14.5988372,20.4956372 15.0020372,20.8988372 15.4988372,20.8988372 C15.9956372,20.8988372 16.3988372,20.4956372 16.3988372,19.9988372 L16.3988372,14.5988372 C16.3988372,14.1020372 15.9956372,13.6988372 15.4988372,13.6988372 Z M24.4988372,13.6988372 C24.0020372,13.6988372 23.5988372,14.1020372 23.5988372,14.5988372 L23.5988372,19.9988372 C23.5988372,20.4956372 24.0020372,20.8988372 24.4988372,20.8988372 L27.1988372,20.8988372 C27.6956372,20.8988372 28.0988372,20.4956372 28.0988372,19.9988372 C28.0988372,19.5020372 27.6956372,19.0988372 27.1988372,19.0988372 L25.3988372,19.0988372 L25.3988372,18.1988372 L27.1988372,18.1988372 C27.6965372,18.1988372 28.0988372,17.7956372 28.0988372,17.2988372 C28.0988372,16.8020372 27.6965372,16.3988372 27.1988372,16.3988372 L25.3988372,16.3988372 L25.3988372,15.4988372 L27.1988372,15.4988372 C27.6956372,15.4988372 28.0988372,15.0956372 28.0988372,14.5988372 C28.0988372,14.1020372 27.6956372,13.6988372 27.1988372,13.6988372 L24.4988372,13.6988372 Z',
                'selected'   => 'false',
            ],
            'getpocket'       => [
                'color'      => '#ef4056',
                'color-rgba' => '239, 64, 86',
                'path'       => 'M19.8837209,35.2674419 C8.94309593,35.2674419 0.0837209302,26.4924419 0.0837209302,15.6537706 L0.0837209302,3.1592407 C0.0837209302,1.51041192 1.43723721,0.167444477 3.10715843,0.167444477 L36.6602834,0.167444477 C38.3302047,0.167444477 39.6837209,1.51041453 39.6837209,3.1592407 L39.6837209,15.6537706 C39.6837209,26.4924419 30.8243459,35.2674419 19.8837209,35.2674419 Z M19.8837209,25.3674419 C19.257941,25.3674419 18.639191,25.1986919 18.0063785,24.6432244 L8.18372093,14.5674419 C7.14309593,13.5338494 7.06575349,11.7303331 8.10637849,10.8022081 C9.15051977,9.7686157 10.8169247,9.7686157 11.7555959,10.8022081 L19.8837209,18.7510369 L27.9063785,10.8022081 C28.9505198,9.7686157 30.6169247,9.7686157 31.5555959,10.8022081 C32.5962209,11.8322869 32.6243459,13.6393169 31.5837209,14.5674419 L21.6555959,24.6432244 C21.1598922,25.2197869 20.5095035,25.3674419 19.8837209,25.3674419 L19.8837209,25.3674419 Z',
                'selected'   => 'false',
                'full-svg'   => '<svg fill="#fff" preserveAspectRatio="xMidYMid meet" height="2em" width="2em" viewBox="0 -5 50 50">
  <g fill="none" fill-rule="evenodd" transform="translate(-2 -5)">
    <polygon points="0 45 0 0 45 0 45 45"></polygon>
    <path fill="#FFF" d="M19.8837209,35.2674419 C8.94309593,35.2674419 0.0837209302,26.4924419 0.0837209302,15.6537706 L0.0837209302,3.1592407 C0.0837209302,1.51041192 1.43723721,0.167444477 3.10715843,0.167444477 L36.6602834,0.167444477 C38.3302047,0.167444477 39.6837209,1.51041453 39.6837209,3.1592407 L39.6837209,15.6537706 C39.6837209,26.4924419 30.8243459,35.2674419 19.8837209,35.2674419 Z M19.8837209,25.3674419 C19.257941,25.3674419 18.639191,25.1986919 18.0063785,24.6432244 L8.18372093,14.5674419 C7.14309593,13.5338494 7.06575349,11.7303331 8.10637849,10.8022081 C9.15051977,9.7686157 10.8169247,9.7686157 11.7555959,10.8022081 L19.8837209,18.7510369 L27.9063785,10.8022081 C28.9505198,9.7686157 30.6169247,9.7686157 31.5555959,10.8022081 C32.5962209,11.8322869 32.6243459,13.6393169 31.5837209,14.5674419 L21.6555959,24.6432244 C21.1598922,25.2197869 20.5095035,25.3674419 19.8837209,25.3674419 L19.8837209,25.3674419 Z" transform="translate(2.616 5.233)"></path>
  </g>
</svg>'
            ],
            'qzone'           => [
                'color'      => '#F1C40F',
                'color-rgba' => '241, 196, 15',
                'path'       => 'M39.7081437,14.9473034 C39.618664,14.6721034 39.3906851,14.4673034 39.1129088,14.4137034 L27.0175783,12.0817034 L20.9710802,1.20330336 C20.6722956,0.665703359 19.9191093,0.665703359 19.6203247,1.20330336 L13.5738266,12.0825034 L1.4784961,14.4137034 C1.20071978,14.4673034 0.97274089,14.6713034 0.883261121,14.9473034 C0.793781352,15.2233034 0.856806233,15.5273034 1.04743704,15.7409034 L9.36516513,25.0705034 L7.8517899,38.3065034 C7.81911033,38.5945034 7.94049158,38.8785034 8.16924856,39.0481034 C8.39800553,39.2185034 8.69834632,39.2481034 8.95355818,39.1249034 L20.3058176,33.6777034 L32.4330495,39.1329034 C32.5334224,39.1785034 32.639242,39.2002094 32.7450616,39.2002094 C32.9193526,39.2002094 33.0920875,39.1401034 33.2321428,39.0233034 C33.4585655,38.8369034 33.564385,38.5369034 33.5075848,38.2441034 L32.0751304,29.6001034 C34.3012315,28.0001034 35.002286,26.7921034 35.0536396,26.5905034 C35.1275577,26.3033034 35.0404122,25.9977034 34.8272169,25.7969034 C34.7252877,25.7001034 34.6015723,25.6377034 34.470854,25.6073034 C21.8518723,32.0001034 11.8853823,28.8217034 11.736768,28.8001034 C11.4395396,28.7561034 10.958683,28.5561034 10.958683,28.0001034 C10.958683,27.7193034 11.0839548,27.3905034 11.3212707,27.2001034 C14.3628047,24.7513034 22.6299573,18.4001034 22.6299573,18.4001034 C22.6299573,18.4001034 18.7512038,16.8057034 10.958683,17.6001034 C10.6077668,17.6361034 10.2381764,17.2705034 10.2039407,16.9073034 C10.1681488,16.5353034 10.243623,16.2089034 10.5392953,16.0065034 C10.737707,15.8697034 14.695825,14.4521034 21.8518723,14.4001034 C25.6839407,14.3721034 27.6579422,15.9217034 27.7614275,15.9921034 C27.9621734,16.1297034 28.0890012,16.3553034 28.1061191,16.6025034 C28.123237,16.8497034 28.0267544,17.0913034 27.8470169,17.2561034 L19.4966092,23.9761034 C24.3028399,25.8081034 31.183445,24.7937034 31.183445,24.7937034 L39.5439678,15.7401034 C39.7338205,15.5265034 39.7968455,15.2225034 39.7081437,14.9473034 L39.7081437,14.9473034 Z',
                'selected'   => 'false',
            ],
            'refind'          => [
                'color'      => '#4286f4',
                'color-rgba' => '66, 134, 244',
                'path'       => [
                    'M19.9998326,11.8574059 C17.82159,11.8574059 15.7742259,12.7022594 14.2349791,14.2359833 C12.6985774,15.7668619 11.8525523,17.801841 11.8525523,19.9656904 C11.8525523,22.129205 12.6987448,24.1635146 14.2349791,25.6942259 C15.7742259,27.2282845 17.82159,28.0731381 19.9996653,28.0731381 C22.1777406,28.0731381 24.2247699,27.2282845 25.7641841,25.6948954 C27.3005858,24.1641841 28.1466109,22.129205 28.1466109,19.9656904 C28.1466109,17.8016736 27.3005858,15.7665272 25.7641841,14.2356485 C24.2256067,12.7020921 22.1779079,11.8574059 19.9998326,11.8574059 Z',
                    'M36.6321339,30.9933054 C41.8078661,23.2610879 40.9722176,12.7154812 34.1240167,5.89205021 C26.3240167,-1.88033473 13.6766527,-1.88033473 5.87665272,5.89205021 C-1.9241841,13.6644351 -1.9241841,26.2657741 5.87665272,34.0386611 C12.7243515,40.8620921 23.3076151,41.6955649 31.0686192,36.5379079 L27.2564017,31.6913808 C25.0974059,33.0261088 22.6050209,33.7427615 20.0001674,33.7427615 C16.3117992,33.7427615 12.8433473,32.3106276 10.2338075,29.7109623 C7.62225941,27.1084519 6.18359833,23.6476987 6.18359833,19.9661925 C6.18359833,16.2841841 7.62192469,12.8230962 10.2338075,10.2200837 C12.8430126,7.61991632 16.3114644,6.18828452 20.0001674,6.18828452 C23.6883682,6.18828452 27.1571548,7.61991632 29.7665272,10.219749 C32.3790795,12.8225941 33.8172385,16.2838494 33.8172385,19.9661925 C33.816569,22.5594979 33.0999163,25.0420084 31.7653556,27.1924686 L36.6323013,30.9933054 L36.6321339,30.9933054 Z',
                ],
                'selected'   => 'false',
            ],
            'renren'          => [
                'color'         => '#005baa',
                'color-rgba'    => '0, 91, 170',
                'path'          => 'M10.9151163,0.104651163 C4.81199128,0.983558721 0.115116279,6.26402616 0.115116279,12.6413712 C0.115116279,15.9847299 1.40886628,19.0292625 3.51824128,21.2968387 C7.71238256,19.2507462 10.9151163,14.8913712 10.9151163,9.92379244 L10.9151163,0.104651163 Z M14.5151163,0.104651163 L14.5151163,9.92379244 C14.5151163,14.9898087 17.3944125,19.5144174 21.7116,21.5042625 L21.7151163,21.5007462 C25.9092576,19.4546512 28.9151163,14.8913712 28.9151163,9.92379244 L28.9151163,0.104651163 C26.1096488,0.508947384 23.6065238,1.85191744 21.7151163,3.79605872 C19.8237113,1.85191744 17.3205863,0.50895 14.5151163,0.104651163 L14.5151163,0.104651163 Z M32.5151163,0.104651163 L32.5151163,9.92379244 C32.5151163,14.9898087 35.3944125,19.5144174 39.7116,21.5042625 C41.9405049,19.2191049 43.3151163,16.0902 43.3151163,12.6413712 C43.3151163,6.26402616 38.6182413,0.983558721 32.5151163,0.104651163 Z M12.7151163,17.4331674 C11.4037875,19.9855125 9.0694125,22.193325 6.62605378,23.7296512 C8.42957006,24.7316049 10.5073038,25.3046512 12.7151163,25.3046512 C14.8350375,25.3046512 16.8319125,24.7773087 18.5826951,23.8456674 C16.1323038,22.3093387 14.0369913,20.0030887 12.7151163,17.4331674 Z M30.7151163,17.4331674 C29.4037875,19.9855125 27.0694125,22.193325 24.6260538,23.7296512 C26.4295701,24.7316049 28.5073038,25.3046512 30.7151163,25.3046512 C32.8350375,25.3046512 34.8319125,24.7773087 36.5826951,23.8456674 C34.1323038,22.3093387 32.0369913,20.0030887 30.7151163,17.4331674 Z',
                'selected'      => 'false',
                'viewbox-total' => '0 -8 50 50'
            ],
            'surfingbird'     => [
                'color'      => '#6dd3ff',
                'color-rgba' => '109, 211, 255',
                'path'       => '345.451,190.59 341.83,224.1 283.864,281.158 253.073,281.158 241.293,311.951 241.293,354.518 199.629,342.744 247.635,256.701 120.837,185.15 215.936,197.832 169.742,131.715 295.637,199.646 332.771,157.982 349.98,157.982 390.735,176.096 ',
                'selected'   => 'false',
                'full-svg'   => '<svg height="2em" id="Layer_1" version="1.1" viewBox="50 50 512 512" width="2em" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:svg="http://www.w3.org/2000/svg">
       <defs id="defs12"></defs>
       <g id="g5176">
          <polygon id="polygon9" points="345.451,190.59 341.83,224.1 283.864,281.158 253.073,281.158 241.293,311.951 241.293,354.518 199.629,342.744 247.635,256.701 120.837,185.15 215.936,197.832 169.742,131.715 295.637,199.646 332.771,157.982 349.98,157.982 390.735,176.096 " style="fill:#ffffff" transform="matrix(1.4820402,0,0,1.4820402,-123.08513,-104.30843)"></polygon>
       </g>
</svg>'
            ],
            'skype'           => [
                'color'      => '#00aff0',
                'color-rgba' => '0, 175, 240',
                'path'       => 'M11.425,0 C5.12812558,0 0,5.05937442 0,11.275 C0,13.1812512 0.496874419,15.0406256 1.425,16.7 C1.20625116,17.85 1.1,19.0343744 1.1,20.2 C1.1,30.6 9.65937442,39.05 20.2,39.05 C21.2812512,39.05 22.3625,38.9781256 23.425,38.8 C25.0062512,39.5906256 26.7843744,40 28.575,40 C34.8718744,40 40,34.9406256 40,28.725 C40,27.0562512 39.6312512,25.4625 38.925,23.975 C39.1812512,22.7468744 39.325,21.4781256 39.325,20.2 C39.325,9.80312558 30.7406256,1.35 20.2,1.35 C19.2093744,1.35 18.2093744,1.425 17.225,1.575 C15.4687512,0.546874419 13.4781256,0 11.425,0 Z M20.075,7.5 C21.7125,7.5 23.1468744,7.67812558 24.35,8.05 C25.5562512,8.41875116 26.5781256,8.90937442 27.375,9.525 C28.1812512,10.1468744 28.7968744,10.8093744 29.175,11.5 C29.5562512,12.1968744 29.75,12.9062512 29.75,13.575 C29.75,14.2218744 29.475,14.7875 28.975,15.3 C28.475,15.8125 27.8468744,16.075 27.1,16.075 C26.4218744,16.075 25.8937512,15.9187512 25.525,15.6 C25.1812512,15.3 24.8187512,14.8437512 24.425,14.175 C23.9687512,13.3187512 23.4343744,12.6218744 22.8,12.15 C22.1843744,11.6906256 21.1375,11.475 19.725,11.475 C18.4125,11.475 17.3406256,11.7125 16.55,12.225 C15.7843744,12.7187512 15.425,13.3031256 15.425,13.975 C15.425,14.3875 15.5531256,14.7281256 15.8,15.025 C16.0625,15.3375 16.4093744,15.5968744 16.875,15.825 C17.3562512,16.0625 17.8531256,16.2656256 18.35,16.4 C18.8593744,16.5375 19.7281256,16.7562512 20.9,17.025 C22.3812512,17.3406256 23.7437512,17.6718744 24.95,18.05 C26.1687512,18.4375 27.2156256,18.9218744 28.075,19.475 C28.95,20.0375 29.6562512,20.7593744 30.15,21.625 C30.6437512,22.4906256 30.875,23.5531256 30.875,24.8 C30.8718744,26.2875 30.4531256,27.6375 29.6,28.825 C28.75,30.0093744 27.4968744,30.9625 25.875,31.625 C24.2687512,32.2812512 22.3406256,32.6 20.15,32.6 C17.5187512,32.6 15.3187512,32.1531256 13.6,31.25 C12.3687512,30.5968744 11.3406256,29.6937512 10.575,28.6 C9.79375116,27.4937512 9.4,26.4093744 9.4,25.35 C9.4,24.6906256 9.66875116,24.1218744 10.175,23.65 C10.6781256,23.1875 11.3093744,22.95 12.075,22.95 C12.7031256,22.95 13.2593744,23.1312512 13.7,23.5 C14.125,23.8531256 14.4843744,24.3781256 14.775,25.05 C15.1,25.7843744 15.4468744,26.3843744 15.825,26.875 C16.1812512,27.3406256 16.6906256,27.7437512 17.35,28.05 C18.0125,28.3625 18.9218744,28.525 20.025,28.525 C21.5406256,28.525 22.7687512,28.2031256 23.7,27.575 C24.6125,26.9625 25.075,26.2281256 25.075,25.325 C25.075,24.6125 24.825,24.0375 24.35,23.6 C23.85,23.1406256 23.1937512,22.7937512 22.4,22.55 C21.5656256,22.2968744 20.4468744,22.0156256 19.05,21.725 C17.15,21.3218744 15.5343744,20.8375 14.25,20.3 C12.9343744,19.7468744 11.875,18.9781256 11.1,18.025 C10.3125,17.0531256 9.9,15.8562512 9.9,14.425 C9.9,13.0593744 10.3218744,11.8093744 11.15,10.75 C11.9656256,9.7 13.1593744,8.88437442 14.7,8.325 C16.2187512,7.76875116 18.0312512,7.5 20.075,7.5 L20.075,7.5 Z',
                'selected'   => 'false',
            ],
            'telegram'        => [
                'color'      => '#37AEE2',
                'color-rgba' => '55, 174, 226',
                'path'       => [
                    'M38.1275185,14.078125 L33.3734792,38.3508773 C33.3734792,38.3508773 33.1691533,39.4600671 31.7934531,39.4600671 C31.0624531,39.4600671 30.6855312,39.1123356 30.6855312,39.1123356 L20.3880752,30.5675052 L15.3497604,28.0280417 L8.88371125,26.3084137 C8.88371125,26.3084137 7.73264125,25.9759115 7.73264125,25.0240885 C7.73264125,24.230904 8.91670896,23.8527106 8.91670896,23.8527106 L35.9687842,13.1059942 C35.9675162,13.1047263 36.7949665,12.8077575 37.3977865,12.8090214 C37.7683617,12.8090214 38.190971,12.9676638 38.190971,13.4435752 C38.190971,13.7608483 38.127515,14.078125 38.127515,14.078125 L38.1275185,14.078125 Z',
                    'M24.230904,33.7554762 L19.8829769,38.0374115 C19.8829769,38.0374115 19.6938802,38.1833565 19.441331,38.1897031 C19.3537633,38.1922425 19.259849,38.1782813 19.1633987,38.1351331 L20.3868073,30.5649694 L24.230904,33.7554762 Z',
                    'M32.9838675,18.1341585 C32.7693912,17.8549583 32.3734329,17.8041921 32.0942292,18.0161325 L15.347221,28.0381921 C15.347221,28.0381921 18.0199398,35.5157129 18.42732,36.8101921 C18.8359681,38.1059392 19.1633952,38.1363975 19.1633952,38.1363975 L20.3868037,30.5662337 L32.8645665,19.0225254 C33.1437667,18.810585 33.1958008,18.4133587 32.9838604,18.1341585 L32.9838675,18.1341585 Z',
                ],
                'selected'   => 'false',
                'full-svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" viewBox="0 0 60 60">
  <g fill="none" fill-rule="evenodd" transform="translate(-20 -20) scale(1.5)">
  <polygon points="0 60 0 0 60 0 60 60"></polygon>
      <g fill-rule="nonzero" transform="translate(5 5)">
      <path fill="#FFF" d="M38.1275185,14.078125 L33.3734792,38.3508773 C33.3734792,38.3508773 33.1691533,39.4600671 31.7934531,39.4600671 C31.0624531,39.4600671 30.6855312,39.1123356 30.6855312,39.1123356 L20.3880752,30.5675052 L15.3497604,28.0280417 L8.88371125,26.3084137 C8.88371125,26.3084137 7.73264125,25.9759115 7.73264125,25.0240885 C7.73264125,24.230904 8.91670896,23.8527106 8.91670896,23.8527106 L35.9687842,13.1059942 C35.9675162,13.1047263 36.7949665,12.8077575 37.3977865,12.8090214 C37.7683617,12.8090214 38.190971,12.9676638 38.190971,13.4435752 C38.190971,13.7608483 38.127515,14.078125 38.127515,14.078125 L38.1275185,14.078125 Z"></path>
      <path fill="#B0BEC5" d="M24.230904,33.7554762 L19.8829769,38.0374115 C19.8829769,38.0374115 19.6938802,38.1833565 19.441331,38.1897031 C19.3537633,38.1922425 19.259849,38.1782813 19.1633987,38.1351331 L20.3868073,30.5649694 L24.230904,33.7554762 Z"></path>
      <path fill="#CFD8DC" d="M32.9838675,18.1341585 C32.7693912,17.8549583 32.3734329,17.8041921 32.0942292,18.0161325 L15.347221,28.0381921 C15.347221,28.0381921 18.0199398,35.5157129 18.42732,36.8101921 C18.8359681,38.1059392 19.1633952,38.1363975 19.1633952,38.1363975 L20.3868037,30.5662337 L32.8645665,19.0225254 C33.1437667,18.810585 33.1958008,18.4133587 32.9838604,18.1341585 L32.9838675,18.1341585 Z"></path>
    </g>
  </g>
</svg>'
            ],
            'threema'         => [
                'color'      => '#000000',
                'color-rgba' => '0, 0, 0',
                'path'       => 'M3.83721017,0.0872084302 C1.76908605,0.0872084302 0.0872110465,1.76908343 0.0872110465,3.83720756 L0.0872110465,33.8372084 C0.0872110465,35.9053326 1.76908605,37.5872084 3.83721017,37.5872084 L33.8115741,37.5872084 C34.8240741,37.5872084 35.7725015,37.192139 36.4812515,36.4702657 C37.1918773,35.7502657 37.576575,34.7940733 37.5615733,33.8115741 L37.587211,3.78593895 C37.5572067,1.74781308 35.8753317,0.0872084302 33.8372084,0.0872084302 L3.83721017,0.0872084302 Z M18.8372093,3.83721017 C26.0859584,3.83721017 31.9622102,8.4534593 31.9622102,14.149711 C31.9622102,19.8459602 26.085961,24.4622119 18.8372093,24.4622119 L14.6697279,24.4622119 C11.545977,24.4622119 5.71220843,26.3372102 5.71220843,26.3372102 C5.71220843,26.3372102 9.46220756,22.7965395 9.46220756,21.7559145 C9.46220756,21.5440404 9.28123953,21.2830352 9.03373953,20.9905352 C8.25374041,20.3005334 7.593975,19.5324279 7.07085,18.698052 C6.33585087,17.663052 5.71220843,16.3609823 5.71220843,14.6697331 L5.74516831,14.6770561 C5.73391831,14.5008052 5.71220843,14.3278378 5.71220843,14.1497137 C5.71220843,8.45346453 11.5884576,3.83721017 18.8372093,3.83721017 L18.8372093,3.83721017 Z M18.6101581,7.59453227 C16.6474073,7.71196395 15.0872102,9.34549012 15.0872102,11.3372084 L15.0872102,13.2122093 C14.0522102,13.2122093 13.2122093,14.0522102 13.2122093,15.0872102 L13.2122093,18.8372093 C13.2122093,19.8722093 14.0522102,20.7122102 15.0872102,20.7122102 L22.587211,20.7122102 C23.622211,20.7122102 24.4622119,19.8722093 24.4622119,18.8372093 L24.4622119,15.0872102 C24.4622119,14.0522102 23.622211,13.2122093 22.587211,13.2122093 L22.587211,11.549611 C22.587211,9.68211105 21.3175439,7.94410378 19.4744198,7.63847791 C19.1809831,7.58996163 18.8905552,7.57775669 18.6101608,7.59453227 L18.6101581,7.59453227 Z M18.8372093,9.46221017 C19.8722093,9.46221017 20.7122102,10.3040843 20.7122102,11.337211 L20.7122102,13.2122119 L16.962211,13.2122119 L16.962211,11.337211 C16.962211,10.3040869 17.8022119,9.46221017 18.8372119,9.46221017 L18.8372093,9.46221017 Z M11.3372084,30.0872093 C12.3722084,30.0872093 13.2122093,30.9272102 13.2122093,31.9622102 C13.2122093,32.9972102 12.3722084,33.837211 11.3372084,33.837211 C10.3022084,33.837211 9.46220756,32.9972102 9.46220756,31.9622102 C9.46220756,30.9272102 10.3022084,30.0872093 11.3372084,30.0872093 L11.3372084,30.0872093 Z M18.8372093,30.0872093 C19.8722093,30.0872093 20.7122102,30.9272102 20.7122102,31.9622102 C20.7122102,32.9972102 19.8722093,33.837211 18.8372093,33.837211 C17.8022093,33.837211 16.9622084,32.9972102 16.9622084,31.9622102 C16.9622084,30.9272102 17.8022093,30.0872093 18.8372093,30.0872093 Z M26.3372102,30.0872093 C27.3722102,30.0872093 28.212211,30.9272102 28.212211,31.9622102 C28.212211,32.9972102 27.3722102,33.837211 26.3372102,33.837211 C25.3022102,33.837211 24.4622093,32.9972102 24.4622093,31.9622102 C24.4622093,30.9272102 25.3022102,30.0872093 26.3372102,30.0872093 Z',
                'selected'   => 'false',
            ],
            'yahoomail'       => [
                'color'      => '#720e9e',
                'color-rgba' => '114, 14, 158',
                'path'       => 'M28.1348028,8.71051403 C27.2798608,9.08637519 19.46,18.9798569 18.9445012,21.4073874 C18.8308933,22.2457551 18.9559513,30.3766508 19.0695592,31.5959141 C19.585058,31.7827186 23.304884,31.620159 23.9875058,31.8061685 L23.9056497,34.319682 C23.2370766,34.2350239 18.5102088,34.2548967 15.8132598,34.2548967 C14.442413,34.2548967 10.0363921,34.5021145 8.68316708,34.437859 L8.93896752,32.0455697 C9.68160095,31.9558771 12.7541299,32.2688076 13.4275754,31.0976364 C13.762471,30.5152305 13.6572274,22.788018 13.5435383,21.4815792 C13.2590719,20.084123 6.40605568,6.07524642 4.61553364,3.7800689 L0,3.7800689 L0,0.258341282 L15.5659861,0.258341282 L15.5659861,0.501319555 C15.5796287,0.501319555 15.6056148,0.505294117 15.6188515,0.509666137 L15.5659861,1.10479067 L15.5659861,3.77993641 L10.8720881,3.77993641 C12.9641299,8.77105991 15.9637355,14.7968945 17.2430626,17.692496 L23.5126914,8.42288822 L19.7859629,8.42288822 L19.2610441,4.90500265 L32.912181,4.90500265 L32.8114037,5.15579756 C32.8219606,5.15579756 32.8446984,5.15977213 32.8595592,5.15977213 L31.8843503,7.46727079 C31.8756612,7.46727079 31.8608816,7.47124535 31.8532483,7.47508746 L31.4598028,8.42288822 L28.9417517,8.42288822 C28.6157889,8.5401378 28.3288863,8.6446688 28.1348028,8.71051403 Z M32.5891416,30.7662904 L31.3838747,30.6693111 L30.0391763,30.401558 L30.0506265,34.1622893 L31.1111021,34.3113355 L32.2540835,34.4491203 L32.5891416,30.7662904 Z M34.9282947,12.7898728 C34.5216937,12.7664229 30.8237935,12.1954107 30.3432135,12.0401378 L30.400058,27.9275252 L32.4928306,28.2033598 L34.9282947,12.7898728 Z',
                'selected'   => 'false',
            ],
            'wordpress'       => [
                'color'      => '#21759b',
                'color-rgba' => '33, 117, 155',
                'path'       => 'M20.9302326,0.230232558 C9.51553256,0.230232558 0.230232558,9.51643256 0.230232558,20.9302326 C0.230232558,32.3440326 9.51553256,41.6302326 20.9302326,41.6302326 C32.3449326,41.6302326 41.6302326,32.3440326 41.6302326,20.9302326 C41.6302326,9.51643256 32.3449326,0.230232558 20.9302326,0.230232558 Z M20.9302326,4.73023256 C24.7642326,4.73023256 28.2796326,6.06673256 31.0525326,8.28973256 C30.1921326,8.86213256 29.6602326,9.85573256 29.6602326,10.9402326 C29.6602326,12.3802326 30.4702326,13.6402326 31.3702326,15.0802326 C32.0902326,16.2502326 32.8102326,17.7802326 32.8102326,19.9402326 C32.8102326,21.4702326 32.3602326,23.3602326 31.4602326,25.7002326 L29.6602326,31.6402326 L23.2702326,12.5602326 C24.3502326,12.4702326 25.3402326,12.3802326 25.3402326,12.3802326 C26.2402326,12.2902326 26.1502326,10.9402326 25.2502326,10.9402326 C25.2502326,10.9402326 25.2502326,10.9402326 25.1602326,10.9402326 C25.1602326,10.9402326 22.2802326,11.2102326 20.3902326,11.2102326 C18.6802326,11.2102326 15.7102326,10.9402326 15.7102326,10.9402326 C15.7102326,10.9402326 15.7102326,10.9402326 15.6202326,10.9402326 C14.7202326,10.9402326 14.6302326,12.3802326 15.5302326,12.3802326 C15.5302326,12.3802326 16.4302326,12.4702326 17.4202326,12.5602326 L20.2102326,20.1202326 L16.3402326,31.7302326 L9.86023256,12.4702326 C10.9402326,12.3802326 11.9302326,12.2902326 11.9302326,12.2902326 C12.8302326,12.2002326 12.7402326,10.8502326 11.8402326,10.8502326 C11.8402326,10.8502326 11.8402326,10.8502326 11.7502326,10.8502326 C11.7502326,10.8502326 9.81343256,11.0320326 8.07373256,11.0968326 C11.0329326,7.23223256 15.6814326,4.73023256 20.9302326,4.73023256 L20.9302326,4.73023256 Z M4.73023256,20.9302326 C4.73023256,19.3102326 4.97413256,17.7487326 5.41603256,16.2736326 L12.0976326,34.5040326 C7.66513256,31.6141326 4.73023256,26.6209326 4.73023256,20.9302326 Z M16.1404326,36.4102326 L20.9302326,22.3702326 L26.0467326,36.2941326 C24.4375326,36.8287326 22.7203326,37.1302326 20.9302326,37.1302326 C19.2625326,37.1302326 17.6542326,36.8773326 16.1404326,36.4102326 Z M30.2038326,34.2052326 L30.2038326,34.2052326 L34.9702326,20.3902326 C35.6776326,18.5902326 36.0592326,17.0224326 36.2212326,15.6166326 C36.7999326,17.2825326 37.1302326,19.0654326 37.1302326,20.9302326 C37.1302326,26.4301326 34.3888326,31.2775326 30.2038326,34.2052326 Z',
                'selected'   => 'false',
            ],
            'wechat'          => [
                'color'      => '#4EC034',
                'color-rgba' => '78, 192, 52',
                'path'       => 'M27.7561832,11.4320611 C24.059542,11.6251908 20.8450382,12.7458015 18.2352672,15.2775573 C15.5984733,17.8354198 14.3948092,20.969771 14.7238168,24.8552672 C13.2789313,24.6763359 11.9629008,24.4793893 10.6393893,24.3679389 C10.1822901,24.3294656 9.63984733,24.3841221 9.25267176,24.6025954 C7.96748092,25.3277863 6.73541985,26.1465649 5.2751145,27.0593893 C5.54305344,25.8474809 5.71648855,24.7862595 6.02351145,23.7654962 C6.24931298,23.0152672 6.14473282,22.5977099 5.45358779,22.1091603 C1.01603053,18.9761832 -0.854503817,14.2874809 0.545343511,9.46030534 C1.84045802,4.99465649 5.02091603,2.28641221 9.34244275,0.874656489 C15.240916,-1.05206107 21.869771,0.913282443 25.4564885,5.59633588 C26.7519084,7.28793893 27.5462595,9.18656489 27.7561832,11.4320611 Z M10.7429008,9.92793893 C10.7769466,9.04503817 10.0119084,8.24961832 9.10320611,8.22305344 C8.17282443,8.19572519 7.40763359,8.90671756 7.38045802,9.82351145 C7.3529771,10.7526718 8.06366412,11.4972519 9.00076336,11.5210687 C9.92977099,11.5445802 10.7085496,10.8326718 10.7429008,9.92793893 Z M19.6193893,8.22244275 C18.7073282,8.23923664 17.9366412,9.01603053 17.9528244,9.90244275 C17.9694656,10.8212214 18.7254962,11.54 19.6633588,11.5287023 C20.6036641,11.5174046 21.3167939,10.7909924 21.3079389,9.85282443 C21.3001527,8.9319084 20.5474809,8.20549618 19.6193893,8.22244275 Z M36.0612214,34.4778626 C34.890687,33.9566412 33.8169466,33.1746565 32.6737405,33.0552672 C31.5349618,32.9363359 30.3378626,33.5932824 29.1464122,33.7151145 C25.5172519,34.0864122 22.2659542,33.0749618 19.5850382,30.5957252 C14.4862595,25.8796947 15.2148092,18.6485496 21.1138931,14.7838168 C26.3567939,11.3490076 34.0458015,12.4940458 37.7422901,17.26 C40.9680916,21.4187786 40.5890076,26.9393893 36.6509924,30.4331298 C35.5114504,31.4442748 35.101374,32.2763359 35.8325191,33.609313 C35.9674809,33.8554198 35.9829008,34.1670229 36.0612214,34.4778626 L36.0612214,34.4778626 Z M22.7369466,21.5772519 C23.4821374,21.5780153 24.0957252,20.9948092 24.1239695,20.2587786 C24.1537405,19.479542 23.5270229,18.8259542 22.7467176,18.8227481 C21.9741985,18.8192366 21.3270229,19.4819847 21.3538931,20.2496183 C21.3792366,20.9830534 21.9970992,21.5763359 22.7369466,21.5772519 Z M31.3264122,18.8258015 C30.6033588,18.8207634 29.9890076,19.4126718 29.959542,20.1432061 C29.9282443,20.9244275 30.5354198,21.5659542 31.3085496,21.5679389 C32.0563359,21.5703817 32.6471756,21.0048855 32.6743511,20.2607634 C32.7033588,19.4777099 32.0958779,18.831145 31.3264122,18.8258015 L31.3264122,18.8258015 Z',
                'selected'   => 'false',
                'url'        => 'wechat.com/',
            ],
        ];
    }

    /**
     * AJAX Call back to save the set up button config for setup.
     *
     * @action wp_ajax_set_button_config
     */
    public function setButtonConfig()
    {
        check_ajax_referer($this->plugin->meta_prefix, 'nonce');

        if (! isset($_POST['config'], $_POST['button']) || '' === $_POST['config']) { // WPCS: input var ok.
            wp_send_json_error('Button Config Set Failed');
        }

        $networks = isset($_POST['config']['networks']) ?
            array_map('sanitize_text_field', wp_unslash($_POST['config']['networks'])) :
            ''; // WPCS: input var ok.
        // Set Purposes.
        $purposes = isset($_POST['config']['publisher_purposes']) ?
            $_POST['config']['publisher_purposes'] :
	        ''; // WPCS: input var ok.

        $first    = isset($_POST['first']) && 'upgrade' !== $_POST['first'] ? false : true; // Input var okay.
        $type     = isset($_POST['type']) ? true : false; // Input var okay.
        $button   = sanitize_text_field(wp_unslash($_POST['button'])); // WPCS: input var ok.
        $config   = $_POST['config']; // WPCS: input var ok. WPCS: sanitization ok. Can't sanitize initially.

        // If user doesn't have a sharethis account already.
        if (! $type) {
            $newconfig[strtolower($button)] = $config;
            $config                         = $newconfig;
        } else {
            $config = 'platform' !== $button ? json_decode(str_replace('\\', '', $config), true) : $config;
        }

        $restrictions = isset($config[$button]['publisher_restrictions']) ? $config[$button]['publisher_restrictions'] : '';

        if (! $first) {
            $current_config = get_option('sharethis_button_config', true);
            $current_config = false !== $current_config && null !== $current_config ?
                $current_config :
                [];
            $current_config[$button] = array_map(
                'sanitize_text_field',
                wp_unslash($_POST['config'])
            ); // WPCS: input var ok.
            $current_config[$button]['networks'] = $networks;

            if ('gdpr' === $button) {
	            $current_config[$button]['publisher_purposes']     = $purposes;
            }

            $config = $current_config;

	        if ('gdpr' === $button) {
		        $config['gdpr']['publisher_restrictions'] = $restrictions;

		        var_dump($config['gdpr']);
	        }
        }
        // Make sure bool is "true" or "false".
        if (isset($config['inline'])) {
            $config['inline']['enabled'] = true === $config['inline']['enabled'] ||
                                           '1' === $config['inline']['enabled'] ||
                                           'true' === $config['inline']['enabled'] ? 'true' : 'false';
        }

        if (isset($config['sticky'])) {
            $config['sticky']['enabled'] = true === $config['sticky']['enabled'] ||
                                           '1' === $config['sticky']['enabled'] ||
                                           'true' === $config['sticky']['enabled'] ? 'true' : 'false';
        }

        if (isset($config['gdpr'])) {
            $config['gdpr']['enabled'] = true === $config['gdpr']['enabled'] ||
                                         '1' === $config['gdpr']['enabled'] ||
                                         'true' === $config['gdpr']['enabled'] ? 'true' : 'false';

            // Remove network.
            unset($config['gdpr']['networks']);
        }

        update_option('sharethis_button_config', $config);

        if ($first && 'platform' !== $button) {
            update_option('sharethis_first_product', strtolower($button));
            update_option('sharethis_' . strtolower($button), 'true');
        }
    }

    /**
     * AJAX Call back to save the set up gdpr config for setup.
     *
     * @action wp_ajax_set_gdpr_config
     */
    public function setGdprConfig()
    {
        check_ajax_referer($this->plugin->meta_prefix, 'nonce');

        if (! isset($_POST['config']) || '' === $_POST['config']) { // WPCS: input var ok.
            wp_send_json_error('GDPR Config Set Failed');
        }

        $first          = isset($_POST['first']) ? true : false; // Input var okay.
        $current_config = get_option('sharethis_button_config', true);
        $config         = false !== $current_config && null !== $current_config ? $current_config : array();
        $config['gdpr'] = $_POST['config']; // WPCS: input var ok.

        // Make sure bool is "true" or "false".
        $config['gdpr']['enabled'] = true === $config['gdpr']['enabled'] ||
                                     '1' === $config['gdpr']['enabled'] ||
                                     'true' === $config['gdpr']['enabled'] ? 'true' : 'false';

        // Add purposes back
        $config['gdpr']['publisher_purposes'] = $purposes;

        update_option('sharethis_button_config', $config);

        if ($first) {
            update_option('sharethis_gdpr', 'true');
        }
    }

	/**
	 * Helper function get vendors.
	 *
	 * @param string $page
	 * @return array
	 */
    private function getVendors()
    {
    	$response = wp_remote_get('https://vendorlist.consensu.org/v2/vendor-list.json');

    	return json_decode(wp_remote_retrieve_body($response), true);
    }
}
