<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://pbdigital.com.au/
 * @since             1.0.0
 * @package           Live_Chat_Stream
 *
 * @wordpress-plugin
 * Plugin Name:       Live chat Stream
 * Plugin URI:        https://github.com/pbdigital/live-chat-stream-plugin
 * Description:       
 * Version:           1.0.0
 * Author:            Paul Bright
 * Author URI:        https://pbdigital.com.au/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       live-chat-stream
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LIVE_CHAT_STREAM_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-live-chat-stream-activator.php
 */
function activate_live_chat_stream() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-live-chat-stream-activator.php';
	Live_Chat_Stream_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-live-chat-stream-deactivator.php
 */
function deactivate_live_chat_stream() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-live-chat-stream-deactivator.php';
	Live_Chat_Stream_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_live_chat_stream' );
register_deactivation_hook( __FILE__, 'deactivate_live_chat_stream' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-live-chat-stream.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_live_chat_stream() {

	$plugin = new Live_Chat_Stream();
	$plugin->run();

}
run_live_chat_stream();


require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/pbdigital/live-chat-stream-plugin',
	__FILE__,
	'live-chat-stream'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');
$myUpdateChecker->getVcsApi()->enableReleaseAssets();
