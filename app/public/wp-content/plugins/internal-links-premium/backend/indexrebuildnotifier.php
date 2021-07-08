<?php
namespace ILJ\Backend;

use ILJ\Core\Options;
use ILJ\Helper\Help;

class IndexRebuildNotifier
{

    /**
     * Adds actions for the notifier
     *
     * @return void
     * @since  1.1.3
     */
    public function addActions()
    {
        add_action('wp_ajax_ilj_rebuild_index', [ '\ILJ\Helper\Ajax', 'indexRebuildAction__premium_only' ]);
        //add_action(Editor::ILJ_ACTION_AFTER_KEYWORDS_UPDATE, [ '\ILJ\Backend\IndexRebuildNotifier', 'setNotifier' ]);
        add_action('admin_notices', [ '\ILJ\Backend\IndexRebuildNotifier', 'registerNotifier' ]);
        add_action('admin_enqueue_scripts', [ $this, 'registerAssets' ]);
        add_action('enqueue_block_editor_assets', [ $this, 'registerAssetsGutenberg' ]);
    }

    /**
     * Registers all assets for the frontend rebuild notification
     *
     * @return void
     * @since  1.1.3
     */
    public function registerAssets()
    {
        wp_enqueue_style('ilj_index_rebuild_notifier', ILJ_URL . 'admin/css/ilj_index_rebuild.css', [], ILJ_VERSION);

        $current_screen = get_current_screen();

        if (method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor() ) {
            return;
        }

        wp_register_script(
            'ilj_index_rebuild_notifier',
            ILJ_URL . 'admin/js/ilj_ajax_index_rebuild.js',
            [],
            ILJ_VERSION
        );

        wp_enqueue_script('ilj_index_rebuild_notifier');

        wp_localize_script(
            'ilj_index_rebuild_notifier', 'ilj_index_rebuild_notifier', [
            'error_500' => self::getErrorNotification()
            ] 
        );
    }

    /**
     * Registers assets for the notifier on pages with gutenberg editor
     *
     * @since 1.1.5
     *
     * @return void
     */
    public function registerAssetsGutenberg()
    {
        $current_screen = get_current_screen();

        if (!method_exists($current_screen, 'is_block_editor') || !$current_screen->is_block_editor() ) {
            return;
        }

        $notifier_flag = Options::getOption(Options::ILJ_OPTION_KEY_INDEX_NOTIFY);

        wp_register_script(
            'ilj_index_rebuild_notifier_gutenberg',
            ILJ_URL . 'admin/js/gutenberg/ilj_ajax_index_rebuild.js',
            array( 'wp-data', 'wp-editor', 'wp-i18n', 'wp-notices', 'wp-polyfill' ),
            ILJ_VERSION
        );

        wp_enqueue_script('ilj_index_rebuild_notifier_gutenberg');

        wp_localize_script(
            'ilj_index_rebuild_notifier_gutenberg', 'ilj_index_rebuild_notifier', [
            'notifier_flag' => $notifier_flag,
            'label'         => self::getRegisterNotifierLabel(),
            'message'       => self::getRegisterNotifierMessage(),
            'error_500' => self::getErrorNotification()
            ] 
        );
    }


    /**
     * Responsible for the notifier screen in the admin dashboard
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function registerNotifier()
    {
        $notifier_flag = Options::getOption(Options::ILJ_OPTION_KEY_INDEX_NOTIFY);

        if ($notifier_flag) {
            $notification_template = '<div class="%1$s"><p><strong>%2$s</strong></p><p>%3$s</p><p>%4$s<div class="clear"></div></p></div>';
            $class          = esc_attr('notice notice-warning is-dismissible ilj-index-rebuild-message');
            $message        = self::getRegisterNotifierMessage();
            $refresh_button = '<a class="button ilj-index-rebuild" href="#">' . self::getRegisterNotifierLabel() . '</a>';

            printf($notification_template, $class, 'Internal Link Juicer:', esc_html($message), $refresh_button);
            return;
        }
    }

    /**
     * Returns the label for the index-rebuild notifier
     *
     * @since 1.1.5
     *
     * @return string
     */
    private static function getRegisterNotifierLabel()
    {
        return  __('Rebuild index', 'internal-links');
    }

    /**
     * Returns the message for the index-rebuild notifier
     *
     * @since 1.1.5
     *
     * @return string
     */
    private static function getRegisterNotifierMessage()
    {
        return __('You have made changes to your internal link configuration. Please update the index to apply the changes.', 'internal-links');
    }

    /**
     * Returns an error notification
     *
     * @since 1.1.5
     *
     * @return string
     */
    private static function getErrorNotification()
    {
        $hide_notification = __('Hide this notification.', 'internal-links');
        $dismissable = sprintf('<button type="button" class="notice-dismiss"><span class="screen-reader-text">%s</span></button>', $hide_notification);
        $notification_template = '<div class="%1$s"><p><strong>%2$s</strong></p><p class="message">%3$s</p>%4$s</div>';
        $class          = esc_attr('notice notice-warning is-dismissible');
        $message        = __('An error has occurred. The problem may be insufficient server resources for a too large index setup. It is possible that the index has been built completely or only partially.', 'internal-links');
        $message       .= '<br/>' . __('Please consider switching to the CLI mode for index building', 'internal-links');
        $message       .= sprintf(' (<a href="%s" target="_blank" rel="noopener">', Help::getLinkUrl('index-generation-mode/', 'mode-none', 'warning', 'index rebuild notification')) . __('more information', 'internal-links') . '</a>).';

        return sprintf($notification_template, $class, 'Internal Link Juicer:', esc_html($message), $dismissable);
    }

    /**
     * Sets the notifier active
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function setNotifier()
    {
        Options::setOption(Options::ILJ_OPTION_KEY_INDEX_NOTIFY, 1);
    }

    /**
     * Sets the notifier inactive
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function unsetNotifier()
    {
        Options::setOption(Options::ILJ_OPTION_KEY_INDEX_NOTIFY, 0);
    }
}
