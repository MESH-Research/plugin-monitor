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
 *
 * @param string $plugin       Path to the main plugin file from plugins directory.
 * @param bool   $network_wide Whether to enable the plugin for all sites in the network
 *                             or just the current site. Multisite only. Default is false.
 */
function alert( $plugin, $network_wide ) {
	$user = wp_get_current_user();

	$alert_emails = apply_filters( 'plugin_monitor_alert_emails', [ get_option( 'admin_email' ) ] );

	$message = sprintf(
		"%s %s '%s' at the %s level",
		( ! empty( $user->user_login ) ) ? $user->user_login : 'unknown user',
		current_action(),
		print_r( $plugin, true ),
		( $network_wide ) ? 'network' : 'site'
	);

	error_log( $message );

	foreach ( $alert_emails as $email ) {
		wp_mail( $email, 'PLUGIN MONITOR ALERT', $message );
	}
}

add_action( 'activated_plugin', __NAMESPACE__ . '\alert', 10, 2 );
add_action( 'deactivated_plugin', __NAMESPACE__ . '\alert', 10, 2 );
