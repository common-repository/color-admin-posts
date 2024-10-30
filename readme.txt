=== Color Admin Posts ===
Contributors: GeekPress, wp_media
Tags: admin, posts, pages, admin, draft, pending, publish, future, private, color, colors, status
Requires at least: 2.8
Tested up to: 4.6
Stable tag: 1.0.3

Change the background colors of the post/page within the admin based on the current status : Draft, Pending, Published, Future, Private.

== Description ==

Change the background colors of the post/page within the admin based on the current status : Draft, Pending, Published, Future, Private.

The color change is achieved with a colopicker (Farbtastic).

Translation: English, French

= Our Plugins =
* <a href="https://wordpress.org/plugins/imagify/">Imagify</a>: Best Image Optimizer to speed up your website with lighter images.
* <a href="http://wp-rocket.me">WP Rocket</a>: Best caching plugin to speed-up your WordPress website.
* <a href="https://wordpress.org/plugins/rocket-lazy-load/">Rocket Lazy Load</a>: Best Lazy Load script to reduce the number of HTTP requests and improves the websites loading time.

== Installation ==

1. Upload the complete `color-admin-post` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Update Color Admin Colors' under the 'Settings' tab and configure the plugin


== Screenshots ==

1. Admin Section
2. Default appearance

== Changelog ==

= 1.0.3 =
* Fix PHP Notices
 * Constant CAP_VERSION already defined in ../color-admin-post.php
 * register_uninstall_hook was called incorrectly
 * Undefined variable: desc in ../color-admin-post.php

= 1.0.2 =
* Do not remove the colors saved when plugin is desactived
* Optimize plugin with WordPress functions

= 1.0.1 =
* Limit Farbtastic scripts in admin area

= 1.0 =
* Initial release.
