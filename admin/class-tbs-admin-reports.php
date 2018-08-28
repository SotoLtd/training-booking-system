<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TBS_Admin_Reports {
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
		add_filter('woocommerce_screen_ids', array($this, 'wc_screen_id'));
	}
	
	public function add_message($type, $message){
		if(!isset($this->messages[$type])){
			$this->messages[$type] = array();
		}
		$this->messages[$type][] = $message;
	}
	public function has_submission_errors(){
		return isset($this->messages['error']) && count($this->messages['errros']) > 0;
	}
	public function wc_screen_id($screen_ids){
		$screen_ids[] = 'booking-system_page_tbs-reports';
		return $screen_ids;
	}
	
	/**
	 * Enqueue Styles for bookings features
	 */
	public function enqueue_styles(){
		
	}
	/**
	 * Enqueue scripts for bookings features
	 */
	public function enqueue_scripts(){
		wp_enqueue_script( $this->admin->get_plugin_name() . '-reports', $this->admin->get_assets_url('js/reports.js'), array( 'jquery', 'wc-enhanced-select', 'selectWoo' ), WC_VERSION, true );
	}
	/**
	 * Get Tabs
	 * @return array
	 */
	public function get_tabs(){
		$tabs = array(
			'dashboard' => array(
				'id' => 'dashboard',
				'title' => __('Reports dashboard', TBS_i18n::get_domain_name()),
				'description' => '',
			),
			'trainers' => array(
				'id' => 'trainers',
				'title' => __('Trainers report', TBS_i18n::get_domain_name()),
				'description' => '',
			),
		);
		return $tabs;
	}
	/**
	 * Get current action
	 * @return string
	 */
	public function get_current_tab(){
		$tabs_keys = array_keys($this->get_tabs());
		return ! empty( $_REQUEST['tab'] ) && in_array($_REQUEST['tab'], $tabs_keys) ? sanitize_title( $_REQUEST['tab'] ) : array_shift($tabs_keys);
	}
	/**
	 * Add Course page
	 */
	public function add_reports_page(){
		$reports_page = add_submenu_page(
				'booking-system',
				'Reports', 
				'Reports', 
				'manage_options',
				'tbs-reports', 
				array($this, 'render_reports_page')
		);
		add_action( 'load-' . $reports_page, array( $this, 'reports_actions' ) );
	}
	/**
	 * Perform actions on reports page
	 */
	public function reports_actions(){
		switch( $this->get_current_tab() ){
			case 'trainers': 
				$this->trainers_actions();
				break;
		}
		
	}
	public function trainers_actions(){
		if(empty($_REQUEST['trainer_id']) || empty($_REQUEST['action'])){
			return;
		}
		$report = new TBS_Trainers_Report($_REQUEST['trainer_id']);
		if(!$report->object_found()){
			$this->add_message('error', __('Trainer not found!', TBS_i18n::get_domain_name()));
			return;
		}
		$report->prepare_items();
		switch($_REQUEST['action']){
			case 'download':
				if($report->download()){
					die();
				}
				break;
			case 'email':
				$report->email();
				break;
		}
		if($report->has_message()){
			$this->messages = $report->get_messages();
		}
		
		
//		$args = array(
//			'label'   => 'Number of record per page',
//			'default' => 10,
//			'option'  => 'trainers_records_per_page'
//		);
//		add_screen_option( 'per_page', $args );
//		
//		$this->set_list_table('trainers-records');
//		$this->list_table->set_base_url(self::url('trainers'));
//		$this->list_table->set_trainer_id($_GET['trainer_id']);
//		$this->trainers_lists_actions();
	}
	
	/**
	 * Set List table for booking
	 * @param string $list list name
	 * @return boolean
	 */
	public function set_list_table($list){
		$supported_list_tables = array(
			'trainers-records',
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
	 * Do booking list actions
	 */
	public function trainers_lists_actions(){
		$doaction = $this->list_table->current_action();
		if ( $doaction ) {
			$this->do_list_action($doaction);
		}
		$this->list_table->set_query_data();
		$this->list_table->prepare_items();
	}
	/**
	 * Do actions
	 * @param type $doaction
	 */
	public function do_list_action($doaction){
		
	}
	/**
	 * Render reports page
	 */
	public function render_reports_page(){
		$this->page_header();
		switch( $this->get_current_tab() ){
			case 'trainers': 
				$this->display_trainers_reports();
				break;
			case 'dashboard':
			default:
				$this->display_dashboard();
				break;
		}
		$this->page_footer();
	}
	public function page_header(){
		$page_title = __('Reports', TBS_i18n::get_domain_name());
		$tabs = $this->get_tabs();
		$current_tab_key = $this->get_current_tab();
		$tabs_container_id = 'reports-tabs';
		include_once( dirname( __FILE__ ) . '/partials/header.php' );
	}
	
	public function page_footer(){
		include_once( dirname( __FILE__ ) . '/partials/footer.php' );
	}
	
	/**
	 * Display reports dashboard
	 */
	public function display_dashboard(){
		include_once( dirname( __FILE__ ) . '/partials/reports-dashboard.php' );
	}
	/**
	 * Display trainer report page
	 */
	public function display_trainers_reports(){
		include_once( dirname( __FILE__ ) . '/partials/reports-trainers.php' );
	}
	
	/**
	 * Get Bookings Url
	 * @param type $action
	 * @param type $extra_query_args
	 * @return string
	 */
	public static function url($tab = '', $extra_query_args = array()){
		$query_args = array('page' => 'tbs-reports');
		if($tab){
			$query_args['tab'] = $tab;
		}
		if( is_array($extra_query_args)){
			$query_args = array_merge($query_args, $extra_query_args);
		}
		return add_query_arg($query_args, admin_url('admin.php'));
	}
}
