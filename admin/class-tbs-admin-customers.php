<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TBS_Admin_Customers {
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
		$screen_ids[] = 'booking-system_page_tbs-customers';
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
		wp_enqueue_script('user-profile');
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'wc-users', WC()->plugin_url() . '/assets/js/admin/users' . $suffix . '.js', array( 'jquery', 'wc-enhanced-select', 'selectWoo' ), WC_VERSION, true );
		wp_enqueue_script( 'wc-users' );
		wp_localize_script(
			'wc-users',
			'wc_users_params',
			array(
				'countries'              => json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
				'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', 'woocommerce' ),
			)
		);
	}
	/**
	 * Set List table for booking
	 * @param string $list list name
	 * @return boolean
	 */
	public function set_list_table($list){
		$supported_list_tables = array(
			'customers',
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
	public function add_customers_page(){
		$customers_page = add_submenu_page(
				'booking-system',
				'Customers', 
				'Customers', 
				'manage_customers',
				'tbs-customers', 
				array($this, 'render_customers_page')
		);
		add_action( 'load-' . $customers_page, array( $this, 'customers_actions' ) );
	}
	/**
	 * Do some actions before rendering course page
	 */
	public function customers_actions(){
		$option = 'per_page';
		$args   = array(
			'label'   => 'Number of customers per page',
			'default' => 12,
			'option'  => 'customers_per_page'
		);

		add_screen_option( $option, $args );
		$this->maybe_load_list_table();
		$deleted = isset($_REQUEST['deleted']) ? explode(',', $_REQUEST['deleted']) : false;
		if($deleted && count($deleted) >0){
			$message = _n('%s customer deleted.', '%s customers deleted.', count($deleted), TBS_i18n::get_domain_name());
			$this->add_message('success', sprintf($message, count($deleted)));
		}
	}
	public function maybe_load_list_table(){
		$current_action = $this->get_current_action();
		if(!in_array( $current_action, array('edit') )){
			$this->set_list_table('customers');
			$this->list_table->set_base_url(self::url());
			$this->customers_lists_actions();
		}
		$this->handle_submit();
	}
	/**
	 * Do booking list actions
	 */
	public function customers_lists_actions(){
		if(empty($this->list_table)){
			return;
		}
		$doaction = $this->list_table->current_action();
		if ( $doaction ) {
			$this->do_list_action($doaction);
		}
		$this->list_table->set_query_data();
		$this->list_table->prepare_items();
	}
	/**
	 * Do list specific actions
	 */
	public function do_list_action($doaction){
		$redirect = false;
		$deleted = array();
		switch ($doaction){
			case 'delete': 
				$customer_id = tbs_arr_get('customer_id', $_GET);
				check_admin_referer('tbs_delete_customer_' . $customer_id, '_tbsnonce');
				if(!current_user_can('manage_options')){
					wp_die( "You don't have sufficient permission.", __( 'WordPress Failure Notice' ), 403 );
				}
				
				$deleted = $this->delete_customer($customer_id);
				$redirect = true;
				break;
			case 'bulk-delete':
				check_admin_referer( $this->list_table->get_nonce_bulk_action_name(), '_wpnonce' );
				if(!current_user_can('manage_options')){
					wp_die( "You don't have sufficient permission.", __( 'WordPress Failure Notice' ), 403 );
				}
				$customer_ids = tbs_arr_get('bulk-delete', $_POST);
				$deleted = $this->delete_customer($customer_ids);
				$redirect = true;
				break;
			default:
				$redirect = false;
		}
		if(!$redirect){
			return;
		}
		
		$pagenum = $this->list_table->get_pagenum();
		$sendback = remove_query_arg( array('action', 'customer_id', '_tbsnonce', '_wpnonce', 'bulk-delete', 'paged'), wp_get_referer() );
		
		if ( ! $sendback ){
			$sendback = self::url();
		}
		if($deleted && count($deleted) > 0 ){
			$sendback = add_query_arg('deleted', implode( ',', $deleted ), $sendback);
		}
		if($pagenum > 1){
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );
		}
		wp_redirect( $sendback,  200 );
		exit;
	}
	/**
	 * Delete customers
	 * @param type $customer_ids
	 * @return type
	 */
	public function delete_customer($customer_ids){
		$deleted = array();
		if(empty($customer_ids)){
			return false;
		}
		if( !is_array( $customer_ids )){
			$customer_ids = array($customer_ids);
		}
		foreach($customer_ids as $customer_id){
			$customer = new WC_Customer($customer_id);
			if(!$customer->get_id()){
				continue;
			}
			if($customer->delete()){
				$deleted[] = $customer_id;
			}
		}
		return $deleted;
	}
	/**
	 * Handle settins form submission
	 * @return type
	 */
	public function handle_submit(){
		if(empty($_POST['tbs_customer_form_action'])){
			return;
		}
		$form_action = '';
		if( in_array(trim($_POST['tbs_customer_form_action']), array('add_new', 'update') )){
			$form_action = trim($_POST['tbs_customer_form_action']);
		}
		if(!$form_action){
			wp_die( "You don't have sufficient permission.", __( 'WordPress Failure Notice' ), 403 );
			exit();
		}
		$form_nonce_action = '';
		$customer_id = false;
		if('add_new' == $form_action){
			$form_nonce_action = 'tbs_add_customer';
		}elseif('update' == $form_action){
			$customer_id = absint($_GET['customer_id']);
			$form_nonce_action = 'tbs_update_customer_' . $customer_id;
		}
		check_admin_referer($form_nonce_action, '_tbsnonce');
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if(!current_user_can('manage_customers')){
			wp_die( "You don't have sufficient permission.", __( 'WordPress Failure Notice' ), 403 );
			exit();
		}
		$posted_data = array();
		$fields = array_keys($this->get_customer_fields());
		foreach($fields as $key){
			$posted_data[$key] = tbs_arr_get($key, $_POST, '');
		}
		$account_type = tbs_arr_get('account_type', $_POST, 'normal');
		$pass1 = $pass2 = '';
		if ( isset( $_POST['pass1'] ) ){
			$pass1 = $_POST['pass1'];
		}
		if ( isset( $_POST['pass2'] ) ){
			$pass2 = $_POST['pass2'];
		}
		// Check for blank password when adding a user.
		if ( 'update' != $form_action && empty( $pass1 ) ) {
			$this->add_message( 'error', __( '<strong>ERROR</strong>: Please enter a password.', TBS_i18n::get_domain_name() ) );
			return;
		}

		// Check for "\" in password.
		if ( false !== strpos( wp_unslash( $pass1 ), "\\" ) ) {
			$this->add_message( 'error', __( '<strong>ERROR</strong>: Passwords may not contain the character "\\".', TBS_i18n::get_domain_name() ) );
			return;
		}

		// Checking the password has been typed twice the same.
		if ( ( 'update' == $form_action || ! empty( $pass1 ) ) && $pass1 != $pass2 ) {
			$this->add_message( 'error', __( '<strong>ERROR</strong>: Please enter the same password in both password fields.', TBS_i18n::get_domain_name() ) );
			return;
		}
		switch($form_action){
			case 'update':
				$customer = new WC_Customer( $customer_id );
				if(!$customer->get_id()){
					$this->add_message( 'error', __( '<strong>ERROR</strong>: Customer could not be found.', TBS_i18n::get_domain_name() ) );
					return;
				}
				if(!empty($pass1)){
					$customer->set_password($pass1);
				}
				break;
			case 'add_new': 
				$customer_id = wc_create_new_customer( $posted_data['email'], '', $pass1 );

				if ( is_wp_error( $customer_id ) ) {
					$this->add_message( 'error', __( '<strong>ERROR</strong>: Customer could not be created. Please again.', TBS_i18n::get_domain_name() ) );
					return;
				}
				$customer = new WC_Customer( $customer_id );
				break;
			default:
				$this->add_message( 'error', __( '<strong>ERROR</strong>: Invalide action.', TBS_i18n::get_domain_name() ) );
				return;
		}
		if ( ! empty( $posted_data['first_name'] ) ) {
			$customer->set_first_name( $posted_data['first_name'] );
		}

		if ( ! empty( $posted_data['last_name'] ) ) {
			$customer->set_last_name( $posted_data['last_name'] );
		}

		// If the display name is an email, update to the user's full name.
		if ( is_email( $customer->get_display_name() ) ) {
			$customer->set_display_name( $posted_data['first_name'] . ' ' . $posted_data['last_name'] );
		}

		foreach ( $posted_data as $key => $value ) {
			// Use setters where available.
			if ( is_callable( array( $customer, "set_billing_{$key}" ) ) ) {
				$customer->{"set_billing_{$key}"}( $value );

			// Store custom fields prefixed with wither shipping_ or billing_.
			} else {
				$customer->update_meta_data( 'billing_' . $key, $value );
			}
		}
		
		if(!$customer_id = $customer->save()){
			$message = 'update' == $form_action ? __( '<strong>ERROR</strong>: Customer update failed.', TBS_i18n::get_domain_name() ) : __( '<strong>ERROR</strong>: Customer create failed.', TBS_i18n::get_domain_name() );
			$this->add_message( 'error', $message );
			return;
		}
		update_user_meta($customer_id, 'account_type', $account_type);
		if('update' == $form_action){
			$added_or_updated = 'updated';
		}else{
			$added_or_updated = 'added';
		}
		wp_redirect(self::url('edit', array('customer_id' => $customer_id, $added_or_updated => 1)), 200);
		exit();
		
	}
	public function render_customers_page(){
		$args = array(
			'title' => 'Customers'
		);
		switch( $this->get_current_action() ){
			case 'edit': 
				$this->display_edit_form($args);
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
	public function display_edit_form(){
		
		$form_data = array();
		$customers_data = array(
			'ID' => false,
			'account_type' => 'normal',
			'first_name' => '',
			'last_name'  => '',
			'company' => '',
			'address_1' => '',
			'address_2' => '',
			'city' => '',
			'postcode' => '',
			'country' => '',
			'state' => '',
			'email' => '',
			'phone' => '',
		);
		$customer_id = false;
		if(isset($_POST['tbs_customer_form_action'])){
			$customers_data = array(
				'account_type' => tbs_arr_get( 'account_type', $_POST, '' ),
				'first_name' => tbs_arr_get( 'first_name', $_POST, '' ),
				'last_name'  => tbs_arr_get( 'last_name', $_POST, '' ),
				'company' => tbs_arr_get( 'company', $_POST, '' ),
				'address_1' => tbs_arr_get( 'address_1', $_POST, '' ),
				'address_2' => tbs_arr_get( 'address_2', $_POST, '' ),
				'city' => tbs_arr_get( 'city', $_POST, '' ),
				'postcode' => tbs_arr_get( 'postcode', $_POST, '' ),
				'country' => tbs_arr_get( 'country', $_POST, '' ),
				'state' => tbs_arr_get( 'state', $_POST, '' ),
				'email' => tbs_arr_get( 'email', $_POST, '' ),
				'phone' => tbs_arr_get( 'phone', $_POST, '' ),
			);
		}elseif(!empty($_REQUEST['customer_id'])){
			$customer_id = absint($_REQUEST['customer_id']);
			$customer = new WC_Customer($customer_id, false);
			if(!$customer->get_id()){
				echo '<div class="notice notice-error"><p>Customer could not be found.</p></div>';
				return;
			}
			$account_type = get_user_meta($customer_id, 'account_type', true);
			if(!$account_type){
				$account_type = 'normal';
			}
			$customers_data = array(
				'ID' => $customer->get_id(),
				'account_type' => $account_type,
				'first_name' => $customer->get_billing_first_name('edit'),
				'last_name'  => $customer->get_billing_last_name('edit'),
				'company' => $customer->get_billing_company(),
				'address_1' => $customer->get_billing_address_1(),
				'address_2' => $customer->get_billing_address_2(),
				'city' => $customer->get_billing_city(),
				'postcode' => $customer->get_billing_postcode(),
				'country' => $customer->get_billing_country(),
				'state' => $customer->get_billing_state(),
				'email' => $customer->get_billing_email(),
				'phone' => $customer->get_billing_phone(),
			);
		}
		if(empty($_REQUEST['customer_id'])){
			$form_data['form_title'] = __('Add new customer', TBS_i18n::get_domain_name());
			$form_data['form_url']	= self::url('edit');
			$form_data['form_action'] = 'add_new';
			$form_data['nonce_action'] = 'tbs_add_customer';
			$customers_data['country'] = 'GB';
		}else{
			$customers_data['ID'] = $customer_id;
			$form_data['form_title'] = sprintf(__('Edit customer: %s', TBS_i18n::get_domain_name()), $customer->get_display_name());
			$form_data['form_url']	= self::url('edit', array('customer_id' => $customer_id));
			$form_data['nonce_action'] = 'tbs_update_customer_' . $customer_id;
			$form_data['form_action'] = 'update';
		}
		
		if(!empty($_GET['updated'])){
			$this->add_message('success', __('Customer updated successfully!', TBS_i18n::get_domain_name()));
		}
		if(!empty($_GET['added'])){
			$this->add_message('success', __('Customer created successfully!', TBS_i18n::get_domain_name()));
		}
		
		include_once( dirname( __FILE__ ) . '/partials/customer-edit-form.php' );
	}
	/**
	 * Display booking list
	 */
	public function display_course_list(){
		if($this->list_table){
			include_once( dirname( __FILE__ ) . '/partials/customers.php' );
		}
	}
	/**
	 * Get Customers fields
	 * @return array
	 */
	public function get_customer_fields(){
		return array(
			'first_name' => array(
				'label'       => __( 'First name', 'woocommerce' ),
				'description' => '',
			),
			'last_name' => array(
				'label'       => __( 'Last name', 'woocommerce' ),
				'description' => '',
			),
			'company' => array(
				'label'       => __( 'Company', 'woocommerce' ),
				'description' => '',
			),
			'address_1' => array(
				'label'       => __( 'Address line 1', 'woocommerce' ),
				'description' => '',
			),
			'address_2' => array(
				'label'       => __( 'Address line 2', 'woocommerce' ),
				'description' => '',
			),
			'city' => array(
				'label'       => __( 'City', 'woocommerce' ),
				'description' => '',
			),
			'postcode' => array(
				'label'       => __( 'Postcode / ZIP', 'woocommerce' ),
				'description' => '',
			),
			'country' => array(
				'label'       => __( 'Country', 'woocommerce' ),
				'description' => '',
				'class'       => 'js_field-country',
				'type'        => 'select',
				'options'     => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries(),
			),
			'state' => array(
				'label'       => __( 'State / County', 'woocommerce' ),
				'description' => __( 'State / County or state code', 'woocommerce' ),
				'class'       => 'js_field-state',
			),
			'phone' => array(
				'label'       => __( 'Phone', 'woocommerce' ),
				'description' => '',
			),
			'email' => array(
				'label'       => __( 'Email address', 'woocommerce' ),
				'description' => '',
			),
		);
	}
	/**
	 * Get Bookings Url
	 * @param type $action
	 * @param type $extra_query_args
	 * @return string
	 */
	public static function url($action = '', $extra_query_args = array()){
		$query_args = array('page' => 'tbs-customers');
		if($action){
			$query_args['action'] = $action;
		}
		if( is_array($extra_query_args)){
			$query_args = array_merge($query_args, $extra_query_args);
		}
		return add_query_arg($query_args, admin_url('admin.php'));
	}
}
