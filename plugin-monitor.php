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
 * Log & email when a plugin is (de)activated.
 *
 * @param string $plugin       Path to the main plugin file from plugins directory.
 * @param bool   $network_wide Whether to enable the plugin for all sites in the network
 *                             or just the current site. Multisite only. Default is false.
 */
function alert( $plugin, $network_wide ) {
	$current_user = wp_get_current_user();

	$alert_emails = [];

	if ( defined( 'PLUGIN_MONITOR_ALERT_EMAILS' ) ) {
		foreach ( explode( ',', constant( 'PLUGIN_MONITOR_ALERT_EMAILS' ) ) as $email ) {
			$alert_emails[] = $email;
		}
	} elseif ( defined( 'GLOBAL_SUPER_ADMINS' ) ) {
		foreach ( explode( ',', constant( 'GLOBAL_SUPER_ADMINS' ) ) as $login ) {
			$admin_user     = get_user_by( 'login', $login );
			$alert_emails[] = $admin_user->user_email;
		}
	} else {
		$alert_emails[] = get_option( 'admin_email' );
	}

	$message = sprintf(
		"%s %s '%s' at the %s level",
		( ! empty( $current_user->user_login ) ) ? $current_user->user_login : 'unknown user',
		str_replace( '_', ' ', current_action() ),
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
