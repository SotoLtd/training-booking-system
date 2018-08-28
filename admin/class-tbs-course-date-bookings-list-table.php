<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Booking List Table
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/admin
 * @author     TTS <mmhasaneee@gmail.com>
 */
class TBS_Course_Date_Bookings_List_Table extends WP_List_Table {
	private $query_data = array();
	private $course_date;
	/**
	 * Constructor
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		parent::__construct(array(
			'singular'	=> __('Booking', TBS_i18n::get_domain_name()),
			'plural'	=> __('Bookings', TBS_i18n::get_domain_name()),
			'ajax'		=> false,
		));
	}
	public function set_course_date( TBS_Course_Date $course_date ){
		$this->course_date = $course_date;
	}
	/**
	 * Get bulk nonce action name
	 */
	public function get_nonce_bulk_action_name (){
		return 'bulk-' . $this->_args['plural'];
	}
	
	/**
	 * Set query all query args
	 */
	public function set_query_data(){
		$this->query_data['per_page'] = $this->get_items_per_page('bookings_per_page', 12);
		$this->query_data['current_page'] = $this->get_pagenum();
		
		if(isset($_REQUEST['orderby'])){
			$this->query_data['orderby'] = trim($_REQUEST['orderby']);
		}else{
			$this->query_data['orderby'] = 'date';
		}
		if(isset($_REQUEST['order'])){
			$this->query_data['order'] = $_REQUEST['order'];
		}else{
			$this->query_data['order'] = 'DESC';
		}
		if(isset($_REQUEST['status'])){
			$this->query_data['status'] = $_REQUEST['status'];
		}
		if(isset($_REQUEST['booking_type'])){
			if('manual' == $_REQUEST['booking_type']){
				$this->query_data['created_via'] = 'tbs_manual_booking';
			}elseif('online' == $_REQUEST['booking_type']){
				$this->query_data['created_via'] = 'checkout';
			}
		}
		if(isset($_REQUEST['past_bookings'])){
			$this->query_data['past_or_upcoming_bookings'] = 'past';
		}
		
		if(isset($_REQUEST['upcoming_bookings'])){
			$this->query_data['past_or_upcoming_bookings'] = 'upcoming';
		}
	}
	/**
	 * Get a single query args
	 * @param string $key
	 * @param mix $default
	 * @return type
	 */
	public function get_query_arg($key, $default = ''){
		return isset($this->query_data[$key]) ? $this->query_data[$key] : $default;
	}
	/**
	 * Return text for no booking found.
	 */
	public function no_items() {
		_e( 'No bookings found.', TBS_i18n::get_domain_name() );
	}
	
