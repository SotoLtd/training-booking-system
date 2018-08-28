<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://mhmasum.me/
 * @since             1.0.0
 * @package           Training_Booking_System
 *
 * @wordpress-plugin
 * Plugin Name:       Training Booking System
 * Plugin URI:        http://thetrainingsocieti.co.uk/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            TTS
 * Author URI:        http://mhmasum.me/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tbs
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
define( 'TBS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tbs-activator.php
 */
function activate_training_booking_system() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tbs-activator.php';
	TBS_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tbs-deactivator.php
 */
function deactivate_training_booking_system() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tbs-deactivator.php';
	TBS_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_training_booking_system' );
register_deactivation_hook( __FILE__, 'deactivate_training_booking_system' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-training-booking-system.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function tbs_assets_url($path = ''){
	$url = plugin_dir_url(__FILE__);
	if($path){
		$url .= $path;
	}
	return $url;
}
/**
 * 
 * @param strig $name
 * @param bool $inlcude Default true
 * @param array $data
 * @return string
 */
function tbs_get_template_part($name, $inlcude = true, $data = array()){
	$file_name = plugin_dir_path( __FILE__ ) . 'public/partials/' . $name . '.php';
	if(!$inlcude){
		return $file_name;
	}
	extract($data);
	unset($data);
	if( file_exists( $file_name )){
		include $file_name;
	}
}
function tbs_plugin_root_path(){
	return plugin_dir_path( __FILE__ );
}
function tbs_get_libarary($name = '', $is_dir = true){
	$file_name = plugin_dir_path( __FILE__ ) . 'includes/library/' . $name ;
	if(!$is_dir){
		$file_name .= '.php';
	}
	return $file_name;
}

function run_training_booking_system() {

	$plugin = new Training_Booking_System();
	$plugin->run();

}
run_training_booking_system();
