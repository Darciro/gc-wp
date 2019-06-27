=== FG Joomla to WordPress Premium K2 module ===
Contributors: Frédéric GILLES
Plugin Uri: https://www.fredericgilles.net/fg-joomla-to-wordpress/
Tags: joomla, wordpress, migrator, converter, import, k2
Requires at least: 4.5
Tested up to: 4.9.7
Stable tag: 2.17.0
Requires PHP: 5.3
License: GPLv2

A plugin to migrate K2 content from Joomla to WordPress
Needs the plugin «FG Joomla to WordPress Premium» to work

== Description ==

This is the K2 module. It works only if the plugin FG Joomla to WordPress Premium is already installed.
It has been tested with **Joomla version 1.5 to 3.8** and **WordPress 4.9**. It is compatible with multisite installations.

Major features include:

* migrates K2 items
* migrates K2 categories
* migrates K2 tags
* migrates K2 comments
* migrates K2 images
* migrates K2 attachments
* migrates K2 custom fields
* migrates K2 authors
* migrates K2 videos
* migrates K2 images galleries
* migrates K2 navigation menus
* option to import the K2 items as posts or as pages
* option to import the video and the gallery at the top or at the bottom of the post
* option to keep the K2 items IDs
* option to redirect the K2 URLs
* option to redirect the K2 advanced SEF URLs
* compatible with ACF 4 & ACF Pro 5
* compatible with Toolset Types

== Installation ==

1.  Prerequesite: Buy and install the plugin «FG Joomla to WordPress Premium»
2.  Extract plugin zip file and load up to your wp-content/plugin directory
3.  Activate Plugin in the Admin => Plugins Menu
4.  Run the importer in Tools > Import > Joomla (FG)

== Translations ==
* English (default)
* French (fr_FR)
* Spanish (es_ES)
* German (de_DE)
* Russian (ru_RU)
* Polish (pl_PL)
* Greek (el_EL)
* other can be translated

== Changelog ==

= 2.17.0 =
New: Greek translation (thanks to Kostas A.)

= 2.16.0 =
New: Compatible with Toolset Types

= 2.15.0 =
New: Add the function "get_k2_element_type" used by the WPML add-on

= 2.14.1 =
New: Modify the K2 links containing "&amp;"

= 2.14.0 =
New: Modify the K2 category internal links

= 2.13.0 =
Tweak: Add the hook "fgj2wp_k2_get_featured_image"

= 2.12.1 =
Fixed: Notice: Undefined index: date
Tweak: Use https for the YouTube URL

= 2.12.0 =
New: Add an option to import the K2 extra fields to ACF
New: Compatible with ACF 4
New: Compatible with ACF Pro 5
Tested with WordPress 4.9

= 2.11.0 =
New: Import the K2 images captions

= 2.10.1 =
Fixed: Galleries with empty classes were not imported
Tested with WordPress 4.8

= 2.10.0 =
New: Redirect categories URLs like /itemlist/category/xx-
Fixed: URLs like /item/xx- were not redirected

= 2.9.0 =
New: Redirect URLs like /item/xx
Tested with WordPress 4.7

= 2.8.0 =
New: Import the K2 Link custom fields

= 2.7.1 =
Tweak: Code refactoring
Tested with WordPress 4.6

= 2.7.0 =
New: Add an option to import the K2 items as posts or as pages

= 2.6.0 =
Compatibility with Joom!Fish 2.3.0

= 2.5.0 =
Fixed: Rewrite the function to delete only the imported data
Tested with WordPress 4.5.2

= 2.4.0 =
Compatibility with FG Joomla to WordPress Premium 3.4.0

= 2.3.0 =
New: Better handle the progress bar
New: Don't log the [COUNT] data in the log window

= 2.2.0 =
New: Ability to stop and resume the import

= 2.1.0 =
New: Add an option to import the video and the gallery at the top or at the bottom of the post

= 2.0.0 =
New: Run the import in AJAX
New: Compatible with PHP 7
Fixed: "</a></iframe>" was replaced by "</a]"

= 1.14.1 =
Fixed: Compatibility with FG Joomla to WordPress Premium 2.11.0

= 1.14.0 =
Tweak: Use the WordPress 4.4 term metas: performance improved, nomore need to add a category prefix
Tested with WordPress 4.4

