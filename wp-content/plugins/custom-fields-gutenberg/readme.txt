=== Custom Fields for Gutenberg ===

Plugin Name: Custom Fields for Gutenberg Block Editor
Plugin URI: https://perishablepress.com/custom-fields-gutenberg/
Description: Restores the Custom Field meta box for the Gutenberg Block Editor.
Tags: gutenberg, custom fields, meta box, field, box, restore, display
Author: Jeff Starr
Author URI: https://plugin-planet.com/
Donate link: https://monzillamedia.com/donate.html
Contributors: specialk
Requires at least: 4.5
Tested up to: 5.2
Stable tag: 1.7
Version: 1.7
Requires PHP: 5.6.20
Text Domain: custom-fields-gutenberg
Domain Path: /languages
License: GPL v2 or later

Restores the Custom Field meta box for the Gutenberg Block Editor.



== Description ==

Restores the Custom Field meta box for the Gutenberg Block Editor.

__Update:__ This plugin currently is not needed, as WordPress version 5.0+ displays Custom Fields natively. Just click the settings button (three dots) and go to Options, where you will find the option to display the Custom Fields meta box. So this plugin still works great, but it is recommended to use native WP custom fields instead. For more information, read [this post](https://wordpress.org/support/topic/please-read-7/).


**Features**

* Easy to use
* Clean code
* Built with the WordPress API
* Lightweight, fast and flexible
* Works great with other WordPress plugins
* Plugin options configurable via settings screen
* Focused on flexibility, performance, and security
* One-click restore plugin default options
* Translation ready


**Options**

* Specify the post types that should display custom fields
* Exclude custom fields that are protected/hidden
* Exclude custom fields with empty values
* Exclude specific custom fields by name


**Planned Features**

* Ajaxify adding of new Custom Fields
* Ajax method to Delete custom fields


**Privacy**

This plugin does not collect or store any user data. It does not set any cookies, and it does not connect to any third-party locations. Thus, this plugin does not affect user privacy in any way.



== Screenshots ==

1. Plugin Settings Screen (showing default options)
2. Custom Fields displayed on Gutenberg screen



== Installation ==

**Installing the plugin**

1. Upload the plugin to your blog and activate
2. Configure the plugin settings as desired
3. Enable theme switcher via settings or shortcode

[More info on installing WP plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)


**Usage**

Works just like the original "Custom Fields" meta box, except:

* __Edit custom field__     &mdash; make any changes and then click the Post "Update" or "Publish" button
* __Add new custom field__  &mdash; add new custom field, click "Update" or "Publish", and then reload the page
* __Delete custom field__   &mdash; set the field custom field Key/Name to a blank value, click "Update" or "Publish", then reload the page


**Uninstalling**

This plugin cleans up after itself. All plugin settings will be removed from your database when the plugin is uninstalled via the Plugins screen. Custom Fields will NOT be removed.


**Like the plugin?**

If you like Custom Fields for Gutenberg, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/custom-fields-gutenberg/reviews/?rate=5#new-post). It helps to keep development and support going strong. Thank you!



== Upgrade Notice ==

To upgrade this plugin, remove the old version and replace with the new version. Or just click "Update" from the Plugins screen and let WordPress do it for you automatically.

Note: uninstalling the plugin from the WP Plugins screen results in the removal of all settings and data from the WP database. Custom Fields will NOT be removed.



== Frequently Asked Questions ==

**Is this plugin needed with WP 5.0 and beyond?**

No. As of WordPress 5.0, Custom Fields are natively supported, so this plugin is not needed to view custom fields on posts (via the "Edit Post" screen). Understand however that custom fields may not be supported after 2021, so this plugin may again be useful if/when that happens. For more information, check out [this post](https://wordpress.org/support/topic/please-read-7/).


**Got a question?**

Send any questions or feedback via my [contact form](https://perishablepress.com/contact/)



== Support development of this plugin ==

I develop and maintain this free plugin with love for the WordPress community. To show support, you can [make a donation](https://monzillamedia.com/donate.html) or purchase one of my books:

* [The Tao of WordPress](https://wp-tao.com/)
* [Digging into WordPress](https://digwp.com/)
* [.htaccess made easy](https://htaccessbook.com/)
* [WordPress Themes In Depth](https://wp-tao.com/wordpress-themes-book/)

And/or purchase one of my premium WordPress plugins:

* [BBQ Pro](https://plugin-planet.com/bbq-pro/) - Super fast WordPress firewall
* [Blackhole Pro](https://plugin-planet.com/blackhole-pro/) - Automatically block bad bots
* [Banhammer Pro](https://plugin-planet.com/banhammer-pro/) - Monitor traffic and ban the bad guys
* [GA Google Analytics Pro](https://plugin-planet.com/ga-google-analytics-pro/) - Connect your WordPress to Google Analytics
* [USP Pro](https://plugin-planet.com/usp-pro/) - Unlimited front-end forms

Links, tweets and likes also appreciated. Thanks! :)



== Changelog ==

If you like Custom Fields for Gutenberg, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/custom-fields-gutenberg/reviews/?rate=5#new-post). It helps to keep development and support going strong. Thank you!


**1.7 (2019/04/28)**

* Bumps [minimum PHP version](https://codex.wordpress.org/Template:Server_requirements) to 5.6.20
* Updates default translation template
* Tests on WordPress 5.2

**1.6 (2019/03/06)**

* Renames plugin to "Custom Fields for Gutenberg Block Editor"
* Checks admin user for the plugin settings shortcut link
* Only display custom fields for Block Editor (not Classic Editor)
* Refines appearance/styles on plugin settings page
* Tweaks plugin settings screen UI
* Generates new default translation template
* Tests on WordPress 5.1 and 5.2 (alpha)

**1.5 (2019/02/02)**

* Tests on WordPress 5.1
* Updates docs/readme regarding WP and Custom Fields

**1.4 (2018/11/12)**

* Refactors plugin for changes in WP/Gutenberg
* Adds option to force display Custom Fields meta box (for ACF plugin)
* Custom Fields box now loads on posts that do not have any meta data
* Resolves issue where new custom fields could not be added
* Adds homepage link to Plugins screen
* Updates default translation template
* Tests on WordPress 5.0 (beta)

**1.3 (2018/08/14)**

* Adds `rel="noopener noreferrer"` to all [blank-target links](https://perishablepress.com/wordpress-blank-target-vulnerability/)
* Makes it possible to delete any custom field by setting its key/name to blank value
* Adds "rate plugin" link to settings page
* Updates donate link
* Updates GDPR blurb
* Regenerates default translation template
* Further tests on WP versions 4.9 and 5.0 (alpha)

**1.2 (2018/03/27)**

* Improves logic of `g7g_cfg_get_post_types()`
* Replaces `update_option` with `delete_option` in `g7g_cfg_reset_options`
* Renames `G7G_DisplayCustomFields` to `G7G_CFG_CustomFields`
* Replaces readme.txt URL with WP Plugin Directory URL
* Further tests on WordPress 5.0 (alpha)

**1.1 (2018/03/26)**

* Replaces `filemtime()` with `g7g_cfg_random_string()`
* Changes name and slug to meet WP Directory requirements
* Renames plugin from "Gutenberg Display Custom Fields" to "Custom Fields for Gutenberg"
* Renames plugin constants
* Renames all functions
* Renames options, et al
* Regenerates default translation file

**1.0 (2018/03/25)**

* Initial release
