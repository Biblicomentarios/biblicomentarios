<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Usage tracking
 *
 * @access public
 * @since  1.8.2
 * @return void
 */
class IRP_Tracking {

    public function __construct() {
        //We send once a week (while tracking is allowed) to check in which can be used
        //to determine active sites
        add_action('irp_weekly_scheduled_events', array($this, 'sendTracking'));

        //add_action('irp_tracking_on', array($this, 'enableTracking'));
        //add_action('irp_tracking_off', array($this, 'disableTracking'));
        //add_action('admin_notices', array($this, 'admin_notice'));
    }

    private function getThemeData() {
        $theme_data     = wp_get_theme();
        $theme          = array(
            'name'       => $theme_data->display( 'Name', false, false ),
            'theme_uri'  => $theme_data->display( 'ThemeURI', false, false ),
            'version'    => $theme_data->display( 'Version', false, false ),
            'author'     => $theme_data->display( 'Author', false, false ),
            'author_uri' => $theme_data->display( 'AuthorURI', false, false ),
        );
        $theme_template = $theme_data->get_template();
        if ( $theme_template !== '' && $theme_data->parent() ) {
            $theme['template'] = array(
                'version'    => $theme_data->parent()->display( 'Version', false, false ),
                'name'       => $theme_data->parent()->display( 'Name', false, false ),
                'theme_uri'  => $theme_data->parent()->display( 'ThemeURI', false, false ),
                'author'     => $theme_data->parent()->display( 'Author', false, false ),
                'author_uri' => $theme_data->parent()->display( 'AuthorURI', false, false ),
            );
        }
        else {
            $theme['template'] = '';
        }
        unset( $theme_template );
        return $theme;
    }
    private function getPluginData() {
        //retrieve plugins (active/inactive) list
        if(!function_exists('get_plugins')) {
            include ABSPATH . '/wp-admin/includes/plugin.php';
        }

        $plugins=array();
        $active_plugin = get_option( 'active_plugins' );
        foreach ( $active_plugin as $plugin_path ) {
            if ( ! function_exists( 'get_plugin_data' ) ) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }

            $plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path );

            $slug= str_replace( '/' . basename( $plugin_path ), '', $plugin_path );
            $plugins[ $slug ] = array(
                'version'    => $plugin_info['Version'],
                'name'       => $plugin_info['Name'],
                'plugin_uri' => $plugin_info['PluginURI'],
                'author'     => $plugin_info['AuthorName'],
                'author_uri' => $plugin_info['AuthorURI'],
            );
        }
        unset( $active_plugins, $plugin_path );
        return $plugins;
    }
    //obtain tracking data into an associative array
    public function getData() {
        global $irp;

        //retrieve blog info
        $result['wp_url']=home_url();
        $result['wp_version']=get_bloginfo('version');
        $result['wp_language']=get_bloginfo('language');
        $result['wp_wpurl']=get_bloginfo('wpurl');
        $result['wp_admin_email']=get_bloginfo('admin_email');

        $result['plugins']=$this->getPluginData();
        $result['theme']=$this->getThemeData();

        //to obtain for each post type its count
        $post_types=$irp->Utils->query(IRP_QUERY_POST_TYPES);
        $data=array();
        foreach ($post_types as $v) {
            $v=$v['name'];
            $data[$v]=intval(wp_count_posts($v)->publish);
        }
        $result['post_types']=$data;

        $data=array(
            'debug'=>(defined('WP_DEBUG') && WP_DEBUG)
            , 'maxTime'=>$irp->Options->getMaxExecutionTime()
            , 'active'=>$irp->Options->isActive()
            , 'engineSearch'=>$irp->Options->getEngineSearch()
            , 'rewriteAtEnd'=>$irp->Options->isRewriteAtEnd()
            , 'rewriteThresold'=>$irp->Options->getRewriteThreshold()
            , 'rewriteBoxesCount'=>$irp->Options->getRewriteBoxesCount()
            , 'rewriteActive'=>$irp->Options->isRewriteActive()
            , 'metaboxPostTypes'=>$irp->Options->getMetaboxPostTypes()
            , 'rewritePostTypes'=>$irp->Options->getRewritePostTypes()
        );
        $data=$irp->Utils->parseArgs($irp->Options->getTemplateStyle(), $data);
        $result['iwpm_plugin_name']=IRP_PLUGIN_SLUG;
        $result['iwpm_plugin_version']=IRP_PLUGIN_VERSION;
        $result['iwpm_plugin_data']=$data;
        $result['iwpm_plugin_install_date']=$irp->Options->getPluginInstallDate();
        $result['iwpm_plugin_update_date']=$irp->Options->getPluginUpdateDate();

        $result['iwpm_tracking_enable']=$irp->Options->isTrackingEnable();
        $result['iwpm_logger_enable']=$irp->Options->isLoggerEnable();
        $result['iwpm_feedback_email']=$irp->Options->getFeedbackEmail();

        return $result;
    }

    //send tracking data info to our server
    public function sendTracking($override = FALSE) {
        global $irp;

        $result=-1;
        if(!$override && !$irp->Options->isTrackingEnable())
            return $result;

        // Send a maximum of once per week
        $last_send=$irp->Options->getTrackingLastSend();
        if(!$override && $last_send>strtotime('-1 week'))
            return $result;

        //add_filter('https_local_ssl_verify', '__return_false');
        //add_filter('https_ssl_verify', '__return_false');
        //add_filter('block_local_requests', '__return_false');
        $data=$irp->Utils->remotePost('usage', $this->getData());
        if($data) {
            $result=intval($data['id']);
            $irp->Options->setTrackingLastSend(time());
        }
        return $result;
    }

    public function enableTracking() {
        global $irp;

        $irp->Options->setTrackingEnable(TRUE);
        $irp->Options->setTrackingNotice(FALSE);
        $this->sendTracking(TRUE);
    }
    public function disableTracking() {
        global $irp;

        $irp->Options->setTrackingEnable(FALSE);
        $irp->Options->setTrackingNotice(FALSE);
        $this->sendTracking(TRUE);
    }

    public function admin_notice() {
        global $irp;

        if(!$irp->Options->isTrackingNotice() || $irp->Options->isTrackingEnable() || !current_user_can('manage_options'))
            return;

        if(FALSE && (
            stristr(network_site_url('/'), 'dev'      ) !== false ||
            stristr(network_site_url('/'), 'localhost') !== false ||
            stristr(network_site_url('/'), ':8888'    ) !== false // This is common with MAMP on OS X
           )) {
            $irp->Options->setTrackingNotice(TRUE);
        } else {
            $yes_url=add_query_arg('irp_action', 'manager_trackingOn');
            $no_url=add_query_arg('irp_action', 'manager_trackingOff');

            ?>
            <div class="updated">
                <p>
                    <?php $irp->Lang->P('Allow IntellyWP to track plugin usage?');?>
                    &nbsp;<a href="<?php echo esc_url($yes_url)?>" class="button-primary"><?php $irp->Lang->P('Oh yes :)')?></a>
                    &nbsp;<a href="<?php echo esc_url($no_url)?>" class="button-secondary"><?php $irp->Lang->P('Refuse!')?></a>
                </p>
            </div>
        <?php }
    }
}