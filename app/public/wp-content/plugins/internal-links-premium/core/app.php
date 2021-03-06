<?php
namespace ILJ\Core;

use ILJ\Backend\AdminMenu;
use ILJ\Backend\MenuPage\Tools;
use ILJ\Backend\RatingNotifier;
use ILJ\Backend\User;
use ILJ\Helper\Replacement;
use ILJ\Backend\Environment;
use ILJ\Helper\Capabilities;
use ILJ\Enumeration\TagExclusion;
use ILJ\Backend\Menupage\Settings;

/**
 * The main app
 *
 * Coordinates all steps for the plugin usage
 *
 * @package ILJ\Core
 *
 * @since 1.0.1
 */
class App
{
    private static $instance = null;

    /**
     * Initializes the construction of the app
     *
     * @static
     * @since  1.0.1
     *
     * @return void
     */
    public static function init()
    {
        if (null !== self::$instance) {
            return;
        }

        self::$instance = new self;

        $last_version = Environment::get('last_version');

        if ($last_version != ILJ_VERSION) {
            ilj_install_db();
            Options::setOptionsDefault();
        }

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            if (!Environment::get('pro_initiated')) {
                Options::setOptionsDefault();
            }
        }
    }

    protected function __construct()
    {
        $this->initSettings();
        $this->loadIncludes();

        add_action('admin_init', ['\ILJ\Core\Options', 'init']);
        add_action('admin_init', ['\ILJ\Backend\Editor', 'addAssets']);
        add_action('plugins_loaded', [$this, 'afterPluginsLoad']);
        add_action('publish_future_post', [$this, 'publishFuturePost'], 99);
        add_action(IndexBuilder::ILJ_ACTION_TRIGGER_BUILD_INDEX, [$this, 'triggerRebuildIndex']);

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            add_action(
                'init', function () {
                    \ILJ\Posttypes\CustomLinks::init();

		            if (class_exists('WP_CLI')) {
			            \WP_CLI::add_command('ilj index', '\ILJ\Command\IndexCommand');
			            \WP_CLI::add_command('ilj import', '\ILJ\Command\ImportCommand');
			            \WP_CLI::add_command('ilj export', '\ILJ\Command\ExportCommand');
		            }
                }
            );

            add_action(
                'wp_loaded', function () {
                    $taxonomies = get_taxonomies(
                        [
                            'public'   => true,
                            '_builtin' => false
                        ]
                    );

                    $taxonomies = array_merge($taxonomies, ['category', 'post_tag']);

                    foreach ($taxonomies as $taxonomy) {
                        add_action($taxonomy . '_edit_form_fields', ['\ILJ\Backend\Editor', 'renderKeywordMetaBoxTaxonomy__premium_only']);
                        add_action('edit_' . $taxonomy, ['\ILJ\Backend\Editor', 'saveKeywordMetaTaxonomy__premium_only']);
                    }
                }
            );
        }
    }

    /**
     * Initialising all menu and settings related stuff
     *
     * @since 1.0.1
     *
     * @return void
     */
    protected function initSettings()
    {
        add_action('admin_menu', ['\ILJ\Backend\AdminMenu', 'init']);
        add_filter('plugin_action_links_' . ILJ_NAME, [$this, 'addSettingsLink']);
    }

    /**
     * Loads all include files
     *
     * @since 1.0.1
     *
     * @return void
     */
    public function loadIncludes()
    {
        $include_files = ['install', 'uninstall'];

        foreach ($include_files as $file) {
            include_once ILJ_PATH . 'includes/' . $file . '.php';
        }
    }

    /**
     * Handles post transitions for scheduled posts
     *
     * @since 1.1.5
     * @param int $post_id Post ID.
     *
     * @return void
     */
    public function publishFuturePost($post_id)
    {
        if (!$this->postAffectsIndex($post_id)) {
            return;
        }

        do_action(IndexBuilder::ILJ_ACTION_TRIGGER_BUILD_INDEX);
    }

    /**
     * Gets called after all plugins are loaded for registering actions and filter
     *
     * @since 1.0.1
     *
     * @return void
     */
    public function afterPluginsLoad()
    {
        Compat::init();
        RatingNotifier::init();

        $this->registerActions();
        $this->registerFilter();

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            $text_domain = 'internal-links';
            $locale = apply_filters('plugin_locale', determine_locale(), $text_domain);
            $mofile = $text_domain . '-' . $locale . '.mo';
            load_textdomain($text_domain, ILJ_PATH . '/languages/' . $mofile);
        } else {
            load_plugin_textdomain(
                'internal-links',
                false,
                false
            );
        }

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            \ILJ\Core\Shortcodes::register();
        }
    }

    /**
     * Registers all actions for the plugin
     *
     * @since 1.1.5
     *
     * @return void
     */
    protected function registerActions()
    {
        $capability = current_user_can('administrator');

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            $configured_role = Capabilities::mapRoleToCapability(Options::getOption(\ILJ\Core\Options\EditorRole::getKey()));

            if ($configured_role && $configured_role != '') {
                $capability = current_user_can($configured_role);
            }
        }

        add_action('admin_post_' . Options::KEY, array('\ILJ\Helper\Post', 'resetOptionsAction'));

        if (!$capability) {
            return;
        }

        add_action('load-post.php', ['\ILJ\Backend\Editor', 'addKeywordMetaBox']);
        add_action('load-post-new.php', ['\ILJ\Backend\Editor', 'addKeywordMetaBox']);
        add_action('save_post', ['\ILJ\Backend\Editor', 'saveKeywordMeta'], 10, 2);
        
        add_action('wp_ajax_ilj_search_posts', ['\ILJ\Helper\Ajax', 'searchPostsAction']);
        add_action('wp_ajax_ilj_hide_promo', ['\ILJ\Helper\Ajax', 'hidePromo']);
        add_action('wp_loaded', ['\ILJ\Backend\Column', 'addConfiguredLinksColumn']);
        add_action('wp_ajax_ilj_rating_notification_add', ['\ILJ\Helper\Ajax', 'ratingNotificationAdd']);
        add_action('wp_ajax_ilj_upload_import', ['\ILJ\Helper\Ajax', 'uploadImport']);
        add_action('wp_ajax_ilj_start_import', ['\ILJ\Helper\Ajax', 'startImport']);
        add_action('wp_ajax_ilj_export_settings', ['\ILJ\Helper\Ajax', 'exportSettings']);
        add_action('wp_ajax_ilj_render_link_detail_statistic', ['\ILJ\Helper\Ajax', 'renderLinkDetailStatisticAction']);
        add_action('wp_ajax_ilj_render_links_statistic', ['\ILJ\Helper\Ajax', 'renderLinksStatisticAction']);
        add_action('wp_ajax_ilj_render_anchor_detail_statistic', ['\ILJ\Helper\Ajax', 'renderAnchorDetailStatisticAction']);
        add_action('wp_ajax_ilj_render_anchors_statistic', ['\ILJ\Helper\Ajax', 'renderAnchorsStatistic']);

        $this->addPostIndexTrigger();

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            add_action('wp_ajax_ilj_search_terms', ['\ILJ\Helper\Ajax', 'searchTermsAction__premium_only']);
            add_action('wp_ajax_ilj_import_keyword_intern', ['\ILJ\Helper\Ajax', 'importKeywordIntern__premium_only']);

            $this->addTermsIndexTrigger__premium_only();

            if (Options::getOption(\ILJ\Core\Options\IndexGeneration::getKey()) === \ILJ\Enumeration\IndexMode::MANUALLY) {
                $indexRebuildNotifier = new \ILJ\Backend\IndexRebuildNotifier();
                $indexRebuildNotifier->addActions();
                return;
            }
        }
    }

    /**
     * Triggers all actions for automatic index building mode.
     *
     * @since  1.1.0
     * @return void
     */
    protected function addPostIndexTrigger()
    {
        add_action(
            'publish_future_post', function ($post_id) {
                if (!$this->postAffectsIndex($post_id)) {
                    return;
                }
                $this->triggerRebuildIndex();
            }, 99
        );

        //rebuild index after keyword meta got updated on gutenberg editor:
        add_action(
            'updated_post_meta', function ($meta_id, $post_id, $meta_key, $meta_value) {
                if (!is_admin() || !function_exists('get_current_screen')) {
                    return;
                }

                $current_screen = get_current_screen();
                
                if ($meta_key != \ILJ\Database\Postmeta::ILJ_META_KEY_LINKDEFINITION || !$current_screen || !method_exists($current_screen, 'is_block_editor') || !$current_screen->is_block_editor() ) {
                    return;
                }

                if (!$this->postAffectsIndex($post_id)) {
                    return;
                }

                $this->triggerRebuildIndex();
            }, 10, 4
        );

        add_action(
            'transition_post_status', function ($new_status, $old_status, $post) {
                if ($old_status != 'publish' && $new_status != 'publish') {
                    return;
                }

                if (empty($_POST) && $new_status != 'trash') {
                    return;
                }

                $this->triggerRebuildIndex();
            }, 10, 3
        );
    }

    protected function addTermsIndexTrigger__premium_only()
    {
        add_action('create_term', [$this, 'triggerRebuildIndex']);
        add_action('edit_term', [$this, 'triggerRebuildIndex']);
        add_action('delete_term_taxonomy', [$this, 'triggerRebuildIndex']);
    }

    /**
     * Triggers the recreation of the linkindex
     *
     * @since  1.1.0
     * @return void
     */
    public function triggerRebuildIndex()
    {
        User::update('index', ['last_trigger' => new \DateTime()]);

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            $index_mode = Options::getOption(\ILJ\Core\Options\IndexGeneration::getKey());

            if ($index_mode === \ILJ\Enumeration\IndexMode::NONE) {
                return;
            }

            if ($index_mode === \ILJ\Enumeration\IndexMode::MANUALLY) {
                \ILJ\Backend\IndexRebuildNotifier::setNotifier();
                return;
            }
        }

        add_action(
            'shutdown', function () {
                $index_builder = new IndexBuilder();
                $index_builder->buildIndex();
            }
        );
    }

    /**
     * Registers plugin relevant filters
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function registerFilter()
    {
        add_filter(
            'the_content', function ($content) {
                $link_builder = new LinkBuilder(get_the_ID(), 'post');
                return $link_builder->linkContent($content);
            }, 99
        );

        $tag_exclusions = Options::getOption(\ILJ\Core\Options\NoLinkTags::getKey());

        if (is_array($tag_exclusions) && count($tag_exclusions)) {
            add_filter(
                Replacement::ILJ_FILTER_EXCLUDE_TEXT_PARTS, function ($search_parts) use ($tag_exclusions) {
                    foreach ($tag_exclusions as $tag_exclusion) {
                        $regex = TagExclusion::getRegex($tag_exclusion);

                        if ($regex) {
                            $search_parts[] = $regex;
                        }
                    }
                    return $search_parts;
                }
            );
        }

        \ILJ\ilj_fs()->add_filter(
            'reshow_trial_after_every_n_sec', function ($thirty_days_in_sec) {
                // 40 days in sec.
                return 60 * 24 * 60 * 60;
            }
        );

        \ILJ\ilj_fs()->add_filter(
            'show_first_trial_after_n_sec', function ($day_in_sec) {
                // 3 days in sec.
                return 3 * 24 * 60 * 60;
            }
        );

        \ILJ\ilj_fs()->add_filter(
            'show_affiliate_program_notice', function () {
                return false;
            }
        );

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            add_filter(Tools::ILJ_MENUPAGE_TOOLS_IMPORT_INTERN_POST . '-'. 'title', ['\ILJ\Backend\MenuPage\Tools', 'getImportKeywordsByPostTitle__premium_only']);

            add_filter(
                'term_description', function ($description) {
                    if (is_admin()) {
                         return $description;
                    }

                    $link_builder = new LinkBuilder(get_queried_object()->term_id, 'term');
                    return $link_builder->linkContent($description);
                }
            );
        }
    }

    /**
     * Adds a link to the plugins settings page on plugins overview
     *
     * @since 1.0.0
     *
     * @param  array $links All links that get displayed
     * @return array
     */
    public function addSettingsLink($links)
    {
        $settings_link = '<a href="admin.php?page=' . AdminMenu::ILJ_MENUPAGE_SLUG . '-' . Settings::ILJ_MENUPAGE_SETTINGS_SLUG . '">' . __('Settings') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Checks if a (changed) post affects the index creation
     *
     * @since  1.2.0
     * @param  int $post_id The ID of the post
     * @return bool
     */
    protected function postAffectsIndex($post_id)
    {
        $post = get_post($post_id);

        if (!$post || !in_array($post->post_status, ['publish', 'trash'])) {
            return false;
        }

        return true;
    }
}
