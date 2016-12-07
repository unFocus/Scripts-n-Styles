=== Scripts n Styles ===
Contributors: WraithKenny, CaptainN
Tags: admin, CSS, javascript, code, custom, Style
Requires at least: 4.2.2
Tested up to: 4.7
Stable tag: 3.3.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin allows Admin users to individually add custom CSS, Classes and JavaScript directly to Post, Pages or any other custom post types.

== Description ==

This plugin allows Admin users the ability to add custom CSS and JavaScript directly into individual Post, Pages or any other registered custom post types. You can also add classes to the body tag and the post container. There is a Global settings page for which you can write Scripts n Styles for the entire blog.

Admin's can also add classes to the TinyMCE "Formats" dropdown which users can use to style posts and pages directly. As of Scripts n Styles 3+ styles are reflected in the post editor.

Because only well trusted users should ever be allowed to insert JavaScript directly into the pages of your site, this plugin restricts usage to admin type users. Admin's have access to even more sensitive areas by definition, so that should be relatively safe ;)

= Notes about the implementation: =

*   Admin users, or more specifically, *any user with the `manage_options` and `unfiltered_html` capabilities* (which by default is *only* the admin type user) can use this plugin's functionality. Some plugins extend user rolls, and so this plugin would naturally extend include rolls that have the appropriate capability.
*   CSS Styles are embeded, not linked, at the bottom of the `head` element with `style` tags by using `wp-head`. If your theme doesn't have this hook, this plugin (as well as most others) won't work.
*   JavaScript is embeded, not linked, at the bottom of the `body` (or `head`) element with `script` tags by using `wp-footer` (or `wp-head`). If your theme doesn't have this hook, this plugin (as well as most others) won't work.
*   **There is no input validation.** This plugin puts exactly what you type in the meta box directly into the `html` with no error checking. You are an Admin, and we trust you to be carefull. Try not to break anything.
*	Do to the licensing of the libraries used, this plugin is released "GPL 3.0 or later" if you care about those things.

= Contact: =

You'll have better luck contacting the other if you try me on [Twitter](http://twitter.com/WraithKenny) and [Github](https://github.com/unFocus/Scripts-n-Styles/issues). If that fails, I have an [open thread](http://wordpress.org/support/topic/contacting-scripts-n-styles-author "Contacting Scripts n Styles' author") on the support forums that will trigger an email.

== Installation ==

This plugin does not require any special activation or template tags. Just get it from wordpress.org/extend, install and activate like normal.

== Frequently Asked Questions ==

= Will I lose all of my custom Styles and Scripts if I uninstall the plugin? =

Yes, absolutely. **YOU WLL LOSE ALL CUSTOMIZATIONS.** Be sure that you do not want these customizations before you uninstall.

= Why would you do that to me? =

Well, because plugins are supposed to, and should be expected to clean up after themselves. If you disable and uninstall the plugin, as a developer, I am supposed to assume that you no longer want me to store all of that now useless data in your database.

= Can I get around that somehow? =

Sure, if you are an Admin, just go to the plugin editor and wipe out the uninstall.php and then WordPress will not delete the meta data on uninstall.

== Screenshots ==

1. Settings Page for Writing Scripts n Styles that apply to the whole blog.
2. The Scripts panel of the Meta Box.
3. The Styles panel of the Meta Box.
4. The Classes panel. Add classes to the Style dropdown!
5. Enqueue panel. You can enqueue jQuery from here if you need!
6. Your styles are reflected in the Editor.

== Changelog ==

= 3.3 =
* See the github repo commits

= 3.2.1 =
* metabox add shortcode bugfix

= 3.2 =
* Add AJAX to Shortcode Tab
* Add "Chosen" for selects
* General UI Improvements
* Add CoffeeScript Support
* Add Hoops Widget
* Add Global Hoops Shortcodes
* Add Markdown mode to code editor
* Add (fix) code editor themes
* Theme Support (Beta Feature)

= 3.1.1 =
* Add (fix) CodeMirror Themes

= 3.1 =
* Feature: Dynamic Shortcodes.
* Feature: LESS.js support.
* Bug Fix: Proper output escaping.

= 3.0.3 =
* Bug Fix: wpautop formatting.

= 3.0.2 =
* Bug Fix: Fatal Error on post save

= 3.0.1 =
* Option to show Metabox by default
* Check upgrade in more places
* Fix double Settings Message on general-options
* Fix empty post showing on usage
* Cleaned up constants (internal)

= 3 =
* AJAX Saving of Meta-box
* Dynamically populate the Styles Dropdown for TinyMCE
* Styles preview in Post Editor
* Enqueue dependant scripts if you need (like jQuery)
* Adjustable menu placement
* CodeMirror Themes

= 2.0.3 =
* fixed some bugs

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

= 3.3 =
Bug fixes and lib upgrades

= 3.2.1 =
Bug fix (add shortcode)

= 3.2 =
New Major Features

= 3.1.1 =
Add (fix) CodeMirror Themes

= 3.1 =
New Features and Bug fixes

= 3.0.3 =
Bug fix (wpauto issue)

= 3.0.2 =
Bug fix

= 3.0.1 =
Bug fixes

= 3 =
Adds new features.

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
