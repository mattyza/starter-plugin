<?php
/**
 * Plugin Name: Starter Plugin
 * Plugin URI: http://domain.com/starter-plugin/
 * Description: Hey there! I'm your new starter plugin.
 * Version: 1.0.0
 * Author: Matty
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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'classes/class-starter-plugin-init.php';