	public function get_views(){
		$all_booking_class = $manual_booking_class = $online_booking_class = $completed_booking_class = $draft_booking_class = $past_booking_class = $upcoming_booking_class = '';
		if(isset($_REQUEST['booking_type']) && 'manual' == $_REQUEST['booking_type']){
			$manual_booking_class = ' class="current"';
		}
		if(isset($_REQUEST['booking_type']) && 'online' == $_REQUEST['booking_type']){
			$online_booking_class = ' class="current"';
		}
		if(isset($_REQUEST['status']) && 'completed' == $_REQUEST['status']){
			$completed_booking_class = ' class="current"';
		}
		if(isset($_REQUEST['status']) && 'tbs-draft' == $_REQUEST['status']){
			$draft_booking_class = ' class="current"';
		}
		if(!empty($_REQUEST['past_bookings'])){
			$past_booking_class = ' class="current"';
		}
		if(!empty($_REQUEST['upcoming_bookings'])){
			$upcoming_booking_class = ' class="current"';
		}
		if(!$manual_booking_class && !$online_booking_class && !$completed_booking_class && !$draft_booking_class && !$past_booking_class && !$upcoming_booking_class){
			$all_booking_class = ' class="current"';
		}
		$views = array();
		$all_inner_html = __('All bookings', TBS_i18n::get_domain_name());
		$views['all'] = '<a '. $all_booking_class .' href="'. TBS_Admin_Course_Date_Info::url('view', array('course_date_id' => $this->course_date->get_id(), 'tab' => 'bookings', 'all_bookings' => 1,)) .'">' . $all_inner_html . '</a>';

		$manual_inner_html = __('Manual bookings', TBS_i18n::get_domain_name());
		$views['manual'] = '<a '. $manual_booking_class .' href="'. TBS_Admin_Course_Date_Info::url('view', array('course_date_id' => $this->course_date->get_id(),  'tab' => 'bookings','booking_type' => 'manual',)) .'">' . $manual_inner_html . '</a>';
		
		$online_inner_html = __('Online bookings', TBS_i18n::get_domain_name());
		$views['online'] = '<a '. $online_booking_class .' href="'. TBS_Admin_Course_Date_Info::url('view', array('course_date_id' => $this->course_date->get_id(),  'tab' => 'bookings','booking_type' => 'online',)) .'">' . $online_inner_html . '</a>';

		$completed_inner_html = __('Completed bookings', TBS_i18n::get_domain_name());
		$views['completed'] = '<a '. $completed_booking_class .' href="'. TBS_Admin_Course_Date_Info::url('view', array('course_date_id' => $this->course_date->get_id(),  'tab' => 'bookings','status' => 'completed',)) .'">' . $completed_inner_html . '</a>';
		
		$draft_inner_html = __('Draft bookings', TBS_i18n::get_domain_name());
		$views['draft'] = '<a '. $draft_booking_class .' href="'. TBS_Admin_Course_Date_Info::url('view', array('course_date_id' => $this->course_date->get_id(),  'tab' => 'bookings','status' => 'tbs-draft',)) .'">' . $draft_inner_html . '</a>';
		
		$past_bookings_inner_html = __('Past bookings', TBS_i18n::get_domain_name());
		$views['past_bookings'] = '<a '. $past_booking_class .' href="'. TBS_Admin_Course_Date_Info::url('view', array('course_date_id' => $this->course_date->get_id(),  'tab' => 'bookings','past_bookings' => 1,)) .'">' . $past_bookings_inner_html . '</a>';
		
//		$upcoming_bookings_inner_html = __('Upcoming bookings', TBS_i18n::get_domain_name());
//		$views['upcoming_bookings'] = '<a '. $upcoming_booking_class .' href="'. TBS_Admin_Course_Date_Info::url('view', array('course_date_id' => $this->course_date->get_id(),  'tab' => 'bookings','upcoming_bookings' => 1,)) .'">' . $upcoming_bookings_inner_html . '</a>';
		
		return $views;
	}
	/**
	 * Associative array of columns
	 * @return array
	 */
	public function get_columns() {
		return array(
			'booking_title' => __("Booking", TBS_i18n::get_domain_name()),
			'company' => __("Company", TBS_i18n::get_domain_name()),
			'delegates' => __("Delegates", TBS_i18n::get_domain_name()),
			'reserves' => __("Reserves", TBS_i18n::get_domain_name()),
			'total' => __("Total", TBS_i18n::get_domain_name()),
			'date' => __("Date", TBS_i18n::get_domain_name()),
			'booking_status' => __("Status", TBS_i18n::get_domain_name()),
		);
	}
	/**
	 * Columns to make sortable.
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'booking_title' => 'ID',
			'company' => 'company',
			'customer'   => 'customer',
			'total' => 'total',
			'date'     => array( 'date', true )
		);
	}
	/**
	 * 
	 */
	function column_booking_title ($item){
		$delete_nonce = wp_create_nonce('tbs-delete_booking');
		
		$actions = array();
		if('checkout' == $item['created_via']){
			$actions['view'] = sprintf('<a href="%s">View</a>', TBS_Admin_Online_Bookings::url('details', array('booking_id' => $item['ID'],) ) );
		}elseif('tbs_manual_booking' == $item['created_via']){
			$actions['view'] = sprintf('<a href="%s">View</a>', TBS_Admin_Manual_Bookings::url('edit', array('booking_id' => $item['ID'],) ) );
		}
		
		echo $item['title'];
		echo $this->row_actions( $actions );
	}
	/**
	 * Get column output
	 * @param type $item
	 * @param type $column_name
	 * @return type
	 */
	public function column_default( $item, $column_name ) {
		switch($column_name){
			case 'booking_status': 
				return 'tbs-draft' != $item['status'] ? wc_get_order_status_name($item['status']) : __('Draft', TBS_i18n::get_domain_name());
			case 'company': 
				return $item['company'];
			case 'delegates': 
				return $item['delegates'];
			case 'reserves': 
				return $item['reserves'];
			case 'total': 
				return $item['total_view'];
			case 'date':
				return $item['date_view'];
			default: 
				return isset($item[$column_name]) ? $item[$column_name] : '';
				
		}
	}
	/**
	 * GEt title for the name column
	 * @param obj $order
	 * @return string
	 */
	public function get_formatted_order_title($order){
		if ( $order->get_customer_id() ) {
			$user     = get_user_by( 'id', $order->get_customer_id() );
			$username = '<a href="user-edit.php?user_id=' . absint( $order->get_customer_id() ) . '">';
			$username .= esc_html( ucwords( $user->display_name ) );
			$username .= '</a>';
		} elseif ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
			/* translators: 1: first name 2: last name */
			$username = trim( sprintf( _x( '%1$s %2$s', 'full name', TBS_i18n::get_domain_name() ), $order->get_billing_first_name(), $order->get_billing_last_name() ) );
		} elseif ( $order->get_billing_company() ) {
			$username = trim( $order->get_billing_company() );
		} else {
			$username = __( 'Guest', TBS_i18n::get_domain_name() );
		}

