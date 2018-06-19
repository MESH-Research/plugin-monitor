<?php
/**
 * Plugin Name:     Plugin Monitor
 * Plugin URI:      https://github.com/mlaa/plugin-monitor.git
 * Description:     Plugin Monitor
 * Author:          MLA
 * Author URI:      https://github.com/mlaa
 * Text Domain:     plugin-monitor
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Plugin_Monitor
 */

namespace MLA\PluginMonitor;

/**
 * Log & email site admin when plugins are (de)activated.
 */
function alert( $plugin, $network_wide ) {
	$user = wp_get_current_user();

	$message = sprintf(
		"%s %s '%s' at the %s level",
		( ! empty( $user->user_login ) ) ? $user->user_login : 'unknown user',
		current_action(),
		print_r( $plugin, true ),
		( $network_wide ) ? 'network' : 'site'
	);
	trigger_error( $message );
	wp_mail( get_option( 'admin_email' ), 'PLUGIN MONITOR ALERT', $message );
}

add_action( 'activated_plugin', __NAMESPACE__ . '\alert', 10, 2 );
add_action( 'deactivated_plugin', __NAMESPACE__ . '\alert', 10, 2 );
