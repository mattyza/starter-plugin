<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Starter Plugin Post Type Meta Fields Class
 *
 * Handles registration of post meta fields for REST API / block editor access,
 * and enqueuing of the block editor sidebar panel assets.
 *
 * @package WordPress
 * @subpackage Starter_Plugin
 * @category Plugin
 * @author Matty
 * @since 1.0.0
 */
class Starter_Plugin_Post_Type_Meta_Fields {

	/**
	 * The post type this instance manages meta fields for.
	 * @access protected
	 * @since  1.0.0
	 * @var    string
	 */
	protected $post_type;

	/**
	 * Callable that returns the field definitions array.
	 * Signature: callable(): array
	 * @access protected
	 * @since  1.0.0
	 * @var    callable
	 */
	protected $fields_callback;

	/**
	 * Callable that returns the section definitions array.
	 * Signature: callable(): array
	 * @access protected
	 * @since  1.0.0
	 * @var    callable
	 */
	protected $sections_callback;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since  1.0.0
	 * @param string   $post_type         The post type slug.
	 * @param callable $fields_callback   Returns the field definitions (same shape as get_custom_fields_settings()).
	 * @param callable $sections_callback Returns the section definitions (same shape as get_field_sections()).
	 */
	public function __construct( $post_type, callable $fields_callback, callable $sections_callback ) {
		$this->post_type         = $post_type;
		$this->fields_callback   = $fields_callback;
		$this->sections_callback = $sections_callback;

		add_action( 'init', array( $this, 'register' ) );

		if ( is_admin() ) {
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		}
	}

	/**
	 * Register post meta fields for REST API access and block editor support.
	 *
	 * Calls register_post_meta() for each field so that values are readable
	 * and writable through the REST API and therefore by the block editor sidebar.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function register() {
		$fields = call_user_func( $this->fields_callback );

		foreach ( $fields as $key => $field ) {
			$type = isset( $field['type'] ) ? $field['type'] : 'text';

			register_post_meta(
				$this->post_type,
				'_' . $key,
				array(
					'show_in_rest'      => true,
					'single'            => true,
					'type'              => 'string',
					'default'           => isset( $field['default'] ) ? $field['default'] : '',
					'sanitize_callback' => ( 'url' === $type ) ? 'esc_url_raw' : 'sanitize_text_field',
					'auth_callback'     => function ( $allowed, $meta_key, $object_id, $user_id, $cap, $caps ) {
						return current_user_can( 'edit_post', $object_id );
					},
				)
			);
		}
	}

	/**
	 * Enqueue block editor assets for the meta fields sidebar panels.
	 *
	 * Runs only on the block editor screen for this post type. Enqueues
	 * meta-fields.js and passes field + section definitions as localised data.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_block_editor_assets() {
		$screen = get_current_screen();
		if ( ! $screen || $screen->post_type !== $this->post_type ) {
			return;
		}

		$field_data  = call_user_func( $this->fields_callback );
		$sections    = call_user_func( $this->sections_callback );
		$fields_json = array();

		foreach ( $field_data as $key => $field ) {
			$fields_json[] = array(
				'key'         => $key,
				'name'        => isset( $field['name'] ) ? $field['name'] : $key,
				'description' => isset( $field['description'] ) ? $field['description'] : '',
				'type'        => isset( $field['type'] ) ? $field['type'] : 'text',
				'default'     => isset( $field['default'] ) ? $field['default'] : '',
				'section'     => isset( $field['section'] ) ? $field['section'] : 'default',
			);
		}

		$handle = 'starter-plugin-' . $this->post_type . '-meta-fields';

		wp_enqueue_script(
			$handle,
			plugins_url( '../assets/js/meta-fields.js', __FILE__ ),
			array( 'wp-plugins', 'wp-editor', 'wp-element', 'wp-components', 'wp-data' ),
			Starter_Plugin()->version,
			true
		);

		wp_localize_script(
			$handle,
			'starterPluginMetaFields',
			array(
				'fields'   => $fields_json,
				'sections' => $sections,
				'postType' => $this->post_type,
			)
		);
	}
}