= 1.13.1 =
Fixed: K2 authors who are not admins or Joomla authors were not assigned to their posts
Fixed: Notice: Undefined offset

= 1.13.0 =
New: If the featured image doesn't exist in the src folder, try to get it in the cache folder
Tested with WordPress 4.3.1

= 1.12.6 =
Fixed: K2 images were not found on some servers: changed the url_exists function
Fixed: PHP Notice:  Undefined index
Tested with WordPress 4.3

= 1.12.5 =
Fixed: Fatal error: Class 'fgj2wpp_urlrewriting' not found

= 1.12.4 =
Fixed: Notice: Undefined index
Tweak: Code optimization
Tested with WordPress 4.2

= 1.12.3 =
Tweak: Restructure and optimize the images import functions

= 1.12.2 =
Fixed: The K2 featured images were not imported in the post content

= 1.12.1 =
Fixed: the joomla_query() function was returning only one row
Tested with WordPress 4.1

= 1.12.0 =
New: Import the K2 meta keywords as tags
Fixed: Import the K2 items even if the articles are skipped

= 1.11.3 =
Update the Spanish translation

= 1.11.2 =
Fixed: Remove extra slashes in the media filenames

= 1.11.1 =
New: Import YouTube videos with full http link

= 1.11.0 =
New: Import the YouTube videos

= 1.10.0 =
New: Display the number of K2 items and categories when testing the database connection
Tested with WordPress 4.0.0

= 1.9.3 =
Fixed: Import featured images even when they are external
New: Help screen

= 1.9.2 =
New: Improve the speed of the menus import
New: Redirect the K2 advanced SEF URLs

= 1.9.1 =
New: Refactor the menus import
Tested with WordPress 3.9

= 1.9.0 =
New: Option to keep the K2 items IDs
Tested with WordPress 3.8.1

= 1.8.0 =
New: Full refactoring of the URL redirect

= 1.7.3 =
Fixed: The trashed items were imported
Fixed: The trashed categories were imported
Fixed: Notice: Undefined property: fgj2wp_k2::$attachments_count

= 1.7.2 =
Fixed: Rewrite rules not deactivated after plugin deactivation

= 1.7.1 =
Fixed: Display the number of comments
Fixed: The attachments with spaces were not imported

= 1.7.0 =
New: Import K2 navigation menus

= 1.6.4 =
New translation: Spanish (thanks to Bradis García L.)
Fixed: Fatal error and undefined index notice fixes

= 1.6.3 =
New translation: Polish (Thanks to Łukasz Z.)

= 1.6.2 =
Optimize the Joomla connection

= 1.6.1 =
Fixed: Notice when $_POST['k2_images'] was not defined
Tested with WordPress 3.6

= 1.6.0 =
New: Import images captions
Tested with WordPress 3.5.2

= 1.5.2 =
Fixed: Replaces the publication date by the creation date as Joomla uses the creation date for sorting articles

= 1.5.1 =
New: Option to not use the first post image as the featured image

= 1.5.0 =
Migrates K2 images galleries
New translation: Russian (Thanks to Julia N.)

= 1.4.2 =
Fixed: Duplicates in multiselect fields
Fixed: Ability to import iframed videos even for non super-admins. We can use the plugin http://wordpress.org/extend/plugins/iframe/ to view the iframes.

= 1.4.1 =
Fixed: the K2 options were not saved when testing the connection

= 1.4.0 =
Migrates the K2 videos
Fixed: multiselect extra fields now get the value and not the index
Add hooks for getting views

= 1.3.0 =
URL redirect for the K2 items (SEO)

= 1.2.2 =
Fix the "modify internal links" for K2 items when not using SEF

= 1.2.1 =
Tested with WordPress 3.5.1
Modifies the K2 ID post meta
Fix the "modify internal links" for K2 items

= 1.2.0 =
Option to import the K2 images in the content or just as featured images
Ability to import K2 items as WordPress pages

= 1.1.0 =
Migrates the K2 authors

= 1.0.1 =
Tested with WordPress 3.5
Ability to migrate non K2 databases even when the K2 module is activated

= 1.0.0 =
Structured as a module of «FG Joomla to WordPress Premium»
