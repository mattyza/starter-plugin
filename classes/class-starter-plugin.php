<?php
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
	private static $instance = null;

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

	/**
	 * The taxonomies we're registering.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $taxonomies = array();
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
		require_once 'class-starter-plugin-settings.php';
			$this->settings = Starter_Plugin_Settings::instance();

		if ( is_admin() ) {
			require_once 'class-starter-plugin-admin.php';
			$this->admin = Starter_Plugin_Admin::instance();
		}
		// Admin - End

		// Post Types - Start
		require_once 'class-starter-plugin-post-type.php';
		require_once 'class-starter-plugin-taxonomy.php';

		// Register an example post type. To register other post types, duplicate this line.
		$this->post_types['thing'] = new Starter_Plugin_Post_Type( 'thing', __( 'Thing', 'starter-plugin' ), __( 'Things', 'starter-plugin' ), array( 'menu_icon' => 'dashicons-carrot' ) );

		// Register an example taxonomy, connected to our post type. To register other taxonomies, duplicate this line.
		$this->taxonomies['thing-category'] = new Starter_Plugin_Taxonomy();
		// Post Types - End
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
	}

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
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'starter-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone () {}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup () {}

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 */
	public function install () {
		$this->log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 */
	private function log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}
}
