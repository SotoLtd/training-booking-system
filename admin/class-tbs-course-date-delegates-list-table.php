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
class TBS_Course_Date_Delegates_List_Table extends WP_List_Table {
	private $query_data = array();
	private $course_date;
	/**
	 * Constructor
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		parent::__construct(array(
			'singular'	=> __('Delegate', TBS_i18n::get_domain_name()),
			'plural'	=> __('Delegates', TBS_i18n::get_domain_name()),
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
			$this->query_data['orderby'] = 'first_name';
		}
		if(isset($_REQUEST['order'])){
			$this->query_data['order'] = $_REQUEST['order'];
		}else{
			$this->query_data['order'] = 'ASC';
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
		_e( 'No delegates found.', TBS_i18n::get_domain_name() );
	}
	/**
	 * Associative array of columns
	 * @return array
	 */
	public function get_columns() {
		return array(
			'id'    => 'id',
			//'login' => __("Username", TBS_i18n::get_domain_name()),
			'first_name' => __("First Name", TBS_i18n::get_domain_name()),
			'last_name' => __("Last Name", TBS_i18n::get_domain_name()),
			'company' => __("Company", TBS_i18n::get_domain_name()),
			'email' => __("Email", TBS_i18n::get_domain_name()),
			'notes' => __("Notes", TBS_i18n::get_domain_name()),
		);
	}
	/**
	 * Columns to make sortable.
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'id'    => 'id',
			'first_name'    => 'first_name',
			'last_name'   => 'last_name',
			'email' => 'email',
		);
	}
	/**
	 * Get column output
	 * @param type $item
	 * @param type $column_name
	 * @return type
	 */
	public function column_default( $delegate, $column_name ) {
		switch($column_name){
			case 'id':
				return '#' . $delegate->get_id();
			case 'first_name': 
				return $delegate->get_first_name();
			case 'last_name': 
				return $delegate->get_last_name();
			case 'company': 
				return $delegate->get_company();
			case 'email': 
				return $delegate->has_email() ? $delegate->get_email(true) : '';
			case 'notes': 
				return $delegate->get_notes();
			default: 
				return '';
				
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
		$title = sprintf(
			__( '%1$s by %2$s', TBS_i18n::get_domain_name() ),
			'<a href="' . TBS_Admin_Manual_Bookings::url( 'edit', array( 'booking_id' => $order->get_id() ) ) . '" class="row-title"><strong>#' . esc_attr( $order->get_order_number() ) . '</strong></a>',
			$username
		);

		if ( $order->get_billing_email() ) {
			$title .= '<small class="meta email"><a href="' . esc_url( 'mailto:' . $order->get_billing_email() ) . '">' . esc_html( $order->get_billing_email() ) . '</a></small>';
		}

		$title .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details', TBS_i18n::get_domain_name() ) . '</span></button>';
		return $title;
	}
	/**
	 * Get bookings
	 */
	public function get_delegates(){
		$this->set_query_data();
		
		$args = array(
			'number' => $this->get_query_arg('per_page', 12),
			'pages' => $this->get_query_arg( 'current_page', 1 ),
			'orderby' => $this->get_query_arg( 'orderby'),
			'order' => $this->get_query_arg( 'order'),
			'count_total' => true,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'tbs_course_dates',
					'value' => $this->course_date->get_id(),
					'compare' => '='
				),
			),
		);
		if('first_name' == $this->get_query_arg( 'orderby')){
			$args['meta_key'] = 'first_name';
			$args['orderby'] = 'meta_value';
		}
		if('last_name' == $this->get_query_arg( 'orderby')){
			$args['meta_key'] = 'last_name';
			$args['orderby'] = 'meta_value';
		}
		
		$delegates_query = new WP_User_Query($args);
		
		if($delegates_query->get_total() < 1){
			$this->set_pagination_args(array(
				'total_items' => 0,
				'total_pages' => 0,
				'per_page' => $this->get_query_arg('per_page', 12),
			));
			return array();
		}
		$this->set_pagination_args(array(
			'total_items' => $delegates_query->get_total(),
			'total_pages' => ceil( $delegates_query->get_total() / $this->get_query_arg('per_page', 12) ),
			'per_page' => $this->get_query_arg('per_page', 12)
		));
		$delegates = array();
		foreach($delegates_query->get_results() as $user ){
			$delegates[] = new TBS_Delegate($user);
		}
		return $delegates;
	}
	
	/**
	 * Prepare items for the table
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		$this->items = $this->get_delegates();
	}
	
}