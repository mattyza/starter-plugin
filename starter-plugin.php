<?php
/**
 * Plugin Name: Starter Plugin
 * Plugin URI: http://domain.com/starter-plugin/
 * Description: Hey there! I'm your new starter plugin.
 * Version: 1.0.0
 * Author: Matty Cohen
 * Author URI: http://domain.com/
 * Requires at least: 4.0.0
 * Tested up to: 4.0.0
 *
 * Text Domain: starter-plugin
 * Domain Path: /languages/
 *
 * @package Starter_Plugin
 * @category Core
 * @author Matty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once 'classes/class-starter-plugin.php';

/**
 * Returns the main instance of Starter_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Starter_Plugin
 */
function starter_plugin() {
	return Starter_Plugin::instance();
}
add_action( 'plugins_loaded', 'starter_plugin' );
