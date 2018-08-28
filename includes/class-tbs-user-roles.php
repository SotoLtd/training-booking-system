<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TBS_User_Roles {
	/**
	 * Capabilites of course manager
	 * @var array
	 */
	private static $course_manager_caps = array(
		'manage_course_dates' => true,
		'manage_bookings' => true,
		'manage_customers' => true,
	);
	/**
	 * Capabilities of bookers/ Shop Manager
	 * @var array
	 */
	private static $bookers_caps = array(
		'list_delegates' => true,
		'create_delegates' => true,
		'edit_delegates' => true,
		'remove_delegates' => true,
	);
	/**
	 * Capabilities of delegates
	 * @var array
	 */
	private static $delegates_caps = array(
		'edit_delegate' => true,
		'remove_delegate' => true,
	);
	/**
	 * Get capabilities for WooCommerce - these are assigned to admin/shop manager during installation or reset.
	 *
	 * @return array
	 */
	 private static function get_core_capabilities() {
		$capabilities = array();

		$capability_type = TBS_Custom_Types::get_location_data('type');

		$capabilities = array(
			// Post type
			"edit_{$capability_type}",
			"read_{$capability_type}",
			"delete_{$capability_type}",
			"edit_{$capability_type}s",
			"edit_others_{$capability_type}s",
			"publish_{$capability_type}s",
			"read_private_{$capability_type}s",
			"delete_{$capability_type}s",
			"delete_private_{$capability_type}s",
			"delete_published_{$capability_type}s",
			"delete_others_{$capability_type}s",
			"edit_private_{$capability_type}s",
			"edit_published_{$capability_type}s",
		);

		return $capabilities;
	}
	/**
	 * Get course manager caps
	 * @return array
	 */
	public static function get_courser_manger_caps(){
		return self::$course_manager_caps;
	}

	/**
	 * Create user role: Course Manager
	 * @global WP_Roles $wp_roles
	 * @return void
	 */
	public static function create_course_manager_role(){
		global $wp_roles;
		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		// Create Delegate role and assign capabilties
		// First remove role so that capabilitiesa re reassigned
		remove_role('course_manager');
		add_role('course_manager', __('Course Manager', TBS_i18n::get_domain_name()), self::$course_manager_caps);
		$wp_roles->add_cap('course_manager', 'view_admin_dashboard', true);
		$wp_roles->add_cap('course_manager', 'read', true);
		
		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap ) {
			$wp_roles->add_cap( 'course_manager', $cap );
			$wp_roles->add_cap( 'administrator', $cap );
		}
		// Now assign the capabilites to customer and shop_manage and admin
		foreach (self::$course_manager_caps as $cap => $grant){
			$wp_roles->add_cap('administrator', $cap, $grant);
		}
	}
	/**
	 * Create user role: Delegate
	 * @global WP_Roles $wp_roles
	 * @return type
	 */
	public static function create_delegate_role(){
		global $wp_roles;
		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$bookers_capabilities = array_merge(self::$bookers_caps, self::$delegates_caps);
		
		// Create Delegate role and assign capabilties
		// First remove role so that capabilitiesa re reassigned
		remove_role('delegate');
		add_role('delegate', __('Delegate', TBS_i18n::get_domain_name()), self::$delegates_caps);
		// Now assign the capabilites to customer and shop_manage and admin
		foreach ($bookers_capabilities as $cap => $grant){
			$wp_roles->add_cap('customer', $cap, $grant);
			$wp_roles->add_cap('shop_manager', $cap, $grant);
			$wp_roles->add_cap('course_manager', $cap, $grant);
			$wp_roles->add_cap('administrator', $cap, $grant);
		}
	}
	/**
	 * Remove Role: Course Manager
	 * @global WP_Roles $wp_roles
	 * @return type
	 */
	public static function remove_course_manage_role(){
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		foreach (self::$course_manager_caps as $cap => $grant){
			$wp_roles->remove_cap('administrator', $cap);
		}
		
		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap ) {
			$wp_roles->remove_cap( 'course_manager', $cap );
			$wp_roles->remove_cap( 'administrator', $cap );
		}
		remove_role('course_manager');
	}
	public static function remove_delegate_role(){
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$bookers_capabilities = array_merge(self::$bookers_caps, self::$delegates_caps);
		foreach ($bookers_capabilities as $cap => $grant){
			$wp_roles->remove_cap('customer', $cap);
			$wp_roles->remove_cap('shop_manager', $cap);
			$wp_roles->remove_cap('course_manager', $cap);
			$wp_roles->remove_cap('administrator', $cap);
		}
		remove_role('delegate');
	}
	/**
	 * Create user roles
	 */
	public static function create_roles(){
		self::create_course_manager_role();
		self::create_delegate_role();
	}
	/**
	 * Remove roles
	 */
	public static function remove_roles(){
		self::remove_delegate_role();
		self::remove_course_manage_role();
	}
}

if(isset($_GET['mhm_role'])){
	add_action('init', 'mhm_add_role');
	function mhm_add_role(){
		TBS_User_Roles::create_roles();
	}
}