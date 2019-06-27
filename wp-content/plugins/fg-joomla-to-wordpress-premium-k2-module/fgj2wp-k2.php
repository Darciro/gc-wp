<?php
/**
 * Plugin Name: FG Joomla to WordPress Premium K2 module
 * Depends:		FG Joomla to WordPress Premium
 * Plugin Uri:  https://www.fredericgilles.net/fg-joomla-to-wordpress/
 * Description: A plugin to migrate K2 content from Joomla to WordPress
 * 				Needs the plugin «FG Joomla to WordPress Premium» to work
 * Version:     2.17.0
 * Author:      Frédéric GILLES
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

require_once 'fgj2wp-k2-url-rewriting.php';

add_action( 'admin_init', 'fgj2wp_k2_test_requirements' );

if ( !function_exists( 'fgj2wp_k2_test_requirements' ) ) {
	function fgj2wp_k2_test_requirements() {
		new fgj2wp_k2_requirements();
	}
}

if ( !class_exists('fgj2wp_k2_requirements', false) ) {
	class fgj2wp_k2_requirements {
		private $parent_plugin = 'fg-joomla-to-wordpress-premium/fg-joomla-to-wordpress-premium.php';
		private $required_premium_version = '3.20.0';

		public function __construct() {
			load_plugin_textdomain( 'fgj2wp_k2', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			if ( !is_plugin_active($this->parent_plugin) ) {
				add_action( 'admin_notices', array($this, 'fgj2wp_k2_error') );
			} else {
				$plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $this->parent_plugin);
				if ( !$plugin_data or version_compare($plugin_data['Version'], $this->required_premium_version, '<') ) {
					add_action( 'admin_notices', array($this, 'fgj2wp_k2_version_error') );
				}
			}
		}
		
		/**
		 * Print an error message if the Premium plugin is not activated
		 */
		function fgj2wp_k2_error() {
			echo '<div class="error"><p>[fgj2wp_k2] '.__('The K2 module needs the «FG Joomla to WordPress Premium» plugin to work. Please install and activate <strong>FG Joomla to WordPress Premium</strong>.', 'fgj2wp_k2').'<br /><a href="https://www.fredericgilles.net/fg-joomla-to-wordpress/" target="_blank">https://www.fredericgilles.net/fg-joomla-to-wordpress/</a></p></div>';
		}
		
		/**
		 * Print an error message if the Premium plugin is not at the required version
		 */
		function fgj2wp_k2_version_error() {
			printf('<div class="error"><p>[fgj2wp_k2] '.__('The K2 module needs at least the <strong>version %s</strong> of the «FG Joomla to WordPress Premium» plugin to work. Please install and activate <strong>FG Joomla to WordPress Premium</strong> at least the <strong>version %s</strong>.', 'fgj2wp_k2').'<br /><a href="https://www.fredericgilles.net/fg-joomla-to-wordpress/" target="_blank">https://www.fredericgilles.net/fg-joomla-to-wordpress/</a></p></div>', $this->required_premium_version, $this->required_premium_version);
		}
	}
}

if ( !defined('WP_LOAD_IMPORTERS') && !defined('DOING_AJAX') ) return;

add_action( 'plugins_loaded', 'fgj2wp_k2_load', 25 );

