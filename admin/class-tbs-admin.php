<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/admin
 * @author     TTS <mmhasaneee@gmail.com>
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class TBS_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	
	private $course_handler;
	private $manual_booking_handler;
	private $online_booking_handler;
	private $course_date_info_handler;
	private $settings_handler;
	private $customers_hander;
	private $reports_handler;
	private $tools_handler;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		$this->course_handler = new TBS_Admin_Courses($this);
		$this->manual_booking_handler = new TBS_Admin_Manual_Bookings($this);
		$this->online_booking_handler = new TBS_Admin_Online_Bookings($this);
		$this->course_date_info_handler = new TBS_Admin_Course_Date_Info($this);
		$this->customers_hander = new TBS_Admin_Customers($this);
		$this->customers_hander = new TBS_Admin_Customers($this);
		$this->reports_handler = new TBS_Admin_Reports($this);
		$this->settings_handler = new TBS_Admin_Settings($this);
		$this->wc_coupon_handler = new TBS_Admin_WC_Coupon($this);
		$this->tools_handler = new TBS_Admin_Tools($this);

	}
	/**
	 * Get Course Handler object
	 * @return obj
	 */
	public function get_course_handler(){
		if( !is_a( $this->course_handler, 'TBS_Admin_Courses' )){
			$this->course_handler = new TBS_Admin_Courses($this);
		}
		return $this->course_handler;
	}
	/**
	 * Get Bookings Handler object
	 * @return obj
	 */
	public function get_manual_booking_handler(){
		if( !is_a( $this->manual_booking_handler, 'TBS_Admin_Manual_Bookings' )){
			$this->manual_booking_handler = new TBS_Admin_Manual_Bookings($this);
		}
		return $this->manual_booking_handler;
	}
	/**
	 * Get Bookings Handler object
	 * @return obj
	 */
	public function get_online_booking_handler(){
		if( !is_a( $this->online_booking_handler, 'TBS_Admin_Online_Bookings' )){
			$this->online_booking_handler = new TBS_Admin_Online_Bookings($this);
		}
		return $this->online_booking_handler;
	}
	/**
	 * Get Bookings Handler object
	 * @return obj
	 */
	public function get_course_date_info_handler(){
		if( !is_a( $this->course_date_info_handler, 'TBS_Admin_Course_Date_Info' )){
			$this->course_date_info_handler = new TBS_Admin_Course_Date_Info($this);
		}
		return $this->course_date_info_handler;
	}
	/**
	 * Get Customers Handler object
	 * @return obj
	 */
	public function get_customers_hander(){
		if( !is_a( $this->customers_hander, 'TBS_Admin_Customers' )){
			$this->customers_hander = new TBS_Admin_Customers($this);
		}
		return $this->customers_hander;
	}
	/**
	 * Get Reports Handler object
	 * @return obj
	 */
	public function get_reports_hander(){
		if( !is_a( $this->reports_handler, 'TBS_Admin_Reports' )){
			$this->reports_handler = new TBS_Admin_Reports($this);
		}
		return $this->reports_handler;
	}
	/**
	 * Get Reports Handler object
	 * @return obj
	 */
	public function get_tools_hander(){
		if( !is_a( $this->tools_handler, 'TBS_Admin_Tools' )){
			$this->tools_handler = new TBS_Admin_Tools($this);
		}
		return $this->tools_handler;
	}
	/**
	 * Get Settings Handler object
	 * @return obj
	 */
	public function get_settings_handler(){
		if( !is_a( $this->settings_handler, 'TBS_Admin_Settings' )){
			$this->settings_handler = new TBS_Admin_Settings($this);
		}
		return $this->settings_handler;
	}
	/**
	 * Get WC Coupon Handler object
	 * @return obj
	 */
	public function get_wc_coupon_handler(){
		if( !is_a( $this->wc_coupon_handler, 'TBS_Admin_WC_Coupon' )){
			$this->wc_coupon_handler = new TBS_Admin_WC_Coupon($this);
		}
		return $this->wc_coupon_handler;
	}
	/**
	 * Get plugin name
	 * @return string Plugin Name
	 */
	public function get_plugin_name(){
		return $this->plugin_name;
	}
	/**
	 * Get plugin version
	 * @return string Plugin Name
	 */
	public function get_plugin_version(){
		return $this->version;
	}
	/**
	 * Check if currect screen for Bookings and its submenu pages
	 * @return boolean
	 */
	public function is_course_screen(){
		$curent_screen = get_current_screen();
		$screens_ids = array(
			'booking-system_page_tbs-course-dates',
		);
		if(in_array($curent_screen->id, $screens_ids)){
			return true;
		}
		return false;
	}
	/**
	 * Check if currect screen for Bookings and its submenu pages
	 * @return boolean
	 */
	public function is_booking_screen(){
		$curent_screen = get_current_screen();
		$screens_ids = array(
			'booking-system_page_tbs-manual-bookings',
		);
		if(in_array($curent_screen->id, $screens_ids)){
			return true;
		}
		return false;
	}
	/**
	 * Check if currect screen for Bookings and its submenu pages
	 * @return boolean
	 */
	public function is_online_booking_screen(){
		$curent_screen = get_current_screen();
		$screens_ids = array(
			'booking-system_page_tbs-online-bookings',
		);
		if(in_array($curent_screen->id, $screens_ids)){
			return true;
		}
		return false;
	}
	/**
	 * Check if currect screen for course date info and its submenu pages
	 * @return boolean
	 */
	public function is_course_date_info_screen(){
		$curent_screen = get_current_screen();
		$screens_ids = array(
			'booking-system_page_tbs-course-date-info',
		);
		if(in_array($curent_screen->id, $screens_ids)){
			return true;
		}
		return false;
	}
	/**
	 * Check if currect screen for customers and its submenu pages
	 * @return boolean
	 */
	public function is_customers_screen(){
		$curent_screen = get_current_screen();
		$screens_ids = array(
			'booking-system_page_tbs-customers',
		);
		if(in_array($curent_screen->id, $screens_ids)){
			return true;
		}
		return false;
	}
	/**
	 * Check if current screen for reports and its sub menu pages
	 * @return boolean
	 */
	public function is_reports_screen(){
		$curent_screen = get_current_screen();
		$screens_ids = array(
			'booking-system_page_tbs-reports',
		);
		if(in_array($curent_screen->id, $screens_ids)){
			return true;
		}
		return false;
	}
	/**
	 * Check if current screen for reports and its sub menu pages
	 * @return boolean
	 */
	public function is_tools_screen(){
		$curent_screen = get_current_screen();
		$screens_ids = array(
			'booking-system_page_tbs-tools',
		);
		if(in_array($curent_screen->id, $screens_ids)){
			return true;
		}
		return false;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$curent_screen = get_current_screen();
		$course_type = TBS_Custom_Types::get_course_data('type');
		if ( isset( $curent_screen->post_type ) && ($curent_screen->post_type == $course_type) && ($curent_screen->id == $course_type) ) {
			wp_enqueue_style('jquery-ui', $this->get_assets_url('library/jquery-ui/jquery-ui.min.css'));
		}
		if($this->is_booking_screen() || $this->is_course_screen()){
			wp_enqueue_style('jquery-ui', $this->get_assets_url('library/jquery-ui/jquery-ui.min.css'));
		}
		wp_enqueue_style( $this->plugin_name, $this->get_assets_url('css/common.css'), array(), $this->version, 'all' );
		
		// Allow booking handler to enqueue its own styles
		$this->course_handler->enqueue_styles();
		$this->manual_booking_handler->enqueue_styles();
		if($this->is_online_booking_screen()){
			$this->online_booking_handler->enqueue_styles();
		}
		if($this->is_course_date_info_screen()){
			$this->course_date_info_handler->enqueue_styles();
		}
		if($this->is_customers_screen()){
			$this->customers_hander->enqueue_styles();
		}
		
		if($this->is_reports_screen()){
			$this->reports_handler->enqueue_styles();
		}
		if($this->is_tools_screen()){
			$this->tools_handler->enqueue_styles();
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$curent_screen = get_current_screen();
		$course_type = TBS_Custom_Types::get_course_data('type');
		$course_cat_type = TBS_Custom_Types::get_course_category_data('type');
		if ( in_array( $curent_screen->id, array($course_cat_type) ) ) {
			wp_enqueue_media();
		} elseif ( isset( $curent_screen->post_type ) && ($curent_screen->post_type == $course_type) && ($curent_screen->id == $course_type) ) {
			if ( function_exists( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
			$jquery_ui_components = array(
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-draggable',
				'jquery-ui-resizable',
				'jquery-ui-button',
				'jquery-ui-mouse',
				'jquery-ui-menu',
				'jquery-effects-core',
				'jquery-ui-position',
				
				'jquery-ui-datepicker',
				'jquery-ui-dialog',
				'jquery-ui-selectmenu',
				'jquery-ui-sortable'
			);
			wp_enqueue_script( 'jquery' );
			foreach($jquery_ui_components as $component){
				wp_enqueue_script( $component, array('jquery') );
			}
			wp_enqueue_script( $this->plugin_name . '-course-edit', $this->get_assets_url('js/course-edit.js'), array_merge(array('jquery'), $jquery_ui_components), $this->version, true );

			$s_data			 = array();
			$s_data['ajaxUrl']	 = admin_url( '/admin-ajax.php' );
			wp_localize_script( $this->plugin_name . '-course-edit', 'WPTBS', $s_data );
		}
		// Allow course handler to enqueue its own scripts
		$this->course_handler->enqueue_scripts();
		// Allow booking handler to enqueue its own scripts
		$this->manual_booking_handler->enqueue_scripts();
		if($this->is_online_booking_screen()){
			$this->online_booking_handler->enqueue_scripts();
		}
		if($this->is_course_date_info_screen()){
			$this->course_date_info_handler->enqueue_scripts();
		}
		if($this->is_reports_screen()){
			$this->reports_handler->enqueue_scripts();
		}
		if($this->is_tools_screen()){
			$this->tools_handler->enqueue_scripts();
		}
	}
	/**
	 * 
	 */
	public function set_screen_option($status, $option, $value){
		if(false !== strpos($option, 'per_page')) {
			$value = min($value, 50);
		}
		return $value;
	}
	/**
	 * Register Metaboxes
	 * @param type $post_type
	 * @param type $post
	 */
	public function meta_boxes($post_type, $post){
		$course_type = TBS_Custom_Types::get_course_data('type');
		if( $course_type == $post_type){
			add_meta_box( $course_type . '-dates', 'Course Dates', array($this, 'course_date_meta_box'), $post_type, 'normal', 'high' );
			add_meta_box( $course_type . '-fields', 'Course Fields', array('TBS_Course_Meta_Fields', 'display_meta_fields'), $post_type, 'normal', 'high' );
		}
		$location_type = TBS_Custom_Types::get_location_data('type');
		if($location_type == $post_type){
			add_meta_box( $course_type . '-fields', 'Location Data', array('TBS_Location_Meta_Fields', 'display_meta_fields'), $post_type, 'normal', 'high' );
		}
		$trainer_type = TBS_Custom_Types::get_trainer_data('type');
		if($trainer_type == $post_type){
			add_meta_box( $course_type . '-fields', 'Trainer Data', array('TBS_Trainer_Meta_Fields', 'display_meta_fields'), $post_type, 'normal', 'high' );
		}
	}
	
	public function course_date_meta_box($post){
		$screen = get_current_screen();
		$new_course_page = false;
		$course_dates = array();
		
		if(isset($screen->action) && 'add' == $screen->action){
			$new_course_page = true;
		}else{
			$course = new TBS_Course($post->ID);
			$course_dates = $course->get_dates(array(
				'type' => 'all' // expired, upcoming, running
			));
		}
		
		$partial_data = array(
			'course_id' => $post->ID,
			'course_dates' => $course_dates,
			'new_course_page' => $new_course_page,
			'admin_handler' => $this,
		);
		
		$this->get_partial('course-dates-metabox', false, $partial_data);
	}
	
	public function save_meta_fields($post_id){
		$post_type = get_post_type($post_id);
		if($post_type == TBS_Custom_Types::get_course_data('type')){
			TBS_Course_Meta_Fields::save_fields($post_id);
		}
		// Save location meta fields
		if($post_type == TBS_Custom_Types::get_location_data('type')){
			TBS_Location_Meta_Fields::save_fields($post_id);
		}
		// Save location meta fields
		if($post_type == TBS_Custom_Types::get_trainer_data('type')){
			TBS_Trainer_Meta_Fields::save_fields($post_id);
		}
	}
	
	public function course_category_add_fields(){
		TBS_Course_Category_Fields::add_fields();
	}
	public function course_category_edit_fields( $term, $taxonomy ) {
		TBS_Course_Category_Fields::edit_fields($term, $taxonomy);
	}
	public function save_course_category_fields( $term_id, $tt_id, $taxonomy ) {
		TBS_Course_Category_Fields::save_fields($term_id, $tt_id, $taxonomy);
	}
	
	public function admin_menu(){
		add_menu_page( 
			__( 'Booking System', TBS_i18n::get_domain_name() ), 
			__( 'Booking System', TBS_i18n::get_domain_name() ), 
			'manage_bookings', 
			'booking-system', 
			null, 
			null, 
			'25.5'
		);
		$this->manual_booking_handler->add_bookings_page();
		$this->online_booking_handler->add_bookings_page();
		$this->course_date_info_handler->add_course_date_info_page();
		$this->course_handler->add_course_page();
		$this->customers_hander->add_customers_page();
		$this->reports_handler->add_reports_page();
		$this->tools_handler->add_tools_page();
		$this->settings_handler->add_settings_page();
	}
	

	/**
	 * Adds the order processing count to the menu.
	 */
	public function remove_booking_syestem_submenu() {
		global $submenu;

		if ( isset( $submenu['booking-system'] ) ) {
			// Remove 'WooCommerce' sub menu item
			unset( $submenu['booking-system'][0] );
		}
	}
	/**
	 * Reorder the menu items in admin.
	 *
	 * @param mixed $menu_order
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$bookings_menu_order = array();

		// Get index of post type menu
		$booking_course = array_search( 'edit.php?post_type=' . TBS_Custom_Types::get_course_data('slug'), $menu_order );
		$booking_trainer = array_search( 'edit.php?post_type=' . TBS_Custom_Types::get_trainer_data('slug'), $menu_order );

		// Loop through menu order and do some rearranging
		foreach ( $menu_order as $index => $item ) {

			if ( ( ( 'booking-system' ) == $item ) ) {
				$bookings_menu_order[] = 'edit.php?post_type=' . TBS_Custom_Types::get_course_data('slug');
				$bookings_menu_order[] = 'edit.php?post_type=' . TBS_Custom_Types::get_trainer_data('slug');
				$bookings_menu_order[] = $item;
				unset( $menu_order[ $booking_course ] );
				unset( $menu_order[ $booking_trainer ] );
			} else {
				$bookings_menu_order[] = $item;
			}
		}

		// Return order
		return $bookings_menu_order;
	}
	
	/**
	 * Ajax handler for geting course dates for edit form
	 */
	public function ajax_get_course_date_edit_form(){
		/**
		 * @todo Add a nonce for security tighten
		 */
		check_admin_referer();
		if(empty($_POST['course_id'])){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Please provide a course ID.',
			));
		}
		$course_id = absint($_POST['course_id']);
		$type = in_array($_POST['type'], array('add', 'edit'))?$_POST['type']: 'add';
		
		if(!current_user_can('manage_course_dates')){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Permission error.',
			));
		}
		
		$course = new TBS_Course($course_id);
		
		if(!$course->exists()){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'No course found!',
			));
		}
		if('edit' == $type && empty($_POST['course_date_id'])){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Please provide a course date ID.',
			));
		}
		$course_date = false;
		$form_data = array(
			'form_is_private' => $course->is_private(),
			'form_trainer_id' => $course->get_trainer_id(),
			'form_joining_instruction' => $course->joining_instruction,
			'form_duration' => $course->duration,
			'form_start_finish_time' => $course->course_time,
			'form_price' => $course->price,
			'form_max_delegates' => $course->max_delegates,
			'form_location_id' => $course->get_course_location_id(),
			'form_custom_location' => '',
			'form_map' => $course->course_map,
		);
		if('edit' == $type){
			$course_date = new TBS_Course_Date($_POST['course_date_id']);
			if(!$course_date->exists()){
				wp_send_json(array(
					'status' => 'NOTOK',
					'html' => 'No course date found!',
				));
			}
			$form_data = $course_date->get_edit_form_data();
		}
		$partials_data = array(
			'course' => $course,
			'course_date' => $course_date,
			'form_data' => $form_data,
			'form_type' => $type,
		);
		wp_send_json(array(
			'status' => 'OK',
			'html' => $this->get_partial_ouput( 'course-date-edit-form', $partials_data ),
		));
	}
	/**
	 * Ajax handler for adding course date
	 */
	public function ajax_add_course_date(){
		check_admin_referer();
		$course_id = absint($_POST['course_id']);
		if(empty($_POST['date_data'])){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Empty fields!',
			));
		}
		$date_data = $_POST['date_data'];
		if ( !current_user_can( 'manage_course_dates' ) ) {
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Permission error.',
			));
		}
		if(!$course_id){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Please provide a course ID.',
			));
		}
		if(!$date_data){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Please provide date information.',
			));
		}
		$course = new TBS_Course($course_id);
		if(!$course->exists()){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'No course found!',
			));
		}
		
		if(
			empty($date_data['start_date']) || empty($date_data['end_date']) || empty($date_data['duration']) ||
			empty($date_data['trainer']) || empty($date_data['location']) || 
			empty($date_data['price']) || empty($date_data['start_finish_time'])
		){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Empty fields!',
			));
		}
		$course_date_info = array();
		$date_data = wp_parse_args($date_data, array(
			'start_date' => '',
			'end_date' => '',
			'duration' => '',
			'is_private' => '',
			'max_delegates'=> '',
			'price'=> '',
			'trainer'=> '',
			'location' =>'',
			'custom_location' => '',
			'joining_instruction'=> '',
			'start_finish_time' => '',
			'map' => '',
		));
		$course_date_info = array(
			'start_date' => strtotime($date_data['start_date']),
			'end_date' => strtotime($date_data['end_date']) + 86399,
			'duration' => $date_data['duration'],
			'is_private' => (bool)$date_data['is_private'],
			'max_delegates'=> $date_data['stock'],
			'price'=> $date_data['price'],
			'trainer'=> $date_data['trainer'],
			'location' => $date_data['location'],
			'custom_location' => $date_data['custom_location'],
			'joining_instruction'=> $date_data['joining_instruction'],
			'start_finish_time' => $date_data['start_finish_time'],
			'map' => $date_data['map'],
		);
		
		if($course_date = $course->add_date($course_date_info)){
			wp_send_json(array(
				'status' => 'OK',
				'html' => $this->get_partial_ouput( 'course-date-row', array('course_date' => $course_date) ),
			));
		}
		wp_send_json(array(
			'status' => 'NOTOK',
			'html' => 'Failed!',
		));
	}
	/**
	 * Ajax Handler for updating course date
	 */
	public function update_course_date(){
		check_admin_referer();
		$course_id = !empty($_POST['course_id'])?absint($_POST['course_id']):false;
		$course_date_id = !empty($_POST['course_date_id'])?absint($_POST['course_date_id']):false;
		if(!$course_date_id || !$course_id){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Invalide course info!',
			));
		}
		if(empty($_POST['date_data'])){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Empty fields!',
			));
		}
		$date_data = $_POST['date_data'];
		if ( !current_user_can( 'manage_course_dates' ) ) {
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Permission error.',
			));
		}
		if(!$course_id){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Please provide a course ID.',
			));
		}
		if(!$date_data){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Please provide date information.',
			));
		}
		$course = new TBS_Course($course_id);
		if(!$course->exists()){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'No course found!',
			));
		}
		
		if(empty($date_data['start_date']) || empty($date_data['end_date']) || empty($date_data['duration']) || empty($date_data['trainer']) || empty($date_data['price'])){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Empty fields!',
			));
		}
		$course_date_info = array();
		$date_data = wp_parse_args($date_data, array(
			'start_date' => '',
			'end_date' => '',
			'duration' => '',
			'is_private' => '',
			'max_delegates'=> '',
			'price'=> '',
			'trainer'=> '',
			'location' =>'',
			'custom_location' => '',
			'joining_instruction'=> '',
			'start_finish_time' => '',
			'map' => '',
		));
		$course_date_info = array(
			'course_date_id' => $course_date_id,
			'start_date' => strtotime($date_data['start_date']),
			'end_date' => strtotime($date_data['end_date']) + 86399,
			'duration' => $date_data['duration'],
			'is_private' => (bool)$date_data['is_private'],
			'max_delegates'=> $date_data['stock'],
			'price'=> $date_data['price'],
			'trainer'=> $date_data['trainer'],
			'location' => $date_data['location'],
			'custom_location' => $date_data['custom_location'],
			'joining_instruction'=> $date_data['joining_instruction'],
			'start_finish_time' => $date_data['start_finish_time'],
			'map' => $date_data['map'],
		);
		
		if($course_date = $course->add_date($course_date_info)){
			wp_send_json(array(
				'status' => 'OK',
				'html' => $this->get_partial_ouput( 'course-date-row', array('course_date' => $course_date) ),
			));
		}
		wp_send_json(array(
			'status' => 'NOTOK',
			'html' => 'Failed!',
		));
	}
	/**
	 * Ajax handler for deleting course date
	 */
	public function delete_course_date(){
		check_admin_referer();
		$course_date_id = absint($_POST['course_date_id']);
		if(empty($course_date_id)){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Empty fields!',
			));
		}
		if(!current_user_can('manage_course_dates')){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Permission error.',
			));
		}
		$woo_product = wc_get_product($course_date_id);
		if(!$woo_product && 0 === $woo_product->get_id()){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Course Date not found!',
			));
		}
		$woo_product->delete(true);
		if( 0 !== $woo_product->get_id()){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Course Date could not be deleted!',
			));
		}
		wp_send_json(array(
				'status' => 'OK',
				'html' => 'Course Date deleted!',
			));
	}
	/**
	 * User extra fields
	 * @param type $user
	 * @return type
	 */
	public function display_user_extra_fields($profileuser){
        $this->get_partial('user-fields', false, array(
			'admin' => $this,
			'profileuser' => $profileuser,
		));
    }
	/**
	 * User extra fields
	 * @param type $user
	 * @return type
	 */
	public function save_user_extra_fields($user_id){
        $mailer = WC()->mailer();
		$emails = $mailer->get_emails();
		foreach ( $emails as $email_key => $email ){
			if($email->is_customer_email()){
				continue;
			}
			$user_settings_key = 'tbs_' . strtolower($email_key);
			$user_posted_value = isset($_POST[$user_settings_key]) ? 'true' : 'false';
			update_user_meta($user_id, $user_settings_key, $user_posted_value);
		}
    }
	/**
	 * Hook the action of updating delegate stock when any order/booking is trashed
	 * @param type $id
	 * @return type
	 */
	public function trash_post($id){
		
		if ( ! current_user_can( 'manage_bookings' ) || ! $id ) {
			return;
		}

		$post_type = get_post_type( $id );
		switch ( $post_type ) {
			case 'shop_order' :
				$order = wc_get_order($id);
				if(!$order || in_array( $order->get_status(), array('cancelled', 'refunded', 'failed') )){
					break;
				}
				$line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
		
				foreach($line_items as $line_item_id => $order_item){
					$_product = $order_item->get_product();
					if ( $_product && $_product->exists() && $_product->managing_stock() ) {
						$old_stock    = $_product->get_stock_quantity();
						$order_item_qty = $order_item->get_quantity();
						$stock_change = apply_filters( 'woocommerce_restore_order_stock_quantity', $order_item_qty, $item_id );
						$new_quantity = wc_update_product_stock( $_product, $stock_change, 'increase' );
						$item_name    = $_product->get_sku() ? $_product->get_sku() : $_product->get_id();
						$note         = sprintf( __( 'Item %1$s stock increased from %2$s to %3$s.', 'woocommerce' ), $item_name, $old_stock, $new_quantity );
						$order->add_order_note( $note );
					}
				}
				$this->remove_course_dates_from_bookings_delegates_on_trash($order);
				break;
		}
	}
	/**
	 * Hook the action of updating delegate stock when any order/booking is trashed
	 * @param type $id
	 * @return type
	 */
	public function untrashed_post($id){
		
		if ( ! current_user_can( 'manage_bookings' ) || ! $id ) {
			return;
		}

		$post_type = get_post_type( $id );
		switch ( $post_type ) {
			case 'shop_order' :
				$order = wc_get_order($id);
				if(!$order || in_array( $order->get_status(), array('cancelled', 'refunded', 'failed') )){
					break;
				}
				$line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
		
				foreach($line_items as $line_item_id => $order_item){
					$_product = $order_item->get_product();
					if ( $_product && $_product->exists() && $_product->managing_stock() ) {
						$old_stock    = $_product->get_stock_quantity();
						$order_item_qty = $order_item->get_quantity();
						$stock_change = apply_filters( 'woocommerce_restore_order_stock_quantity', $order_item_qty, $item_id );
						$new_quantity = wc_update_product_stock( $_product, $stock_change, 'decrease' );
						$item_name    = $_product->get_sku() ? $_product->get_sku() : $_product->get_id();
						$note         = sprintf( __( 'Item %1$s stock decreased from %2$s to %3$s.', 'woocommerce' ), $item_name, $old_stock, $new_quantity );
						$order->add_order_note( $note );
					}
				}
				$this->add_course_dates_from_bookings_delegates_on_untrash($order);
				break;
		}
	}
	
	public function remove_course_dates_from_bookings_delegates_on_trash($order){
		if('completed' != $order->get_status()){
			return;
		}
		
		$order_delegates = get_post_meta($order->get_id(), 'delegates', true);

		$order_delegates_data = array();
		$delegates_id = array();
		if( is_array( $order_delegates ) && count($order_delegates) > 0 ){
			foreach($order_delegates as $course_date_id => $d_ids){
				$course_date = new TBS_Course_Date($course_date_id);
				if(!$course_date->exists()){
					continue;
				}
				if(!is_array($d_ids) || count($d_ids) == 0){
					continue;
				}
				$serial_no = 0;
				foreach($d_ids as $d_id){
					delete_user_meta($d_id, 'tbs_course_dates', $course_date_id);
				}
			}
		}
	}
	
	public function add_course_dates_from_bookings_delegates_on_untrash($order){
		if('completed' != $order->get_status()){
			return;
		}
		
		$order_delegates = get_post_meta($order->get_id(), 'delegates', true);

		$order_delegates_data = array();
		$delegates_id = array();
		if( is_array( $order_delegates ) && count($order_delegates) > 0 ){
			foreach($order_delegates as $course_date_id => $d_ids){
				$course_date = new TBS_Course_Date($course_date_id);
				if(!$course_date->exists()){
					continue;
				}
				if(!is_array($d_ids) || count($d_ids) == 0){
					continue;
				}
				$serial_no = 0;
				foreach($d_ids as $d_id){
					add_user_meta($d_id, 'tbs_course_dates', $course_date_id);
				}
			}
		}
	}
	/**
	 * Output Javascript templates 
	 */
	public function js_templates(){
		$curent_screen = get_current_screen();
		$course_type = TBS_Custom_Types::get_course_data('type');
		if ( isset( $curent_screen->post_type ) && ($curent_screen->post_type == $course_type) && ($curent_screen->id == $course_type) ) {
			$partial_data = array(
				'course_id' => $_GET['post'],
				'admin_handler' => $this,
			);

			$this->get_partial('course-js-templates.tpl', false, $partial_data);
		}
		if($this->is_course_screen() && isset($_GET['course_id'])){
			$partial_data = array(
				'course_id' => $_GET['course_id'],
				'admin_handler' => $this,
			);

			$this->get_partial('course-js-templates.tpl', false, $partial_data);
		}
		if($this->is_booking_screen()){
			$this->manual_booking_handler->js_tempalates();
		}
	}
	/**
	 * Get admin root path
	 * @return string
	 */
	public function root_path(){
		return plugin_dir_path(__FILE__);
	}
	/**
	 * 
	 * @param string $name Partial name
	 * @param bool $return Return partial file path
	 * @param array $data Partial data. exports as vaiable
	 * @return string
	 */
	public function get_partial($name, $return = true, $data = array()){
		$partial_path = plugin_dir_path(__FILE__) . 'partials/' . $name . '.php';
		
		if($return){
			return $partial_path;
		}
		if(!file_exists($partial_path)){
			return;
		}
		if( is_array(  $data)){
			extract($data);
			unset($data);
		}
		include $partial_path;
		
	}
	/**
	 * Get partial html output
	 * @param string $name
	 * @param array $data
	 * @return string
	 */
	public function get_partial_ouput($name, $data = array()){
		ob_start();
		$this->get_partial($name, false, $data);
		return ob_get_clean();
	}
	/**
	 * Get admin assets url
	 * @param string $path
	 * @return string
	 */
	public function get_assets_url($path = ''){
		return plugin_dir_url(__FILE__) . 'assets/' . $path;
	}

}
