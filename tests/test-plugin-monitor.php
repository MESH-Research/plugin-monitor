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
	 * Ensure alert fires when plugins are (de)activated.
	 */
	public function test_alert() {
		set_error_handler( function( $errno, $errstr ) {
			$this->assertContains( 'activated_plugin', $errstr );
		} );

		activate_plugin( 'hello.php' );

		set_error_handler( function( $errno, $errstr ) {
			$this->assertContains( 'deactivated_plugin', $errstr );
		} );

		deactivate_plugins( 'hello.php' );
	}
}
