<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://mhmasum.me/
 * @since      1.0.0
 *
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/includes
 * @author     TTS <mmhasaneee@gmail.com>
 */
class TBS_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wp_roles;
		flush_rewrite_rules();
		
		if(!class_exists( 'TBS_User_Roles' )){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class-tbs-user-roles.php';
		}

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$bookers_capabilities = array(
			'list_delegates' => true,
			'create_delegates' => true,
			'edit_delegates' => true,
			'remove_delegates' => true,
		);
		$deligates_capabities = array(
			'edit_delegate' => true,
			'remove_delegate' => true,
		);
		$bookers_capabilities = array_merge($bookers_capabilities, $deligates_capabities);
		foreach ($bookers_capabilities as $cap => $grant){
			$wp_roles->remove_cap('customer', $cap);
			$wp_roles->remove_cap('shop_manager', $cap);
			$wp_roles->remove_cap('administrator', $cap);
		}
		remove_role('delegate');
	}

}
