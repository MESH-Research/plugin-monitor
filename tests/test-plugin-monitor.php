<?php
/**
 * Class PluginMonitorTest
 *
 * @package Plugin_Monitor
 */

/**
 * PluginMonitor test case.
 */
class PluginMonitorTest extends WP_UnitTestCase {

	/**
	 * Ensure alert fires when plugins are activated.
	 */
	public function test_activate_alert() {
		$activate_test = function( $args ) {
			$this->assertContains( 'activated_plugin', $args['message'] );
			$this->assertNotContains( 'deactivated_plugin', $args['message'] );
		};

		add_filter( 'wp_mail', $activate_test );
		activate_plugin( 'hello.php' );

		remove_filter( 'wp_mail', $activate_test );
	}

	/**
	 * Ensure alert fires when plugins are deactivated.
	 */
	public function test_deactivate_alert() {
		$deactivate_test = function( $args ) {
			$this->assertContains( 'deactivated_plugin', $args['message'] );
		};

		add_filter( 'wp_mail', $deactivate_test );
		deactivate_plugins( 'hello.php' );
	}
}