if ( !function_exists( 'fgj2wp_k2_load' ) ) {
	function fgj2wp_k2_load() {
		if ( !defined('FGJ2WPP_LOADED') ) return;

		load_plugin_textdomain( 'fgj2wp_k2', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		global $fgj2wpp;
		new fgj2wp_k2($fgj2wpp);
	}
}

if ( !class_exists('fgj2wp_k2', false) ) {
	class fgj2wp_k2 {
		
		public $comments_count = 0;
		public $media_count = 0;
		public $tags_count = 0;
		private $k2_options = array();
		private $imported_fields = array();
		private $imported_options = array();
		
		/**
		 * Sets up the plugin
		 *
		 */
		public function __construct($plugin) {
			
			$this->plugin = $plugin;
			
			add_action( 'fgj2wp_post_empty_database', array($this, 'reset_k2_counters') );
			add_action( 'fgj2wp_post_save_plugin_options', array($this, 'save_k2_options') );
			add_filter( 'fgj2wp_pre_display_joomla_info', array($this, 'get_k2_info'), 10, 1 );
			add_filter( 'fgj2wp_import_categories', array($this, 'import_categories'), 10, 1 );
			add_filter( 'fgj2wpp_post_get_authors', array($this, 'import_authors'), 10, 1);
			add_action( 'fgj2wp_pre_import', array($this, 'remove_keep_joomla_id'));
			add_action( 'fgj2wp_pre_import', array($this, 'set_posts_autoincrement'), 11);
			add_action( 'fgj2wp_pre_import', array($this, 'check_acf_version'));
			add_action( 'fgj2wp_post_import', array($this, 'import_extra_field_groups'), 8);
			add_action( 'fgj2wp_post_import', array($this, 'import_items'), 9 ); // The K2 items must be imported before all the users to get the authors roles
			add_filter( 'fgj2wp_k2_pre_insert_post', array($this, 'add_import_id'), 10, 2);
			add_filter( 'fgj2wp_k2_pre_insert_post', array($this, 'pre_insert_post'), 10, 2 );
			add_action( 'fgj2wp_k2_post_insert_post', array($this, 'post_insert_post'), 10, 2 );
			add_action( 'fgj2wp_k2_post_insert_post', array($this, 'import_comments'), 10, 2 );
			add_filter( 'fgj2wp_pre_get_joomla_id_in_link', array($this, 'get_k2_item_id_in_link'), 10, 2 );
			add_filter( 'fgj2wp_get_wp_term_from_joomla_url', array($this, 'get_k2_category_id_in_link'), 10, 2 );
			add_action( 'fgj2wp_pre_import_menus', array($this, 'set_imported_k2_items_list') );
			add_filter( 'fgj2wp_get_menus_add_extra_criteria', array($this, 'add_menus_extra_criteria'), 10, 1 );
			add_filter( 'fgj2wp_get_menu_item', array($this, 'get_menu_item'), 10, 3 );
			
			add_action( 'fgj2wp_import_notices', array($this, 'display_comments_count') );
			add_action( 'fgj2wp_import_notices', array($this, 'display_media_count') );
			add_action( 'fgj2wp_import_notices', array($this, 'display_tags_count') );
			
			add_filter( 'fgj2wp_pre_display_admin_page', array($this, 'process_admin_page'), 11, 1 );
			add_filter( 'fgj2wp_get_database_info', array($this, 'get_database_info') );
			add_filter( 'fgj2wp_get_total_elements_count', array($this, 'get_total_elements_count') );

			add_action( 'fgj2wp_post_display_behavior_options', array($this, 'display_k2_options') );
			add_action( 'fgj2wp_help_options', array($this, 'display_k2_options_help') );
			
			add_filter( 'fgj2wp_wpml_element_type', array($this, 'get_k2_element_type'), 11, 2 );
			
			// Default values
			$this->k2_options = array(
				'k2_items'				=> 'as_posts',
				'k2_images'				=> 'in_content',
				'k2_video'				=> 'bottom',
				'k2_fields'				=> 'wp',
				'keep_k2_id'			=> false,
				'redirect_k2_url'		=> false,
				'redirect_k2_sef_url'	=> false,
			);
			$options = get_option('fgj2wpk2_options');
			if ( is_array($options) ) {
				$this->k2_options = array_merge($this->k2_options, $options);
			}
		}

		/**
		 * Add information to the admin page
		 * 
		 * @param array $data
		 * @return array
		 */
		public function process_admin_page($data) {
			$data['title'] .= ' ' . __('+ K2 module', __CLASS__);
			$data['description'] .= "<br />" . __('The K2 module will also import K2 content (items, categories, comments, tags, images, videos, attachments and custom fields).', __CLASS__);
			return $data;
		}
		
		/**
		 * Get the WordPress database info
		 * 
		 * @param string $database_info Database info
		 * @return string Database info
		 */
		public function get_database_info($database_info) {
			
			// Comments
			$comments_count_obj = wp_count_comments();
			$comments_count = $comments_count_obj->total_comments;
			$database_info .= sprintf(_n('%d comment', '%d comments', $comments_count, __CLASS__), $comments_count) . "<br />";
			
			return $database_info;
		}

		/**
		 * Add K2 options to the admin page
		 * 
		 */
		public function display_k2_options() {
			echo '<tr><th>' . __('K2 items:', __CLASS__) . '</th><td>';
			echo '<input type="radio" name="k2_items" id="k2_items_as_posts" value="as_posts" ' . checked($this->k2_options['k2_items'], 'as_posts', false) . ' /> <label for="k2_items_as_posts">' . __('Import the K2 items as posts', __CLASS__) . '</label><br />';
			echo '<input type="radio" name="k2_items" id="k2_items_as_pages" value="as_pages" ' . checked($this->k2_options['k2_items'], 'as_pages', false) . ' /> <label for="k2_items_as_pages">' . __('Import the K2 items as pages', __CLASS__) . '</label>';
			echo '</td></tr>';
			
			echo '<tr><th>' . __('K2 images:', __CLASS__) . '</th><td>';
			echo '<input type="radio" name="k2_images" id="k2_images_in_content" value="in_content" ' . checked($this->k2_options['k2_images'], 'in_content', false) . ' /> <label for="k2_images_in_content">' . __('Import the K2 images in the content + as featured images', __CLASS__) . '</label><br />';
			echo '<input type="radio" name="k2_images" id="k2_images_featured" value="featured" ' . checked($this->k2_options['k2_images'], 'featured', false) . ' /> <label for="k2_images_featured">' . __('Import the K2 images as featured images only', __CLASS__) . '</label>';
			echo '</td></tr>';
			
			echo '<tr><th>' . __('K2 video and gallery:', __CLASS__) . '</th><td>';
			echo '<input type="radio" name="k2_video" id="k2_video_top" value="top" ' . checked($this->k2_options['k2_video'], 'top', false) . ' /> <label for="k2_video_top">' . __('Top', __CLASS__) . '</label><br />';
			echo '<input type="radio" name="k2_video" id="k2_video_bottom" value="bottom" ' . checked($this->k2_options['k2_video'], 'bottom', false) . ' /> <label for="k2_video_bottom">' . __('Bottom', __CLASS__) . '</label>';
			echo '</td></tr>';
			
			echo '<tr><th>' . __('K2 extra fields:', __CLASS__) . '</th><td>';
			echo '<input type="radio" name="k2_fields" id="k2_fields_wp" value="wp" ' . checked($this->k2_options['k2_fields'], 'wp', false) . ' /> <label for="k2_fields_wp">' . __('As regular WordPress custom fields', __CLASS__) . '</label><br />';
			echo '<input type="radio" name="k2_fields" id="k2_fields_acf" value="acf" ' . checked($this->k2_options['k2_fields'], 'acf', false) . ' /> <label for="k2_fields_acf">' . __('As ACF custom fields', __CLASS__) . '</label> (<small>' . sprintf(__('the %s plugin is required.', __CLASS__), '<a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">Advanced Custom Fields</a>') . ')</small><br />';
			echo '<input type="radio" name="k2_fields" id="k2_fields_types" value="types" ' . checked($this->k2_options['k2_fields'], 'types', false) . ' /> <label for="k2_fields_types">' . __('As Toolset Types custom fields', __CLASS__) . '</label> (<small>' . sprintf(__('the %s plugin is required.', __CLASS__), '<a href="https://wp-types.com/home/types-manage-post-types-taxonomy-and-custom-fields/?aid=45636&affiliate_key=k1v2vvL3JGBm" target="_blank">Toolset Types</a>') . ')</small>';
			echo '</td></tr>';
			
			echo '<tr><th>' . __('K2 SEO:', __CLASS__) . '</th><td>';
			echo '<input type="checkbox" name="keep_k2_id" id="keep_k2_id" value="1" ' . checked($this->k2_options['keep_k2_id'], 1, false) . ' /> <label for="keep_k2_id">' . __('Keep the K2 items IDs. <sub>Note that the Joomla articles IDs won\'t be kept.</sub>', __CLASS__) . '</label><br />';
			echo '<input type="checkbox" name="redirect_k2_url" id="redirect_k2_url" value="1" ' . checked($this->k2_options['redirect_k2_url'], 1, false) . ' /> <label for="redirect_k2_url">' . __('Redirect the K2 URLs', __CLASS__) . '</label><br />';
			echo '<input type="checkbox" name="redirect_k2_sef_url" id="redirect_k2_sef_url" value="1" ' . checked($this->k2_options['redirect_k2_sef_url'], 1, false) . ' /> <label for="redirect_k2_sef_url">' . __('Redirect the K2 advanced SEF URLs. <sub>Note that the Joomla articles URLs won\'t be redirected.</sub>', __CLASS__) . '</label><br />';
			echo '</td></tr>';
		}

		/**
		 * Add K2 options help to the help tab
		 * 
		 */
		public function display_k2_options_help() {
			echo '<h2>K2 options</h2>
<p><strong>K2 images:</strong> You can import the K2 image as the post featured image or in the post content.</p>

<p><strong>K2 video and gallery:</strong> You can import the K2 video and gallery at the top of the post or at the bottom.</p>

<p><strong>K2 SEO:</strong><br />
<ul>
<li><strong>Keep the K2 items IDs:</strong> With this option checked, the WordPress post IDs will be the same as the K2 ones. If you choose this option, you need to empty all the WordPress content before the import. This option is not compatible with the "Keep the Joomla articles IDs" option.</li>
<li><strong>Redirect the K2 URLs:</strong> With this option checked, the old K2 item links will be automatically redirected to the new WordPress URLs.</li>
<li><strong>Redirect the K2 advanced SEF URLs:</strong> If you checked the K2 advanced SEF option in Joomla, you must check this option.</li>
</ul>
</p>
';
		}

		/**
		 * Reset the stored K2 counters
		 * 
		 */
		public function reset_k2_counters() {
			update_option('fgj2wp_last_k2_item_id', 0);
			update_option('fgj2wp_last_k2_category_id', 0);
		}

		/**
		 * Get K2 info
		 *
		 * @param string $message Message to display when displaying Joomla info
		 * @return string Message
		 */
		public function get_k2_info($message) {
			// K2 categories
			$k2_categories_count = $this->get_k2_categories_count();
			$message .= sprintf(_n('%d K2 category', '%d K2 categories', $k2_categories_count, __CLASS__), $k2_categories_count) . '<br />';
			
			// K2 items
			$k2_items_count = $this->get_k2_items_count();
			$message .= sprintf(_n('%d K2 item', '%d K2 items', $k2_items_count, __CLASS__), $k2_items_count) . '<br />';
			
			// K2 comments
			$k2_comments_count = $this->get_k2_comments_count();
			$message .= sprintf(_n('%d K2 comment', '%d K2 comments', $k2_comments_count, __CLASS__), $k2_comments_count) . '<br />';
			
			return $message;
		}
		
		/**
		 * Update the number of total elements found in Joomla
		 * 
		 * @param int $count Number of total elements
		 * @return int Number of total elements
		 */
		public function get_total_elements_count($count) {
			if ( !isset($this->plugin->premium_options['skip_categories']) || !$this->plugin->premium_options['skip_categories'] ) {
				$count += $this->get_k2_categories_count();
			}
			$count += $this->get_k2_items_count();
			$count += $this->get_k2_comments_count();
			return $count;
		}
		
		/**
		 * Get the number of K2 categories
		 * 
		 * @return int Number of categories
		 */
		private function get_k2_categories_count() {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}k2_categories c
				WHERE c.trash = 0
			";
			$result = $this->plugin->joomla_query($sql);
			$cat_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $cat_count;
		}
		
		/**
		 * Get the number of K2 items
		 * 
		 * @return int Number of items
		 */
		private function get_k2_items_count() {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}k2_items p
				WHERE p.published >= 0 -- don't get the trash
				AND p.trash = 0
			";
			$result = $this->plugin->joomla_query($sql);
			$items_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $items_count;
		}
		
		/**
		 * Get the number of K2 comments
		 * 
		 * @return int Number of comments
		 */
		private function get_k2_comments_count() {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}k2_comments AS c
			";
			$result = $this->plugin->joomla_query($sql);
			$comments_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $comments_count;
		}
		
		/**
		 * Import categories
		 *
		 * @return int Number of categories imported
		 */
		public function import_categories($all_categories) {
			$cat_count = 0;
			
			do {
				if ( $this->plugin->import_stopped() ) {
					break;
				}
				$categories = $this->get_categories($this->plugin->chunks_size); // Get the K2 categories
				
				if ( ($categories != null) && (count($categories) > 0) ) {
					$all_categories = array_merge($all_categories, $categories);
					// Insert the categories
					$cat_count += $this->plugin->insert_categories($categories, 'category', 'fgj2wp_last_k2_category_id');
				}
			} while ( ($categories != null) && (count($categories) > 0) );
			$this->plugin->display_admin_notice(sprintf(_n('%d K2 category imported', '%d K2 categories imported', $cat_count, __CLASS__), $cat_count));
			
			return $all_categories;
		}
		
		/**
		 * Get K2 categories
		 *
		 * @param int $limit Number of categories max
		 * @return array of Categories
		 */
		private function get_categories($limit) {
			$categories = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			$last_category_id = (int)get_option('fgj2wp_last_k2_category_id'); // to restore the import where it left

			$sql = "
				SELECT CONCAT('k', c.id) AS id, c.name AS title, 'k2' AS type, c.alias AS name, c.description, CONCAT('k', c.parent) AS parent_id, '' AS date
				FROM ${prefix}k2_categories c
				WHERE c.trash = 0
				AND c.id > '$last_category_id'
				ORDER BY c.id
				LIMIT $limit
			";
			
			$categories = $this->plugin->joomla_query($sql);
			return $categories;
		}
		
		/**
		 * Import the K2 authors
		 * 
		 * @param arary $authors Authors
		 * @return array Authors
		 */
		public function import_authors($authors) {
			$k2_authors = $this->get_authors();
			$authors = array_merge($authors, $k2_authors);
			return $authors;
		}
		
		/**
		 * Get all the K2 authors
		 * 
		 * @return array Users
		 */
		private function get_authors() {
			$users = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			$extra_cols = '';
			if ( version_compare($this->plugin->joomla_version, '1.5', '<=') ) {
				$extra_cols = ', u.usertype'; // User group
			}
			$sql = "
				SELECT DISTINCT u.id, u.name, u.username, u.email, u.password, u.registerDate
				$extra_cols
				FROM ${prefix}users u
				INNER JOIN ${prefix}k2_items i ON i.created_by = u.id
			";
			$users = $this->plugin->joomla_query($sql);
			return $users;
		}
		
		/**
		 * Import K2 items
		 *
		 */
		public function import_items() {
			$imported_items_count = 0;
			
			$this->plugin->log(__('Importing K2 items...', __CLASS__));
			
			$post_type = ($this->k2_options['k2_items'] == 'as_pages') ? 'page' : 'post';
			
			$this->custom_fields = $this->get_custom_fields(); // Get the custom fields
			
			// Hook for doing other actions before the import
			do_action('fgj2wp_k2_pre_import_items');
			
			do {
				if ( $this->plugin->import_stopped() ) {
					break;
				}
				
				$items = $this->get_items($this->plugin->chunks_size); // Get the K2 items
				$items_count = count($items);
				
				if ( is_array($items) ) {
					foreach ( $items as $item ) {
						
						// Hook for modifying the K2 item before processing
						$item = apply_filters('fgj2wp_k2_pre_process_item', $item);
						
						// Medias
						$item_media = array();
						$featured_image = array();
						$featured_image_id = 0;
						if ( !$this->plugin->plugin_options['skip_media'] ) {
							
							// K2 featured image
							$featured_image = $this->get_featured_image($item['id']);
							if ( $featured_image ) {
								// Caption
								$caption_att = '';
								if ( !empty($item['image_caption']) ) {
									$caption_att = ' class="caption" title="' . $item['image_caption'] . '"';
								}
								// Force the import of the featured image
								$featured_image_id = $this->plugin->import_media($item['image_credits'], $featured_image, $item['date'], array('force_external' => true), $item['image_caption']);
								if ( $featured_image_id && ($this->k2_options['k2_images'] != 'in_content') ) {
									$this->media_count++;
								}
								if ( $this->k2_options['k2_images'] == 'in_content' ) {
									$img_featured_image = sprintf('<img src="%s" alt="%s"%s />', $featured_image, $item['image_credits'], $caption_att);
									$item['introtext'] = $img_featured_image . $item['introtext'];
								}
							} else {
								// Featured image from the content
								list($featured_image_id, $item) = $this->plugin->get_and_process_featured_image($item);
							}
							
							// Attachments
							$attachments_links = $this->build_attachment_links($item['id']);
							if ( !empty($item['fulltext']) ) {
								$item['fulltext'] .= $attachments_links;
							} else {
								$item['introtext'] .= $attachments_links;
							}
							
							// Import media
							$result = $this->plugin->import_media_from_content($item['introtext'] . $item['fulltext'], $item['date']);
							$item_media = $result['media'];
							$this->media_count += $result['media_count'];
							
							// K2 Video
							$video_content = $this->build_video_content($item);
							if ( !empty($item['fulltext']) ) {
								if ( $this->k2_options['k2_video'] == 'bottom' ) {
									$item['fulltext'] .= $video_content;
								} else {
									$item['fulltext'] = $video_content . $item['fulltext'] ;
								}
							} else {
								if ( $this->k2_options['k2_video'] == 'bottom' ) {
									$item['introtext'] .= $video_content;
								} else {
									$item['introtext'] = $video_content . $item['introtext'] ;
								}
							}
							
							// K2 image gallery
							$gallery_content = $this->build_gallery_content($item);
							if ( !empty($item['fulltext']) ) {
								if ( $this->k2_options['k2_video'] == 'bottom' ) {
									$item['fulltext'] .= $gallery_content;
								} else {
									$item['fulltext'] = $gallery_content . $item['fulltext'] ;
								}
							} else {
								if ( $this->k2_options['k2_video'] == 'bottom' ) {
									$item['introtext'] .= $gallery_content;
								} else {
									$item['introtext'] = $gallery_content . $item['introtext'] ;
								}
							}
						}
						
						// Category ID
						if ( array_key_exists($item['catid'], $this->plugin->imported_categories) ) {
							$cat_id = $this->plugin->imported_categories[$item['catid']];
						} else {
							$cat_id = 1; // default category
						}
						
						// Define excerpt and post content
						list($excerpt, $content) = $this->plugin->set_excerpt_content($item);
						
						// Process content
						$excerpt = $this->plugin->process_content($excerpt, $item_media);
						$content = $this->plugin->process_content($content, $item_media);
						
						// Status
						$status = ($item['state'] == 1)? 'publish' : 'draft';
						
						// Tags
						$tags = $this->get_tags($item['id']);
						if ( $this->plugin->plugin_options['meta_keywords_in_tags'] && !empty($item['metakey']) ) {
							$tags = array_merge($tags, explode(',', $item['metakey']));
						}
						$this->plugin->import_tags($tags, 'post_tag');
						$this->tags_count += count($tags);
						
						// Insert the post
						$new_post = array(
							'post_category'		=> array($cat_id),
							'post_content'		=> $content,
							'post_date'			=> $item['date'],
							'post_excerpt'		=> $excerpt,
							'post_status'		=> $status,
							'post_title'		=> $item['title'],
							'post_name'			=> $item['alias'],
							'post_type'			=> $post_type,
							'tags_input'		=> $tags,
							'menu_order'        => $item['ordering'],
						);
						
						// Hook for modifying the WordPress post just before the insert
						$new_post = apply_filters('fgj2wp_k2_pre_insert_post', $new_post, $item);
						
						$new_post_id = wp_insert_post($new_post);
						if ( $new_post_id ) { 
							// Add links between the post and its medias
							$this->plugin->add_post_media($new_post_id, $new_post, $item_media, false);
							
							// Set the featured image
							if ( !empty($featured_image_id) ) {
								set_post_thumbnail($new_post_id, $featured_image_id);
							}

							// Add the K2 ID as a post meta in order to modify links and to import comments
							add_post_meta($new_post_id, '_fgj2wp_old_k2_id', $item['id'], true);
							
							// Add the custom fields
							$this->import_custom_fields($new_post_id, $item['extra_fields']);
							
							// Increment the Joomla last imported post ID
							update_option('fgj2wp_last_k2_item_id', $item['id']);

							$imported_items_count++;
							
							// Hook for doing other actions after inserting the post
							do_action('fgj2wp_k2_post_insert_post', $new_post_id, $item);
						}
					}
				}
				$this->plugin->progressbar->increment_current_count($items_count);
			} while ( ($items != null) && ($items_count > 0) );
			$this->plugin->display_admin_notice(sprintf(_n('%d K2 item imported', '%d K2 items imported', $imported_items_count, __CLASS__), $imported_items_count));
		}
		
		/**
		 * Save the K2 options
		 *
		 */
		public function save_k2_options() {
			$this->k2_options = array_merge($this->k2_options, $this->validate_form_info());
			update_option('fgj2wpk2_options', $this->k2_options);
		}
		
		/**
		 * Validate POST info
		 *
		 * @return array Form parameters
		 */
		private function validate_form_info() {
			$k2_items = filter_input(INPUT_POST, 'k2_items', FILTER_SANITIZE_STRING);
			$k2_images = filter_input(INPUT_POST, 'k2_images', FILTER_SANITIZE_STRING);
			$k2_video = filter_input(INPUT_POST, 'k2_video', FILTER_SANITIZE_STRING);
			$k2_fields = filter_input(INPUT_POST, 'k2_fields', FILTER_SANITIZE_STRING);
			return array(
				'k2_items'				=> !empty($k2_items)? $k2_items : 'as_posts',
				'k2_images'				=> !empty($k2_images)? $k2_images : 'in_content',
				'k2_video'				=> !empty($k2_video)? $k2_video : 'bottom',
				'k2_fields'				=> !empty($k2_fields)? $k2_fields : 'wp',
				'keep_k2_id'			=> filter_input(INPUT_POST, 'keep_k2_id', FILTER_VALIDATE_BOOLEAN),
				'redirect_k2_url'		=> filter_input(INPUT_POST, 'redirect_k2_url', FILTER_VALIDATE_BOOLEAN),
				'redirect_k2_sef_url'	=> filter_input(INPUT_POST, 'redirect_k2_sef_url', FILTER_VALIDATE_BOOLEAN),
			);
		}
		
		/**
		 * Get K2 items
		 *
		 * @param int $limit Number of items max
		 * @return array of Items
		 */
		private function get_items($limit=1000) {
			$items = array();
			$last_id = (int)get_option('fgj2wp_last_k2_item_id'); // to restore the import where it left
			$prefix = $this->plugin->plugin_options['prefix'];

			// Hooks for adding extra cols and extra joins
			$extra_cols = apply_filters('fgj2wp_k2_get_items_add_extra_cols', '');
			$extra_joins = apply_filters('fgj2wp_k2_get_items_add_extra_joins', '');

			$sql = "
				SELECT p.id, 'k2_items' AS type, p.title, p.alias, p.introtext, p.fulltext, p.video, p.video_caption, p.video_credits, p.gallery, p.published AS state, CONCAT('k', p.catid) AS catid, p.modified, p.created AS `date`, p.metakey, p.metadesc, p.ordering, p.image_caption, p.image_credits,
				p.extra_fields, p.created_by, p.created_by_alias
				$extra_cols
				FROM ${prefix}k2_items p
				$extra_joins
				WHERE p.published >= 0 -- don't get the trash
				AND p.trash = 0
				AND p.id > '$last_id'
				ORDER BY p.id
				LIMIT $limit
			";

			$items = $this->plugin->joomla_query($sql);
			return $items;
		}

		/**
		 * Get the featured image
		 * 
		 * @param int $item_id Item ID
		 * @return string Image name
		 */
		private function get_featured_image($item_id) {
			$found_image_name = '';
			$image_md5 = md5("Image" . $item_id);
			
			// Try to get the original image in the src folder
			$image_name = 'media/k2/items/src/' . $image_md5 . '.jpg';
			$image_url = trailingslashit($this->plugin->plugin_options['url']) . $image_name;
			if ( $this->plugin->url_exists($image_url) ) {
				$found_image_name = $image_name;
			} else {
				
				// if the featured image doesn't exist in the src folder, try to get the XL image in the cache folder
				$image_name = 'media/k2/items/cache/' . $image_md5 . '_XL.jpg';
				$image_url = trailingslashit($this->plugin->plugin_options['url']) . $image_name;
				if ( $this->plugin->url_exists($image_url) ) {
					$found_image_name = $image_name;
				}
			}
			$found_image_name = apply_filters('fgj2wp_k2_get_featured_image', $found_image_name, $item_id);
			return $found_image_name;
		}
		
		/**
		 * Get the images in the gallery
		 * 
		 * @param int $item_id Item ID
		 * @return array Images names
		 */
		private function get_gallery_images($item_id) {
			$images = array();
			$matches = array();
			$gallery_dir = '/media/k2/galleries/' . $item_id;
			$item_url = untrailingslashit($this->plugin->plugin_options['url']) . '/index.php?option=com_k2&view=item&id=' . $item_id;
			
			// Read the K2 item
			$response = wp_remote_get($item_url); // Uses WordPress HTTP API

			// Check for the images in the gallery
			if ( is_array($response) && !empty($response['body']) ) {
				if ( preg_match_all("#($gallery_dir/.*?)\"#", $response['body'], $matches) ) {
					$images = preg_replace('/ .*/', '', $matches[1]); // Remove the " class =" in the image path
				}
			}
			return $images;
		}
		
		/**
		 * Get the K2 tags of an item
		 * 
		 * @param int $item_id
		 * @return array Array of tags
		 */
		private function get_tags($item_id) {
			$tags = array();
			$prefix = $this->plugin->plugin_options['prefix'];

			$sql = "
				SELECT t.name
				FROM ${prefix}k2_tags_xref AS x
				INNER JOIN ${prefix}k2_tags AS t ON t.id = x.tagID AND t.published = 1
				WHERE x.itemID = '$item_id'
			";

			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				$tags[] = $row['name'];
			}
			return $tags;
		}
		
		/**
		 * Display the number of imported tags
		 * 
		 */
		public function display_tags_count() {
			$this->plugin->display_admin_notice(sprintf(_n('%d K2 tag imported', '%d K2 tags imported', $this->tags_count, __CLASS__), $this->tags_count));
		}

		/**
		 * Import the K2 comments
		 * 
		 * @param int $new_post_id New post ID
		 * @param array $item K2 item
		 */
		public function import_comments($new_post_id, $item) {
			$comments = $this->get_comments($item['id']);
			$comments_count = count($comments);
			foreach ( $comments as $comment ) {
				$data = array(
					'comment_post_ID' => $new_post_id,
					'comment_author' => $comment['userName'],
					'comment_author_email' => $comment['commentEmail'],
					'comment_author_url' => $comment['commentURL'],
					'comment_content' => $comment['commentText'],
					'comment_type' => '',
					'comment_parent' => 0,
					'comment_date' => $comment['commentDate'],
					'comment_approved' => $comment['published'],
				);
				$comment_id = wp_insert_comment($data);
				if ( !empty($comment_id) ) {
					add_comment_meta($comment_id, '_fgj2wp_old_k2_comment_id', $comment['id'], true);
					$this->comments_count++;
				}
			}
			if ( $comments_count != 0 ) {
				$this->plugin->progressbar->increment_current_count($comments_count);
			}
		}

		/**
		 * Get the K2 comments of an item
		 * 
		 * @param int $item_id
		 * @return array Array of comments
		 */
		private function get_comments($item_id) {
			$comments = array();
			$prefix = $this->plugin->plugin_options['prefix'];

			$sql = "
				SELECT c.id, c.userID, c.userName, c.commentDate, c.commentText, c.commentEmail, c.commentURL, c.published
				FROM ${prefix}k2_comments AS c
				WHERE c.itemID = '$item_id'
			";

			$comments = $this->plugin->joomla_query($sql);
			return $comments;
		}

		/**
		 * Display the number of imported comments
		 * 
		 */
		public function display_comments_count() {
			$this->plugin->display_admin_notice(sprintf(_n('%d K2 comment imported', '%d K2 comments imported', $this->comments_count, __CLASS__), $this->comments_count));
		}

		/**
		 * Build the K2 attachments links
		 * 
		 * @param int $item_id
		 * @return string attachment links
		 */
		public function build_attachment_links($item_id) {
			$attachment_link = '';
			$attachments = $this->get_attachments($item_id);
			foreach ( $attachments as $attachment ) {
				$attachment_name = '/media/k2/attachments/' . $attachment['filename'];
				$attachment_url = untrailingslashit($this->plugin->plugin_options['url']) . $attachment_name;
				if ( $this->plugin->url_exists($attachment_url) ) {
					$attachment_link .= "<br />\n" . '<a href="' . $attachment_url . '" alt="' . $attachment['titleAttribute'] . '">' . $attachment['title'] . '</a>';
				}
			}
			return $attachment_link;
		}

		/**
		 * Get the K2 attachments of an item
		 * 
		 * @param int $item_id
		 * @return array Array of attachments
		 */
		private function get_attachments($item_id) {
			$attachments = array();
			$prefix = $this->plugin->plugin_options['prefix'];

			$sql = "
				SELECT a.filename, a.title, a.titleAttribute
				FROM ${prefix}k2_attachments AS a
				WHERE a.itemID = '$item_id'
			";

			$attachments = $this->plugin->joomla_query($sql);
			return $attachments;
		}
		
		/**
		 * Build the video content from the K2 video fields
		 * 
		 * @param array $item K2 item
		 * @return string Video content
		 */
		private function build_video_content($item) {
			$video_content = '';
			if ( !empty($item['video']) ) {
				$video_content .= "<div class=\"k2_video\">";
				
				// Video caption
				if ( !empty($item['video_caption']) ) {
					$video_content .= "<div class=\"k2_video_caption\">" . $item['video_caption'] . "</div>\n";
				}
				
				$video_data = $item['video'];
				
				// Embedded video
				// Replace the iframe tag by a shortcode because WordPress removes it if we are not a super-admin
				// We can use the plugin https://wordpress.org/plugins/iframe/ to view the iframe
				$video_data = preg_replace("#<iframe(.*?)>(.*?)</iframe>#", "[iframe$1 $2]", $video_data);
				
				// YouTube
				if ( preg_match("#{youtube}#i", $video_data) ) {
					$video_data = preg_replace("#{youtube}(.*?){/youtube}#i", "$1", $video_data);
					if ( preg_match('#^http#i', $video_data) ) {
						$video_data = '[embed]' . $video_data . "[/embed]\n";
					} else {  // YouTube short link
						$video_data = '[embed]https://www.youtube.com/watch?v=' . $video_data . "[/embed]\n";
					}
				}
				
				$video_content .= $video_data;
				
				// Video credits
				if ( !empty($item['video_credits']) ) {
					$video_content .= "<div class=\"k2_video_credits\">" . $item['video_credits'] . "</div>\n";
				}
				$video_content .= "</div>\n";
			}
			return $video_content;
		}
		
		/**
		 * Build the gallery content from the K2 gallery field
		 * 
		 * @param array $item K2 item
		 * @return string Gallery content
		 */
		private function build_gallery_content($item) {
			$gallery_content = '';
			if ( !empty($item['gallery']) ) {
				$images_ids = array();
				$images = $this->get_gallery_images($item['id']);
				foreach ( $images as $image ) {
					$image_url = untrailingslashit($this->plugin->plugin_options['url']) . $image;
					// Import the image
					$image_name = str_replace("%20", " ", basename($image)); // Replace %20 by spaces
					$image_name = preg_replace('/\..*$/', '', $image_name); // Remove extension
					$attachment_id = $this->plugin->import_media($image_name, $image_url, $item['date']);
					if ( $attachment_id ) {
						$this->media_count++;
						$images_ids[] = $attachment_id;
					}
				}
				
				// Create gallery shortcode
				if ( !empty($images_ids) ) {
					$gallery_content = '[gallery ids="' .  implode(',', $images_ids) . '"]';
				}
			}
			return $gallery_content;
		}
		
		/**
		 * Display the number of imported media
		 * 
		 */
		public function display_media_count() {
			$this->plugin->display_admin_notice(sprintf(_n('%d K2 media imported', '%d K2 media imported', $this->media_count, __CLASS__), $this->media_count));
		}

		/**
		 * Get the K2 custom fields
		 * 
		 */
		private function get_custom_fields() {
			$custom_fields = array();
			$prefix = $this->plugin->plugin_options['prefix'];

			$sql = "
				SELECT ef.id, ef.name, ef.value, ef.type
				FROM ${prefix}k2_extra_fields AS ef
				WHERE ef.published = 1
				ORDER BY ef.ordering
			";

			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				$options = json_decode($row['value']);
				$values = array();
				foreach ( $options as $option ) {
					if ( !empty($option->value) ) {
						$values[$option->value] = $option->name;
					}
				}
				$custom_fields[$row['id']] = (object) array(
					'name'	=> $row['name'],
					'value'	=> $values,
					'type'	=> $row['type'],
				);
			}
			return $custom_fields;
		}
		
		/**
		 * Import the extra fields for the current post
		 *
		 * @param int $new_post_id WordPress post ID
		 * @param string $json_extra_fields Extra field in JSON
		 */
		private function import_custom_fields($new_post_id, $json_extra_fields) {
			$extra_fields = json_decode($json_extra_fields, ARRAY_A);
			if ( is_array($extra_fields) ) {
				foreach ( $extra_fields as $extra_field ) {
					switch ( $this->k2_options['k2_fields'] ) {
						case 'acf':
							$this->import_custom_field_as_acf($extra_field, $new_post_id);
							break;
						case 'types':
							$this->import_custom_field_as_types($extra_field, $new_post_id);
							break;
						default:
							$this->import_custom_field_as_wp($extra_field, $new_post_id);
					}
				}
			}
		}
		
		/**
		 * Import a K2 custom field as a regular WordPress custom field
		 * 
		 * @since 2.12.0
		 * 
		 * @param array $extra_field Extra field data
		 * @param int $new_post_id WordPress post ID
		 */
		private function import_custom_field_as_wp($extra_field, $new_post_id) {
			if ( isset($this->custom_fields[$extra_field['id']]) ) {
				$custom_field = $this->custom_fields[$extra_field['id']];

				switch ( $custom_field->type ) {

					// Text value
					case 'textfield':
						$custom_value = $extra_field['value'];
						break;

					// Link
					case 'link':
						$custom_value = isset($extra_field['value'][1])? $extra_field['value'][1] : ''; // Target URL is in the second position
						break;

					// Indexed value
					default:
						if ( is_array($extra_field['value']) ) {
							// multi-select field
							$custom_values = array();
							foreach ( $extra_field['value'] as $indexed_value ) {
								if ( array_key_exists($indexed_value, $custom_field->value) ) {
									$custom_values[] = $custom_field->value[$indexed_value];
								}
							}
							$custom_value = implode(', ', $custom_values);
						} else {
							// mono-select field
							$custom_value = $extra_field['value'];
						}
				}
				add_post_meta($new_post_id, $custom_field->name, $custom_value, true);
			}
		}
		
		/**
		 * Import a K2 custom field as an ACF custom field
		 * 
		 * @since 2.12.0
		 * 
		 * @param array $extra_field Extra field data
		 * @param int $new_post_id WordPress post ID
		 */
		private function import_custom_field_as_acf($extra_field, $new_post_id) {
			if ( isset($this->imported_fields[$extra_field['id']]) ) {
				$imported_field = $this->imported_fields[$extra_field['id']];
				$value = '';
				switch ( $imported_field['type'] ) {
					case 'url':
						$value = $extra_field['value'][1]; // URL
						break;
					case 'date_picker':
						$value = str_replace('-', '', $extra_field['value']); // Date format = YYYYmmdd
						break;
					case 'image':
						$image_id = $this->plugin->import_media(basename($extra_field['value']), $extra_field['value'], date('Y-m-d H:i:s'));
						if ( $image_id ) {
							$value = $image_id;
						}
						break;
					default:
						$value = $extra_field['value'];
				}
				if ( !empty($value) ) {
					add_post_meta($new_post_id, $imported_field['slug'], $value, true);
					add_post_meta($new_post_id, '_' . $imported_field['slug'], $imported_field['key'], true);
				}
			}
		}
		
		/**
		 * Import a K2 custom field as an Toolset Types custom field
		 * 
		 * @since 2.16.0
		 * 
		 * @param array $extra_field Extra field data
		 * @param int $new_post_id WordPress post ID
		 */
		private function import_custom_field_as_types($extra_field, $new_post_id) {
			if ( isset($this->imported_fields[$extra_field['id']]) ) {
				$imported_field = $this->imported_fields[$extra_field['id']];
				$value = '';
				switch ( $imported_field['type'] ) {
					case 'url':
						$value = $extra_field['value'][1]; // URL
						break;
					case 'date':
						$value = strtotime($extra_field['value']); // Date format = timestamp
						break;
					case 'image':
						$image_id = $this->plugin->import_media(basename($extra_field['value']), $extra_field['value'], date('Y-m-d H:i:s'));
						if ( $image_id ) {
							$value = wp_get_attachment_url($image_id);
						}
						break;
					case 'checkboxes':
						$value = array();
						foreach ( $extra_field['value'] as $extra_field_value ) {
							if ( isset($this->imported_options[$extra_field['id']][$extra_field_value]) ) {
								$imported_option = $this->imported_options[$extra_field['id']][$extra_field_value];
								$value[$imported_option] = array($extra_field_value);
							}
						}
						break;
					default:
						$value = $extra_field['value'];
				}
				if ( !empty($value) ) {
					add_post_meta($new_post_id, 'wpcf-' . $imported_field['slug'], $value, true);
				}
			}
		}
		
		/**
		 * Set the imported K2 items global variable
		 *
		 */
		public function set_imported_k2_items_list() {
			$this->plugin->imported_k2_items = $this->get_imported_k2_items();
			ksort($this->plugin->imported_k2_items);
		}
		
		/**
		 * Returns the imported posts mapped with their K2 ID
		 *
		 * @return array of post IDs [k2_item_id => wordpress_post_id]
		 */
		private function get_imported_k2_items() {
			global $wpdb;
			$posts = array();
			
			$sql = "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_fgj2wp_old_k2_id'";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				$posts[$result->meta_value] = $result->post_id;
			}
			ksort($posts);
			return $posts;
		}
		
		/**
		 * Get the K2 item ID in a link
		 * Used to modify K2 internal links
		 *
		 * @param array('meta_key' => $meta_key, 'meta_value' => $meta_value) $meta_key_value
		 * @param string $link
		 * @return array('meta_key' => $meta_key, 'meta_value' => $meta_value)
		 */
		public function get_k2_item_id_in_link($meta_key_value, $link) {
			$matches = array();
			if ( $meta_key_value['meta_value'] != 0 ) {
				return $meta_key_value; // ID already found
			}
			// Without URL rewriting
			if ( preg_match("#view=item&(amp;)?id=(\d+)#", $link, $matches) ) {
				$meta_key_value['meta_key'] = '_fgj2wp_old_k2_id';
				$meta_key_value['meta_value'] = $matches[2];
			}
			// With URL rewriting
			elseif ( preg_match("#(.*)/item/(\d+)-(.*)#", $link, $matches) ) {
				$meta_key_value['meta_key'] = '_fgj2wp_old_k2_id';
				$meta_key_value['meta_value'] = $matches[2];
			}
			return $meta_key_value;
		}
		
		/**
		 * Get the K2 category ID in a link
		 * Used to modify K2 internal links
		 *
		 * @since 2.14.0
		 * 
		 * @param $term WP_Term WordPress term | null
		 * @param string $link
		 * @return WP_Term WordPress term | null
		 */
		public function get_k2_category_id_in_link($term, $link) {
			$matches = array();
			if ( empty($term) ) {
				// Without URL rewriting
				if ( preg_match("#option=com_k2&.*task=category&(amp;)?id=(\d+)#", $link, $matches) ) {
					$k2_category_id = $matches[2];
					// Search a term by its id
					$args = array(
						'hide_empty' => false, // also retrieve terms which are not used yet
						'meta_query' => array(
							array(
							   'key'       => '_fgj2wp_old_category_id',
							   'value'     => 'k' . $k2_category_id,
							   'compare'   => '='
							)
						)
					);
					$terms = get_terms($args);
					if ( count($terms) > 0 ) {
						$term = $terms[0];
					}
				}
			}
			return $term;
		}
		
		/**
		 * Add the K2 menus in the menu query
		 */
		public function add_menus_extra_criteria($extra_criteria) {
			$sql = "
					OR (type = 'component'
						AND link LIKE '%option=com_k2%'
						AND (link LIKE '%id=%')
						)
			";
			return $extra_criteria . $sql;
		}
		
		/**
		 * Get the menu item data (object_id, type, url, object)
		 * 
		 * @param array $menu Menu item row
		 * @param string $post_type Post type
		 * @return array Menu item
		 */
		public function get_menu_item($menu_item, $menu, $post_type) {
			$matches = array();
			if ( !is_null($menu_item) ) {
				return $menu_item;
			}
			$menu_item_object_id = 0;
			$menu_item_type = '';
			$menu_item_url = '';
			$menu_item_object = '';
			switch ( $menu['type'] ) {
				case 'component':
					if ( preg_match('/view=item(&.*)?&id=(\d+)/', $menu['link'], $matches) ) {
						// K2 item
						$menu_item_type = 'post_type';
						$menu_item_object = $post_type;
						$k2_item_id = $matches[2];
						if ( array_key_exists($k2_item_id, $this->plugin->imported_k2_items) ) {
							$menu_item_object_id = $this->plugin->imported_k2_items[$k2_item_id];
						} else {
							return;
						}
						
					} elseif ( preg_match('/view=(itemlist)(&.*)?&id=(\d+)/', $menu['link'], $matches) ) {
						// K2 category
						$menu_item_type = 'taxonomy';
						$menu_item_object = 'category';
						
						$taxonomy = $matches[1];
						$jterm_id = 'k' . $matches[3];
						if ( ($taxonomy == 'itemlist') && preg_match('/task=category/', $menu['link']) ) {
							if ( array_key_exists($jterm_id, $this->plugin->imported_categories) ) {
								$menu_item_object_id = $this->plugin->imported_categories[$jterm_id];
							} else {
								return;
							}
						} else {
							return;
						}
					} else {
						return;
					}
					break;
				
				default: return;
			}
			
			return array(
				'object_id'	=> $menu_item_object_id,
				'type'		=> $menu_item_type,
				'url'		=> $menu_item_url,
				'object'	=> $menu_item_object,
			);
		}
		
		/**
		 * Remove the filter "keep Joomla IDs" if the "keep K2 IDs" option is set
		 * 
		 */
		public function remove_keep_joomla_id() {
			if ( $this->k2_options['keep_k2_id'] ) {
				remove_filter('fgj2wp_pre_insert_post', array($this->plugin, 'add_import_id'));
			}
		}
		
		/**
		 * Set the posts table autoincrement to the last K2 ID + 100
		 * 
		 */
		public function set_posts_autoincrement() {
			global $wpdb;
			if ( $this->k2_options['keep_k2_id'] ) {
				$last_k2_article_id = $this->get_last_k2_article_id() + 100;
				$sql = "ALTER TABLE $wpdb->posts AUTO_INCREMENT = $last_k2_article_id";
				$wpdb->query($sql);
			}
		}
		
		/**
		 * Get the last K2 article ID
		 *
		 * @return int Last K2 item ID
		 */
		private function get_last_k2_article_id() {
			$max_id = 0;
			$prefix = $this->plugin->plugin_options['prefix'];

			$sql = "
				SELECT max(id) AS max_id
				FROM ${prefix}k2_items
			";
			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				$max_id = $row['max_id'];
				break;
			}
			return $max_id;
		}
		
		/**
		 * Keep the K2 ID
		 * 
		 * @param array $new_post New post
		 * @param array $item K2 item
		 * @return array Post
		 */
		public function add_import_id($new_post, $item) {
			if ( $this->k2_options['keep_k2_id'] ) {
				$new_post['import_id'] = $item['id'];
			}
			return $new_post;
		}
		
		/**
		 * Pre_insert_post hook
		 * 
		 * @param array $new_post WordPress post
		 * @param array $item K2 item
		 * @return array WordPress post
		 */
		public function pre_insert_post($new_post, $item) {
			return apply_filters('fgj2wp_pre_insert_post', $new_post, $item);
		}
		
		/**
		 * Post_insert_post hook
		 * 
		 * @param array $new_post_id WordPress post ID
		 * @param array $item K2 item
		 */
		public function post_insert_post($new_post_id, $item) {
			return do_action('fgj2wp_post_insert_post', $new_post_id, $item);
		}
		
		/**
		 * Check the ACF version
		 * 
		 * @since 2.12.0
		 */
		public function check_acf_version() {
			$this->acf_version = get_option('acf_version', 0);
		}
		
		/**
		 * Import the extra field groups
		 * 
		 * @since 2.12.0
		 */
		public function import_extra_field_groups() {
			if ( !isset($this->k2_options['k2_fields']) || !in_array($this->k2_options['k2_fields'], array('acf', 'types')) ) {
				return;
			}
			$extra_fields_groups = $this->get_extra_field_groups();
			switch ( $this->k2_options['k2_fields'] ) {
				case 'acf':
					$this->import_extra_field_groups_as_acf($extra_fields_groups);
					break;
				case 'types':
					$this->import_extra_field_groups_as_types($extra_fields_groups);
					break;
			}
		}
		
		/**
		 * Get the K2 extra fields groups
		 * 
		 * @since 2.12.0
		 * 
		 * @return array Extra fields groups
		 */
		private function get_extra_field_groups() {
			$groups = array();
			$prefix = $this->plugin->plugin_options['prefix'];

			$sql = "
				SELECT id, name
				FROM ${prefix}k2_extra_fields_groups
			";
			
			$groups = $this->plugin->joomla_query($sql);
			return $groups;
		}
		
		/**
		 * Import the extra field groups as ACF fields groups
		 * 
		 * @since 2.16.0
		 * 
		 * @param array $extra_fields_groups Extra fields groups
		 */
		private function import_extra_field_groups_as_acf($extra_fields_groups) {
			foreach ( $extra_fields_groups as $group ) {
				// Create the ACF group
				$fields_group_id = $this->create_acf_group($group);
				
				// Create the ACF fields
				$fields = $this->get_extra_fields($group['id']);
				foreach ( $fields as $field ) {
					if ( version_compare($this->acf_version, '5', '<') ) {
						// ACF version 4
						$this->create_acf4_field($field, $fields_group_id);
					} else {
						// ACF version 5
						$this->create_acf5_field($field, $fields_group_id);
					}
				}
			}
		}
		
		/**
		 * Create the ACF fields group
		 * 
		 * @since 2.12.0
		 * 
		 * @param array $group Extra fields group
		 * @return int Fields group ID
		 */
		private function create_acf_group($group) {
			$meta_key = '_fgj2wp_old_k2_extra_fields_group_id';
			
			// Check if the group already exists
			$new_post_id = $this->plugin->get_wp_post_id_from_meta($meta_key, $group['id']);
			
			if ( empty($new_post_id) ) {
				$group_title = $group['name'];
				$group_slug = sanitize_title(FG_Joomla_to_WordPress_Tools::convert_to_latin($group_title));
				if ( version_compare($this->acf_version, '5', '<') ) {
					// ACF version 4
					$post_type = 'acf';
					$group_slug = 'acf_' . $group_slug;
					$post_excerpt = '';
					$content = '';
				} else {
					// ACF version 5
					$post_type = 'acf-field-group';
					$group_slug = 'group_' . uniqid();
					$post_excerpt = $group_slug;
					$content = array(
						'location' => $this->build_acf5_locations($group['id']),
						'position' => 'normal',
						'style' => 'default',
						'label_placement' => 'top',
						'instruction_placement' => 'label',
						'hide_on_screen' => '',
						'description' => '',
					);
				}
				
				// Insert the post
				$new_post = array(
					'post_title'		=> $group_title,
					'post_name'			=> $group_slug,
					'post_content'		=> serialize($content),
					'post_excerpt'		=> $post_excerpt,
					'post_type'			=> $post_type,
					'post_status'		=> 'publish',
					'comment_status'	=> 'closed',
					'ping_status'		=> 'closed',
				);
				$new_post_id = wp_insert_post($new_post, true);
				if ( !is_wp_error($new_post_id) ) {
					add_post_meta($new_post_id, $meta_key, $group['id'], true);
					
					if ( version_compare($this->acf_version, '5', '<') ) {
						// ACF version 4
						add_post_meta($new_post_id, 'position', 'normal', true);
						add_post_meta($new_post_id, 'layout', 'no_box', true);
						add_post_meta($new_post_id, 'hide_on_screen', '', true);
						$rules = $this->build_acf4_rules($group['id']);
						foreach ( $rules as $rule ) {
							add_post_meta($new_post_id, 'rule', $rule, false);
						}
					}
				}
			}
			return $new_post_id;
		}
		
		/**
		 * Build the ACF field group locations
		 * 
		 * @since 2.12.0
		 * 
		 * @param int $group_id K2 Group ID
		 * @return array ACF locations
		 */
		private function build_acf5_locations($group_id) {
			$locations = array();
			$group_categories = $this->get_group_categories($group_id);
			foreach ( $group_categories as $category ) {
				$locations[] = array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'post',
					),
					array(
						'param' => 'post_category',
						'operator' => '==',
						'value' => 'category:' . $category['alias'],
					),
				);
			}
			return $locations;
		}
		
		/**
		 * Build the ACF 4 field group rules
		 * 
		 * @since 2.12.0
		 * 
		 * @param int $group_id K2 Group ID
		 * @return array ACF rules
		 */
		private function build_acf4_rules($group_id) {
			$rules = array();
			$group_categories = $this->get_group_categories($group_id);
			$group_no = 0;
			foreach ( $group_categories as $category ) {
				$rules[] = array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
					'order_no' => 0,
					'group_no' => $group_no,
				);
				$kcatid = 'k' . $category['id'];
				if ( isset($this->plugin->imported_categories[$kcatid]) ) {
					$wp_cat_id = $this->plugin->imported_categories[$kcatid];
					$rules[] = array(
						'param' => 'post_category',
						'operator' => '==',
						'value' => $wp_cat_id,
						'order_no' => 1,
						'group_no' => $group_no,
					);
				}
				$group_no++;
			}
			return $rules;
		}
		
		/**
		 * Get the K2 categories linked to the extra fields group
		 * 
		 * @param int $group_id K2 extra fields group ID
		 * @return array Categories
		 */
		private function get_group_categories($group_id) {
			$categories = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			$sql = "
				SELECT c.id, c.alias
				FROM ${prefix}k2_categories c
				WHERE c.trash = 0
				AND c.extraFieldsGroup = '$group_id'
			";
			
			$categories = $this->plugin->joomla_query($sql);
			return $categories;
		}
		
		/**
		 * Get the K2 extra fields
		 * 
		 * @since 2.12.0
		 * 
		 * @param int $group_id Group ID
		 * @return array Extra fields
		 */
		private function get_extra_fields($group_id) {
			$fields = array();
			$prefix = $this->plugin->plugin_options['prefix'];

			$sql = "
				SELECT id, name, value, type, ordering
				FROM ${prefix}k2_extra_fields
				WHERE `group` = '$group_id'
				AND published = 1
			";
			
			$fields = $this->plugin->joomla_query($sql);
			return $fields;
		}
		
		/**
		 * Create an ACF field (version 4)
		 * 
		 * @since 2.12.0
		 * 
		 * @param array $field Field data
		 * @param int $fields_group_id Field group ID
		 */
		private function create_acf4_field($field, $fields_group_id) {
			$field_slug = sanitize_title(FG_Joomla_to_WordPress_Tools::convert_to_latin($field['name']));
			$values = json_decode($field['value'], ARRAY_A);
			$field_type = $this->map_acf_field_type($field['type'], $values);
			
			// Check if the field already exists
			$field_key = '';
			$post_metas = get_post_meta($fields_group_id);
			foreach ( $post_metas as $meta_key => $meta_value ) {
				if ( preg_match('/^field_/', $meta_key) && is_array($meta_value) && isset($meta_value[0]) ) {
					$post_meta = unserialize($meta_value[0]);
					if ( isset($post_meta['label']) && ($post_meta['label'] == $field['name']) ) {
						$field_key = $meta_key;
						break;
					}
				}
			}
			if ( empty($field_key) ) {
				$acf4_field_type = ($field_type == 'url')? 'text' : $field_type; // URL type doesn't exist in ACF 4
				$field_key = 'field_' . uniqid();
				$field_data = array(
					'key' => $field_key,
					'label' => $field['name'],
					'name' => $field_slug,
					'type' => $acf4_field_type,
					'instructions' => '',
					'required' => $values[0]['required'],
					'default_value' => $values[0]['value'],
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
					'order_no' => $field['ordering'] - 1, // ACF starts at 0
				);
				// Multiple select
				if ( $field['type'] == 'multipleSelect' ) {
					$field_data['multiple'] = 1;
				}
				// Allow null
				if ( $values[0]['showNull'] == 1 ) {
					$field_data['allow_null'] = 1;
				}
				// Choices
				if ( count($values) > 1 ) {
					$choices = array();
					foreach ( $values as $item ) {
						$choices[$item['value']] = $item['name'];
					}
					$field_data['choices'] = $choices;
				}
				add_post_meta($fields_group_id, $field_key, $field_data, true);
			}
			$this->imported_fields[$field['id']] = array(
				'slug' => $field_slug,
				'key' => $field_key,
				'type' => $field_type,
			);
		}
		
		/**
		 * Create an ACF field (version 5)
		 * 
		 * @since 2.12.0
		 * 
		 * @param array $field Field data
		 * @param int $fields_group_id Field group ID
		 */
		private function create_acf5_field($field, $fields_group_id) {
			$post_type = 'acf-field';
			$meta_key = '_fgj2wp_old_k2_extra_field_id';
			
			$field_slug = sanitize_title(FG_Joomla_to_WordPress_Tools::convert_to_latin($field['name']));
			$values = json_decode($field['value'], ARRAY_A);
			$field_type = $this->map_acf_field_type($field['type'], $values);
			
			// Check if the field already exists
			$new_post_id = $this->plugin->get_wp_post_id_from_meta($meta_key, $field['id']);
			if ( !empty($new_post_id) ) {
				$post = get_post($new_post_id);
				$field_key = $post->post_name;
			} else {
				// Insert the post
				$field_key = 'field_' . uniqid();
				$content = array(
					'type' => $field_type,
					'instructions' => '',
					'required' => $values[0]['required'],
					'default_value' => $values[0]['value'],
				);
				// Multiple select
				if ( $field['type'] == 'multipleSelect' ) {
					$content['multiple'] = 1;
				}
				// Allow null
				if ( $values[0]['showNull'] == 1 ) {
					$content['allow_null'] = 1;
				}
				// Choices
				if ( count($values) > 1 ) {
					$choices = array();
					foreach ( $values as $item ) {
						$choices[$item['value']] = $item['name'];
					}
					$content['choices'] = $choices;
				}
				$new_post = array(
					'post_title'		=> $field['name'],
					'post_name'			=> $field_key,
					'post_content'		=> serialize($content),
					'post_excerpt'		=> $field_slug,
					'post_type'			=> $post_type,
					'post_parent'		=> $fields_group_id,
					'menu_order'		=> $field['ordering'] - 1, // K2 starts at 1 and ACF starts at 0
					'post_status'		=> 'publish',
					'comment_status'	=> 'closed',
					'ping_status'		=> 'closed',
				);
				$new_post_id = wp_insert_post($new_post, true);
			}
			if ( !is_wp_error($new_post_id) ) {
				add_post_meta($new_post_id, $meta_key, $field['id'], true);
				$this->imported_fields[$field['id']] = array(
					'slug' => $field_slug,
					'key' => $field_key,
					'type' => $field_type,
				);
			}
		}
		
		/**
		 * Map the K2 field type to the ACF field type
		 * 
		 * @since 2.12.0
		 * 
		 * @param string $k2_field_type K2 field type
		 * @param array $values Field options
		 * @return string ACF field type
		 */
		private function map_acf_field_type($k2_field_type, $values) {
			switch ( $k2_field_type ) {
				case 'textfield':
					$acf_type = 'text';
					break;
				case 'textarea':
					if ( $values[0]['editor'] == 1 ) {
						$acf_type = 'wysiwyg';
					} else {
						$acf_type = 'textarea';
					}
					break;
				case 'select':
				case 'multipleSelect':
					$acf_type = 'select';
					break;
				case 'radio':
					$acf_type = 'radio';
					break;
				case 'link':
					$acf_type = 'url';
					break;
				case 'date':
					$acf_type = 'date_picker';
					break;
				case 'image':
					$acf_type = 'image';
					break;
				case 'header':
					$acf_type = 'message';
					break;
				default:
					$acf_type = 'text';
			}
			return $acf_type;
		}
		
		/**
		 * Import the extra field groups as Toolset Types fields groups
		 * 
		 * @since 2.16.0
		 * 
		 * @param array $extra_fields_groups Extra fields groups
		 */
		private function import_extra_field_groups_as_types($extra_fields_groups) {
			foreach ( $extra_fields_groups as $group ) {
				// Register the Types custom fields group
				$fields_group_id = $this->get_types_custom_fields_group($group);
				
				if ( !empty($fields_group_id) ) {
					// Register the Types custom fields
					$fields = $this->get_extra_fields($group['id']);
					$this->register_types_post_fields($fields, $fields_group_id);
				}
			}
		}
		
		/**
		 * Get a custom fields group and create it if it doesn't exist yet
		 * 
		 * @since 2.16.0
		 * 
		 * @param string $group Custom fields group
		 * @return int Field group post ID
		 */
		private function get_types_custom_fields_group($group) {
			$fields_group_title = $group['name'];
			$fields_group_name = sanitize_title($fields_group_title);
			
			// Test if the fields group doesn't already exist
			$fields_group_posts = get_posts(array(
				'name' => $fields_group_name,
				'post_type' => 'wp-types-group',
				'post_status' => 'publish',
				'posts_per_page' => 1,
			));
			if ( $fields_group_posts ) {
				$fields_group_post_id = $fields_group_posts[0]->ID;
			} else {
				$fields_group_post_id = $this->create_types_custom_fields_group($fields_group_title, $fields_group_name);
			}
			
			$this->create_types_field_group_post_type_relation($fields_group_post_id, 'post');
			
			return $fields_group_post_id;
		}
		
		/**
		 * Create a custom fields group
		 * 
		 * @since 2.16.0
		 * 
		 * @param string $fields_group_title Fields group title
		 * @param string $fields_group_name Fields group name
		 * @return int Field group post ID
		 */
		private function create_types_custom_fields_group($fields_group_title, $fields_group_name) {
			// Create the fields group (in post table)
			$new_post = array(
				'post_content'		=> '',
				'post_status'		=> 'publish',
				'post_title'		=> $fields_group_title,
				'post_name'			=> $fields_group_name,
				'post_type'			=> 'wp-types-group',
			);
			$fields_group_post_id = wp_insert_post($new_post, true);
			if ( $fields_group_post_id ) {
				add_post_meta($fields_group_post_id, '_wpcf_conditional_display', array ('relation' => 'AND', 'custom' => ''), true);
				add_post_meta($fields_group_post_id, '_wp_types_group_templates', 'all', true);
				add_post_meta($fields_group_post_id, '_wp_types_group_admin_styles', '', true);
				add_post_meta($fields_group_post_id, '_wp_types_group_terms', 'all', true);
				add_post_meta($fields_group_post_id, '_wp_types_group_fields', '', true);
				add_post_meta($fields_group_post_id, '_wp_types_group_filters_association', 'any', true);
			}
			return $fields_group_post_id;
		}
		
		/**
		 * Create a relation between the field group and the post type
		 * 
		 * @since 2.16.0
		 * 
		 * @param int $fields_group_post_id Field group post ID
		 * @param string $post_type Post type
		 */
		private function create_types_field_group_post_type_relation($fields_group_post_id, $post_type) {
			if ( !empty($fields_group_post_id) ) {
				$group_post_types_list = get_post_meta($fields_group_post_id, '_wp_types_group_post_types', true);
				$group_post_types = empty($group_post_types_list)? array() : explode(',', $group_post_types_list);
				if ( !in_array($post_type, $group_post_types) ) {
					$group_post_types[] = $post_type;
					$group_post_types_list = implode(',', $group_post_types);
					update_post_meta($fields_group_post_id, '_wp_types_group_post_types', $group_post_types_list);
				}
			}
		}
		
		/**
		 * Register the custom fields for a post type
		 *
		 * @since 2.16.0
		 * 
		 * @param array $custom_fields Custom fields
		 * @param int $fields_group_id Fields group ID
		 * @return int Number of fields imported
		 */
		private function register_types_post_fields($custom_fields, $fields_group_id) {
			$fields_count = 0;
			$wpcf_fields = get_option('wpcf-fields', array());
			if ( !is_array($wpcf_fields) ) {
				$wpcf_fields = array();
			}

			// Create the fields (in option table)
			foreach ( $custom_fields as $field ) {
				$wpcf_field = $this->create_wpcf_field($field, 'postmeta');
				$field_slugs = array_keys($wpcf_field);
				$wpcf_fields = array_merge($wpcf_fields, $wpcf_field);
				$fields[] = $field_slugs[0];
				$fields_count++;
			}
			update_option('wpcf-fields', $wpcf_fields);
			
			// Assign the field to the fields group (in postmeta table)
			update_post_meta($fields_group_id, '_wp_types_group_fields', implode(',', $fields));
			
			return $fields_count;
		}
		
		/**
		 * Create a field object using the WPCF structure
		 * 
		 * @since 2.16.0
		 * 
		 * @param array $field Field data
		 * @param string $meta_type Meta type (postmeta | termmeta | usermeta)
		 * @return array WPCF field
		 */
		private function create_wpcf_field($field, $meta_type) {
			$name = $field['name'];
			$params = json_decode($field['value'], ARRAY_A);
			$field_slug = sanitize_title((isset($params[0]['alias']) && !empty($params[0]['alias']))? $params[0]['alias'] : $name);
			// Map the custom field types
			$type = $this->map_types_field_type($field['type'], $params[0]);
			$default_value = isset($params[0]['value'])? $params[0]['value']: '';
			$default_value = (string)$default_value;

			// Create the field
			$wpcf_field = array(
				$field_slug => array(
					'id' => $field_slug,
					'slug' => $field_slug,
					'type' => $type,
					'name' => $name,
					'description' => '',
					'data' => array(
						'slug-pre-save' => $field_slug,
 						'user_default_value' => $default_value,
						'repetitive' => 0,
						'conditional_display' => array(
							'relation' => 'AND',
							'custom' => '',
						),
	                    'submit-key' => $field_slug,
						'disabled_by_type' => 0,
					),
					'meta_key' => 'wpcf-' . $field_slug,
					'meta_type' => $meta_type,
				),
			);

			// Options for checkboxes and select box
			if ( in_array($type, array('checkboxes', 'radio', 'select')) ) {
				$default_id = '';
				foreach ( $params as $option ) {
					$option_name = (string)$option['name'];
					$option_value = (string)$option['value'];
					$wpcf_option_name = 'wpcf-fields-' . $type . '-option-' . md5($option_name) . '-1';
					$wpcf_option_value = array(
						'title'		=> $option_name,
					);
					if ( $type == 'checkboxes' ) {
						// Checkboxes
						$wpcf_option_value['set_value'] = $option_value;
						$wpcf_option_value['display'] = 'db';
					} else {
						// Select box or radio box
						$wpcf_option_value['value'] = $option_value;
						$wpcf_option_value['display_value'] = $option_value;
					}
					$wpcf_field[$field_slug]['data']['options'][$wpcf_option_name] = $wpcf_option_value;
					$this->imported_options[$field['id']][$option_value] = $wpcf_option_name;
				}
				unset($wpcf_field[$field_slug]['data']['user_default_value']);
				unset($wpcf_field[$field_slug]['data']['repetitive']);
				if ( $type == 'checkboxes' ) {
					$wpcf_field[$field_slug]['data']['save_empty'] = 'no';
				} else {
					$wpcf_field[$field_slug]['data']['display'] = 'db';
				}
				// Default value
				if ( !empty($default_id) ) {
					$wpcf_field[$field_slug]['data']['options']['default'] = $default_id;
				}
			}

			// Required field
			if ( isset($params[0]['required']) && !empty($params[0]['required']) ) {
				$wpcf_field[$field_slug]['data']['validate']['required'] = array(
					'active' => 1,
					'value' => 'true',
					'message' => __('This field is required.', 'fgj2wpp'),
				);
			}
			
			$wpcf_field = apply_filters('fgj2wp_pre_register_wpcf_field', $wpcf_field);
			$this->imported_fields[$field['id']] = array(
				'slug' => $field_slug,
				'type' => $type,
			);
			return $wpcf_field;
		}
		
		/**
		 * Map a custom field type to Types
		 * 
		 * @since 2.16.0
		 * 
		 * @param string $type K2 field type
		 * @param array $params
		 * @return string Mapped WordPress field type
		 */
		private function map_types_field_type($type, $params) {
			switch ( $type ) {
				case 'textfield':
					$type = 'textfield';
					break;
				case 'textarea':
					if ( isset($params['editor']) && !empty($params['editor']) ) {
						$type = 'wysiwyg';
					} else {
						$type = 'textarea';
					}
					break;
				case 'select':
					$type = 'select';
					break;
				case 'multipleSelect':
					$type = 'checkboxes';
					break;
				case 'radio':
					$type = 'radio';
					break;
				case 'link':
					$type = 'url';
					break;
				case 'date':
					$type = 'date';
					break;
				case 'image':
					$type = 'image';
					break;
				case 'header':
					$type = '';
					break;
				default:
					$type = 'textfield';
			}
			return $type;
		}
		
		/**
		 * Get the element_type for WPML
		 * 
		 * @since 2.15.0
		 * 
		 * @param string $element_type Element type
		 * @param array $post Post
		 */
		public function get_k2_element_type($element_type, $post) {
			if ( isset($post['type']) && $post['type'] == 'k2_items' ) {
				$element_type = ($this->k2_options['k2_items'] == 'as_page') ? 'post_page' : 'post_post';
			}
			return $element_type;
		}
	}
}
