<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Starter_Plugin_Admin Class
 *
 * @class Starter_Plugin_Admin
 * @version	1.0.0
 * @since 1.0.0
 * @package	Starter_Plugin
 * @author Matty
 */
final class Starter_Plugin_Admin {
	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		add_action( 'admin_menu', array( $this, 'register_settings_screen' ) );
	} // End __construct()

	/**
	 * Register the admin screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_settings_screen () {
		$this->_hook = add_submenu_page( 'options-general.php', __( 'Starter Plugin Settings', 'starter-plugin' ), __( 'Starter Plugin', 'starter-plugin' ), 'manage_options', 'starter-plugin', array( $this, 'settings_screen' ) );
	} // End register_settings_screen()

	/**
	 * Output the markup for the settings screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function settings_screen () {
		global $title;
?>
		<div class="wrap starter-plugin-wrap">
			<h2><?php echo $title; ?></h2>
		</div><!--/.wrap-->
<?php
	} // End settings_screen()
} // End Class
?>