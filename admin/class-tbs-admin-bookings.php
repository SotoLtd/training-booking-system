<?php

/**
 * The admin-booking specific functionality of the plugin.
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/admin
 * @author     TTS <mmhasaneee@gmail.com>
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class TBS_Admin_Bookings {
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
	}
	/**
	 * Set List table for booking
	 * @param string $list list name
	 * @return boolean
	 */
	public function set_list_table($list){
		$supported_list_tables = array(
			'bookings',
			//'customers' / 'bookers'
			//'delegates'
			//trainers
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
		
		if($this->admin->is_booking_screen()){
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
			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'backbone' );
			
			foreach($jquery_ui_components as $component){
				wp_enqueue_script( $component, array('jquery') );
			}
			wp_enqueue_script( $this->admin->get_plugin_name() . '-bookings', $this->admin->get_assets_url('js/bookings.js'), array_merge(array('jquery'), $jquery_ui_components), $this->admin->get_plugin_version(), true );

			$booking_settings = array();
			$booking_settings['ajaxUrl']	= admin_url( '/admin-ajax.php' );
			$booking_settings['fetchNonce'] = wp_create_nonce('booking-fetch');
			$booking_settings['saveNonce'] = wp_create_nonce('booking-save');
			$booking_settings['curencySymbol'] = '<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol() . '</span>';
			if(isset($_GET['booking_id'])){
				$booking_settings['bookingID']	= absint($_GET['booking_id']);
			}
			wp_localize_script( $this->admin->get_plugin_name() . '-bookings', 'TBS_Booking_Settings', $booking_settings );
		}
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
	 * 
	 */
	public function set_screen_option($status, $option, $value){
		return $value;
	}
	
	/**
	 * Add Bookings page
	 */
	public function add_bookings_page(){
		
		$bookings_page = add_submenu_page(
				'booking-system',
				'Bookings', 
				'Bookings', 
				'manage_bookings',
				'tbs-bookings', 
				array($this, 'render_bookings_page')
		);
		add_action( 'load-' . $bookings_page, array( $this, 'booking_actions' ) );
	}
	/**
	 * Do some actions before rendering booking page
	 */
	public function booking_actions(){
		$option = 'per_page';
		$args   = array(
			'label'   => 'Number of bookings per page',
			'default' => 10,
			'option'  => 'bookings_per_page'
		);

		add_screen_option( $option, $args );
		$this->maybe_load_list_table();
		//$this->mhm_update_bookign_stock();
		
	}
	public function maybe_load_list_table(){
		$current_action = $this->get_current_action();
		if(!in_array( $current_action, array('edit') )){
			$this->set_list_table('bookings');
			$this->booking_lists_actions();
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
		$redirect = false;
		switch ($doaction){
			case 'delete': 
				check_admin_referer('tbs-delete_booking', '_tbsnonce');
				if(!current_user_can('manage_bookings')){
					wp_die( "You don't have sufficient permission.", __( 'WordPress Failure Notice' ), 403 );
				}
				$booking_id = tbs_arr_get('booking_id', $_GET);
				$this->delete_bookings($booking_id);
				$redirect = true;
				break;
			case 'bulk-delete':
				check_admin_referer( $this->list_table->get_nonce_bulk_action_name(), '_wpnonce' );
				if(!current_user_can('manage_bookings')){
					wp_die( "You don't have sufficient permission.", __( 'WordPress Failure Notice' ), 403 );
				}
				$booking_ids = tbs_arr_get('bulk-delete', $_POST);
				$this->delete_bookings($booking_ids);
				$redirect = true;
				break;
			default:
				$redirect = false;
		}
		if(!$redirect){
			return;
		}
		
		$pagenum = $this->list_table->get_pagenum();
		$sendback = remove_query_arg( array('action', 'booking_id', '_tbsnonce', '_wpnonce', 'bulk-delete', 'paged'), wp_get_referer() );
		
		if ( ! $sendback ){
			$sendback = self::url();
		}
		if($pagenum > 1){
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );
		}
		wp_redirect( $sendback );
		exit;
	}
	/**
	 * Delete a booking
	 * @param type $booking_id
	 * @return boolean
	 */
	public function delete_bookings($booking_ids){
		if(empty($booking_ids)){
			return;
		}
		if( !is_array( $booking_ids )){
			$booking_ids = array($booking_ids);
		}
		foreach($booking_ids as $booking_id){
			$order = wc_get_order($booking_id);
			if(!$order){
				continue;
			}
			
			//force_delete
			$order->delete();
		}
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
		$args = array(
			'title' => 'Add new Booking'
		);
		switch( $this->get_current_action() ){
			case 'edit': 
				if(!empty($_GET['booking_id'])){
					$args['new_booking'] = '<a href="' . TBS_Admin_Bookings::url('edit') . '" class="page-title-action">Add Booking</a>'; 
					$args['title'] = 'Edit booking';
				}else{
					$args['title'] = 'Add new booking';
				}
				$this->display_edit_form($args);
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
	public function display_edit_form($booking_form_setting = array()){
		$booking_form_setting = wp_parse_args($booking_form_setting, array(
			'title' => 'Edit booking',
			'new_booking' => '',
		));
		include_once( dirname( __FILE__ ) . '/partials/bookings-edit-form.php' );
	}
	/**
	 * Display booking list
	 */
	public function display_booking_list(){
		include_once( dirname( __FILE__ ) . '/partials/bookings.php' );
	}
	
	
	/**
	 * Handle Ajax request for course date models
	 */
	public function ajax_get_course_dates_models(){
		check_admin_referer('booking-fetch', '_tbsnonce');
		$course_id = absint($_REQUEST['course_id']);
		if(!$course_id){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Invalid course ID!',
			));
		}
		if(!current_user_can('manage_bookings')){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Permission error.',
			));
		}
		$course = new TBS_Course($course_id);
		if(!$course->exists()){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Invalid course ID!',
			));
		}
		$course_dates = $course->get_dates(array(
			'type' => 'upcoming',
			'json_model' => true,
		));
		if( count( $course_dates ) < 1){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'No course dates found!',
			));
		}
		wp_send_json(array(
			'status' => 'OK',
			'courseDates' => $course_dates
		));
	}
	
	/**
	 * Recalculate Taxes and return items data and totals - vats and prices
	 */
	public function ajax_booking_get_items_models(){
		check_admin_referer('booking-fetch', '_tbsnonce');
		if(!isset($_REQUEST['order_id'])){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Parameter missed.',
			));
		}
		
		if(empty($_REQUEST['items'])){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Empty items!',
			));
		}
		$order_id = absint( tbs_arr_get( 'order_id', $_REQUEST ), 0);
		$update = false;
		if(!current_user_can('manage_bookings')){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Permission error.',
			));
		}
		if(!$order_id){
			$order = wc_create_order(array(
				'created_via' => 'tbs_manual_booking',
				'status' => 'wc-tbs-draft,'
			));
		}else{
			$order = wc_get_order($order_id);
			if('tbs_manual_booking' != $order->get_created_via()){
				wp_send_json(array(
					'status' => 'NOTOK',
					'html' => 'You can not edit non manual orders!',
				));
			}
			$update = true;
		}
		if( !$order || is_wp_error( $order )){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Can not create/get order.',
			));
		}
		$order_id = $order->get_id();
		$order_items = $order->get_items();
		$order_items = array_keys($order_items);
		
		$items = tbs_arr_get('items', $_REQUEST, array());
		$prepeared_items = array();
		$line_items_to_delete = array();
		$line_items_updated = array();
		foreach($items as $item){
			$course_date = new TBS_Course_Date($item['id']);
			// Remove the item form response if course does not exist
			if(!$course_date->exists()){
				continue;
			}
			$delegates = !empty($item['delegates']) ? absint($item['delegates']) : 0;
			$delegates_qty = !$course_date->is_private() ? $delegates : 1;
			// Remove the item if no delegates is set
			if($delegates < 1){
				continue;
			}
			if($update && !empty($item['item_id']) && in_array( $item['item_id'], $order_items )){
				// Update order line item that already exists
				$line_item = WC_Order_Factory::get_order_item( absint( $item['item_id'] ) );
				if(!$line_item){
					continue;
				}
				// Get old delegates number
				$old_delegates_number = $line_item->get_quantity();
				$line_item->set_props( array(
					'quantity'     => $delegates,
					'total'        => $item['total'],
					'subtotal'     => $item['subtotal'],
				) );
				$line_item->save();
				// Update delegate stock
				$this->change_wc_product_stock($order, $course_date->get_woo_porduct(), $old_delegates_number, $delegates);
				$line_item_id = absint( $item['item_id']);
				$line_items_updated[] = $line_item_id;
			}else{
			
				$line_item_id = $order->add_product(
					$course_date->get_woo_porduct(), 
					$delegates_qty, 
					array(
						'name' => $course_date->get_course_title_with_date(),
						'total'        => $item['total'],
						'subtotal'     => $item['subtotal'],
					)
				);
				if(!$line_item_id){
					continue;
				}
				$line_item = $order->get_item( $line_item_id );
				if($delegates != $delegates_qty){
					$line_item->set_quantity($delegates);
					$line_item->save();
				}
				// Update delegate stock
				$this->change_wc_product_stock($order, $course_date->get_woo_porduct(), 0, $delegates);
			}
			$course_date->reload();
			$item_data = $course_date->get_json_model();
			$item_data['delegates'] = $delegates;
			$item_data['itemID'] = $line_item_id;
			$item_data['subtotal'] = (float)$line_item->get_subtotal();
			$item_data['total'] = (float)$line_item->get_total();
			$prepeared_items[] = $item_data;
		}
		
		// Delete items that are removed from edit area
		$line_items_to_delete = array_diff( $order_items,  $line_items_updated );
		
		foreach($line_items_to_delete as $order_line_item_id){
			$line_item = WC_Order_Factory::get_order_item( $order_line_item_id );
			if($line_item){
				$old_delegates_number = $line_item->get_quantity();
				$this->change_wc_product_stock($order, $line_item->get_product(), $old_delegates_number, 0);
			}
			$order->remove_item($order_line_item_id);
			wc_delete_order_item( absint( $order_line_item_id ) );
		}
		
		$calculate_tax_args = array(
			'country'  => strtoupper( wc_clean( $_REQUEST['country'] ) ),
			'state'    => strtoupper( wc_clean( $_REQUEST['state'] ) ),
			'postcode' => strtoupper( wc_clean( $_REQUEST['postcode'] ) ),
			'city'     => strtoupper( wc_clean( $_REQUEST['city'] ) ),
		);
		
		
		$order->save();
		$order = wc_get_order( $order_id );
		$order->calculate_taxes( $calculate_tax_args );
		$order->calculate_totals( false );
		$order->save();
		
		$response = array();
		$response['status'] = 'OK';
		$response['ID'] = $order->get_id();
		$response['items'] = $prepeared_items;
		$response['totals'] = $this->admin->get_partial_ouput('booking-edit-total', array('order' => $order));
		wp_send_json($response);
	}
	/**
	 * Handle saving booking
	 */
	public function ajax_save_booking(){
		check_admin_referer('booking-save', '_tbsnonce');
		// Check user permisison
		if(!current_user_can('manage_bookings')){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Permission error.',
			));
		}
		
		if(!isset($_POST['order_id'])){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Parameter missed.',
			));
		}
		
		$order_id = absint( tbs_arr_get( 'order_id', $_REQUEST ), 0);
		
		$update = false;
		
		// Get order status
		$order_status = isset($_POST['status']) && wc_is_order_status('wc-'.$_POST['status']) ? $_POST['status'] : 'tbs-draft';
		$is_draft = 'tbs-draft' == $order_status;
		$data_entry_complete = tbs_arr_get( 'data_entry_complete', $_POST );
		$suppress_order_emails = tbs_arr_get( 'suppress_order_emails', $_POST, false );
		
		if(!$data_entry_complete || $suppress_order_emails){
			$this->disable_woorcommerce_emails();
		}
		// Create or get order
		if(!$order_id){
			$order = wc_create_order(array(
				'created_via' => 'tbs_manual_booking',
				'status' => 'tbs-draft',
			));
		}else{
			$order = wc_get_order($order_id);
			
			if('tbs_manual_booking' != $order->get_created_via()){
				wp_send_json(array(
					'status' => 'NOTOK',
					'html' => 'You can not edit non manual orders!',
				));
			}
			$update = true;
		}
		if( !$order || is_wp_error( $order )){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Can not create/get order.',
			));
		}
		$order_id = $order->get_id();
		
		$booking_data['id'] = $order_id;
		$delegates_posted_data = isset($_POST['delegates']) && is_array($_POST['delegates']) ? $_POST['delegates'] : array();
		
		// Process Customer Adrress
		$customer_address_data = is_array($_POST['customer_data']) ? $_POST['customer_data'] : array();
		$customer_address_data = wp_parse_args($customer_address_data, array(
			'first_name' => '',
			'last_name' => '',
			'company' => '',
			'address_1' => '',
			'address_2' => '',
			'city' => '',
			'postcode' => '',
			'coungtry' => '',
			'email' => '',
			'phone' => '',
		));
		// Create customer only when data entry is done
		if($data_entry_complete){
			$customer_id = tbs_admin_create_customer($customer_address_data);
		}else{
			$customer_id = false;
		}
		
		// Get orer line items
		$order_items = $order->get_items();
		
		$order_items = array_keys($order_items);
		
		// Get new items to 
		$items = !empty($_POST['items']) && is_array($_POST['items']) ? $_POST['items'] : array();

		$prepeared_items = array();
		
		$line_items_updated = array();
		foreach($items as $item){
			$course_date = new TBS_Course_Date($item['id']);
			// Remove the item form response if course does not exist
			if(!$course_date->exists()){
				continue;
			}
			$delegates = !empty($item['delegates']) ? absint($item['delegates']) : 0;
			$delegates_qty = !$course_date->is_private() ? $delegates : 1;
			// Remove the item if no delegates is set
			if($delegates < 1){
				continue;
			}
			if( $update && !empty($item['item_id']) && in_array( $item['item_id'], $order_items )){
				// Update order line item that already exists
				$line_item = WC_Order_Factory::get_order_item( absint( $item['item_id'] ) );
				if(!$line_item){
					continue;
				}
				// Get old delegates number
				$old_delegates_number = $line_item->get_quantity();
				$line_item->set_props( array(
					'quantity'     => $delegates,
					'total'        => $item['total'],
					'subtotal'     => $item['subtotal'],
				) );
				$line_item->save();
				$line_item_id = absint( $item['item_id']);
				$line_items_updated[] = $line_item_id;
				// Update delegate stock
				$this->change_wc_product_stock($order, $course_date->get_woo_porduct(), $old_delegates_number, $delegates);
			}else{
				$line_item_id = $order->add_product(
					$course_date->get_woo_porduct(), 
					$delegates_qty, 
					array(
						'name' => $course_date->get_course_title_with_date(),
						'total'        => $item['total'],
						'subtotal'     => $item['subtotal'],
					)
				);
				if(!$line_item_id){
					continue;
				}
				$line_item = $order->get_item( $line_item_id );
				
				if($delegates != $delegates_qty){
					$line_item->set_quantity($delegates);
					$line_item->save();
				}
				// Update delegate stock
				$this->change_wc_product_stock($order, $course_date->get_woo_porduct(), 0, $delegates);
			}
			$course_date->reload();
			$item_data = $course_date->get_json_model();
			$item_data['delegates'] = $delegates;
			$item_data['itemID'] = $line_item_id;
			$item_data['subtotal'] = (float)$line_item->get_subtotal();
			$item_data['total'] = (float)$line_item->get_total();
			$prepeared_items[] = $item_data;
		}
		// Delete items that are removed from edit area
		$line_items_to_delete = array_diff( $order_items,  $line_items_updated );
		
		foreach($line_items_to_delete as $order_line_item_id){
			$line_item = WC_Order_Factory::get_order_item( $order_line_item_id );
			if($line_item){
				$old_delegates_number = $line_item->get_quantity();
				$this->change_wc_product_stock($order, $line_item->get_product(), $old_delegates_number, 0);
			}
			$order->remove_item($order_line_item_id);
			wc_delete_order_item( absint( $order_line_item_id ) );
		}
		
		// Do calculations
		$order = wc_get_order( $order_id );
		$order->set_created_via( 'tbs_manual_booking' );
		// Save general settigns
		update_post_meta($order_id, 'tbs_data_entry_complete', $data_entry_complete );
		update_post_meta($order_id, 'tbs_email_optin', tbs_arr_get( 'email_optin', $_POST ) );
		update_post_meta($order_id, 'tbs_suppress_order_emails', tbs_arr_get( 'suppress_order_emails', $_POST ) );
		
		// Add address components to order data
		foreach($customer_address_data as $ad_key => $ad_value){
			$ad_key = 'billing_' . $ad_key;
			if ( is_callable( array( $order, "set_{$ad_key}" ) ) ) {
				$order->{"set_{$ad_key}"}( $ad_value );

			// Store custom fields prefixed with wither shipping_ or billing_. This is for backwards compatibility with 2.6.x.
			} else{
				$order->update_meta_data( '_' . $ad_key, $ad_value );
			}
		}
		$calculate_tax_args = array(
			'country'  => strtoupper( wc_clean( $customer_address_data['country'] ) ),
			'state'    => strtoupper( wc_clean( $customer_address_data['state'] ) ),
			'postcode' => strtoupper( wc_clean( $customer_address_data['postcode'] ) ),
			'city'     => strtoupper( wc_clean( $customer_address_data['city'] ) ),
		);
		
		$order->set_customer_id($customer_id);
		$order->calculate_taxes( $calculate_tax_args );
		$order->calculate_totals( false );
		
			
		// Do the delegates management for this order
		$delegates_posted_data = isset($_POST['delegates']) && is_array($_POST['delegates']) ? $_POST['delegates'] : array();
		
		if($data_entry_complete){
			//Campaign monitor subscriber
			$is_optin = tbs_arr_get( 'email_optin', $_POST );
			$camp_monitor_subscribers = array();
			if($is_optin){
				$camp_monitor_subscribers[] = array(
					'EmailAddress' => $order->get_billing_email(),
					'Name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
				);
			}
			
			delete_post_meta($order_id, 'tbs_draft_delegates_data');
			$order_delegates = array();

			$serial_no = 0;
			foreach($delegates_posted_data as $delegate_data){
				$serial_no++;
				$delegate = new TBS_Delegate($delegate_data['email']);
				$delegate->set_first_name($delegate_data['first_name']);
				$delegate->set_last_name($delegate_data['last_name']);
				$delegate->set_notes($delegate_data['notes']);
				$delegate->add_course($delegate_data['courseID']);
				$delegate->add_course_date($delegate_data['courseDateID']);
				$delegate->add_customer($order->get_customer_id());
				$delegate->save();
				if($delegate->exists()){
					$order_delegates[$delegate_data['courseDateID']][] = $delegate->get_id();
				}
				if($is_optin){
					$camp_monitor_subscribers[] = array(
						'EmailAddress' => $delegate->get_email(),
						'Name' => $delegate->get_full_name(),
					);
				}
			}
			update_post_meta($order->get_id(), 'delegates', $order_delegates);
		
			// Initiate Campaign monitor API
			if( $is_optin && count($camp_monitor_subscribers ) > 0){
				tbs_campaign_monitor_import($camp_monitor_subscribers, $order);
			}
		}else{
			update_post_meta($order_id, 'tbs_draft_delegates_data', $delegates_posted_data);
		}
		
		
		// Se order status
		if('tbs-draft' == $order->get_status() && 'completed' == $order_status){
			// Mimic order pending to work wc new order emails
			$order->set_status( 'pending', '', true );
			$order->save();
		}
		$order->set_status( wc_clean( $order_status ), '', true );
		// Save order
		$order->save();
		
		$response = array();
		$response['status'] = 'OK';
		$response['ID'] = $order->get_id();
		$response['editUrl'] = TBS_Admin_Bookings::url( 'edit', array( 'booking_id' => $order->get_id() ) );
		wp_send_json($response);
	}
	/**
	 * Update order status after each manual booking
	 */
	public function change_wc_product_stock($order, $_product, $line_old_quantity, $line_new_quantity){
		if($line_new_quantity == $line_old_quantity){
			return;
		}
		if($line_new_quantity > $line_old_quantity){
			$stock_change = $line_new_quantity - $line_old_quantity;
			$operation = 'decrease';
		}else{
			$stock_change =  $line_old_quantity - $line_new_quantity;
			$operation = 'increase';
		}
		$old_stock    = $_product->get_stock_quantity();
		$new_quantity = wc_update_product_stock( $_product, $stock_change, $operation );
		$item_name    = $_product->get_sku() ? $_product->get_sku() : $_product->get_id();
		$note         = sprintf( __( 'Item %1$s stock %2$s from %3$s to %4$s.', 'woocommerce' ), $item_name, $operation, $old_stock, $new_quantity );
		$order->add_order_note( $note );
	}
	/**
	 * @todo Remove this method when manual booking tool is ready
	 * @return type
	 */
	public function mhm_update_bookign_stock(){
		if(!isset($_GET['mmm'])){
			return;
		}
		$args = array(
			'created_via' => 'tbs_manual_booking',
			'limit' => -1,
		);
		
		$orders = wc_get_orders($args);
		
		foreach($orders as $order){
			$item_data = array();
			$order = new WC_Order($order);
			$line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
		
			foreach($line_items as $line_item_id => $line_item){
				$course_date = new TBS_Course_Date($line_item->get_product());
				$is_private = $course_date->is_private();
				$total = $line_item->get_total();
				$subtotal = $line_item->get_subtotal();
				$delegates_qty = 1;
				if(!$is_private){
					$delegates_qty = $line_item->get_quantity();
				}
				if(0 == $subtotal){
					$subtotal = wc_get_price_excluding_tax( $line_item->get_product(), array( 'qty' => $delegates_qty ) );
					$total = $subtotal;
				}else{
					continue;
				}
				//$this->change_wc_product_stock($order, $line_item->get_product(), 0, $line_item->get_quantity());
				$line_item->set_props( array(
					'total'        => $total,
					'subtotal'     => $subtotal,
				) );
				$line_item->save();
				echo '<a href="'. $course_date->get_permalink() .'">' . $course_date->get_course_title_with_date() . '</a><br/>';
				echo 'Subtotal: ' . $line_item->get_subtotal() . ':: ' . $subtotal .'</br>';
				echo 'Total: ' . $line_item->get_total() . ':: '. $total .'</br>';
			}
		}
		die();
	}
	/**
	 * Hook the action of updating delegate stock when any order/booking is trashed
	 * @param type $id
	 * @return type
	 */
	public function trash_post($id){
		if ( ! current_user_can( 'mangae_bookings' ) || ! $id ) {
			return;
		}

		$post_type = get_post_type( $id );
		switch ( $post_type ) {
			case 'shop_order' :
				$order = wc_get_order($id);
				if(!$order){
					break;
				}
				if('tbs_manual_booking' != $order->get_created_via()){
					wp_die('You can not delete non manually created orders!');
				}
				$line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
		
				foreach($line_items as $line_item_id => $line_item){
					$this->change_wc_product_stock($order, $line_item->get_product(), $line_item->get_quantity(), 0);
				}
			
				break;
		}
	}
	/**
	 * Handle load booking data request
	 */
	public function ajax_load_booking(){
		check_admin_referer('booking-fetch', '_tbsnonce');
		if(empty($_GET['order_id'])){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Parameter missed.',
			));
		}
		
		$order_id = absint($_GET['order_id']);
		
		if(!current_user_can('manage_bookings')){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Permission error.',
			));
		}
		
		$order = wc_get_order($order_id);
		
		if(!$order){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'Order not found!',
			));
		}
		if('tbs_manual_booking' != $order->get_created_via()){
			wp_send_json(array(
				'status' => 'NOTOK',
				'html' => 'You can not edit non manual orders!',
			));
		}
		$data_entry_complete = (bool)$order->get_meta('tbs_data_entry_complete', true);
		$booking_data = array(
			'id' => $order_id,
			'status' => $order->get_status(),
			'dataEntryComplete' => $data_entry_complete,
			'emailOptin' => (bool)$order->get_meta( 'tbs_email_optin', true),
			'suppressOrderEmails' => (bool)$order->get_meta( 'tbs_suppress_order_emails', true),
		);
		// Get Address
		$address = array();
		foreach ( TBS_Admin_Bookings::get_address_fields() as $key => $field ) {
			$field_name = 'billing_' . $key;
			if ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
				$field_value = $order->{"get_$field_name"}( 'edit' );
			} else {
				$field_value = $order->get_meta( '_' . $field_name );
			}
			$address[$key] = $field_value;
		}
		$booking_data['address'] = $address;
		
		// Get line items data
		$prepeared_items = array();
		
		$line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
		
		foreach($line_items as $line_item_id => $line_item){
			$course_date = new TBS_Course_Date($line_item->get_product());
			if(!$course_date->exists()){
				continue;
			}
			$delegates = absint($line_item->get_quantity());
			// Remove the item if no delegates is set
			if($delegates < 1){
				continue;
			}
			$item_data = array();
			$item_data = $course_date->get_json_model();
			$item_data['delegates'] = $delegates;
			$item_data['itemID'] = $line_item_id;
			$item_data['subtotal'] = (float)$line_item->get_subtotal();
			$item_data['total'] = (float)$line_item->get_total();
			$prepeared_items[] = $item_data;
		}
		$booking_data['items'] = $prepeared_items;
		
		// Get Totals
		$booking_data['totals'] = $this->admin->get_partial_ouput('booking-edit-total', array('order' => $order));
		
		// Get Delegates
		$delegates_data = array();
		if(!$data_entry_complete){
			$delegates_data = get_post_meta($order_id, 'tbs_draft_delegates_data', true);
			$delegates_data = is_array($delegates_data)? array_map(array($this, 'fix_draft_delegates_data'), $delegates_data): array();
		}else{
			$order_delegates_ids = get_post_meta($order->get_id(), 'delegates', true);

			if( !is_array( $order_delegates_ids ) || 0 === count($order_delegates_ids)){
				$order_delegates_ids = array();
			}
			foreach($order_delegates_ids as $course_date_id => $d_ids){
				$course_date = new TBS_Course_Date($course_date_id);
				if(!$course_date->exists()){
					continue;
				}
				if(!is_array($d_ids) || count($d_ids) == 0){
					continue;
				}
				$serial_no = 0;
				foreach ($d_ids as $d_id){
					$delegate = new TBS_Delegate($d_id);
					if(!$delegate->exists()){
						continue;
					}
					$serial_no++;
					$delegates_data[] = array(
						'id' => $serial_no,
						'first_name' => $delegate->get_first_name(),
						'last_name' => $delegate->get_last_name(),
						'email' => $delegate->get_email(),
						'notes' => $delegate->get_notes(),
						'courseDateID' => $course_date_id,
						'courseID' => $course_date->get_course_id(),
						'userID' => $delegate->get_id()
					);
				}
			}
		}
		$booking_data['delegates'] = $delegates_data;
		
		wp_send_json(array(
			'status' => 'OK',
			'bookingData' => $booking_data,
		));
		
	}
	/**
	 * Fix drafts delegates data. IDs are returned as string. Needs to convert it to Integers
	 * @param type $delegate
	 * @return array
	 */
	public function fix_draft_delegates_data($delegate){
		$defaults = array(
			'id' => 0,
			'first_name' => '',
			'last_name' => '',
			'email' => '',
			'notes' => '',
			'courseDateID' => 0,
			'courseID' => 0,
			'userID' => 0
		);
		$delegate = wp_parse_args($delegate, $defaults);
		$delegate['id'] = (int)$delegate['id'];
		$delegate['courseDateID'] = (int)$delegate['courseDateID'];
		$delegate['courseID'] = (int)$delegate['courseID'];
		$delegate['userID'] = (int)$delegate['userID'];
		return $delegate;
	}
	/**
	 * disable woocomerce emails
	 */
	public function disable_woorcommerce_emails() {
		add_action('woocommerce_email', array($this, 'unhook_wc_emails_actions'), 20);
	}
	/**
	 * disable woocomerce emails
	 */
	public function enable_woorcommerce_emails() {
		remove_action('woocommerce_email', array($this, 'unhook_wc_emails_actions'), 20);
	}
	public function unhook_wc_emails_actions($wc_emails){

		// Hooks for sending emails during store events
		remove_action( 'woocommerce_low_stock_notification', array( $wc_emails, 'low_stock' ) );
		remove_action( 'woocommerce_no_stock_notification', array( $wc_emails, 'no_stock' ) );
		remove_action( 'woocommerce_product_on_backorder_notification', array( $wc_emails, 'backorder' ) );
		remove_action( 'woocommerce_created_customer_notification', array( $wc_emails, 'customer_new_account' ), 10, 3 );
		
		foreach($wc_emails->emails as $email_class_name => $email_obj){
			add_filter('woocommerce_email_enabled_' . $email_obj->id, '__return_false', 20);
		}
	}
	
	/**
	 * Ouput bookings JS templates
	 */
	function js_tempalates(){
		$partial_data = array(
			'admin_handler' => $this,
		);
		$this->admin->get_partial('bookings-js-templates.tpl', false, $partial_data);
	}
	/**
	 * Get billing address fields for the customer
	 * @return array
	 */
	public static function get_address_fields(){
		return apply_filters( 'woocommerce_admin_billing_fields', array(
			'first_name' => array(
				'label' => __( 'First name', TBS_i18n::get_domain_name() ),
				'show'  => false,
				'custom_attributes' => array(
					'data-modelkey' => 'first_name',
				),
				'required' => true,
			),
			'last_name' => array(
				'label' => __( 'Last name', TBS_i18n::get_domain_name() ),
				'show'  => false,
				'custom_attributes' => array(
					'data-modelkey' => 'last_name',
				),
			),
			'company' => array(
				'label' => __( 'Company', TBS_i18n::get_domain_name() ),
				'show'  => false,
				'custom_attributes' => array(
					'data-modelkey' => 'company',
				),
				'required' => true,
			),
			'address_1' => array(
				'label' => __( 'Address line 1', TBS_i18n::get_domain_name() ),
				'show'  => false,
				'custom_attributes' => array(
					'data-modelkey' => 'address_1',
				),
				'required' => true,
			),
			'address_2' => array(
				'label' => __( 'Address line 2', TBS_i18n::get_domain_name() ),
				'show'  => false,
				'custom_attributes' => array(
					'data-modelkey' => 'address_2',
				),
				'required' => false,
			),
			'city' => array(
				'label' => __( 'City', TBS_i18n::get_domain_name() ),
				'show'  => false,
				'custom_attributes' => array(
					'data-modelkey' => 'city',
				),
				'required' => true,
			),
			'postcode' => array(
				'label' => __( 'Postcode / ZIP', TBS_i18n::get_domain_name() ),
				'show'  => false,
				'custom_attributes' => array(
					'data-modelkey' => 'postcode',
				),
				'required' => true,
			),
			'country' => array(
				'label'   => __( 'Country', TBS_i18n::get_domain_name() ),
				'show'    => false,
				'class'   => 'js_field-country select short',
				'type'    => 'select',
				'options' => array( '' => __( 'Select a country&hellip;', TBS_i18n::get_domain_name() ) ) + WC()->countries->get_allowed_countries(),
				'custom_attributes' => array(
					'data-modelkey' => 'country',
				),
				'value' => 'GB',
				'required' => true,
			),
			'state' => array(
				'label' => __( 'State / County', TBS_i18n::get_domain_name() ),
				'class'   => 'js_field-state select short',
				'show'  => false,
				'custom_attributes' => array(
					'data-modelkey' => 'state',
				),
				'required' => false,
			),
			'email' => array(
				'label' => __( 'Email address', TBS_i18n::get_domain_name() ),
				'custom_attributes' => array(
					'data-modelkey' => 'email',
				),
				'required' => true,
			),
			'phone' => array(
				'label' => __( 'Phone', TBS_i18n::get_domain_name() ),
				'custom_attributes' => array(
					'data-modelkey' => 'phone',
				),
				'required' => true,
			),
		) );
	}
	
	/**
	 * Get fields for delegate
	 * @return array
	 */
	public static function get_delegate_fields(){
		return apply_filters( 'woocommerce_admin_billing_fields', array(
			'first_name' => array(
				'label' => __( 'First name', TBS_i18n::get_domain_name() ),
				'show'  => false,
			),
			'last_name' => array(
				'label' => __( 'Last name', TBS_i18n::get_domain_name() ),
				'show'  => false,
			),
			'email' => array(
				'label' => __( 'Email address', TBS_i18n::get_domain_name() ),
			),
			'notes' => array(
				'label' => __( 'Notes', TBS_i18n::get_domain_name() ),
				'type' => 'textarea'
			),
		) );
	}
	/**
	 * Get Bookings Url
	 * @param type $action
	 * @param type $extra_query_args
	 * @return string
	 */
	public static function url($action = '', $extra_query_args = array()){
		$query_args = array('page' => 'tbs-bookings');
		if($action){
			$query_args['action'] = $action;
		}
		if( is_array($extra_query_args)){
			$query_args = array_merge($query_args, $extra_query_args);
		}
		return add_query_arg($query_args, admin_url('admin.php'));
	}
}