=== Plugin Name ===
Contributors: wraithkenny
Donate link: http://www.unFocus.com/
Tags: admin, per-page CSS styles, per-page JavaScript
Requires at least: 3.0.1
Tested up to: 3.0.1
Stable tag: 1.0

This plugin allows Admin users to individually add custom CSS and JavaScript directly to Post, Pages or any other registered custom post types.


== Description ==

This plugin allows Admin users the ability to add custom CSS (at the bottom of the 'head' tag) and JavaScript (at the bottom of the 'body' tag) directly into individual Post, Pages or any other registered custom post types.

Because only well trusted users should ever be allowed to insert JavaScript directly into the pages of your site, this plugin restricts usage to admin type users. Admin's have access to even more sensitive areas by definition, so that should be relatively safe ;)

A few notes about the implementation:

*   Admin users, or more specifically, *any user with the `manage_options` capability* (which by default is *only* the admin type user) can use this plugin's functionality. Some plugins extend user rolls, and so this plugin would naturally extend include rolls that have the appropriate capability.
*   CSS Styles are included inline, not linked, at the bottom of the `head` element with `style` tags by using `wp-head`. If your theme doesn't have this hook, this plugin (as well as most others) won't work.
*   JavaScript is included inline, not linked, at the bottom of the `body` element with `script` tags by using `wp-footer`. If your theme doesn't have this hook, this plugin (as well as most others) won't work.
*   **There is no input validation.** This plugin puts exactly what you type in the meta box directly into the `html` with no error checking. You are an Admin, and we trust you to be carefull. Try not to break anything.

I plan on implementing the following in future releases:

*   Syntax highlighting via CodeMirror or Skywriter (Bespin).
*   Option of including JavaScript in `wp-head` instead of `wp-footer` (Not very typical).
*   Ability to link .css and .js files.
*   Some sort of input validation.
*   Possibly add option to enable this functionality for Editor rolls. Admin would have to enable it **explicitly**. (Not so sure about this one. Are Editors generally trusted users?)

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

1. I'll add a screenshot in the version with syntax highlighting, but for now, the plugin adds very simple Meta Boxes.

== Changelog ==

= 1.0 =
* Initial Release.

== Upgrade Notice ==

= 1.0 =
Initial Release, there is nothing to upgrade from.