		/* translators: 1: order and number (i.e. Order #13) 2: user name */
		if('checkout' == $order->get_created_via('edit')){
			$title = sprintf(
				__( '%1$s by %2$s', TBS_i18n::get_domain_name() ),
				'<a href="' . TBS_Admin_Online_Bookings::url( 'details', array( 'booking_id' => $order->get_id() ) ) . '" class="row-title"><strong>#' . esc_attr( $order->get_order_number() ) . '</strong></a>',
				$username
			);
		}else{
			$title = sprintf(
				__( '%1$s by %2$s', TBS_i18n::get_domain_name() ),
				'<a href="' . TBS_Admin_Manual_Bookings::url( 'edit', array( 'booking_id' => $order->get_id() ) ) . '" class="row-title"><strong>#' . esc_attr( $order->get_order_number() ) . '</strong></a>',
				$username
			);
		}

		if ( $order->get_billing_email() ) {
			$title .= '<small class="meta email"><a href="' . esc_url( 'mailto:' . $order->get_billing_email() ) . '">' . esc_html( $order->get_billing_email() ) . '</a></small>';
		}

		$title .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details', TBS_i18n::get_domain_name() ) . '</span></button>';
		return $title;
	}
	public function get_delegate_count($order){
		$count = 0;
		foreach($order->get_items('line_item') as $item){
			$product = $item->get_product();
			if(!$product){
				continue;
			}
			if($product->get_id() == $this->course_date->get_id()){
				$count += $item->get_quantity();
			}
		}
		return $count;
	}
	/**
	 * Get bookings
	 */
	public function get_bookings(){
		$this->set_query_data();
		$status = $this->get_query_arg('status', '');
		
		$cd_args = array();
		if($status){
			$cd_args['status'] = array('wc-'. $status);
		}
		$past_or_upcoming_bookings = $this->get_query_arg('past_or_upcoming_bookings', '');
		if($past_or_upcoming_bookings){
			$cd_args['type'] = $past_or_upcoming_bookings;
		}
		
		$order_ids = $this->course_date->get_bookigs_ids($cd_args);
		
		if( count($order_ids) < 1){
			$this->set_pagination_args(array(
				'total_items' => 0,
				'total_pages' => 0,
				'per_page' => $this->get_query_arg('per_page', 3),
			));
			return array();
		}
		$args = array(
			'limit' => $this->get_query_arg('per_page'),
			'page' => $this->get_query_arg( 'current_page', 1 ),
			'order' => $this->get_query_arg( 'order'),
			'orderby' => $this->get_query_arg( 'orderby'),
			'paginate' => true,
			'post__in' => $order_ids,
		);
		if($this->get_query_arg('created_via', '')){
			$args['created_via'] = $this->get_query_arg('created_via');
		}
		if('total' == $this->get_query_arg( 'orderby') ){
			$args['meta_key'] = '_order_total';
			$args['orderby'] = 'meta_value_num';
		}elseif('company' == $this->get_query_arg( 'orderby') ){
			$args['meta_key'] = '_billing_company';
			$args['orderby'] = 'meta_value';
		}
		
		if($status){
			$args['status'] = $this->get_query_arg('status');
		}
		$orders = wc_get_orders($args);
		
		if(empty($orders->total)){
			$this->set_pagination_args(array(
				'total_items' => 0,
				'total_pages' => 0,
				'per_page' => $this->get_query_arg('per_page', 3),
			));//max_num_pages
			return array();
		}
		$this->set_pagination_args(array(
			'total_items' => $orders->total,
			'total_pages' => $orders->max_num_pages,
			'per_page' => $this->get_query_arg('per_page', 3)
		));
		
		$items = array();
		foreach($orders->orders as $order){
			$item_data = array();
			$order = new WC_Order($order);
			$item_data['ID'] = $order->get_id();
			$item_data['status'] = $order->get_status();
			$item_data['title'] = $this->get_formatted_order_title($order);
			$item_data['company'] = $order->get_billing_company();
			if('completed' == $order->get_status()){
				$item_data['delegates'] = tbs_order_course_delegates_count( $order, $this->course_date->get_id() );
				$item_data['reserves'] = '-';
			}elseif( in_array( $item_data['status'], array('cancelled', 'refunded', 'failed') )){
				$item_data['delegates'] = '-';
				$item_data['reserves'] = '-';
			}else{
				$item_data['delegates'] = '-';
				$item_data['reserves'] = tbs_order_course_delegates_count( $order, $this->course_date->get_id() );;
			}
			$item_data['total'] = $order->get_total();
			$item_data['total_view'] = $order->get_total();
			$item_data['date'] = $order->get_date_created()->date( 'c' );
			$item_data['date_view'] = wc_format_datetime( $order->get_date_created() );
			$item_data['created_via'] = $order->get_created_via('edit');
			$items[] = $item_data;
		}
		return $items;
	}
	
	/**
	 * Prepare items for the table
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		$this->items = $this->get_bookings();
	}
	
}