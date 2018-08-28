<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://mhmasum.me/
 * @since      1.0.0
 *
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/includes
 * @author     TTS <mmhasaneee@gmail.com>
 */
class TBS_i18n {
	private static $text_domain_name = 'training-booking-system';


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			self::$text_domain_name,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

	public static function get_domain_name(){
		return self::$text_domain_name;
	}

}
