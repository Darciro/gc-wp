<?php
/**
 * Plugin Name: Seriously Simple Podcasting
 * Version: 1.19.20
 * Plugin URI: https://www.castos.com/seriously-simple-podcasting
 * Description: Podcasting the way it's meant to be. No mess, no fuss - just you and your content taking over the world.
 * Author: Castos
 * Author URI: https://www.castos.com/
 * Requires PHP: 5.3.3
 * Requires at least: 4.4
 * Tested up to: 5.1.1
 *
 * Text Domain: seriously-simple-podcasting
 *
 * @package Seriously Simple Podcasting
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( version_compare( PHP_VERSION, '5.3.3', '<' ) ) { // PHP 5.3.3 or greater
	/**
	 * We are running under PHP 5.3.3
	 * Display an admin notice and gracefully do nothing.
	 */
	if ( is_admin() ) {
		add_action( 'admin_notices', 'ssp_php_version_notice' );
		function ssp_php_version_notice() {
			?>
			<div class="error">
				<p>
					<strong>The Seriously Simple Podcasting plugin requires PHP version 5.3.3 or later. Please contact your web host to upgrade your PHP version or deactivate the plugin.</strong>.
				</p>
				<p>We apologise for any inconvenience.</p>
			</div>
			<?php

		}
	}

	return;
}

define( 'SSP_VERSION', '1.19.20' );
define( 'SSP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SSP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'SSP_PODMOTOR_APP_URL' ) ) {
	define( 'SSP_PODMOTOR_APP_URL', 'https://app.castos.com/' );
}
if ( ! defined( 'SSP_PODMOTOR_EPISODES_URL' ) ) {
	define( 'SSP_PODMOTOR_EPISODES_URL', 'https://episodes.castos.com/' );
}

define( 'SSP_LOG_DIR_PATH', SSP_PLUGIN_PATH . 'log' . DIRECTORY_SEPARATOR );
define( 'SSP_LOG_DIR_URL', SSP_PLUGIN_URL . 'log' . DIRECTORY_SEPARATOR );
define( 'SSP_LOG_PATH', SSP_LOG_DIR_PATH . 'ssp.log.' . date( 'd-m-y' ) . '.txt' );
define( 'SSP_LOG_URL', SSP_LOG_DIR_URL . 'ssp.log.' . date( 'd-m-y' ) . '.txt' );

require_once 'includes/ssp-functions.php';
require_once 'includes/class-ssp-admin.php';
require_once 'includes/class-ssp-frontend.php';

require_once 'includes/class-podmotor-handler.php';
require_once 'includes/class-ssp-external-rss-importer.php';
/**
 * Only require the REST API endpoints if the user is using WordPress greater than 4.7
 */
global $wp_version;
if ( version_compare( $wp_version, '4.7', '>=' ) ) {
	require_once 'includes/class-ssp-wp-rest-api.php';
	require_once 'includes/class-ssp-wp-rest-episodes-controller.php';
}

global $ssp_admin, $ss_podcasting, $ssp_wp_rest_api;
$ssp_admin       = new SSP_Admin( __FILE__, SSP_VERSION );
$ss_podcasting   = new SSP_Frontend( __FILE__, SSP_VERSION );
$ssp_wp_rest_api = new SSP_WP_REST_API( SSP_VERSION );

if ( is_admin() ) {
	global $ssp_settings;
	require_once 'includes/class-ssp-settings.php';
	$ssp_settings = new SSP_Settings( __FILE__, SSP_VERSION );
}
