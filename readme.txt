=== PHP Snippets ===
Contributors: fireproofsocks
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WPR4A9JT355BE
Tags: php, exec, snippet, code
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 4.3

BETA ONLY. Provides an interface for developers to easily add PHP code to their posts via selectable shortcodes.

== Description ==

DO NOT DOWNLOAD THIS YET.  This plugin allows developers to easily add PHP code to their posts and pages.  All *.php* files contained in a special directory will be executable via a corresponding [shortcode].  For example, a file named *my-code.php* can be executed by adding a [my-code] shortcode to your post content.  The source directory is configurable: you can put it anywhere on your server that you wish, e.g. you can enhance the security of your code by storing it _above_ the root directory.  

By keeping all the PHP code in PHP files, you ensure that your WordPress posts and pages remain clean.  All available shortcodes are listed when the user clicks a custom TinyMCE button.

See the [Project Home Page](http://code.google.com/p/wordpress-php-snippets/) for more information.  This plugin requires PHP 5.2.6 or greater.

== Installation ==

You can install this plugin in the standard way from the WordPress plugin admin screen, or you can download its files and upload the `php-snippets` folder to the `/wp-content/plugins/` directory.  


1. Upload the `php-snippets` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a new post or page
1. Notice the new `<?php` button in the TinyMCE toolbar -- click it to get a list of available PHP Snippets, then select the one you want to insert it into your post.

== Frequently Asked Questions ==

= There's a Bug in the Plugin! =

Check to make sure the bug is actually in the plugin and not in one of your Snippets.  This plugin acts mostly as a pass-through.


== Screenshots ==

Coming...


== Changelog ==

= 0.5 =
* Initial release