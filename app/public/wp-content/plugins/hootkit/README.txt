=== HootKit ===
Contributors: wphoot
Tags: widgets, wphoot, demo content, slider
Requires at least: 5.0
Tested up to: 5.7
Requires PHP: 5.6
Stable tag: 2.0.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-3.0.html

HootKit is a great companion plugin for WordPress themes by wpHoot.

== Description ==

HootKit is a great companion plugin for WordPress themes by wpHoot.
This plugin adds extra widgets and features to your theme. Though it will work with any theme, HootKit is primarily developed to work in sync with WordPress themes by wpHoot.

Get free support at <a href="https://wphoot.com/support" target="_blank">wpHoot Support</a>

== Installation ==

1. In your wp-admin (WordPress dashboard), go to Plugins Menu > Add New
2. Search for 'Hootkit' in search field on top right.
3. In the search results, click on 'Install Now' button next to Hootkit result.
4. Once the installation is complete, click Activate button.

You can also install the plugin manually by following these steps:
1. Download the plugin zip file from https://wordpress.org/plugins/hootkit/
2. In your wp-admin (WordPress dashboard), go to Plugins Menu > Add New
3. Click the 'Upload Plugin' button at the top.
4. Upload the zip file you downloaded in Step 1.
5. Once the upload is finish, click on Activate.

== Frequently Asked Questions ==

= What is the plugin license? =

This plugin is released under a GPL license.

= Which themes does HootKit work with? =

The plugin supports all themes, but works best with wpHoot Themes. A few options are available only in compatible wpHoot Themes.

== Changelog ==

= 2.0.2 =
* Add filters to allow force load hootkit for non hoot themes

= 2.0.1 =
* Refactored dirname() usage for compatibility with PHP < 7.0

= 2.0.0 =
* Refactored internal code for more modularity for blocks implementation
* Add hk-gridunit-imglink class to content-grid-link (css bug fix for background image link)
* Added offset option to post grid widget

= 1.2.3 =
* Fix bug for certain widgets with SiteOrigin Page Builder

= 1.2.2 =
* Fix undeclared variable bug from v1.2.1
* Fix post-grid attr to hk-gridunit name bug from v1.2.1

= 1.2.1 =
* Added variable passed in context for link urls in various widgets
* Refactor Content Grid widget template
* Refactor Cover Image widget template
* Add slider option to Cover Image widget

= 1.2.0 =
* Added Top Banner with customizer settings for supporting themes
* Added Fly Cart with customizer settings for supporting themes
* Added Content Grid widget, Hero Image widget
* Include default support for 'post-grid-firstpost-slider' and 'announce-headline'
* Added Timer shortcode for supporting themes
* Added multiple woocommerce widgets
* Added max-width option for sliders
* Refactored code for activated/deactivated module variables
* Settings page for all modules

= 1.1.3 =
* Update hootkit.js for jQuery 3.5 for future versions of WordPress

= 1.1.2 =
* Improve accessibility (keyboard navigation) for ticker widget
* Fix select2 css for z-index in customizer view and overqualify over other plugins loading select2 css

= 1.1.1 =
* Added single/multiple line option to ticker and post ticker
* Bug fix: Adaptive height calculation in horizontal carousel upon load
* Updated select2 script from 4.0.7 to 4.0.13
* Fix select2 css for z-index in customizer view and overqualify over other plugins loading select2 css
* Fixed number argument in HootLit::get_terms()
* Fixed syntax from Hoot_List::categories('category') to Hoot_List::categories(0)
* Fixed polylang issue: Set extract_overwrite filter default to false so widget_title filter results stay

= 1.1.0 =
* Added settings page for user option - activate/deactivate specific modules. Also let themes define default activated modules.
* Updated arguments of apply_filter function for hook 'widget_title'
* Added Subtitle option and css class for widgets for themes which support it
* Combined 'viewall' link in widgets to single template function
* Combined string titles to separate location
* Add notices module, subsequently Add hootkit activate option with time (used for notices)
* Fix ticker post widget - remove flexbox in order to have max-width for content, add empty image to maintain unit height
* Remove height setting for ticker msg box using javascript
* Remove postlistcarousel markup for navigation (let theme templates handle it individually)
* Allow html tags in title (sanitized during display) by setting extract to overwrite - can be changed using filter
* Updated 'seperator' widget option type to simply display a visual separating line
* CSS fix for select2 container - overqualify to override display inline-block set in custom-sdiebars plugin
* Removed deprecated importer code (refer version 1.0.12)

