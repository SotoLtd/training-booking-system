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
class TBS_Bookings_List_Table extends WP_List_Table {
	private $query_data = array();
	private $booking_type = 'manual';
	private $base_url;
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
	/**
	 * Set bookig type. manual|online
	 * @param string $type
	 */
	public function set_booking_type($type){
		$this->booking_type = $type;
	}
	/**
	 * Set base url
	 * @param string $url
	 */
	public function set_base_url($url){
		$this->base_url = $url;
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
			$this->query_data['orderby'] = 'ID';
		}
		if(isset($_REQUEST['order'])){
			$this->query_data['order'] = $_REQUEST['order'];
		}else{
			$this->query_data['order'] = 'DESC';
		}
		if(isset($_REQUEST['completed_bookings'])){
			$this->query_data['status'] = 'completed';
		}
		if(isset($_REQUEST['draft_bookings'])){
			$this->query_data['status'] = 'tbs-draft';
		}
		if(isset($_REQUEST['status'])){
			$this->query_data['status'] = $_REQUEST['status'];
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
	/**
	 * Count bookings
	 */
	public function count_bookings(){
		$type = 'shop_order';
		global $wpdb;

		if ( ! post_type_exists( $type ) )
			return new stdClass;
		
		$meta_value = '';
		$status = array_keys(wc_get_order_statuses());
		switch($this->booking_type){
			case 'manual':
				$meta_value = "('tbs_manual_booking')";
				break;
			case 'online':
				$meta_value =  "('checkout')";
				break;
			default: 
				$meta_value = "('tbs_manual_booking', 'checkout')";
				break;
		}
		if($this->booking_type){}//
		
		$query = "SELECT p.post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} as p";
		$query .= " LEFT JOIN {$wpdb->postmeta} as mt ON p.ID = mt.post_id";
		$query .= " WHERE p.post_type = %s AND mt.meta_key = '_created_via' AND mt.meta_value IN {$meta_value}";
		$query .= " AND p.post_status IN ( '" . implode( "','", $status ) . "' )";

		$query .= ' GROUP BY p.post_status';

		$results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type ), ARRAY_A );
		$counts = array_fill_keys( get_post_stati(), 0 );
		foreach ( $results as $row ) {
			$counts[ $row['post_status'] ] = (int)$row['num_posts'];
		}
		$counts = (object) $counts;

