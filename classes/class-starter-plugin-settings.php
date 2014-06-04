<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Starter_Plugin_Settings Class
 *
 * @class Starter_Plugin_Settings
 * @version	1.0.0
 * @since 1.0.0
 * @package	Starter_Plugin
 * @author Matty
 */
final class Starter_Plugin_Settings {
	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
	} // End __construct()

	/**
	 * Validate the settings.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $input Inputted data.
	 * @return  array        Validated data.
	 */
	public function validate_settings ( $input ) {
		return $input;
	} // End validate_settings()

	/**
	 * Render a field of a given type.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $args The field parameters.
	 * @return  void
	 */
	public function render_field ( $args ) {
		// TODO
	} // End render_field()

	/**
	 * Retrieve the settings fields details
	 * @access  public
	 * @since   1.0.0
	 * @return  array        Settings fields.
	 */
	public function get_settings_sections () {
		$settings_sections = array();
		// Declare the default settings fields.
		$settings_sections['example-fields'] = __( 'Example Fields', 'starter-plugin' );

		return (array)apply_filters( 'starter-plugin-settings-sections', $settings_sections );
	} // End get_settings_sections()

	/**
	 * Retrieve the settings fields details
	 * @access  public
	 * @since   1.0.0
	 * @return  array        Settings fields.
	 */
	public function get_settings_fields () {
		$settings_fields = array();
		// Declare the default settings fields.
		$settings_fields['text'] = array(
										'name' => __( 'Example Text Input', 'starter-plugin' ),
										'type' => 'text',
										'default' => '',
										'section' => 'example-fields',
										'description' => __( 'Place the field description text here.', 'starter-plugin' )
									);
		$settings_fields['textarea'] = array(
										'name' => __( 'Example Textarea', 'starter-plugin' ),
										'type' => 'textarea',
										'default' => '',
										'section' => 'example-fields',
										'description' => __( 'Place the field description text here.', 'starter-plugin' )
									);
		$settings_fields['checkbox'] = array(
										'name' => __( 'Example Checkbox', 'starter-plugin' ),
										'type' => 'checkbox',
										'default' => '',
										'section' => 'example-fields',
										'description' => __( 'Place the field description text here.', 'starter-plugin' )
									);
		$settings_fields['radio'] = array(
										'name' => __( 'Example Radio Buttons', 'starter-plugin' ),
										'type' => 'radio',
										'default' => '',
										'section' => 'example-fields',
										'options' => array(
															'one' => __( 'One', 'starter-plugin' ),
															'two' => __( 'Two', 'starter-plugin' ),
															'three' => __( 'Three', 'starter-plugin' )
													),
										'description' => __( 'Place the field description text here.', 'starter-plugin' )
									);
		$settings_fields['select'] = array(
											'name' => __( 'Example Select', 'starter-plugin' ),
											'type' => 'select',
											'default' => '',
											'section' => 'example-fields',
											'options' => array(
															'one' => __( 'One', 'starter-plugin' ),
															'two' => __( 'Two', 'starter-plugin' ),
															'three' => __( 'Three', 'starter-plugin' )
														),
											'description' => __( 'Place the field description text here.', 'starter-plugin' )
									);

		return (array)apply_filters( 'starter-plugin-settings-fields', $settings_fields );
	} // End get_settings_fields()
} // End Class
?>