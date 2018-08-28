<?php

/**
 * Fired during plugin activation
 *
 * @link       http://mhmasum.me/
 * @since      1.0.0
 *
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/includes
 * @author     TTS <mmhasaneee@gmail.com>
 */
class TBS_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wp_roles;
		$custom_types = TBS_Custom_Types::instance();
		$custom_types->register();
		flush_rewrite_rules();
		// Add roles
		if(!class_exists( 'TBS_User_Roles' )){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class-tbs-user-roles.php';
		}
		TBS_User_Roles::create_roles();
	}

}
