<?php

/**
 * The admin online booking specific functionality of the plugin.
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/admin
 * @author     TTS <mmhasaneee@gmail.com>
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class TBS_Admin_Online_Bookings {
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
	 * Error messages.
	 *
	 * @var array
	 */
	private $errors   = array();

	/**
	 * Messages for various actions to save
	 * These message will be handled on each templates/view
	 *
	 * @var array
	 */
	private $messages = array();
	
	
	public function __construct(TBS_Admin $admin) {
		$this->admin = $admin;
		add_filter('woocommerce_screen_ids', array($this, 'wc_screen_id'));
	}
	public function wc_screen_id($screen_ids){
		$screen_ids[] = 'booking-system_page_tbs-online-bookings';
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
		wp_enqueue_script( $this->admin->get_plugin_name() . '-online-bookings', $this->admin->get_assets_url('js/online-bookings.js'), array( 'jquery', 'wc-enhanced-select', 'selectWoo' ), WC_VERSION, true );

		$default_location = wc_get_customer_default_location();
		wp_localize_script( $this->admin->get_plugin_name() . '-online-bookings', 'tbs_onine_booking_params', array(
			'countries'              => json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
			'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', 'woocommerce' ),
			'default_country'        => isset( $default_location['country'] ) ? $default_location['country'] : '',
			'default_state'          => isset( $default_location['state'] ) ? $default_location['state'] : '',
			'placeholder_name'       => esc_attr__( 'Name (required)', 'woocommerce' ),
			'placeholder_value'      => esc_attr__( 'Value (required)', 'woocommerce' ),
			'ajaxUrl'				 => admin_url( '/admin-ajax.php' ),
		) );
	}

	/**
	 * Add a message.
	 * @param string $text
	 */
	public function add_message( $text ) {
		$this->messages[] = $text;
	}

	/**
	 * Add an error.
	 * @param string $text
	 */
	public function add_error( $text ) {
		$this->errors[] = $text;
	}

	/**
	 * Output messages + errors.
	 */
	public function show_messages() {
		if ( sizeof( $this->errors ) > 0 ) {
			foreach ( $this->errors as $error ) {
				echo '<div id="message" class="error inline"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
			}
		} elseif ( sizeof( $this->messages ) > 0 ) {
			foreach ( $this->messages as $message ) {
				echo '<div id="message" class="updated inline"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
			}
		}
	}
	
	/**
	 * Add Bookings page
	 */
	public function add_bookings_page(){
		$bookings_page = add_submenu_page(
				'booking-system',
				'Online Bookings', 
				'Online Bookings', 
				'manage_bookings',
				'tbs-online-bookings', 
				array($this, 'render_bookings_page')
		);
		add_action( 'load-' . $bookings_page, array( $this, 'booking_actions' ) );
	}
	/**
	 * Do some actions before rendering booking page
	 */
	public function booking_actions(){
		$option = 'per_page';
		$current_action = $this->get_current_action();
		if(in_array( $current_action, array('details', 'view_email_records') )){
			$args = array(
				'label'   => 'Number of email records per page',
				'default' => 10,
				'option'  => 'email_records_per_page'
			);
		}else{
			$args = array(
				'label'   => 'Number of bookings per page',
				'default' => 10,
				'option'  => 'bookings_per_page'
			);
		}

		add_screen_option( $option, $args );
		$this->maybe_load_list_table();
		
	}
	/**
	 * Set List table for booking
	 * @param string $list list name
	 * @return boolean
	 */
	public function set_list_table($list){
		$supported_list_tables = array(
			'bookings',
			'email-records',
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
	public function maybe_load_list_table(){
		$current_action = $this->get_current_action();
		if(!in_array( $current_action, array('details', 'view_email_records') )){
			$this->set_list_table('bookings');
			$this->list_table->set_base_url(self::url());
			$this->list_table->set_booking_type('online');
			$this->booking_lists_actions();
		}else{
			$order_id = tbs_arr_get('booking_id', $_REQUEST, false);
			if(!$order_id){
				return;
			}
			$order = wc_get_order($order_id);
			if($order && !empty($_POST['tbs_resend_email']) && !empty($_POST['tbs_order_email_type'])){
				check_admin_referer('tbs_resend_emails_' . $order_id);
				tbs_resend_wc_emails($order, $_POST['tbs_order_email_type']);
			}
			if($order){
				$this->set_list_table('email-records');
				$this->list_table->set_order($order);
				$this->list_table->set_base_url(self::url());
				$this->list_table->set_booking_type('online');
				$this->booking_lists_actions();
			}
		}
	}
	/**
	 * Do booking list actions
	 */
	public function booking_lists_actions(){
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
	 * Get current action
	 * @return string
	 */
	public function get_current_action(){
		return ! empty( $_REQUEST['action'] ) ? sanitize_title( $_REQUEST['action'] ) : 'list';
	}
	/**
	 * Handles output of the reports page in admin.
	 */
	public function render_bookings_page() {
		switch( $this->get_current_action() ){
			case 'details': 
				$this->display_booking_details();
				break;
			case 'view_email_records': 
				$this->display_email_records_list();
				break;
			default:
				$this->display_booking_list();
				break;
		}
	}
	/**
	 * Display booking edit form
	 * @param array $booking_form_setting
	 */
	public function display_booking_details(){
		$order_id = tbs_arr_get('booking_id', $_REQUEST, false);
		if(!$order_id){
			return;
		}
		$order = wc_get_order($order_id);
		if(!$order){
			return;
		}
		include_once( dirname( __FILE__ ) . '/partials/bookings-details.php' );
	}
	/**
	 * Display booking list
	 */
	public function display_booking_list(){
		include_once( dirname( __FILE__ ) . '/partials/bookings.php' );
	}
	/**
	 * Display email records list
	 */
	public function display_email_records_list(){
		$order_id = tbs_arr_get('booking_id', $_REQUEST, false);
		if(!$order_id){
			return;
		}
		$order = wc_get_order($order_id);
		if(!$order){
			return;
		}
		include_once( dirname( __FILE__ ) . '/partials/email-records.php' );
	}
	
	public function ajax_save_online_booking_customer_details(){
		$order_id = tbs_arr_get('order_id', $_POST,  0);
		if(!$order_id){
			wp_send_json(array(
				'status' => 'NOTOK',
					'html' => 'Invalid order ID!',
			));
		}
		
		$order = wc_get_order( $order_id );
		if(!$order){
			wp_send_json(array(
				'status' => 'NOTOK',
					'html' => 'Order does not exist!',
			));
		}
		check_admin_referer('online-booking-save-customer-detials-'.$order->get_id(), '_tbsnonce');
		if(!current_user_can('manage_bookings')){
			wp_die( "You don't have sufficient permission.", __( 'WordPress Failure Notice' ), 403 );
		}
		$has_error = false;
		$errors = array();
		if(empty($_POST['_billing_first_name'])){
			$errors['_billing_first_name'] = 'First name must not empty';
			$has_error = true;
		}
		if(empty($_POST['_billing_last_name'])){
			$errors['_billing_last_name'] = 'Last name must not empty';
			$has_error = true;
		}
		if(empty($_POST['_billing_address_1'])){
			$errors['_billing_address_1'] = 'Address line 1 must not empty';
			$has_error = true;
		}
		if(empty($_POST['_billing_city'])){
			$errors['_billing_city'] = 'City must not empty';
			$has_error = true;
		}
		if(empty($_POST['_billing_postcode'])){
			$errors['_billing_postcode'] = 'Postcode must not empty';
			$has_error = true;
		}
		if(empty($_POST['_billing_country'])){
			$errors['_billing_country'] = 'Country must not empty';
			$has_error = true;
		}
		if(empty($_POST['_billing_email'])){
			$errors['_billing_email'] = 'Email must not empty';
			$has_error = true;
		}
		if(empty($_POST['_billing_phone'])){
			$errors['_billing_phone'] = 'Phone must not empty';
			$has_error = true;
		}
		if($has_error){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Please fix the errors!',
				'errors' => $errors,
			));
		}
		
		// Update billing fields.
		$billing_fields = TBS_Admin_Manual_Bookings::get_address_fields();
		foreach ( $billing_fields as $key => $field ) {
			if ( ! isset( $field['id'] ) ) {
				$field['id'] = '_billing_' . $key;
			}
			if ( ! isset( $_POST[ $field['id'] ] ) ) {
				continue;
			}
			if ( is_callable( array( $order, 'set_billing_' . $key ) ) ) {
				$order->{"set_billing_$key"}(wc_clean( $_POST[ $field['id'] ] ));
			} else {
				$order->update_meta_data( $field['id'], wc_clean( $_POST[ $field['id'] ] ) );
			}
		}
		if(!$order->save()){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Failed!',
			));
		}
		$address_html = '';
		ob_start();
		?>
			<h4>Address:</h4>
			<address>
				<?php echo ( $address = $order->get_formatted_billing_address() ) ? $address : __( 'N/A', 'woocommerce' ); ?>
			</address>
			<?php if ( $order->get_billing_email() ) : ?>
				<h4>Email address:</h4>
				<p class="woocommerce-customer-details--email"><?php echo wc_make_phone_clickable (esc_html( $order->get_billing_email() )); ?></p>
			<?php endif; ?>
			<?php if ( $order->get_billing_phone() ) : ?>
				<h4>Phone:</h4>
				<p class="woocommerce-customer-details--phone"><?php echo wc_make_phone_clickable(esc_html( $order->get_billing_phone() )); ?></p>
			<?php endif; ?>
		<?php
		$address_html = ob_get_clean();
		wp_send_json(array(
			'status' => 'OK',
			'html' => 'Customer billing details saved successfully!',
			'address_html' => $address_html,
		));
		
	}
	
	public function ajax_tbs_save_online_booking_delegates_details(){
		$order_id = tbs_arr_get('order_id', $_POST,  0);
		if(!$order_id){
			wp_send_json(array(
				'status' => 'NOTOK',
					'html' => 'Invalid order ID!',
			));
		}
		
		$order = wc_get_order( $order_id );
		if(!$order){
			wp_send_json(array(
				'status' => 'NOTOK',
					'html' => 'Order does not exist!',
			));
		}
		check_admin_referer('online-booking-save-delegate-detials-'.$order->get_id(), '_tbsnonce');
		if(!current_user_can('manage_bookings')){
			wp_die( "You don't have sufficient permission.", __( 'WordPress Failure Notice' ), 403 );
		}
		$has_error = false;
		$errors = array();
		$delegates_posted_data = isset($_POST['delegates']) && is_array($_POST['delegates']) ? $_POST['delegates'] : array();
		$order_delegates_data = array();
		$has_error = false;
		$errors = array();
		foreach($delegates_posted_data as $course_date_id => $cd_delegates){
			$course_date = new TBS_Course_Date($course_date_id);
			if(!$course_date->exists()){
				continue;
			}
			foreach ($cd_delegates as $sln => $cd_delegate){
				if(empty($cd_delegate['first_name'])){
					$errors['delegate_' . $course_date_id . '_' . $sln . '_first_name'] = 'Delegate' . ($sln+1) . ' for '. $course_date->get_course_title_with_date() .' first name must not empty';
					$has_error = true;
				}
				if(empty($cd_delegate['last_name'])){
					$errors['delegate_' . $course_date_id . '_' . $sln . '_last_name'] = 'Delegate' . ($sln+1) . ' for '. $course_date->get_course_title_with_date() .' last name must not empty';
					$has_error = true;
				}
				$dd_data = array(
					'first_name' => $cd_delegate['first_name'],
					'last_name' => $cd_delegate['last_name'],
					'email' => $cd_delegate['email'],
					'notes' => $cd_delegate['notes'],
					'course_id' => $cd_delegate['course_id'],
					'course_date_id' => $cd_delegate['course_date_id'],
				);
				if('completed' == $order->get_status()){
					$dd_data['ID'] = $cd_delegate['ID'];
				}
				$order_delegates_data[] = $dd_data;
			}
		}
		
		if($has_error){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Please fix the errors!',
				'errors' => $errors,
			));
		}

		if('completed' == $order->get_status()){
			$order_delegates = array();
			foreach($order_delegates_data as $delegate_data){
				$empty_email = false;
				if(empty($delegate_data['email'])){
					$delegate = new TBS_Delegate($delegate_data['ID']);
					$empty_email = true;
				}else{
					$delegate = new TBS_Delegate($delegate_data['email']);
					if(!$delegate->exists()){
						$delegate = new TBS_Delegate($delegate_data['ID']);
						$delegate->set_email($delegate_data['email']);
					}
				}
				//$delegate = new TBS_Delegate($delegate_data['email']);
				$delegate->set_first_name($delegate_data['first_name']);
				$delegate->set_last_name($delegate_data['last_name']);
				$delegate->set_notes($delegate_data['notes']);
				if($delegate_data['ID'] != $delegate->get_id()){
					$delegate->add_course($delegate_data['course_id']);
					$delegate->add_course_date($delegate_data['course_date_id']);
					$delegate->add_customer($order->get_customer_id());
					delete_user_meta($delegate_data['ID'], 'tbs_course_dates', $delegate_data['course_date_id']);
					delete_user_meta($delegate_data['ID'], 'tbs_courses', $delegate_data['course_id']);
				}
				if($empty_email){
					$delegate->set_empty_email();
				}
				$delegate->save();
				if($delegate->exists()){
					$order_delegates[$delegate_data['course_date_id']][] = $delegate->get_id();
				}
			}
			update_post_meta($order_id, 'delegates', $order_delegates);
		}else{
			update_post_meta($order_id, 'online_delegates_data', $order_delegates_data);
		}
		$delegates_html = '';
		ob_start();
		$show_view_only = true;
		if('completed' == $order->get_status()){
			$order_delegates = get_post_meta($order->get_id(), 'delegates', true);
		}else{
			$order_delegates = get_post_meta($order->get_id(), 'online_delegates_data', true);
		}
		if( is_array( $order_delegates ) && count($order_delegates) > 0 ){
			if('completed' == $order->get_status()){
				include_once( dirname( __FILE__ ) . '/partials/bookings-delegates-completed.php' );
			}else{
				include_once( dirname( __FILE__ ) . '/partials/bookings-delegates-non-completed.php' );
			}
		}
		$delegates_html = ob_get_clean();
		wp_send_json(array(
			'status' => 'OK',
			'html' => 'Delegates details saved successfully!',
			'delegates_html' => $delegates_html,
		));
		
	}
	/**
	 * Get Bookings Url
	 * @param type $action
	 * @param type $extra_query_args
	 * @return string
	 */
	public static function url($action = '', $extra_query_args = array()){
		$query_args = array('page' => 'tbs-online-bookings');
		if($action){
			$query_args['action'] = $action;
		}
		if( is_array($extra_query_args)){
			$query_args = array_merge($query_args, $extra_query_args);
		}
		return add_query_arg($query_args, admin_url('admin.php'));
	}
}