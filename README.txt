=== Scripts n Styles ===
Contributors: WraithKenny, Touvan
Donate link: http://wordpressfoundation.org/donate/
Tags: admin, CSS, javascript, code, custom, Style
Requires at least: 3.1
Tested up to: 3.2
Stable tag: 2.0.1

This plugin allows Admin users to individually add custom CSS, Classes and JavaScript directly to Post, Pages or any other custom post types.

== Description ==

This plugin allows Admin users the ability to add custom CSS (at the bottom of the 'head' tag) and JavaScript (at the bottom of the 'body' tag) directly into individual Post, Pages or any other registered custom post types. You can also add classes to the body tag and the post container (if your theme supports `body_class()` and `post_class()` functions).

Because only well trusted users should ever be allowed to insert JavaScript directly into the pages of your site, this plugin restricts usage to admin type users. Admin's have access to even more sensitive areas by definition, so that should be relatively safe ;)

A few notes about the implementation:

*   Admin users, or more specifically, *any user with the `manage_options` capability* (which by default is *only* the admin type user) can use this plugin's functionality. Some plugins extend user rolls, and so this plugin would naturally extend include rolls that have the appropriate capability.
*   CSS Styles are included inline, not linked, at the bottom of the `head` element with `style` tags by using `wp-head`. If your theme doesn't have this hook, this plugin (as well as most others) won't work.
*   JavaScript is included inline, not linked, at the bottom of the `body` element with `script` tags by using `wp-footer`. If your theme doesn't have this hook, this plugin (as well as most others) won't work.
*   **There is no input validation.** This plugin puts exactly what you type in the meta box directly into the `html` with no error checking. You are an Admin, and we trust you to be carefull. Try not to break anything.

== Installation ==

This plugin does not require any special activation or template tags. Just get it from wordpress.org/extend, install and activate like normal.

== Frequently Asked Questions ==

= Will I lose all of my custom Styles and Scripts if I uninstall the plugin? =

Yes, absolutely. **YOU WLL LOSE ALL CUSTOMIZATIONS.** Be sure that you do not want these customizations before you uninstall.

= Why would you do that to me? =

Well, because plugins are supposed to, and should be expected to clean up after themselves. If you disable and uninstall the plugin, as a developer, I am supposed to assume that you no longer want me to store all of that now useless data in your database.

= Can I get around that somehow? =

Sure, if you are an Admin, just go to the plugin editor and wipe out the uninstall.php (Replace everything with a space character) and then WordPress will not delete the meta data on uninstall.

== Screenshots ==

1. The New and Improved Meta Box.

== Changelog ==

= 2.0.1 =
* Better selection of `post_types` to add Scripts-n-Styles
* micro-optimization for storage of class names.
* Adds option page for globally adding Scripts and Styles.
* Defined a later priority for Scripts n Styles to print after other scripts and styles.
* Added a box for Scripts to be included in the `head`.
* Better adherence to coding standards.
* Tabbed interface on metabox
* added CodeMirror
* began contextual help

= 1.0.2 =
* Added fields for `body_clas`s and `post_class`
* Merged meta boxes
* Cleaned up code
* Improved compatibility
* Added Screenshot

= 1.0.1 =
* Some small plugin meta data updates.

= 1.0 =
* Initial Release.

== Upgrade Notice ==

= 2 =
Adds new features.

= 1.0.3 =
Adds a few new features.

= 1.0.2 =
Minor update. Adds a few new features.

= 1.0.1 =
Some small plugin meta data updates.

= 1.0 =
Initial Release, there is nothing to upgrade from.