		/**
		 * Modify returned post counts by status for the current post type.
		 *
		 * @since 3.7.0
		 *
		 * @param object $counts An object containing the current post_type's post
		 *                       counts by status.
		 * @param string $type   Post type.
		 * @param string $perm   The permission to determine if the posts are 'readable'
		 *                       by the current user.
		 */
		return $counts;
	}
	/**
	 * Get manual views
	 */
	public function get_manual_views(){
		$views = array();
		
		$num_bookings = $this->count_bookings();
		
		$all_count = $num_bookings->{'wc-completed'} + $num_bookings->{'wc-tbs-draft'};
		
		$completed_booking_class = $draft_booking_class = $past_booking_class = $upcoming_booking_class = $all_booking_class = '';
		if(isset($_REQUEST['completed_bookings'])){
			$completed_booking_class = ' class="current"';
		}elseif(isset($_REQUEST['draft_bookings'])){
			$current_draft_class = ' class="current"';
		}else{
			$current_all_class = ' class="current"';
		}
		if(isset($_REQUEST['completed_bookings'])){
			$completed_booking_class = ' class="current"';
		}
		if(!empty($_REQUEST['draft_bookings'])){
			$draft_booking_class = ' class="current"';
		}
		if(!empty($_REQUEST['past_bookings'])){
			$past_booking_class = ' class="current"';
		}
		if(!empty($_REQUEST['upcoming_bookings'])){
			$upcoming_booking_class = ' class="current"';
		}
		if( !$completed_booking_class && !$draft_booking_class && !$past_booking_class && !$upcoming_booking_class){
			$all_booking_class = ' class="current"';
		}
		
		$all_inner_html = __('All bookings', TBS_i18n::get_domain_name());
		$views['all'] = '<a'. $all_booking_class .' href="'. add_query_arg(array('all_bookings' => 1), $this->base_url) .'">' . $all_inner_html . '<span class="count">('. $all_count .')</span></a>';

		$completed_inner_html = __('Completed bookings', TBS_i18n::get_domain_name());
		$views['completed'] = '<a'. $completed_booking_class .' href="'. add_query_arg(array('completed_bookings' => 1), $this->base_url) .'">' . $completed_inner_html . '<span class="count">('. $num_bookings->{'wc-completed'} .')</span></a>';

		$draft_inner_html = __('Draft bookings', TBS_i18n::get_domain_name());
		$views['draft'] = '<a'. $draft_booking_class .' href="'. add_query_arg(array('draft_bookings' => 1), $this->base_url) .'">' . $draft_inner_html . '<span class="count">('. $num_bookings->{'wc-tbs-draft'} .')</span></a>';
		
		switch($this->booking_type){
			case 'manual':
				$booking_type = 'tbs_manual_booking';
				break;
			case 'online':
				$booking_type = 'checkout';
				break;
			default: 
				break;
		}
		$counts_past_booking = count(tbs_get_time_based_bookings_id('past', false, $booking_type));
		$counts_upcoming_booking = count(tbs_get_time_based_bookings_id('upcoming', false, $booking_type));
		
		$past_bookings_inner_html = __('Past bookings', TBS_i18n::get_domain_name());
		$views['past_bookings'] = '<a '. $past_booking_class .' href="'. add_query_arg(array('past_bookings' => 1), $this->base_url) .'">' . $past_bookings_inner_html . '<span class="count">('. $counts_past_booking .')</span></a>';
		
		$upcoming_bookings_inner_html = __('Upcoming bookings', TBS_i18n::get_domain_name());
		$views['upcoming_bookings'] = '<a '. $upcoming_booking_class .' href="'. add_query_arg(array('upcoming_bookings' => 1), $this->base_url) .'">' . $upcoming_bookings_inner_html . '<span class="count">('. $counts_upcoming_booking .')</span></a>';
		
		
		return $views;
	}
	/**
	 * Get online views
	 */
	public function get_online_views(){
		$views = array();
		$view_all_class = '';
		$past_booking_class = $upcoming_booking_class = '';
		if(!empty($_REQUEST['past_bookings'])){
			$past_booking_class = ' class="current"';
		}
		if(!empty($_REQUEST['upcoming_bookings'])){
			$upcoming_booking_class = ' class="current"';
		}
		$current_status = $this->get_query_arg('status', '');
		if(!$current_status && !$past_booking_class && !$upcoming_booking_class){
			$view_all_class = ' class="current" ';
		}
		
		$wc_order_statuses = wc_get_order_statuses();
		$num_bookings = $this->count_bookings();
		
		$total = 0;
		$views['all'] = '';
		foreach($wc_order_statuses as $status_key => $status_title){
			if( !isset($num_bookings->$status_key) ){
				continue;
			}
			$total += $num_bookings->$status_key;
			$view_key = str_replace( 'wc-', '', $status_key );
			if('tbs-draft' == $view_key){
				continue;
			}
			$class = '';
			if($view_key == $current_status ){
				$class = ' class="current" ';
			}
			$views[$view_key] = '<a '. $class .' href="'. add_query_arg(array('status' => $view_key), $this->base_url) .'">' . $status_title . '<span class="count">('. $num_bookings->$status_key .')</span></a>';
			//$views[$view_key] = '<a href="'. add_query_arg(array('status' => $view_key), $this->base_url) .'">' . $status_title . '</a>';
		}
		
		$all_inner_html = __('All bookings', TBS_i18n::get_domain_name());
		$views['all'] = '<a '. $view_all_class .' href="'. add_query_arg(array('all_bookings' => 1), $this->base_url) .'">' . $all_inner_html . '<span class="count">('. $total .')</span></a>';
		switch($this->booking_type){
			case 'manual':
				$booking_type = 'tbs_manual_booking';
				break;
			case 'online':
				$booking_type = 'checkout';
				break;
			default: 
				break;
		}
		$counts_past_booking = count(tbs_get_time_based_bookings_id('past', false, $booking_type));
		$counts_upcoming_booking = count(tbs_get_time_based_bookings_id('upcoming', false, $booking_type));
		
		$past_bookings_inner_html = __('Past bookings', TBS_i18n::get_domain_name());
		$views['past_bookings'] = '<a '. $past_booking_class .' href="'. add_query_arg(array('past_bookings' => 1), $this->base_url) .'">' . $past_bookings_inner_html . '<span class="count">('. $counts_past_booking .')</span></a>';
		
		$upcoming_bookings_inner_html = __('Upcoming bookings', TBS_i18n::get_domain_name());
		$views['upcoming_bookings'] = '<a '. $upcoming_booking_class .' href="'. add_query_arg(array('upcoming_bookings' => 1), $this->base_url) .'">' . $upcoming_bookings_inner_html . '<span class="count">('. $counts_upcoming_booking .')</span></a>';
		
		return $views;
	}
	/**
	 * Get views for the table
	 * @return string|array
	 */
	public function get_views(){
		switch($this->booking_type){
			case 'manual':
				$views = $this->get_manual_views();
				break;
			case 'online':
				$views =  $this->get_online_views();
				break;
			default: 
				$views = array();
				break;
		}
		return $views;
	}
	/**
	 * Get manual columns
	 * @return array
	 */
	public function get_manual_columns(){
		return array(
			'cb' => '<input type="checkbox" />',
			'booking_title' => __("Booking", TBS_i18n::get_domain_name()),
			'delegates' => __("Delegates", TBS_i18n::get_domain_name()),
			'reserves' => __("Reserves", TBS_i18n::get_domain_name()),
			'total' => __("Total", TBS_i18n::get_domain_name()),
			'date' => __("Date", TBS_i18n::get_domain_name()),
			'booking_status' => __("Status", TBS_i18n::get_domain_name()),
		);
	}
	/**
	 * Get online columns
	 * @return array
	 */
	public function get_online_columns(){
		return array(
			'booking_title' => __("Booking", TBS_i18n::get_domain_name()),
			'delegates' => __("Delegates", TBS_i18n::get_domain_name()),
			'reserves' => __("Reserves", TBS_i18n::get_domain_name()),
			'total' => __("Total", TBS_i18n::get_domain_name()),
			'date' => __("Date", TBS_i18n::get_domain_name()),
			'booking_status' => __("Status", TBS_i18n::get_domain_name()),
		);
	}
	/**
	 * Associative array of columns
	 * @return array
	 */
	public function get_columns() {
		$columns = array();
		switch($this->booking_type){
			case 'manual':
				$columns = $this->get_manual_columns();
				break;
			case 'online':
				$columns = $this->get_online_columns();
				break;
			default: 
				$columns = array();
				break;
		}
		return $columns;
	}
	/**
	 * Columns to make sortable.
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'booking_title'    => 'ID',
			'customer'   => 'customer',
			'total' => 'total',
			'booking_status' => 'post_status',
			'date'     => array( 'date', true )
		);
	}
	/**
	* Render the bulk edit checkbox
	*
	* @param array $item
	*
	* @return string
	*/
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
		);
	}
	/**
	 * 
	 */
	function column_booking_title ($item){
		$delete_nonce = wp_create_nonce('tbs-delete_booking');
		switch($this->booking_type){
			case 'manual':
				$actions = array(
					'edit' => sprintf('<a href="%s">Edit</a>', add_query_arg( array('action' => 'edit', 'booking_id' => $item['ID']), $this->base_url) ),
					'delete' => sprintf('<a href="%s">Delete</a>', add_query_arg( array('action' => 'delete', 'booking_id' => $item['ID'], '_tbsnonce' => $delete_nonce), $this->base_url) ),
				);
				break;
			case 'online':
				$actions = array(
					'details' => sprintf('<a href="%s">Details</a>', add_query_arg( array('action' => 'details', 'booking_id' => $item['ID']), $this->base_url) ),
				);
				break;
			default: 
				$columns = array();
				break;
		}
		$order = wc_get_order($item['ID']);
		if('manual' == $this->booking_type && ('completed' == $order->get_status()) && !$order->get_meta( 'tbs_suppress_order_emails', true)) {
			$actions['view-email-record'] = sprintf('<a href="%s">Email records</a>', add_query_arg( array('action' => 'view_email_records', 'booking_id' => $item['ID']), $this->base_url) );
		}
		if('online' == $this->booking_type) {
			$actions['view-email-record'] = sprintf('<a href="%s">Email records</a>', add_query_arg( array('action' => 'view_email_records', 'booking_id' => $item['ID']), $this->base_url) );
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
	* Returns an associative array containing the bulk action
	*
	* @return array
	*/
	public function get_bulk_actions() {
		$actions = array();
		switch($this->booking_type){
			case 'manual':
				$actions = array(
					'bulk-delete' => 'Delete'
				);
				break;
			case 'online':
				$actions = array();
				break;
			default: 
				$actions = array();
				break;
		}
		return $actions;
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

		switch($this->booking_type){
			case 'manual':
				$title = sprintf(
					__( '%1$s by %2$s', TBS_i18n::get_domain_name() ),
					'<a href="' . add_query_arg( array('action' => 'edit', 'booking_id' => $order->get_id()), $this->base_url ) . '" class="row-title"><strong>#' . esc_attr( $order->get_order_number() ) . '</strong></a>',
					$username
				);
				break;
			case 'online':
				$title = sprintf(
					__( '%1$s by %2$s', TBS_i18n::get_domain_name() ),
					'<a href="' . add_query_arg( array('action' => 'details', 'booking_id' => $order->get_id()), $this->base_url ) . '" class="row-title"><strong>#' . esc_attr( $order->get_order_number() ) . '</strong></a>',
					$username
				);
				break;
			default: 
				$columns = array();
				break;
		}

		if ( $order->get_billing_email() ) {
			$title .= '<small class="meta email"><a href="' . esc_url( 'mailto:' . $order->get_billing_email() ) . '">' . esc_html( $order->get_billing_email() ) . '</a></small>';
		}

		$title .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details', TBS_i18n::get_domain_name() ) . '</span></button>';
		return $title;
	}
	/**
	 * Get bookings
	 */
	public function get_bookings(){
		$this->set_query_data();
		$this->count_bookings();
		$args = array(
			'created_via' => 'tbs_manual_booking',
			'limit' => $this->get_query_arg('per_page'),
			'page' => $this->get_query_arg( 'current_page', 1 ),
			'order' => $this->get_query_arg( 'order'),
			'orderby' => $this->get_query_arg( 'orderby'),
			'paginate' => true,
		);
		
		switch($this->booking_type){
			case 'manual':
				$args['created_via'] = 'tbs_manual_booking';
				break;
			case 'online':
				$args['created_via'] = 'checkout';
				break;
			default: 
				break;
		}
		
		if('total' == $this->get_query_arg( 'orderby') ){
			$args['meta_key'] = '_order_total';
			$args['orderby'] = 'meta_value_num';
		}
		
		if($this->get_query_arg('status', '')){
			$args['status'] = $this->get_query_arg('status');
		}
		if($past_or_upcoming = $this->get_query_arg('past_or_upcoming_bookings', '')){
			$include_bookings = tbs_get_time_based_bookings_id($past_or_upcoming, false, $args['created_via']);
			if(!$include_bookings){
				$this->set_pagination_args(array(
					'total_items' => 0,
					'total_pages' => 0,
					'per_page' => $this->get_query_arg('per_page', 3),
				));//max_num_pages
				return array();
			}
			$args['post__in'] = $include_bookings;
		}
		//Allow order by status
		if(!empty($args['orderby']) && 'post_status' == $args['orderby']){
			add_filter('posts_orderby', array($this, 'booking_orderby_clause_status'), 20, 2);
		}
		// Get orders
		$orders = wc_get_orders($args);
		
		//Allow order by status
		if(!empty($args['orderby']) && 'post_status' == $args['orderby']){
			remove_filter('posts_orderby', array($this, 'booking_orderby_clause_status'), 20, 2);
		}
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
			//$order = new WC_Order($order);
			
			$item_data['ID'] = $order->get_id();
			$item_data['status'] = $order->get_status();
			$item_data['title'] = $this->get_formatted_order_title($order);
			if('completed' == $order->get_status()){
				$item_data['delegates'] = $order->get_item_count('line_item');
				$item_data['reserves'] = '-';
			}elseif( in_array( $item_data['status'], array('cancelled', 'refunded', 'failed') )){
				$item_data['delegates'] = '-';
				$item_data['reserves'] = '-';
			}else{
				$item_data['delegates'] = '-';
				$item_data['reserves'] = $order->get_item_count('line_item');
			}
			$item_data['total'] = $order->get_total();
			$item_data['total_view'] = $order->get_total();
			$item_data['date'] = $order->get_date_created()->date( 'c' );
			$item_data['date_view'] = wc_format_datetime( $order->get_date_created() );
			$items[] = $item_data;
		}
		return $items;
	}
	
	public function booking_orderby_clause_status($orderby, $query){
		return 'post_status ' . $query->get('order');
	}
	
	/**
	 * Prepare items for the table
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		$this->items = $this->get_bookings();
	}
	
}