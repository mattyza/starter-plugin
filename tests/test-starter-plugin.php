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
}
