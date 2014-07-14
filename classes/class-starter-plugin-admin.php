<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Starter_Plugin_Admin Class
 *
 * @class Starter_Plugin_Admin
 * @version	1.0.0
 * @since 1.0.0
 * @package	Starter_Plugin
 * @author Jeffikus
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
		$sections = Starter_Plugin()->settings->get_settings_sections( 'all' );
		if ( isset ( $_GET['tab'] ) ) {
			$tab = $_GET['tab'];
		} else {
			list( $first_section ) = array_keys( $sections );
			$tab = $first_section;
		} // End If Statement
   		?>
		<div class="wrap starter-plugin-wrap">
			<h2 class="starter-plugin-title"><?php echo $title; ?></h2>
			<h2 class="nav-tab-wrapper">
				<?php
				foreach ( $sections as $key => $value ) {
					$class = '';

					if ( $tab == $key ) {
						$class = ' nav-tab-active';
					} // End If Statement

					echo '<a href="' . admin_url( 'options-general.php?page=starter-plugin&tab=' . $key ) . '" class="nav-tab' . $class . '">' . $value . '</a>';
				} // End For Loop
				?>
			</h2>
			<form action="options.php" method="post">
				<?php
					settings_fields( 'starter-plugin-settings-' . $tab );
					do_settings_sections( 'starter-plugin-' . $tab );
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

		// Contact Details Settings
		register_setting( 'starter-plugin-settings-general-fields', 'starter-plugin-general-fields', array( $this, 'validate_contact_settings' ) );

		// Register settings sections.
		$sections = Starter_Plugin()->settings->get_settings_sections( 'general-fields' );

		if ( 0 < count( $sections ) ) {
			foreach ( $sections as $k => $v ) {
				add_settings_section( $k, $v, array( $this, 'render_contact_settings' ), 'starter-plugin-general-fields' );
			} // End For Loop
		} // End If Statement

		// Map Details Settings
		register_setting( 'starter-plugin-settings-example-fields', 'starter-plugin-example-fields', array( $this, 'validate_map_settings' ) );

		// Register settings sections.
		$sections = Starter_Plugin()->settings->get_settings_sections( 'example-fields' );

		if ( 0 < count( $sections ) ) {
			foreach ( $sections as $k => $v ) {
				add_settings_section( $k, $v, array( $this, 'render_map_settings' ), 'starter-plugin-example-fields' );
			} // End For Loop
		} // End If Statement

	} // End register_settings()

	/**
	 * Render the settings.
	 * @access  public
	 * @param  array $args arguments.
	 * @since   1.0.0
	 * @return  void
	 */
	public function render_contact_settings ( $args ) {
		$fields = Starter_Plugin()->settings->get_settings_fields( 'general-fields' );

		if ( 0 < count( $fields ) ) {
			foreach ( $fields as $k => $v ) {
				$args 		= $v;
				$args['id'] = $k;

				add_settings_field( $k, $v['name'], array( Starter_Plugin()->settings, 'render_contact_field' ), 'starter-plugin-general-fields', $v['section'], $args );
			} // End For Loop
		} // End If Statement
	} // End render_contact_settings()

	/**
	 * Render the settings.
	 * @access  public
	 * @param  array $args arguments.
	 * @since   1.0.0
	 * @return  void
	 */
	public function render_map_settings ( $args ) {
		$fields = Starter_Plugin()->settings->get_settings_fields( 'example-fields' );

		if ( 0 < count( $fields ) ) {
			foreach ( $fields as $k => $v ) {
				$args = $v;
				$args['id'] = $k;
				add_settings_field( $k, $v['name'], array( Starter_Plugin()->settings, 'render_map_field' ), 'starter-plugin-example-fields', $v['section'], $args );
			} // End For Loop
		} // End If Statement
	} // End render_settings()

	/**
	 * Validate the settings.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $input Inputted data.
	 * @return  array        Validated data.
	 */
	public function validate_contact_settings ( $input ) {
		return Starter_Plugin()->settings->validate_settings( $input, 'contact-fields' );
	} // End validate_contact_settings()

	/**
	 * Validate the settings.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $input Inputted data.
	 * @return  array        Validated data.
	 */
	public function validate_map_settings ( $input ) {
		return Starter_Plugin()->settings->validate_settings( $input, 'example-fields' );
	} // End validate_map_settings()
} // End Class