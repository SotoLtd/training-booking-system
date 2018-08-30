<?php

/**
 * The Admin Bookings Settings for this plugin.
 *
 * @link       http://mhmasum.me/
 * @since      1.0.0
 *
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/admin
 */

/**
 * The admin-settings functionality of the plugin.
 *
 *
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/admin
 * @author     TTS <mmhasaneee@gmail.com>
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class TBS_Admin_Settings {
	private $settings_db_name = 'tbs_settings';
	/**
	 * Main admin handler object
	 * @access   private
	 * @var obj
	 */
	private $admin;
	/**
	 * Container for all settings
	 * @var array
	 */
	private $settings = array();

	/**
	 * Admin message fro various actions.
	 * @access   private
	 * @var      array
	 */
	private $messages= array();
	
	/**
	 * Constructor of this class
	 * @param TBS_Admin $admin
	 */
	public function __construct(TBS_Admin $admin) {
		$this->admin = $admin;
		$this->settings = get_option($this->settings_db_name);
	}
	
	/**
	 * Get settings
	 * @return array
	 */
	public function get_settings(){
		$settings = array();
		
		
		// Course Settings
		$settigns['course-settigns'] = array(
			'title'	=> __('Course Settings', TBS_i18n::get_domain_name()),
			'description' =>  '',
			'fields' => array(
				array(
					'id' => 'course_page_nottice',
					'type' => 'textarea_html',
					'label' => __('Single course page notice'),
					'description' => __('If set this will be displayed on top of the single course page.', TBS_i18n::get_domain_name()),
					'args' => array(
						'wpautop'		 => true,
						'media_buttons'	 => false,
						'textarea_name'	 => 'course_page_nottice',
						'textarea_rows'	 => 5,
						'teeny'			 => true
					),
				),
				array(
					'id' => 'course_date_page_text',
					'type' => 'textarea_html',
					'label' => __('Course dates page text'),
					'description' => __('If set this will be displayed on top of the course dates lisitng page.', TBS_i18n::get_domain_name()),
					'args' => array(
						'wpautop'		 => true,
						'media_buttons'	 => false,
						'textarea_name'	 => 'course_date_page_text',
						'textarea_rows'	 => 5,
						'teeny'			 => true
					),
				),
				array(
					'id' => 'course_template',
					'type' => 'select',
					'label' => __('Course template'),
					'description' => __('Select the default course template. This can be overwritten on course edit page.', TBS_i18n::get_domain_name()),
					'options' => array(
						'old' => __('Template 1', TBS_i18n::get_domain_name()),
						'new' => __('Template 2', TBS_i18n::get_domain_name()),
					),
					'default' => 'new',
				),
				array(
					'id' => 'course_date_list_page_id',
					'type' => 'select_page',
					'label' => __('Course Dates listing page'),
					'description' => __('Select a page for course listing page.', TBS_i18n::get_domain_name()),
				),
			),
		);
		// Campaign Monitor API settings
		$settigns['campaign-monitor-settigns'] = array(
			'title'	=> __('Campaign Monitor Settings', TBS_i18n::get_domain_name()),
			'description' =>  '',
			'fields' => array(
				array(
					'id' => 'ca_clientid',
					'type' => 'text',
					'label' => __('Client ID'),
				),
				array(
					'id' => 'ca_apikey',
					'type' => 'text',
					'label' => __('API Key'),
				),
				array(
					'id' => 'ca_list_id',
					'type' => 'camp_monitor_list',
					'label' => __('List'),
				),
			),
		);
		// Misc
		$settigns['miscellaneous'] = array(
			'title'	=> __('Miscellaneous Settings', TBS_i18n::get_domain_name()),
			'description' =>  '',
			'fields' => array(
				array(
					'id' => 'copy_email',
					'type' => 'text',
					'label' => __('CC Email'),
					'description' => __('Mulitple emails can be added separated by comma(,).', TBS_i18n::get_domain_name()),
				),
				array(
					'id' => 'online_form_manual_email',
					'type' => 'text',
					'label' => __('Online Form Email'),
					'description' => __('Add email address to which the online form will be sent for manual bookings.', TBS_i18n::get_domain_name()),
				),
				array(
					'id' => 'new_customer_form_email',
					'type' => 'text',
					'label' => __('New Customer Form Email'),
					'description' => __('New customer from email address.', TBS_i18n::get_domain_name()),
				),
			),
		);
		
		return $settigns;
	}
	/**
	 * Get Tabs
	 * @return array
	 */
	public function get_tabs(){
		$settings = $this->get_settings();
		$tabs = array();
		foreach($settings as $tab => $tab_settings){
			$tabs[$tab] = array(
				'id' => $tab,
				'title'	=> isset($tab_settings['title']) ? $tab_settings['title'] : '',
				'description' => isset($tab_settings['description']) ? $tab_settings['description'] : '',
			);
		}
		return $tabs;
	}
	/**
	 * Get tabs ID/key
	 * @return array
	 */
	public function get_tabs_key(){
		return array_keys($this->get_settings());
	}
	/**
	 * Get settings value
	 * @param type $id
	 * @param type $default
	 * @return type
	 */
	public function get_setting_value($id, $default = ''){
		return isset($this->settings[$id]) ? $this->settings[$id] : $default;
	}
	/**
	 * Set a value in the settings
	 * @param type $id
	 * @param type $value
	 */
	public function set_settings_value($id, $value){
		$this->settings[$id] = $value;
	}
	public function save_settings(){
		update_option($this->settings_db_name, $this->settings);
	}
	/**
	 * Get tabs settings
	 * @param string $tab
	 * @return array
	 */
	public function get_tab_fields($tab = ''){
		$settings = $this->get_settings();
		if(!isset($settings[$tab])){
			return array();
		}
		foreach($settings[$tab]['fields'] as $index => $field){
			$settings[$tab]['fields'][$index]['value'] = $this->get_setting_value($field['id']);
		}
		return $settings[$tab]['fields'];
	}
	/**
	 * Get current tabs key
	 * @return string
	 */
	public function get_current_tab_key(){
		$tabs_keys = $this->get_tabs_key();
		return isset($_REQUEST['tab']) && in_array($_REQUEST['tab'], $tabs_keys) ? $_REQUEST['tab'] : array_shift($tabs_keys);
	}
	/**
	 * Get current tabs settings
	 * @return array
	 */
	public function get_current_tab(){
		$tabs = $this->get_tabs();
		return $tabs[ $this->get_current_tab_key()];
	}
	/**
	 * Register admin menu page
	 */
	public function add_settings_page() {
		$settings_page = add_submenu_page(
				'booking-system',
				'Booking Settings', 
				'Settings', 
				'manage_options', 
				'tbs-settings',
				array($this, 'render_settings_page')
		);
		add_action( 'load-' . $settings_page, array( $this, 'handle_settings_submit' ) );
	}
	/**
	 * Handle settins form submission
	 * @return type
	 */
	public function handle_settings_submit(){
		if(empty($_POST[ $this->settings_db_name])){
			return;
		}
		check_admin_referer('save_tbs_settings', '_tbsnonce');
		if(!current_user_can('manage_options')){
			wp_die( "You don't have sufficient permission.", __( 'WordPress Failure Notice' ), 403 );
		}
		$settings = array();
		$current_tab_key = $this->get_current_tab_key();
		$currrent_tab = $this->get_current_tab();
		$current_tab_fields = $this->get_tab_fields($current_tab_key);
		$update_count = 0;
		foreach($current_tab_fields as $field){
			$field_key = $field['id'];
			if( isset( $_POST[$field_key] ) && $_POST[$field_key] != $this->get_setting_value( $field_key ) ){
				$this->set_settings_value($field['id'], $_POST[$field['id']]);
				$update_count++;
			}
		}
		if($update_count > 0){
			$this->save_settings();
			$this->messages[] = '<div class="notice notice-success is-dismissible"><p>Settings saved.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		}else{
			$this->messages[] = '<div class="notice notice-info is-dismissible"><p>You have not changed any setting.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		}
	}
	
	/**
	 * Render content for settings page
	 */
	public function render_settings_page(){
		$tabs = $this->get_tabs();
		$current_tab_key = $this->get_current_tab_key();
		$currrent_tab = $this->get_current_tab();
		$current_tab_fields = $this->get_tab_fields($current_tab_key);
		
		$course_settings = $this->settings;
		
		switch($current_tab_key){
			case 'campaign-monitor-settigns': 
				require_once tbs_get_libarary('campaignmonitor-createsend/csrest_clients.php');
				$ca_wrap = new CS_REST_Clients( $this->get_setting_value('ca_clientid'), array('api_key' => $this->get_setting_value( 'ca_apikey' ) ) );
				$ca_lists = $ca_wrap->get_lists();
				break;
		}
		
		include $this->admin->get_partial('bookings-settings');
	}
	
	/**
	 * Get Settings Url
	 * @param type $tab
	 * @param type $extra_query_args
	 * @return string
	 */
	public static function url($tab = '', $extra_query_args = array()){
		$query_args = array('page' => 'tbs-settings');
		if($tab){
			$query_args['tab'] = $tab;
		}
		if( is_array($extra_query_args)){
			$query_args = array_merge($query_args, $extra_query_args);
		}
		return add_query_arg($query_args, admin_url('admin.php'));
	}
	
}