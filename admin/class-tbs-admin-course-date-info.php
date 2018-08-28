<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TBS_Admin_Course_Date_Info {
	/**
	 * Admin handler
	 * @var obj
	 */
	private $admin;
	/**
	 * List table handler
	 * @var obj 
	 */
	private $list_table;
	
	private $course_date;

	/**
	 * Admin message fro various actions.
	 * @access   private
	 * @var      array
	 */
	private $messages= array();
	
	public function __construct(TBS_Admin $admin) {
		$this->admin = $admin;
	}
	
	/**
	 * Enqueue Styles for bookings features
	 */
	public function enqueue_styles(){
		wp_enqueue_style('air-datepicker', $this->admin->get_assets_url('library/air-datepicker/css/datepicker.min.css'));
	}
	/**
	 * Enqueue scripts for bookings features
	 */
	public function enqueue_scripts(){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'air-datepicker', $this->admin->get_assets_url('library/air-datepicker/js/datepicker.min.js'), array('jquery'), $this->admin->get_plugin_version(), true );
		wp_enqueue_script( 'air-datepicker-i18n-en', $this->admin->get_assets_url('library/air-datepicker/js/i18n/datepicker.en.js'), array('jquery'), $this->admin->get_plugin_version(), true );

	}
	/**
	 * Set List table for booking
	 * @param string $list list name
	 * @return boolean
	 */
	public function set_list_table($list){
		$supported_list_tables = array(
			'course-date-info',
			'course-date-bookings',
			'course-date-delegates',
		);
		if( !in_array( $list, $supported_list_tables ) ){
			return false;
		}
		$class_file_name = $this->admin->root_path() . 'class-tbs-' . $list . '-list-table.php';
		if( !file_exists( $class_file_name )){
			return false;
		}
		
		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		$list_class_name_parts = explode('-', $list);
		$list_class_name_parts = array_map('ucfirst', $list_class_name_parts);
		$class_name = 'TBS_' . implode('_', $list_class_name_parts) . '_List_Table';
		require_once $class_file_name;
		$this->list_table = new $class_name;
		return true;
	}
	
	
	/**
	 * Get current action
	 * @return string
	 */
	public function get_current_action(){
		return ! empty( $_REQUEST['action'] ) ? sanitize_title( $_REQUEST['action'] ) : 'list';
	}
	/**
	 * Add Course page
	 */
	public function add_course_date_info_page(){
		
		$course_date_info_page = add_submenu_page(
				'booking-system',
				'Course Date Info', 
				'Course Date Info', 
				'manage_course_dates',
				'tbs-course-date-info', 
				array($this, 'render_course_page')
		);
		add_action( 'load-' . $course_date_info_page, array( $this, 'course_date_info_actions' ) );
	}
	/**
	 * Do some actions before rendering course page
	 */
	public function course_date_info_actions(){
		$option = 'per_page';
		$args = array(
			'label'   => 'Number of items per page',
			'default' => 12,
			'option'  => 'course_items_per_page'
		);
		add_screen_option( $option, $args );
		$this->maybe_load_course();
		$this->maybe_load_list_table();
	}
	public function maybe_load_course(){
		if( 'view' != $this->get_current_action() ){
			return;
		}
		$course_date_id = !empty($_GET['course_date_id']) ? absint($_GET['course_date_id']) : false;
		if(!$course_date_id){
			return;
		}
		$this->course_date = new TBS_Course_Date($course_date_id);
	}
	public function maybe_load_list_table(){
		$current_action = $this->get_current_action();
		if( 'list' == $current_action ){
			$this->set_list_table('course-date-info');
			$this->course_dates_lists_actions();
		}elseif('bookings' == $this->get_course_info_current_tab()){
			$this->set_list_table('course-date-bookings');
			$this->course_dates_bookings_lists_actions();
		}elseif('delegates' == $this->get_course_info_current_tab()){
			$this->set_list_table('course-date-delegates');
			$this->course_dates_delegates_lists_actions();
		}
		
		//$this->handle_submit();
	}
	/**
	 * Handle settins form submission
	 * @return type
	 */
	public function handle_submit(){
		
	}
	/**
	 * Do list specific actions
	 */
	public function do_list_action(){
		
	}
	/**
	 * Do booking list actions
	 */
	public function course_dates_lists_actions(){
		$doaction = $this->list_table->current_action();
		if ( $doaction ) {
			$this->do_list_action($doaction);
		}
		$this->list_table->set_query_data();
		$this->list_table->prepare_items();
	}
	/**
	 * Do booking list actions
	 */
	public function course_dates_bookings_lists_actions(){
		$this->list_table->set_course_date($this->course_date);
		$this->list_table->set_query_data();
		$this->list_table->prepare_items();
	}
	/**
	 * Do booking list actions
	 */
	public function course_dates_delegates_lists_actions(){
		$this->list_table->set_course_date($this->course_date);
		$this->list_table->set_query_data();
		$this->list_table->prepare_items();
	}
	/**
	 * Render course dates info page
	 */
	public function render_course_page(){
		switch( $this->get_current_action() ){
			case 'view': 
				if(!empty($_GET['course_date_id'])){
					$this->display_course_date_info();
				}
				break;
			default:
				$this->display_course_dates_list();
				break;
		}
	}
	/**
	 * Get course info page tabs
	 * @return array
	 */
	public function get_course_info_tabs(){
		return array(
			'general' => __('General', TBS_i18n::get_domain_name()),
			'bookings' => __('Bookings', TBS_i18n::get_domain_name()),
			'delegates' => __('Delegates', TBS_i18n::get_domain_name()),
		);
	}
	/**
	 * Get current tabs key
	 * @return string
	 */
	public function get_course_info_current_tab(){
		$tabs_keys = array_keys( $this->get_course_info_tabs() );
		return isset($_REQUEST['tab']) && in_array($_REQUEST['tab'], $tabs_keys) ? $_REQUEST['tab'] : array_shift($tabs_keys);
	}
	/**
	 * Display course date info page
	 * @param array $booking_form_setting
	 */
	public function display_course_date_info(){
		$course_date_id = !empty($_GET['course_date_id']) ? absint($_GET['course_date_id']) : false;
		if(!$course_date_id){
			return;
		}
		if(!$this->course_date->exists()){
			return;
		}
		include( dirname( __FILE__ ) . '/partials/course-date-info.php' );
		
	}
	/**
	 * Display coruse dates list
	 */
	public function display_course_dates_list(){
		include( dirname( __FILE__ ) . '/partials/course-dates.php' );
	}
	/**
	 * Get Bookings Url
	 * @param type $action
	 * @param type $extra_query_args
	 * @return string
	 */
	public static function url($action = '', $extra_query_args = array()){
		$query_args = array('page' => 'tbs-course-date-info');
		if($action){
			$query_args['action'] = $action;
		}
		if( is_array($extra_query_args)){
			$query_args = array_merge($query_args, $extra_query_args);
		}
		return add_query_arg($query_args, admin_url('admin.php'));
	}
}
