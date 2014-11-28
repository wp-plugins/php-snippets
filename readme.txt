=== PHP Snippets ===
Contributors: fireproofsocks
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WPR4A9JT355BE
Tags: php, exec, snippet, code
Requires at least: 3.0
Tested up to: 4.0.1
Stable tag: 0.9

Provides an interface for developers to easily add PHP code to posts and widgets via selectable shortcodes.

== Description ==

http://www.youtube.com/watch?v=03yDdrhOSN4

This plugin allows developers to easily add PHP code to their posts and widgets.  All *.snippet.php* files contained in the configured directory will be executable via a corresponding [shortcode].  For example, a file named *my-code.snippet.php* can be executed by adding a [my-code] shortcode to your post content.  The source directory is configurable: you can put it anywhere on your server that you wish, e.g. you can enhance the security of your code by storing it _outside_ your server's document root.  

By keeping all the PHP code in PHP files, you ensure that your WordPress posts and pages remain clean.  All available shortcodes are listed when the user clicks a custom TinyMCE button.

See the [Project Home Page](http://code.google.com/p/wordpress-php-snippets/) for more information.  This plugin requires PHP 5.2.6 or greater.

This plugin can save you from writing lots of other plugins because you can easily tie into PHP files without having to register your own plugins.

WARNING: this has not been tested on Windows servers.  Sorry, but I don't have access to one, so I've been unable to tune the permissions checks.

== Installation ==

You can install this plugin in the standard way from the WordPress plugin admin screen, or you can download its files and upload the `php-snippets` folder to the `/wp-content/plugins/` directory.  

1. Upload the `php-snippets` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Get a License Key from http://craftsmancoding.com/products/downloads/php-snippets/ (enter your name and email to complete checkout)
1. Enter the License Key in your PHP Settings page ( /wp-admin/options-general.php?page=php-snippets )
1. Define one or more Snippet directories where you will save your custom PHP Snippets, e.g. [+ABSPATH+]/snippets/

== Usage ==

1. Create a new post or page
1. Click the `<?php` button in the TinyMCE toolbar to see a list of available PHP Snippets
1. Select the Snippet you want to insert into your post: a shortcode should be generated inside your post content.

Optionally, you can manually type in the shortcode and add in any variables you have defined.

== Widget ==

You can add your snippet output to any Widget by using the PHP Widget.

1. In the WP dashboard, navigate to Appearance --> Widgets
1. Drag the PHP Widget from the list of available widgets to any valid widget area on the right.
1. Add a title for your widget
1. Select one of your PHP Snippets
1. Adjust the shortcode as desired (e.g. to add in special parameters)
1. Save the widget instance.


== Frequently Asked Questions ==

= My Snippets are not showing up!=

Make sure your files use the correct extension.  If you have configured the plugin to look for `.snippet.php` files, then any `.php` files will not be selectable as PHP Snippets.

Make sure you have correctly configured your PHP Snippets directory under *Settings --> PHP Snippets*.  The directory has to be a *full* path, e.g. `/home/myuser/public_html/snippets` or you can use the `[+ABSPATH+]` placeholder to point to the web root of your site, e.g. `[+ABSPATH+]mysnippets`


= What information needs to be in the Snippet header? =

As of version 0.9, the information header is optional.  Using it, however, can enhance the user experience because it lets users know what your Snippet does and you can customize the inputs.

Your Snippets should include an information header in the same way that WordPress plugins and themes, but it's simplified.  Include a *Description:* and a *Shortcode:* to indicate a description and a sample shortcode, respectively.

`
/*
Description: Generates a link to a post or page based on its ID.
Shortcode: [link id=123]Click here[/link]
*/`

Look at the examples in the `php-snippets/snippets` directory for some examples.

= What inputs does my Snippet accept? =

Anything you pass in your shortcode will be passed to your Snippet. E.g. `[mySnippet x="123" y="Llama"]` will make the variables `$x` and `$y` available in your Snippet (they would contain "123" and "Llama", respectively).

The `$content` variable is reserved for when you use a _full_ tag, e.g. `[mySnippet]My content goes here[/mySnippet] would make `$content` contain the text "My content goes here".

Warning: WordPress allows *only* lowercase parameter names, so your input variables should be lowercase.


= What should my Snippet output? =

Your Snippet should _print_ its output (not _return_ it).  This makes it easy for you to include reusable bits of HTML.

Technically speaking, your code doesn't _have_ to output anything -- some scripts may only need to execute on the back-end and remain out of site.


= There's a Bug in the Plugin! =

Check to make sure the bug is actually in the plugin and not in one of your Snippets.  This plugin attempts to act primarily as a transparent pass-through, and the error messages should be confined to *your* code (hopefully not mine).  If there's something goofy going on, please let me know by filing a [bug report](http://code.google.com/p/wordpress-php-snippets/issues/list).


== Screenshots ==

1. Use the TinyMCE button to launch the "Snippet Selector" in a thickbox.
2. Select any of the listed PHP Snippets to have its sample shortcode inserted into your post 


== Changelog ==

= 1.0 =

* PHP 5.3.0 is now required.
* Configurable suffix, so now your snippets need not use the '.snippet.php' extension.
* Pay-what-you-like licensing added. 
* Various bug fixes and improvements

= 0.8 =

* Improved permissions checking in Windows environments.
* General cleanup.


= 0.7 =

More thorough permissions checking of the configured directories, added some snippets.

= 0.6 =

Worked out some bugs, cleaned this thing up for the prom.

= 0.5 =
* Initial release


== Upgrade Notice ==

= 1.0 =

This is a big update: it includes support for fully customizable paths and file-extensions, improved error checking, and many other improvements.  To run this version, you must get get a license key.  You can pay what you want for the license key, including free, so don't miss out on the upgrades!  This version requires PHP version 5.3.0 or greater.

= 0.8 =

Improved permissions checking in Windows environments.  General fixes.

= 0.7 =

Improved checking of file permissions.