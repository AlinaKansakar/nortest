<?php
/**
 * Plugin Name: User Role for Flamingo
 * Description: Configure special user role to access the flamingo contacts and messages wihtout admin permissions.
 * Version: 1.0.1
 * Author: Yannick Zipf
 * License: GPLv2 or later
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version. Using https://semver.org
 */
define( 'UR4F_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-h4a-activator.php
 */
function activate_user_role_for_flamingo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ur4f-activator.php';
	UR4F_Activator::activate( UR4F_VERSION );
}
register_activation_hook( __FILE__, 'activate_user_role_for_flamingo' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ur4f.php';

/**
 * Begins execution of the plugin.
 */
function run_user_role_for_flamingo() {
	$plugin = new UR4F();
	$plugin->run();
}
run_user_role_for_flamingo();

?>
