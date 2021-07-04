=== ImageInject ===
Contributors: thoefter, wp-scoop
Tags: insert, imageinject, wpinject, pictures, flickr, api, images, editor, photos, photo, image, inject, creative commons, creative, commons, gallery, media, thumbnail, seo, pixabay, caption, vector, graphics
Tested up to: 5.3
Stable tag: 1.18
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily insert images and photos into your blog posts! ImageInject searches the huge Flickr database for creative commons photos related to any topic.

== Description ==

[ImageInject](http://wpscoop.com/wp-inject "Free plugin to insert images into WordPress posts"), previously called WP Inject, allows you to **easily insert photos and images into your WordPress posts**. Search among many thousands of free creative commons images for any keywords of your choice, then pick the best of them for the article you are working on and insert them into your post or set them as featured image! Best of all it is super fast: Injecting an image only takes 3 clicks in total!

No further setup is required after installing ImageInject. You can start inserting photos right away! To do so the plugin adds an easy to use search metabox to your WordPress editor ("Add New" post screens). Simply enter any keyword to find great photos for your post. 

Advanced users can head to the ImageInject settings page and fine tune the plugin. A lot of different options let you control most aspects of how ImageInject finds and inserts images into your posts. By editing the templates you can control exactly how the photos and automatic attribution will look on your blog!

**Have a look at my [tutorial on how to install and use ImageInject](http://wpscoop.com/wp-inject/#docs "How to insert images with ImageInject") to get started with the plugin.** Besides explaining the basics the tutorial also contains details on all the available settings in ImageInject and how the plugin works together with WordPress SEO by Yoast. 

= 2019 Update =
After a long hiatus I finally found the time to update ImageInject. The latest version 1.17 fixes common issues with Flickr not working and ensures compatibility with the latest WordPress versions. The plugin **works with the Classic Editor plugin as well as with the new Gutenberg editor**, however for the time being the Gutenberg integration is relatively basic and uses the same old metabox. Once I get more comfortable with Gutenberg I will consider further improvements.

= Supported Image Sources: =
*  **Flickr** - Over 200 million creative commons images. Attribution is automatically added where required.
*  **Pixabay** - More than 150,000 high quality public domain photos. No attribution required for any of them!
*  More sources will be added in the future to bring you even more free images for your blog posts!

= Features: =
*  Search thousands of creative commons photos and include any into your posts.
*  Fast and easy: Inserting images takes 3 clicks and less than a minute!
*  Automatically adds the required attribution links next to the image.
*  Set the featured image for your post with a single click.
*  Insert multiple images at once and create whole galleries! 
*  Choose between several image sizes easily.
*  Modify the templates of ImageInject to change how images get displayed in your posts.
*  Images are saved to your own server and added to the WordPress media library.
*  Automatically populated ALT and title tags of the image for search engine optimization.
*  Search for many different keywords and compare the results to find the best matches.
*  Can use and insert your focus keyword set in WordPress SEO by Yoast for easier image search optimization.
*  Display attribution in WordPress caption shortcodes right next to inserted images.

ImageInject has also been implemented into [CMS Commander](http://cmscommander.com/ "Manage WordPress faster!"), my service that allows you to manage any number of WordPress blogs from a single dashboard.

== Changelog ==  

= 1.18 =
- Fixed: Compatibility issue with latest WordPress version

= 1.17 =
- Added: Support for Gutenberg
- Fixed: Flickr integration working again

= 1.16 =
- Fixed: Security improvements

= 1.15 =
- Fixed: Security improvements

= 1.14 =
- Fixed: Switched the attribution link setting for ImageInject to be off by default.

= 1.13 =
- Fixed: Bug that prevented Flickr SSL connection from working on certain servers
- Updated: Support for new Pixabay API changes, which will go into effect on Feb 1st

= 1.12 =
- Added: The caption attribution is now saved to the database, so that it can be reused in future posts.
- Fixed: Attribution is now inserted properly when inserting featured images.

= 1.11 =
- Support for custom post types: Image search form is now displayed for all your custom posts!

= 1.10 =
- WP Inject has been renamed to ImageInject
- Adds support for displaying attribution as a WordPress caption shortcode. You can enable this on the settings page (active by default on new installations).

= 1.06 =
- Fixes a bug that prevents ImageInject from loading all its scripts on multisite blogs.

= 1.05 =
- Fixes a problem that prevented ImageInject from working on blogs that use https://
- Fixes a bug that prevented image search from working on WP multisite blogs.

= 1.04 =
- Resets Pixabay API Keys. Please update to ensure Pixabay keeps working!
- Large preview images are now loaded only when hovering over the thumbnail to increase performance.
- An image search can now be started by pressing the "enter" button while the keyword field is active (previously saved the post).

= 1.03 =
- Fixes a bug that prevented Flickr attribution from getting added when setting a featured image.

= 1.02 =
- Adds large image size (1280px) to Pixabay results
- Fixes a bug that caused unnecessary API requests being sent and prevented new images from getting added when repeating a search.
- Updates the Flickr API to use SSL for all requests.

= 1.01 =
- Pixabay does not require you to enter an API key and username anymore! You can now use Pixabay right away after installing ImageInject.

= 1.00 =
- Adds Pixabay as an additional image source, providing over 150,000 high quality photos for your posts. 

= 0.53 =
- Adds possibility to get more than 100 images per search to the Settings page
- Adds image title, author and date below previews of photos
- Fixes a small bug that could cause image uploads to fail.

= 0.52 =
- Improved error reporting for if the upload of images to the server fails.
- Fallback function used if image upload fails.
- Fixes a bug preventing the saving of photos for certain users.

= 0.51 =
- Fixes a bug that caused a fatal error on network activation on WP multisites.
- Fixes a minor bug with attribution links.

= 0.50 =
- Adds support to save multiple images to your media libary with one click
- Adds WP multisite support: You can now use "network activate" to activate ImageInject on all your blogs at the same time

= 0.41 =
- Fixes a bug that prevented one of the "Save Settings" buttons on the options page from working.

= 0.40 =
- Adds "Filename Template" setting, allowing you to customize the filename of saved images, e.g. for SEO reasons.
- Fixes a display bug on the Settings page for certain browsers.

= 0.31 =
- Adds a small CC icon to attribution text linking to the photo license.
- New tags for the attribution template: {cc_icon}, {license_name}, {license_link}

= 0.30 =
- New attribution template setting.
- New attribution location setting.
- Now uses WordPress own CSS classes for aligning inserted images.

= 0.20 =
- First public release.

== Screenshots ==

1. The metabox is added to the WordPress editor page by ImageInject and allows you to search for photos fast.

2. Photo search results. Hover over any thumbnail and click on one of the size links to insert the photo into your post immediately.

3. You can select multiple photos by clicking on them and then insert them all together.

4. Support for multiple searches. Compare results side by side to find the best matching photos for your post.

== Installation ==

1. Upload the plugin folder to your /wp-content/plugins/ directory
2. Go to the "Plugins" page in your WordPress admin and activate ImageInject
3. Go to the "Add New" post or page screen in your WordPress admin and you will find a new metabox for ImageInject that allows you to easily insert images.

You can read my tutorial for more details on [how to install and use ImageInject](http://wpinject.com/tutorial/ "How to insert images with ImageInject").