<?php
/**
 * FG Joomla to WordPress K2 Module
 * URL Rewriting module
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_filter('fgj2wpp_do_redirect', array('fgj2wp_k2_urlrewriting', 'do_redirect'));
add_filter('fgj2wpp_rewrite_rules', array('fgj2wp_k2_urlrewriting', 'add_rules'));
add_action('fgj2wpp_pre_404_redirect', array('fgj2wp_k2_urlrewriting', 'pre_404_redirect'));

if ( !class_exists('fgj2wp_k2_urlrewriting', false) ) {
	class fgj2wp_k2_urlrewriting {

		private static $rewrite_rules = array(
			// Joomla configured with SEF URLs: /item/id-
			array('rule' => '^.*item/(\d+)-',				'view' => 'post',	'meta_key' => '_fgj2wp_old_k2_id'),
			array('rule' => '^.*item/(\d+)$',				'view' => 'post',	'meta_key' => '_fgj2wp_old_k2_id'),
			array('rule' => '^.*/itemlist/category/(\d+)-',	'view' => 'term',	'meta_key' => '_fgj2wp_old_category_id', 'callback' => array(__CLASS__, 'convert_category_id_callback')),
		);
		private static $redirect_k2_sef_url_rewrite_rules = array(
			// K2 configured with advanced SEF URLs: /id-
			array( 'rule' => '^.*/(\d+)-',					'view' => 'post',	'meta_key' => '_fgj2wp_old_k2_id'),
		);
		
		/**
		 * Redirect or not?
		 *
		 * @param bool $do_redirect
		 * @return bool $do_redirect
		 */
		public static function do_redirect($do_redirect) {
			if ( !$do_redirect ) {
				$k2_options = get_option('fgj2wpk2_options');
				if ( (isset($k2_options['redirect_k2_url']) && !empty($k2_options['redirect_k2_url'])) ||
					 (isset($k2_options['redirect_k2_sef_url']) && !empty($k2_options['redirect_k2_sef_url'])) ) {
					$do_redirect = true;
				}
			}
			return $do_redirect;
		}
		
		/**
		 * Add rewrite rules
		 *
		 * @param array $rewrite_rules Custom rewrite rules
		 * @return array Custom rewrite rules
		 */
		public static function add_rules($rewrite_rules) {
			$k2_options = get_option('fgj2wpk2_options');
			if ( isset($k2_options['redirect_k2_url']) && !empty($k2_options['redirect_k2_url']) ) {
				$rewrite_rules = array_merge(self::$rewrite_rules, $rewrite_rules);
			}
			if ( isset($k2_options['redirect_k2_sef_url']) && !empty($k2_options['redirect_k2_sef_url']) ) {
				$rewrite_rules = array_merge(self::$redirect_k2_sef_url_rewrite_rules, $rewrite_rules);
			}
			return $rewrite_rules;
		}
		
		/**
		 * Try to redirect the Joomla non SEF URLs
		 */
		public static function pre_404_redirect() {
			$matches = array();
			$k2_options = get_option('fgj2wpk2_options');
			if ( isset($k2_options['redirect_k2_url']) && !empty($k2_options['redirect_k2_url']) ) {
				// Joomla configured without SEF URLs: view=item&id=xxx
				$view = get_query_var('view');
				if ( $view == 'item' ) {
					if ( preg_match('/(\d+)/', get_query_var('id'), $matches) ) {
						$old_id = $matches[1];
						FG_Joomla_to_WordPress_URL_Rewriting::redirect('_fgj2wp_old_k2_id', $old_id);
					}
				}
			}
		}
		
		/**
		 * Add a prefix to the category ID
		 * Used to get the K2 category from the termmeta table
		 * 
		 * @param int $category_id Category ID
		 * @return string Category meta value
		 */
		public static function convert_category_id_callback($category_id) {
			return 'k' . $category_id;
		}
		
	}
}
