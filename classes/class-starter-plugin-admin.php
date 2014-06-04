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
	public function __construct () {
		// Register the settings with WordPress.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		// Register the settings screen within WordPress.
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
			<form action="options.php" method="post">
				<?php
					settings_fields( 'starter-plugin-settings' );
					do_settings_sections( 'starter-plugin' );
					submit_button( __( 'Save Changes', 'starter-plugin' ) );
				?>
			</form>
		</div><!--/.wrap-->
<?php
	} // End settings_screen()

	/**
	 * Register the settings within the Settings API.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_settings () {
		// Register the setting we'll use to store our information.
		register_setting( 'starter-plugin-settings', 'starter-plugin', array( $this, 'validate_settings' ) );

		// Register settings sections.
		$sections = Starter_Plugin()->settings->get_settings_sections();

		if ( 0 < count( $sections ) ) {
			foreach ( $sections as $k => $v ) {
				add_settings_section( $k, $v, array( $this, 'render_settings' ), 'starter-plugin' );
			}
		}
	} // End register_settings()

	/**
	 * Render the settings.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function render_settings ( $args ) {
		$fields = Starter_Plugin()->settings->get_settings_fields();

		if ( 0 < count( $fields ) ) {
			foreach ( $fields as $k => $v ) {
				$args = $v;
				$args['id'] = $k;
				add_settings_field( $k, $v['name'], array( Starter_Plugin()->settings, 'render_field' ), 'starter-plugin', $v['section'], $args );
			}
		}
	} // End render_settings()

	/**
	 * Validate the settings.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $input Inputted data.
	 * @return  array        Validated data.
	 */
	public function validate_settings ( $input ) {
		return Starter_Plugin()->settings->validate_settings( $input );
	} // End validate_settings()
} // End Class
?>