<?php

namespace ILJ\Backend\MenuPage;

use ILJ\Backend\AdminMenu;
use ILJ\Backend\MenuPage\AbstractMenuPage;

/**
 * The custom links page
 *
 * Displays the page for custom internal links
 *
 * @package ILJ\Backend\Menupage
 * @since   1.0.0
 */
class CustomLinks extends AbstractMenuPage
{
    public function __construct()
    {
        $this->page_slug  = 'customlinks';
        $this->page_title = __('Custom Links', 'internal-links');
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $submenu = add_submenu_page(
            AdminMenu::ILJ_MENUPAGE_SLUG,
            $this->getTitle() . ' - Internal Link Juicer',
            $this->getTitle(),
            'manage_options',
            'edit.php?post_type=ilj_customlinks',
            false
        );
        $this->page_hook = $submenu;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        return;
    }

}
