<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TBS_Admin_Tools {
	/**
	 * Admin handler
	 * @var obj
	 */
	private $admin;

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
		$screen_ids[] = 'booking-system_page_tbs-tools';
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
		wp_enqueue_script( $this->admin->get_plugin_name() . '-tools', $this->admin->get_assets_url('js/tools.js'), array( 'jquery' ), WC_VERSION, true );

		$s_data			 = array();
		$s_data['ajaxUrl']	 = admin_url( '/admin-ajax.php' );
		wp_localize_script( $this->admin->get_plugin_name() . '-tools', 'TBS_Tools', $s_data );
	}
	/**
	 * Get Tabs
	 * @return array
	 */
	public function get_tabs(){
		$tabs = array(
			'tbs_crm_tool' => array(
				'id' => 'tbs_crm_tool',
				'title' => __('CRM Tools', TBS_i18n::get_domain_name()),
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
	public function add_tools_page(){
		$reports_page = add_submenu_page(
			'booking-system',
			'Tools',
			'Tools',
			'manage_options',
			'tbs-tools',
			array($this, 'render_tools_page')
		);
		add_action( 'load-' . $reports_page, array( $this, 'tools_actions' ) );
	}
	/**
	 * Perform actions on reports page
	 */
	public function tools_actions(){
		switch( $this->get_current_tab() ){
			case 'tbs_crm_tool':
				$this->crm_actions();
				break;
		}

	}
	public function crm_actions(){

	}

	/**
	 * Render tools page
	 */
	public function render_tools_page(){
		$this->page_header();
		switch( $this->get_current_tab() ){
			case 'tbs_crm_tool':
			default:
				$this->display_crm_tools();
				break;
		}
		$this->page_footer();
	}
	public function page_header(){
		$page_title = __('Tools', TBS_i18n::get_domain_name());
		$tabs = $this->get_tabs();
		$current_tab_key = $this->get_current_tab();
		$tabs_container_id = 'reports-tabs';
		include_once( dirname( __FILE__ ) . '/partials/header.php' );
	}

	public function page_footer(){
		include_once( dirname( __FILE__ ) . '/partials/footer.php' );
	}
	/**
	 * Display trainer report page
	 */
	public function display_crm_tools(){
		include_once( dirname( __FILE__ ) . '/partials/tools-crm.php' );
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