= 1.0.17 =
* Updated image size for post-grid and post-list to croped sizes to prevent blurry images
* Add compatibility (for content block 5 images) with Jetpack lazy load (and other lazy plugins)

= 1.0.16 =
* Updates Announce widget options
* Bug Fix: Prevent error when active child theme deleted using FTP

= 1.0.15 =
* Add icon to Widget Ticker

= 1.0.14 =
* Added Post List Carousel widget for themes which support it
* Added wp_reset_postdata() for Ticker Posts
* Add ID to slider hoot data (for easy access via modification hook)

= 1.0.13 =
* Add Ticker Posts widget
* Add option to exclude posts categories in widgets ticker-posts, post-list, post-grid, slider-postcarousel, slider-postimage, content-posts-blocks
* Post Grid widget option changed from number of posts to number of rows

= 1.0.12 =
* Remove comma in inline background-image css (to prevent escape attribute which confuses lazy load plugins)
* Bug fix for empty values (widgets added via customizer)
* Disable importer since wptrt does not allow demo content to be included in themes anymore (code will be deleted shortly)
* Add child theme data (name and author uri) to hoot data

= 1.0.11 =
* Display Widget IDs for each widget on the Widgets screen (hence remove 'widgetid' option from HootKit widgets)
* Highlight Parent Theme in Theme list when a child theme is active
* Fixed args for 'hootkit_content_blocks_start' and 'hootkit_content_blocks_end' action in Content Block widget
* Improved javascript for 'content-block-style5' for newer themes

= 1.0.10 =
* Run script on content-block-style5 on $(window).load instead of $(document).ready to properly calculate image heights
* Added singleSlideView class to slider template (corresponds to multiSlideView for carousels in themes)
* Fixed args for 'hootkit_content_blocks_start' action in Content Block widget
* Added multiselect option (select2 script) for various posts widgets
* SiteOrigin Page Builder compatibility (live preview) (new widget instance doesnt have all option values when post is saved without editing widget even once (in Gutenberg only))

= 1.0.9 =
* Widget Post List - Added No thumbnail option
* Added 'View All' option to Posts Blocks widget, Posts Image slider and Posts Carousel Slider
* Fixed content-block-style5 javascript for certain edge case scenarios (height not set properly when js loads before image or mouse hover out)
* Improved one click description to make sure user understands (added manual input Accept)
* Increased hoot_admin_list_item_count to 999 to remove limitation on terms lists
* Added missing argument for 'the_title' filter to prevent error with certain plugins
* Added data-type argument to slider template

= 1.0.8 =
* Improved slider/carousel template to use custom classes (for different slider styles)
* Link titles for post slider and carousel

= 1.0.6 =
* Added style 5 and 6 support for Content Block and Content Posts Block widgets
* Added variables for scrollspeed and scrollpadding for developers to override it using child themes
* Fixed limit for Content Block widget

= 1.0.5 =
* Added compatibility with latest Hoot Framework functions in v3.0.1
* Added style option for Call To Action widget for themes which support it
* Added profile widget
* Fixed array_search sanitization for vcard urls

= 1.0.4 =
* Bug Fix: Removed Composer autoloader for OCDI whcih did not work on certain installation environments

= 1.0.3 =
* Added support for content installation (ocdi) functionality
* Added nav option and pause time option for sliders and carousels
* Exclude carousel images from Jetpack lazyload
* Update register and load action priority
* Added wphoot themelist to register
* Set theme details data if not present
* Menu order function for wphoot themes

= 1.0.2 =
* Added preset combo (preset colors with bright option)
* Slider and Icon template - Minor updates
* Slider template - Remove counter break (for more slides option)
* Added new widgets

= 1.0.1 =
* Added Icon color option for announce widget
* Added several action and filter hooks for developer modifications
* Compatibility fix for for Jetpack lazy load with sliders
* Reworked widget options array syntax for easy modification using filters
* Cleaned up code at several locations and removed redundant functions
* Updated CSS

= 1.0.0 =
* Initial Public Release