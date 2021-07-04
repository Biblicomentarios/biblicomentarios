<?php

namespace DevOwl\RealCategoryLibrary;

use DevOwl\RealCategoryLibrary\Vendor\DevOwl\Freemium\Assets as FreemiumAssets;
use DevOwl\RealCategoryLibrary\base\UtilsProvider;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core as RpmWpClientCore;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License;
use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Assets as UtilsAssets;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Asset management for frontend scripts and styles.
 */
class Assets {
    use UtilsProvider;
    use UtilsAssets;
    use FreemiumAssets;
    const CUSTOM_POST_TYPE_UI_FILE = 'custom-post-type-ui/custom-post-type-ui.php';
    const REAL_CUSTOM_POST_ORDER_FILE = 'real-custom-post-order/index.php';
    /**
     * Enqueue scripts and styles depending on the type. This function is called
     * from both admin_enqueue_scripts and wp_enqueue_scripts. You can check the
     * type through the $type parameter. In this function you can include your
     * external libraries from src/public/lib, too.
     *
     * @param string $type The type (see utils Assets constants)
     * @param string $hook_suffix The current admin page
     */
    public function enqueue_scripts_and_styles($type, $hook_suffix = null) {
        $isConfigPage = \DevOwl\RealCategoryLibrary\Core::getInstance()
            ->getConfigPage()
            ->isVisible($hook_suffix);
        // Generally check if an entrypoint should be loaded
        if ((!$isConfigPage && !\in_array($type, [self::$TYPE_ADMIN], \true)) || !wp_rcl_active()) {
            return;
        }
        // jQuery scripts (Helper) core.js, widget.js, mouse.js, draggable.js, droppable.js, sortable.js
        $requires = [
            'jquery',
            'jquery-ui-core',
            'jquery-ui-widget',
            'jquery-ui-mouse',
            'jquery-ui-draggable',
            'jquery-ui-droppable',
            'jquery-ui-sortable',
            'jquery-touch-punch'
        ];
        $realUtils = RCL_ROOT_SLUG . '-real-utils-helper';
        \array_walk($requires, 'wp_enqueue_script');
        // Your assets implementation here... See utils Assets for enqueue* methods
        // $useNonMinifiedSources = $this->useNonMinifiedSources(); // Use this variable if you need to differ between minified or non minified sources
        // Our utils package relies on jQuery, but this shouldn't be a problem as the most themes still use jQuery (might be replaced with https://github.com/github/fetch)
        // Enqueue external utils package
        $scriptDeps = $this->enqueueUtils();
        $scriptDeps = \array_merge($scriptDeps, [$realUtils], $requires);
        // real-product-manager-wp-client (for licensing purposes)
        \array_unshift(
            $scriptDeps,
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Core::getInstance()
                ->getAssets()
                ->enqueue($this)
        );
        if ($isConfigPage) {
            $handle = $this->enqueueScript(
                'options',
                [[$this->isPro(), 'options.pro.js'], 'options.lite.js'],
                $scriptDeps
            );
            $this->enqueueStyle('options', 'options.css', [$realUtils]);
        } else {
            $scriptDeps = \array_merge($scriptDeps, ['react-aiot.vendor', 'react-aiot'], $requires);
            $this->enqueueLibraryScript('react-aiot.vendor', 'react-aiot/umd/react-aiot.vendor.umd.js', [
                self::$HANDLE_REACT_DOM
            ]);
            $this->enqueueLibraryScript('react-aiot', 'react-aiot/umd/react-aiot.umd.js', ['react-aiot.vendor']);
            $this->enqueueLibraryStyle('react-aiot.vendor', 'react-aiot/umd/react-aiot.vendor.umd.css');
            $this->enqueueLibraryStyle('react-aiot', 'react-aiot/umd/react-aiot.umd.css', ['react-aiot.vendor']);
            // Enqueue plugin entry points
            $handle = $this->enqueueScript('admin', [[$this->isPro(), 'admin.pro.js'], 'admin.lite.js'], $scriptDeps);
            $this->enqueueStyle('admin', 'admin.css', [$realUtils]);
            // Add inline-style to avoid flickering effect
            if ($this->isScreenBase('edit')) {
                wp_add_inline_style($handle, '#wpbody { display: none; }');
            }
        }
        // Localize script with server-side variables
        wp_localize_script($handle, 'realCategoryLibrary', $this->localizeScript($type));
    }
    /**
     * Localize the WordPress backend and frontend. If you want to provide URLs to the
     * frontend you have to consider that some JS libraries do not support umlauts
     * in their URI builder. For this you can use utils Assets#getAsciiUrl.
     *
     * Also, if you want to use the options typed in your frontend you should
     * adjust the following file too: src/public/ts/store/option.tsx
     *
     * @param string $context
     * @return array
     */
    public function overrideLocalizeScript($context) {
        $core = \DevOwl\RealCategoryLibrary\Core::getInstance();
        $defaultTaxTree = $core->getDefaultTaxTree();
        $canInstallPlugins = current_user_can('activate_plugins');
        $isAvailable = $defaultTaxTree->isAvailable(\true);
        $pluginUpdater = $core->getRpmInitiator()->getPluginUpdater();
        $licenseActivation = $pluginUpdater->getCurrentBlogLicense()->getActivation();
        $showLicenseFormImmediate = !$licenseActivation->hasInteractedWithFormOnce();
        $isDevLicense =
            $licenseActivation->getInstallationType() ===
            \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\license\License::INSTALLATION_TYPE_DEVELOPMENT;
        return \array_merge(
            [
                'showLicenseFormImmediate' => $showLicenseFormImmediate,
                'isDevLicense' => $isDevLicense,
                'licenseActivationLink' => $pluginUpdater->getView()->getActivateLink(\true),
                'tableCheckboxName' => $defaultTaxTree->getTableCheckboxName(),
                'installPluginNonce' => $canInstallPlugins ? wp_create_nonce('updates') : '',
                'pluginCptUi' => [
                    'manageTaxonomiesUrl' => admin_url('admin.php?page=cptui_manage_taxonomies&show_ui=true'),
                    'active' => is_plugin_active(self::CUSTOM_POST_TYPE_UI_FILE),
                    'installed' => \file_exists(path_join(\constant('WP_PLUGIN_DIR'), self::CUSTOM_POST_TYPE_UI_FILE)),
                    'installUrl' => admin_url('plugin-install.php') . '?s=Custom+Post+Type+UI&tab=search&type=term',
                    'activateUrl' => $canInstallPlugins
                        ? add_query_arg(
                            '_wpnonce',
                            wp_create_nonce('activate-plugin_' . self::CUSTOM_POST_TYPE_UI_FILE),
                            admin_url(
                                'plugins.php?action=activate&plugin=' . \urlencode(self::CUSTOM_POST_TYPE_UI_FILE)
                            )
                        )
                        : ''
                ],
                'pluginRcpo' => [
                    'active' => is_plugin_active(self::REAL_CUSTOM_POST_ORDER_FILE),
                    'installed' => \file_exists(
                        path_join(\constant('WP_PLUGIN_DIR'), self::REAL_CUSTOM_POST_ORDER_FILE)
                    ),
                    'installUrl' => admin_url('plugin-install.php') . '?s=Real+Custom+Post+Order&tab=search&type=term',
                    'activateUrl' => $canInstallPlugins
                        ? add_query_arg(
                            '_wpnonce',
                            wp_create_nonce('activate-plugin_' . self::REAL_CUSTOM_POST_ORDER_FILE),
                            admin_url(
                                'plugins.php?action=activate&plugin=' . \urlencode(self::REAL_CUSTOM_POST_ORDER_FILE)
                            )
                        )
                        : ''
                ],
                'postTypes' => \DevOwl\RealCategoryLibrary\TaxTree::getAvailablePostTypes(),
                'canManageOptions' => current_user_can(\DevOwl\RealCategoryLibrary\Core::MANAGE_MIN_CAPABILITY),
                'isAvailable' => $isAvailable,
                'screenSettings' => [
                    'isActive' =>
                        $isAvailable && \DevOwl\RealCategoryLibrary\Options::getInstance()->isActive($defaultTaxTree),
                    'isFastMode' =>
                        $isAvailable && \DevOwl\RealCategoryLibrary\Options::getInstance()->isFastMode($defaultTaxTree)
                ],
                'blogId' => get_current_blog_id(),
                'simplePageOrdering' => is_plugin_active('simple-page-ordering/simple-page-ordering.php'),
                'simplePageOrderingLink' => admin_url('plugin-install.php?tab=search&s=simple+page+ordering'),
                'editOrderBy' => isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '',
                'editOrder' => isset($_GET['order']) ? sanitize_text_field($_GET['order']) : '',
                'typenow' => $defaultTaxTree->getTypeNow(),
                'taxnow' => $defaultTaxTree->getTaxNow() !== null ? $defaultTaxTree->getTaxNow()->objkey : '',
                'allPostCnt' => $defaultTaxTree->getPostCnt(),
                'taxos' => $defaultTaxTree->getTaxos(\true),
                'pluginsUrl' => admin_url('plugins.php')
            ],
            $this->localizeFreemiumScript()
        );
    }
}
