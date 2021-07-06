=== Swift Performance Lite ===
Contributors: swte, doit686868, hostmyblogco, fredawd
Tags: cache, caching, seo, performance, optimizer, cdn, compression, speed
Requires at least: 4.0
Tested up to: 5.7.1
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Swift Performance is a cache and performance booster plugin. It can speed up your site, improve SEO scores and user experience.

== Easy Setup ==

https://www.youtube.com/watch?v=BnOuFja2Klw&t=240s

== Description ==

The need for Speed. Cache & Performance plugin for WordPress!
You created it, we keep it fast! Did you know that……
You Have Just 3 Seconds To Impress Your Visitor. Don’t Lose It By Slow Loading. 95% of customer’s don´t wait if a website takes longer than 5-6 seconds to load!
**Yes, that´s a big number.**
People spend a lot of time and money on building websites and even more on marketing to get traffic… but what happens when those people come to your website and the website is slow to load? You lose sales.
**Slow loading websites lose visitors and sales.**
But we have a solution! Say goodbye to slow loading websites and say hello to Swift Performance Lite or Pro!

Get a complete [Cache & Performance](https://swiftperformance.io/) plugin for free or as a pro.
Swift Performance will increase the loading speed of any WordPress website and provides an intelligent, modern caching system. You can even cache AJAX request, dynamic pages, and you can add exceptions (URL, page or content based rules).

**Here are some of our most popular functions.**

**Caching.** Page caching is working out of the box. It is compatible with WooCommerce, bbPress, Cloudflare and Varnish as well. It will boost your performance, improve SEO scores, and create a better user experience.

**CSS and Javascript optimization.** One of the most important thing for performance is optimize the delivery of static resources. Swift Performance not only combines and minifies the CSS files, but generates the Critical CSS for each page automatically. Also Javascripts (even inline scripts) can be combined, minified, and move to footer without any conflict.
Huge combined javascript files may still be render blocking, however with our unique Async Execute solution, you can not only combine/minify the scripts, but  you can run them individually as soon as a chunk has been downloaded – which provides incredibly fast JS execution, improves SEO scores and user experience.

**Database Optimization.** Keeping your database clean is extremely important for speed. Swift Performance has a built in DB Optimizer to clean expired transients, orphans, duplicated metadata, and spammy comments. You can also schedule every tasks. It has never been easier to keep your WordPress database clean.

**Plugin Organizer.** Plugins are a big part of WordPress, however sometimes not properly written plugins can cause performance issues. With Plugin Organizer you can disable plugins on certain pages, and let plugins run only where it is really necessary. You can set URL match, Frontend, Admin Pages, AJAX action rules and exceptions to get the best results.

**Why you should install Swift Performance Lite?**
After reading this exhaustive features, you can probably imagine why Swift Performance is the best Cache & Performance solution for WordPress.
It is both easy and powerful. Optimizing **WordPress performance** is not rocket science anymore. Our unique **Setup Wizard** will help you to configure the basic settings and improve WordPress performance.

We hear your voice! We are continuously improving the plugin, based on customer’s feedback & needs. This is one of the most important factors & mission.
Give Swift Performance a try.
Want to unlock more features? [Upgrade to our Pro version.](https://swiftperformance.io/)

**Why should you upgrade to Pro?**
We added our secret ingredients to make a product for our professional users, the result is Swift Performance Pro.

**Compute API.** Compute API will speed up merging process and decrease CPU usage. Compute API also provides advanced JS minify, which should be used if default JS minification cause issues on your site. Compute API also provides Critical Font option, with that you can reduce font icon files’ size. This feature is **essential for shared hosting users**, as CPU usage is usually limited for shared hosting plans.

**Critical Icon Fonts.** With Critical Fonts feature you can select icons that you are actually using on your site, and generate customized icon font set from them. There is also a feature to search used icons in your theme/plugins, posts, options, etc. Once you selected the icons that you need, you can enqueue them with one click. If critical icon fonts are enqueued, the plugin will block the original version of the font CSS/font files.

**Unlimited Image Optimizer.** With Image Optimizer you can optimize every images on your site. It will scan the whole site, and pick up every image from themes, plugins and upload folder. Regarding that Image Optimizer is unlimited (no additional fees), you can save a considerable amount of money. You can select images individually, and run batch optimization. Default image quality can be specified in plugin settings, however you can overwrite it before starting the optimization on selected images.

**Schedule DB Optimizer.** Both Pro and Lite versions comes with Database Optimizer. However in Pro you can set scheduled optimization, so you won’t need to do it manually.

**Whitelabel.** With whitelabel option you can re-brand Swift Performance. You can change the plugin name, description, author, even the database prefix for the plugin.

**Remote Cron.** Pro version provides a Remote Cron option. If you set the remote cron, our API server will call wp-cron.php and run WP cronjobs as real cronjobs.

**Extended WooCommerce Features.** With Pro version you can't just cache, but totally disable cart fragments AJAX calls. Pro also provide session cache, with that you can cache and preload dynamic pages like cart and checkout page for WooCommerce.

**Continuous Plugin Updates.** While Swift Performance Lite is maintained as well, these updates are only compatibility updates, however Pro version comes with regular updates which contains new features. Also Pro version has a Beta tester option, if you enable it, you will get updates more frequently.

**FAQ**
**Installation instructions**

1. Upload the plugin files to the /wp-content/plugins/ directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the ‘Plugins’ screen in WordPress
3. Run the Setup wizard, or use the Tools->Swift Performance screen to configure the plugin


**Why should I use Swift Performance plugin?**
Swift Performance is an **all-in-one WordPress performance booster plugin.** It provides everything that you need to speed up your WordPress site (replacing several optimization plugins), and improve user’s website experience.
Swift Performance is already running on 2.000+ websites and based on our customers’ feedback it is the fastest performance plugin on the market.
With the Setup Wizard you can easily configure the plugin and in the same time you have power of setup & control of all settings manually.

**Strange redirects**
If you have strange URLs in your analytics or you can see that the site redirects to an URL like this:
https://www.yoursite.com/wp-content/cache/yoursite.com/desktop/unauthenticated/index.html
This is caused by an improper order of htaccess rules. Probably you are using a force SSL or force www/non-www rule in htaccess. Please remove these rules from .htaccess, and insert them in **Settings > General > Tweaks > Custom htaccess.**

**My website broke**
It is possible that one or multiple files generate conflicts when those are merged and/or minified. If you notice that you website doesn´t show correctly, you might disable merge scripts and styles.

**High CPU Usage**
While Swift is generating the cache, the CPU usage can be higher than usual. Swift is using more aggressive optimization than any other plugin on the market and it needs some CPU. Usually it isn’t an issue and CPU usage can be increased temporarily, but if it goes back to normal after prebuild finished, you don’t need to worry about it.
However, for large sites on relatively small server it can cause too high CPU usage temporarily. Actually, when the server is using CPU it is always using 100%. High usage means the CPU was used for a longer period.
If CPU usage is constantly higher you may need check the configuration. It is recommended to:

- Enable Compute API: Settings > General > Compute API
- Enable Optimize Prebuild Only: Settings > Optimization > General > Optimize Prebuild Only
- If you are not satisfied with Optimize Prebuild Only option, enable Optimize in background instead:  Settings > Optimization > General > Optimize in Background.
- Setting a low number of threads as maximum: Settings > Optimization > General > Maximum threads: set this to 2 or 1. 1 will make the pre-build a bit slow, so try 2 first.
- Exclude third party CSS:  Settings > Optimization > Styles> Exclude 3rd Party CSS.
- Disable Generate Critical CSS as generating Critical CSS is the most CPU intensive process: Settings > Optimization > Styles> Generate Critical CSS.
- Exclude third party JS: Settings > Optimization > Scripts > Exclude 3rd Party Scripts.
- Set Cache Expiry Mode to Action based, if you are not using nonce or anything that can expire on frontend: Settings > Caching > General > Cache Expiry Mode: Action based.
- Enable Prebuild Cache Automatically: Settings > Caching > Warmup > Prebuild Cache Automatically.
- Setup lower Limit prebuild speed (recommended to use on limited shared hosting): Settings > Caching > Warmup > Prebuild Speed: Moderate (or Slow).
- Exclude post types that you wouldn’t like to cache. Autoconfig should find most and exclude them automatically but you can can add them manually: Settings > Caching > Exceptions> Exclude Post Types.

= Key Features =
Quick Setup, Page Cache, Cache Preloading, Gzip Compression, Browser Caching, Remove Query Strings, Lazyload, Minify CSS, Minify JS, Combine JS/CSS, Async Execute Combined JS, Defer JS, CDN Support, Cloudflare Support, Varnish Support, Mobile Detection, Multisite Compatibility, Woocommerce Friendly, WPML Support, Cache For Logged In Users, Database Optimizer, Import/Export, DNS Prefetch, Critical CSS On The Fly, Plugin Organizer, Appcache, AJAX Cache, Proxy 3rd Party JS, Inline Small Images, Google Analytics Bypass, Heartbeat Control

= Reviews =
[Swift Performance Review](https://www.wpmediamastery.com/swift-performance-review-and-settings/) by WPMediaMastery
[Swift Performance Comparison](https://www.alentejowebdesign.nl/wp-rocket-vs-swift-performance/) by Alentejo Webdesign

== Installation ==

1. Upload the plugin files to the /wp-content/plugins/ directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Run the Setup wizard, or use the Tools->Swift Performance screen to configure the plugin

== Screenshots ==

1. Setup Wizard
2. Setup Wizard
3. Dashboard and Warmup Table
4. Settings Panel
5. DB Optimizer
6. Plugin Organizer

== Changelog ==

2.3.5 - 02/06/2021

[FIX] Minor fixes
[FIX] Page specific rules issue
[IMPROVE] Minor improvements
[IMPROVE] Ability to remove redirected URLs from Warmup table
[IMPROVE] Error logging for server side scripts
[NEW] Preprocess Javascript

2.3.4 - 20/05/2021

[FIX] Minor fixes
[FIX] Compatibility issues with Divi and Avada
[FIX] Lazyload filter 3rd party compatibility issues
[IMPROVE] Smart Render HTML
[NEW] Smart Lazyload for Images/Iframes
[NEW] Smart Async Script Delivery
[NEW] Page Specific Rules

2.3.3 – 30/04/2021

[FIX] Minor fixes
[FIX] Prebuild Booster
[IMPROVE] Clear opcache
[IMPROVE] Cloudflare Auth Token support added
[IMPROVE] URLs per pages option for Warmup table
[NEW] Server Side Script
[NEW] Smart Render HTML

= 2.3.1.1 - 2021.03.24.

[FIX] Keep Original Headers 500 Error

= 2.3.1 - 2021.03.22.
[NEW] Image Optimizer
[NEW] Critical CSS API
[NEW] Minify JS API
[NEW] Remote Cronjobs
[NEW] Remote Prebuild
[NEW] Local fonts
[NEW] Force font-display: swap
[NEW] Hide Footprints
[NEW] Disable Admin Notices
[NEW] Background Requests
[NEW] Limit WP Cron
[NEW] Enable Remote Cron
[NEW] WebP support
[NEW] Youtube Smart embed
[NEW] Server Push
[NEW] Ignore Query String
[NEW] Avoid Mixed Content
[NEW] Keep Original Headers
[NEW] Case Insensitive URLs
[NEW] Exclude Cookies
[NEW] Discover New Pages
[NEW] Lazyload Elementor Youtube Background
[NEW] Disable Cart Fragments
[NEW] WooCommerce Session Cache
[NEW] GEO IP Support
[NEW] CDN Custom File Types
[NEW] Exclude File Types from CDN


= 2.1.5 - 2020.09.26.
[FIX] Warmup table filter fatal error

= 2.1.4 - 2020.09.24.
[FIX] Minor bugfixes
[IMPROVE] Improve Compatibility with WordPress 5.5


= 2.1.3 - 2020.05.15.
[FIX] Search in Warmup Table
[IMPROVE] Runcache support
[IMPROVE] Prevent double GZIP issues
[IMPROVE] Minor Improvements
[IMPROVE] Compatibility Improvements
[NEW] Clear cache automatically after update process
[DEPRECATED] Remove Appcache


= 2.1.2 - 2019.12.02 =
[FIX] Remove Summer Sale ad
[IMPROVE] Improve WPML compatibility

= 2.1.1 - 2019.09.30 =
[FIX] Minor bugfixes
[IMPROVE] Flying Pages compatibility

= 2.1 - 2019.08.17 =
[FIX] Minor bugfixes
[FIX] Prevent to create unnecessary Image Optimizer tables
[IMPROVE] Improve UI
[IMPROVE] Safe send headers
[NEW] Ability to disable prebuild booster
[NEW] Hungarian translation added

= 2.0.10 - 2019.03.18 =
[FIX] Prebuild cache issues

= 2.0.9 - 2019.03.17 =
[FIX] Fatal error on New Post

= 2.0.8 - 2019.03.17 =
[FIX] Minor fixes
[FIX] Permanent admin notices
[FIX] Measure max timeout on Setup
[FIX] Google Analytics Bypass issues with latest analytics.js

= 2.0.7 - 2019.03.02 =
[FIX] Minor fixes
[IMPROVE] WooCommerce scheduled sales compatibility

= 2.0.6 - 2019.02.10 =
[FIX] WP Staging conflict

= 2.0.5 - 2019.02.03 =
[FIX] Non-latin characters in URL issues
[FIX] Minor Layout fixes

= 2.0.4 - 2019.01.26 =
[FIX] Minor Fixes
[FIX] Elementor editor conflict fix
[IMPROVE] Override Cloudflare Host (useful for subdomains)
[IMPROVE] Archive & Category Prebuild

= 2.0.2 - 2019.01.20 =
[FIX] Minor Fixes
[FIX] WP CLI issues
[FIX] XML-RPC issue
[IMPROVE] Critical CSS
[IMPROVE] POT file updated

= 2.0.1 - 2018.12.12 =
[FIX] PHP 7.3 compatibility issues
[FIX] WP CLI issues

= 2.0 - 2018.12.10 =
[FIX] Minor fixes
[FIX] PHP warning on prebuild
[IMPROVE] Config Framework
[IMPROVE] Setup Wizard

= 1.8.5 =
[FIX] Warmup Table cache status
[IMPROVE] Optimize Warmup Table on Dashboard
[IMPROVE] Minor improvements
[IMPROVE] WPML Multi-domains handling

= 1.8.3 =
[FIX] Cache Status table issues
[IMPROVE] Minor improvements
[IMPROVE] Setup Wizard
[IMPROVE] CDN manager

= 1.8.2 =
[FIX] Early loader fix
[IMPROVE] Auto configure

= 1.8.1 =
[FIX] Typo fix
[FIX] Missing homepage cache
[FIX] Remove settings from Customizer
[FIX] Minor fixes
[IMPROVE] Prebuild & Warmup table
[IMPROVE] Setup Wizard
[IMPROVE] Minor improvements
[NEW] Auto configurator
[NEW] Exclude archive/author/feed/REST requests

= 1.7.2 =
[FIX] Minor fixes
[FIX] Remove remote ads
[FIX] Remove Pro Updater

= 1.7.1 =
Increase version number because WP Repo bug

= 1.7.0.1 =
[FIX] Backend JS error

= 1.7 =
[FIX] Force SSL/www-non www Redirect issues
[FIX] Use local time and localized format for timestamps
[FIX] Missing Warmup Table issue
[FIX] Elementor Editor conflict
[IMPROVE] Setup/Uninstall
[IMPROVE] Prompt alert before clearing cache on Settings panel
[IMPROVE] Clearing Dynamic cache
[IMPROVE] Speed up and decrease CPU usage for prebuild process
[IMPROVE] Minor improvements
[IMPROVE] Domain mapping compatibility
[IMPROVE] Speed up plugin dashboard
[NEW] Pro Updater

= 1.6.8.1 =
[IMPROVE] Clear user cache when user was deleted

= 1.6.8 =
[FIX] Remove unnecessary API calls
[IMPROVE] Anonymize IP for Bypass Analytics
[NEW] Add ability to disable cookies


= 1.6.7.1 =
[IMPROVE] Remove Forced Activation


= 1.6.7 =
[FIX] Minor bugfixes

= 1.6.6.2 =

* [FIX] subfolder installation issues
* [FIX] missing font issues
* [IMPROVE] DB cleanup on uninstall
* [IMPROVE] Include styles

= 1.6.6 =

* [IMPROVE] Critical CSS
* [FIX] EWWW Image Optimizer compatibility
* [FIX] Polylang compatibility
* [FIX] Add prefix for DOM parser constants

= 1.6.5 =

* [FIX] Social links in Setup Wizard
* [FIX] CSS realpath
* [IMPROVE] Speed up creating Warmup table
* [NEW] New action hooks

= 1.6.4 =

* [IMPROVE] Prebuild cache
* [IMPROVE] Feed cache
* [IMPROVE] Critical CSS
* [IMPROVE] Merge JS
* [FIX] Warmup table cache status

= 1.6.3.2 =
* [FIX] Setup Wizard issues


= 1.6.3.1 =
* [FIX] CSS real path fix

= 1.6.3 =
* [NEW] Include Scripts
* [NEW] Shared logged in cache
* [NEW] Optimize WooCommerce get refreshed fragments
* [IMPROVE] Plugin organizer is_mobile/is_desktop rules
* [IMPROVE] Include Styles
* [IMPROVE] Gravatar Cache
* [FIX] WP Engine Object cache compatibility
* [FIX] @import issues

= 1.6.1.1 =
* [FIX] Setup Wizard issues
