<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TBS_Admin_Courses {
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
		if($this->admin->is_booking_screen()){
			
		}
	}
	/**
	 * Enqueue scripts for bookings features
	 */
	public function enqueue_scripts(){
		if($this->admin->is_course_screen()){
			wp_enqueue_media();
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
			wp_enqueue_script( $this->admin->get_plugin_name() . '-course-edit', $this->admin->get_assets_url('js/course-edit.js'), array_merge(array('jquery'), $jquery_ui_components), $this->admin->get_plugin_version(), true );

			$s_data			 = array();
			$s_data['ajaxUrl']	 = admin_url( '/admin-ajax.php' );
			wp_localize_script( $this->admin->get_plugin_name() . '-course-edit', 'WPTBS', $s_data );
		}
	}
	/**
	 * Set List table for booking
	 * @param string $list list name
	 * @return boolean
	 */
	public function set_list_table($list){
		$supported_list_tables = array(
			'courses',
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
		$class_name = 'TBS_' . ucfirst($list) . '_List_Table';
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
	public function add_course_page(){
		
		$course_page = add_submenu_page(
				'booking-system',
				'Courses', 
				'Courses', 
				'manage_course_dates',
				'tbs-course-dates', 
				array($this, 'render_course_page')
		);
		add_action( 'load-' . $course_page, array( $this, 'course_actions' ) );
	}
	/**
	 * Do some actions before rendering course page
	 */
	public function course_actions(){
		$this->maybe_load_list_table();
	}
	public function maybe_load_list_table(){
		$current_action = $this->get_current_action();
		if(!in_array( $current_action, array('edit') )){
			$this->set_list_table('courses');
			$this->course_lists_actions();
		}
		$this->handle_submit();
	}
	/**
	 * Handle settins form submission
	 * @return type
	 */
	public function handle_submit(){
		if(empty($_REQUEST['course_id'])){
			return;
		}
		if(empty($_POST['tbs_cm_course'])){
			return;
		}
		check_admin_referer('save_tbs_cm_course', '_tbsnonce');
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if(!current_user_can('manage_course_dates')){
			wp_die( "You don't have sufficient permission.", __( 'WordPress Failure Notice' ), 403 );
		}
		
		$course_id = absint($_REQUEST['course_id']);
		
		if ( isset( $_POST['course_location'] ) ) {
			update_post_meta( $course_id, 'course_location', $_POST['course_location'] );
		}else{
			delete_post_meta( $course_id, 'course_location' );
		}
		if ( isset( $_POST['joining_instruction'] ) ) {
			update_post_meta( $course_id, 'joining_instruction', $_POST['joining_instruction'] );
		}else{
			delete_post_meta( $course_id, 'joining_instruction' );
		}
		$this->messages[] = '<div class="notice notice-success is-dismissible"><p>Course saved.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		
	}
	/**
	 * Do list specific actions
	 */
	public function do_list_action(){
		
	}
	/**
	 * Do booking list actions
	 */
	public function course_lists_actions(){
		$doaction = $this->list_table->current_action();
		if ( $doaction ) {
			$this->do_list_action($doaction);
		}
		$this->list_table->set_query_data();
		$this->list_table->prepare_items();
	}
	public function render_course_page(){
		$args = array(
			'title' => 'Courses'
		);
		switch( $this->get_current_action() ){
			case 'edit': 
				if(!empty($_GET['course_id'])){
					$args['title'] = 'Edit Course: ' . get_the_title($_GET['course_id']);
					$this->display_edit_form($args);
				}
				break;
			default:
				$this->display_course_list();
				break;
		}
	}
	/**
	 * Display booking edit form
	 * @param array $booking_form_setting
	 */
	public function display_edit_form($form_settings = array()){
		$form_settings = wp_parse_args($form_settings, array(
			'title' => 'Edit Courses',
		));
		include_once( dirname( __FILE__ ) . '/partials/course-edit-form.php' );
	}
	/**
	 * Display booking list
	 */
	public function display_course_list(){
		include_once( dirname( __FILE__ ) . '/partials/courses.php' );
	}
	/**
	 * Get Bookings Url
	 * @param type $action
	 * @param type $extra_query_args
	 * @return string
	 */
	public static function url($action = '', $extra_query_args = array()){
		$query_args = array('page' => 'tbs-course-dates');
		if($action){
			$query_args['action'] = $action;
		}
		if( is_array($extra_query_args)){
			$query_args = array_merge($query_args, $extra_query_args);
		}
		return add_query_arg($query_args, admin_url('admin.php'));
	}
}
