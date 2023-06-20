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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Returns the main instance of Starter_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Starter_Plugin
 */
function Starter_Plugin() {
	return Starter_Plugin::instance();
} // End Starter_Plugin()

add_action( 'plugins_loaded', 'Starter_Plugin' );

/**
 * Main Starter_Plugin Class
 *
 * @class Starter_Plugin
 * @version	1.0.0
 * @since 1.0.0
 * @package	Starter_Plugin
 * @author Matty
 */
final class Starter_Plugin {
	/**
	 * Starter_Plugin The single instance of Starter_Plugin.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_path;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * The settings object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings;
	// Admin - End

	// Post Types - Start
	/**
	 * The post types we're registering.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $post_types = array();
	// Post Types - End
	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct () {
		$this->token       = 'starter-plugin';
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->version     = '1.0.0';

		// Admin - Start
		require_once  'classes/class-starter-plugin-settings.php' ;
			$this->settings = Starter_Plugin_Settings::instance();

		if ( is_admin() ) {
			require_once  'classes/class-starter-plugin-admin.php' ;
			$this->admin = Starter_Plugin_Admin::instance();
		}
		// Admin - End

		// Post Types - Start
		require_once  'classes/class-starter-plugin-post-type.php' ;
		require_once  'classes/class-starter-plugin-taxonomy.php' ;

		// Register an example post type. To register other post types, duplicate this line.
		$this->post_types['thing'] = new Starter_Plugin_Post_Type( 'thing', __( 'Thing', 'starter-plugin' ), __( 'Things', 'starter-plugin' ), array( 'menu_icon' => 'dashicons-carrot' ) );
		// Post Types - End
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
	} // End __construct()

	/**
	 * Main Starter_Plugin Instance
	 *
	 * Ensures only one instance of Starter_Plugin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Starter_Plugin()
	 * @return Main Starter_Plugin instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'starter-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 */
	public function install () {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()
} // End Class
