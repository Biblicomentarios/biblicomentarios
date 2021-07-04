<?php
class IRP_Tabs {
    private $tabs = array();

    function init() {
        global $irp;
        if($irp->Utils->isAdminUser()) {
            add_action('admin_menu', array(&$this, 'attachMenu'));
            add_filter('plugin_action_links', array(&$this, 'pluginActions'), 10, 2);
            if($irp->Utils->isPluginPage()) {
                add_action('admin_enqueue_scripts', array(&$this, 'enqueueScripts'));
            }
        }
    }

    function attachMenu() {
        global $irp;

        if(!$irp->Plugin->isActive(IRP_PLUGINS_INTELLY_RELATED_POSTS_PRO)) {
            $name='Inline Related Posts';
            add_submenu_page('options-general.php'
                , $name, $name
                , 'manage_options', IRP_PLUGIN_SLUG, array(&$this, 'showTabPage'));
        }
    }
    function pluginActions($links, $file) {
        global $irp;
        if($file==IRP_PLUGIN_SLUG.'/index.php'){
            $settings = "<a href='".IRP_PAGE_SETTINGS."'>" . $irp->Lang->L('Settings') . '</a> ';
            $url=IRP_INTELLYWP_SITE.IRP_PLUGIN_SLUG.'?utm_source=free-users&utm_medium=irp-plugins&utm_campaign=IRP';
            $premium = "<a href='".$url."' target='_blank'>" . $irp->Lang->L('PREMIUM') . '</a> ';
            $links = array_merge(array($settings, $premium), $links);
        }
        return $links;
    }
    function enqueueScripts() {
        global $irp;
        wp_enqueue_script('jquery');
        wp_enqueue_script('suggest');
        wp_enqueue_script('jquery-ui-autocomplete');

        $uri='//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css';
        wp_enqueue_style('font-awesome', $uri);

        $this->wpEnqueueStyle('assets/css/style.css');
        $this->wpEnqueueStyle('assets/deps/select2-3.5.2/select2.css');
        $this->wpEnqueueScript('assets/deps/select2-3.5.2/select2.min.js');
        $this->wpEnqueueScript('assets/deps/starrr/starrr.js');

        $this->wpEnqueueScript('assets/deps/qtip/jquery.qtip.min.js');
        $this->wpEnqueueScript('assets/js/common.js');
    }
    function wpEnqueueStyle($uri, $name='') {
        if($name=='') {
            $name=explode('/', $uri);
            $name=$name[count($name)-1];
            $dot=strrpos($name, '.');
            if($dot!==FALSE) {
                $name=substr($name, 0, $dot);
            }
            $name=IRP_PLUGIN_PREFIX.'_'.$name;
        }

        $v='?v='.IRP_PLUGIN_VERSION;
        wp_enqueue_style($name, IRP_PLUGIN_URI.$uri.$v);
    }
    function wpEnqueueScript($uri, $name='', $version=FALSE) {
        if($name=='') {
            $name=explode('/', $uri);
            $name=$name[count($name)-1];
            $dot=strrpos($name, '.');
            if($dot!==FALSE) {
                $name=substr($name, 0, $dot);
            }
            $name=IRP_PLUGIN_PREFIX.'_'.$name;
        }

        $v='?v='.IRP_PLUGIN_VERSION;
        $deps=array();
        wp_enqueue_script($name, IRP_PLUGIN_URI.$uri.$v, $deps, $version, FALSE);
    }

    function showTabPage() {
        global $irp;

        if($irp->Plugin->isActive(IRP_PLUGINS_INTELLY_RELATED_POSTS_PRO)) {
            $irp->Options->pushWarningMessage('YouHaveThePremiumVersion', IRP_TAB_SETTINGS_URI);
            $irp->Options->writeMessages();
            return;
        }

        $defaultTab=IRP_TAB_SETTINGS;
        if($irp->Options->isShowWhatsNew()) {
            $tab=IRP_TAB_WHATS_NEW;
            $defaultTab=$tab;
            $this->tabs[IRP_TAB_WHATS_NEW]=$irp->Lang->L('What\'s New');
        } else {
            $tab = $irp->Utils->qs('tab', $defaultTab);
            $this->tabs[IRP_TAB_SETTINGS] = $irp->Lang->L('Settings');
            $this->tabs[IRP_TAB_DOCS] = $irp->Lang->L('FAQ & Docs');
        }

        ?>
        <div class="wrap" style="margin:5px;">
            <?php
            $this->showTabs($defaultTab);
            $header='';
            switch ($tab) {
                case IRP_TAB_SETTINGS:
                    $header='Settings';
                    break;
                case IRP_TAB_WHATS_NEW:
                    $header='';
                    break;
            }

            if($irp->Lang->H($header.'Title')) { ?>
                <h2><?php $irp->Lang->P($header . 'Title', IRP_PLUGIN_VERSION) ?></h2>
                <?php if ($irp->Lang->H($header . 'Subtitle')) { ?>
                    <div><?php $irp->Lang->P($header . 'Subtitle') ?></div>
                <?php } ?>
                <div style="clear:both;"></div>
            <?php }

            if($tab!=IRP_TAB_WHATS_NEW) {
                irp_ui_first_time();
            }

            switch ($tab) {
                case IRP_TAB_SETTINGS:
                    irp_ui_settings();
                    break;
                case IRP_TAB_WHATS_NEW:
                    irp_ui_whats_new();
                    break;
            }

            if($irp->Options->isShowWhatsNew()) {
                $irp->Options->setShowWhatsNew(FALSE);
            }
            ?>
        </div>
    <?php }

