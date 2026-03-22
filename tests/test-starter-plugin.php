<?php
/**
 * Class Test_Starter_Plugin
 *
 * @package Starter_Plugin
 */

/**
 * Sample test case.
 */
class Test_Starter_Plugin extends WP_UnitTestCase {
	public function set_up() {
        parent::set_up();
        
        // Mock that we're in WP Admin context.
		// See https://wordpress.stackexchange.com/questions/207358/unit-testing-in-the-wordpress-backend-is-admin-is-true
        set_current_screen( 'edit-post' );
        
        $this->starter_plugin = new Starter_Plugin();
    }

    public function tear_down() {
        parent::tear_down();
    }

	public function test_has_correct_token() {
		$has_correct_token = ( 'starter-plugin' === $this->starter_plugin->token );
		
		$this->assertTrue( $has_correct_token );
	}

	public function test_has_admin_interface() {
		$has_admin_interface = ( is_a( $this->starter_plugin->admin, 'Starter_Plugin_Admin' ) );
		
		$this->assertTrue( $has_admin_interface );
	}

	public function test_has_settings_interface() {
		$has_settings_interface = ( is_a( $this->starter_plugin->settings, 'Starter_Plugin_Settings' ) );
		
		$this->assertTrue( $has_settings_interface );
	}

	public function test_has_post_types() {
		$has_post_types = ( 0 < count( $this->starter_plugin->post_types ) );
		
		$this->assertTrue( $has_post_types );
	}

	public function test_has_load_plugin_textdomain() {
		$has_load_plugin_textdomain = ( is_int( has_action( 'init', [ $this->starter_plugin, 'load_plugin_textdomain' ] ) ) );
		
		$this->assertTrue( $has_load_plugin_textdomain );
	}

	/**
	 * register_post_meta_fields() should register each field via register_post_meta()
	 * with show_in_rest enabled so the block editor can read and write the values.
	 */
	public function test_post_meta_fields_registered_in_rest() {
		$post_type_obj = $this->starter_plugin->post_types['thing'];
		$post_type_obj->register_post_meta_fields();

		$registered = get_registered_meta_keys( 'post', 'thing' );

		$this->assertArrayHasKey( '_url', $registered );
		$this->assertTrue( $registered['_url']['show_in_rest'] );
	}

	/**
	 * get_field_sections() should return an array that includes the 'info' section.
	 */
	public function test_get_field_sections_has_info_section() {
		$post_type_obj = $this->starter_plugin->post_types['thing'];
		$sections      = $post_type_obj->get_field_sections();

		$this->assertIsArray( $sections );
		$this->assertArrayHasKey( 'info', $sections );
	}

	/**
	 * The 'starter_plugin_field_sections' filter should allow external code
	 * to add or modify sections.
	 */
	public function test_get_field_sections_is_filterable() {
		add_filter(
			'starter_plugin_field_sections',
			function( $sections ) {
				$sections['extra'] = 'Extra';
				return $sections;
			}
		);

		$post_type_obj = $this->starter_plugin->post_types['thing'];
		$sections      = $post_type_obj->get_field_sections();

		$this->assertArrayHasKey( 'extra', $sections );

		// Clean up.
		remove_all_filters( 'starter_plugin_field_sections' );
	}

	/**
	 * The init action should include a callback for register_post_meta_fields()
	 * so that meta registration runs at the correct hook.
	 */
	public function test_register_post_meta_fields_hooked_on_init() {
		$post_type_obj = $this->starter_plugin->post_types['thing'];
		$priority      = has_action( 'init', array( $post_type_obj, 'register_post_meta_fields' ) );

		$this->assertIsInt( $priority );
	}
}

