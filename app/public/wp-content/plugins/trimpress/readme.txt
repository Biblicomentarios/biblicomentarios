=== TrimPress ===
Contributors: davidmatthew
Tags: heartbeat, cart fragments, emojis, oembed, xml-rpc
Requires at least: 5.0
Tested up to: 5.7
Requires PHP: 7.0
Stable tag: 1.0.1
License: GNU GPL v3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

== Description ==

TrimPress optimizes and trims some of the cruft from WordPress for a lighter, more secure theme!

== Features ==

* Slow down the Heartbeat API to save on admin-ajax usage.
* Disable cart fragments, a resource-intensive WooCommerce script (TrimPress automatically detects WooCommerce and only shows this option if it is active).
* Remove unnecessary clutter from the WordPress <head> section, like RSS, RSD, WLW manifest and adjacent post links.
* Disable the built-in WordPress code editors that allow users to modify plugin and theme code.
* Limit post revisions, which can cause unnecessary database bloat.
* Remove the oEmbed script which transforms YouTube, Twitter and other social links into embedded media by automatically fetching extra scripts and styles from these sites.
* Disable automatic emoji rendering, which adds several extra scripts and styles to your site.
* Disable the XML-RPC interface, an older system for remote WordPress access that can be exploited by hackers. 
* Remove the meta generator tag and version url parameters that let potential attackers know what WordPress version you're using.
* Disable comment autolinking, a feature often exploited by spammers.

== Development ==

* Namespaced and object-oriented code.
* Adheres to [WordPress Coding Standards](https://github.com/WordPress/WordPress-Coding-Standards).

== Installation ==

1. No special set-up required - just click install and activate, and you're good to go!
2. If you manually download the plugin, just unzip to the WordPress plugins folder and the plugin will be automatically detected. It can then be activated as normal.

== Screenshots ==

1. The first set of options.
2. The second set of options.

== Changelog ==

= 1.0.1 =
* Added full translation support.

= 1.0.0 =
* Initial release.
