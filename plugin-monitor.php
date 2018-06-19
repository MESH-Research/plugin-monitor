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
 * Option name under which to save plugin states.
 *
 * @var string
 */
const OPTION_KEY = 'plugin_monitor_plugin_states';

/**
 * Record current plugin activation states for future reference.
 *
 * @return bool
 */
function save_plugin_states() {
	return update_option( OPTION_KEY, get_current_plugin_states() );
}

/**
 * Delete the option where known plugin states are recorded.
 *
 * @return bool
 */
function delete_plugin_states() {
	return delete_option( OPTION_KEY );
}

/**
 * Get a list of plugin states from the last time remember_plugin_states() ran.
 *
 * @return array List of plugin states.
 */
function get_previous_plugin_states() {
	return get_option( OPTION_KEY, [] );
}

/**
 * Get a list of plugin states from current options.
 *
 * @return array List of plugin states.
 */
function get_current_plugin_states() {
	$all_plugins   = apply_filters( 'all_plugins', get_plugins() );
	$plugin_states = [];

	// Determine active states of all plugins.
	foreach ( $all_plugins as $plugin => $attrs ) {
		if ( is_plugin_active_for_network( $plugin ) ) {
			$plugin_states[ $plugin ] = 'active-network';
		} elseif ( is_plugin_active( $plugin ) ) {
			$plugin_states[ $plugin ] = 'active';
		} else {
			$plugin_states[ $plugin ] = 'inactive';
		}
	}

	return $plugin_states;
}

/**
 * Check that current plugin activation states match previously known plugin activation states.
 *
 * Log/alert any discrepancies.
 */
function check_plugin_states() {
	$previous_plugin_states = get_previous_plugin_states();
	$current_plugin_states  = get_current_plugin_states();

	if ( empty( $previous_plugin_states ) ) {
		$message = 'PLUGIN MONITOR NOTICE: No previous plugin states found.';
		error_log( $message );
	} elseif ( $previous_plugin_states !== $current_plugin_states ) {
		$message = sprintf(
			'PLUGIN MONITOR ALERT: Detected change in plugin activation states! Previous: %s Current: %s',
			print_r( $previous_plugin_states, true ),
			print_r( $current_plugin_states, true )
		);
		error_log( $message );
		wp_mail( get_option( 'admin_email' ), 'PLUGIN MONITOR ALERT', $message );
	}
}

add_action( 'init', __NAMESPACE__ . '\check_plugin_states', 15 );
add_action( 'init', __NAMESPACE__ . '\save_plugin_states', 20 );