    function getPluginsCount() {
        global $irp;
        $index=1;
        while($irp->Lang->H('Plugin'.$index.'.Name')) {
            $index++;
        }
        return $index-1;
    }
    function drawPluginWidget($id) {
        global $irp;
        ?>
        <div class="irp-plugin-widget">
            <b><?php $irp->Lang->P('Plugin'.$id.'.Name') ?></b>
            <br>
            <i><?php $irp->Lang->P('Plugin'.$id.'.Subtitle') ?></i>
            <br>
            <ul style="list-style: circle;">
                <?php
                $index=1;
                while($irp->Lang->H('Plugin'.$id.'.Feature'.$index)) { ?>
                    <li><?php $irp->Lang->P('Plugin'.$id.'.Feature'.$index) ?></li>
                    <?php $index++;
                } ?>
            </ul>
            <a style="float:right;" class="button-primary" href="<?php $irp->Lang->P('Plugin'.$id.'.Permalink') ?>" target="_blank">
                <?php $irp->Lang->P('PluginCTA')?>
            </a>
            <div style="clear:both"></div>
        </div>
        <br>
    <?php }
    function drawContactUsWidget() {
        global $irp;
        ?>
        <b><?php $irp->Lang->P('Sidebar.Title') ?></b>
        <ul style="list-style: circle;">
            <?php
            $index=1;
            while($irp->Lang->H('Sidebar'.$index.'.Name')) { ?>
                <li>
                    <a href="<?php $irp->Lang->P('Sidebar'.$index.'.Url')?>" target="_blank">
                        <?php $irp->Lang->P('Sidebar'.$index.'.Name')?>
                    </a>
                </li>
                <?php $index++;
            } ?>
        </ul>
    <?php }

    function showTabs($defaultTab) {
        global $irp;
        $tab=$irp->Check->of('tab', $defaultTab);
        if($tab==IRP_TAB_DOCS) {
            $irp->Utils->redirect(IRP_TAB_DOCS_URI);
        }
        ?>
        <h2 class="nav-tab-wrapper" style="float:left; width:97%;">
            <?php
            foreach ($this->tabs as $k=>$v) {
                $active = ($tab==$k ? 'nav-tab-active' : '');
                $target='_self';

                $styles=array();
                $styles[]='float:left';
                $styles[]='margin-left:10px';
                if($k==IRP_TAB_DOCS) {
                    $target='_blank';
                    $styles[] ='background-color:#F2E49B';
                }
                $styles=implode(';', $styles);
                ?>
                <a target="<?php echo $target ?>"  style="<?php echo $styles?>" class="nav-tab <?php echo $active?>" href="?page=<?php echo IRP_PLUGIN_SLUG?>&tab=<?php echo $k?>"><?php echo $v?></a>
            <?php
            }
            ?>
            <style>
                .starrr {display:inline-block}
                .starrr i{font-size:16px;padding:0 1px;cursor:pointer;color:#2ea2cc;}
            </style>
            <div style="float:right; display:none;" id="rate-box">
                <span style="font-weight:700; font-size:13px; color:#555;"><?php $irp->Lang->P('Rate us')?></span>
                <div id="irp-rate" class="starrr" data-connected-input="irp-rate-rank"></div>
                <input type="hidden" id="irp-rate-rank" name="irp-rate-rank" value="5" />
                <?php  $irp->Utils->twitter('intellywp') ?>
            </div>
            <script>
                jQuery(function() {
                    jQuery(".starrr").starrr();
                    jQuery('#irp-rate').on('starrr:change', function(e, value){
                        var url='https://wordpress.org/support/view/plugin-reviews/<?php echo IRP_PLUGIN_SLUG?>?rate=5#postform';
                        window.open(url);
                    });
                    jQuery('#rate-box').show();
                });
            </script>
        </h2>
        <div style="clear:both;"></div>
    <?php }
}
