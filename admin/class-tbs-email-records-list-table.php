<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * TBS_Email_Records_List_Table
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/admin
 * @author     TTS <mmhasaneee@gmail.com>
 */
class TBS_Email_Records_List_Table extends WP_List_Table {
	private $query_data = array();
	public $order;
	private $booking_type = 'manual';
	private $base_url;
	/**
	 * Constructor
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		parent::__construct(array(
			'singular'	=> __('Email Record', TBS_i18n::get_domain_name()),
			'plural'	=> __('Email Records', TBS_i18n::get_domain_name()),
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
	 * Set Order
	 * @param WC_Order $order
	 */
	public function set_order( WC_Order $order){
		$this->order = $order;
	}
	public function can_be_shown_manual(){
		$data_entry_comleted = 'completed' ==  $this->order->get_status();
		if('manual' == $this->booking_type && $data_entry_comleted && !$this->order->get_meta( 'tbs_suppress_order_emails', true)) {
			return true;
		}
		return false;
	}
	/**
	 * Set query all query args
	 */
	public function set_query_data(){
		$this->query_data['per_page'] = $this->get_items_per_page('email_records_per_page', 12);
		$this->query_data['current_page'] = $this->get_pagenum();
		if(isset($_REQUEST['orderby'])){
			$this->query_data['orderby'] = trim($_REQUEST['orderby']);
		}else{
			$this->query_data['orderby'] = 'date_created';
		}
		if(isset($_REQUEST['order'])){
			$this->query_data['order'] = $_REQUEST['order'];
		}else{
			$this->query_data['order'] = 'DESC';
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
		_e( 'No records found.', TBS_i18n::get_domain_name() );
	}
	
	public function get_views(){
		$views = array();
		
		return $views;
	}
	/**
	 * Associative array of columns
	 * @return array
	 */
	public function get_columns() {
		return array(
			//'type' => __("Type", TBS_i18n::get_domain_name()),
			'date' => __("Date", TBS_i18n::get_domain_name()),
			'note' => __("Note", TBS_i18n::get_domain_name()),
			//'sentby' => __("Sender", TBS_i18n::get_domain_name()),
		);
	}
	/**
	 * Get column output
	 * @param type $item
	 * @param type $column_name
	 * @return type
	 */
	public function column_default( $record, $column_name ) {
		switch($column_name){
			case 'type':
				return $record['type'];
			case 'date':
				return $record['date'];
			case 'note':
				return $record['note'];
			default: 
				return '';
				
		}
	}

	/**
	 * Columns to make sortable.
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'date'    => 'date_created',
		);
	}
	
	public function get_email_records(){
		$this->set_query_data();
		
		$args = array(
			'post_id' => $this->order->get_id(),
			'type' => 'order_note',
			'status' => 'approve',
			'count'  => true,
			'offset' => $this->get_query_arg('per_page') * ($this->get_query_arg( 'current_page', 1 ) - 1),
			'order' => $this->get_query_arg( 'order'),
			'orderby' => $this->get_query_arg( 'orderby'),
		);
		$args['meta_query'] = array(
			array(
				'key'     => 'tbs_email_type',
				'value'   => array('booking_confirmation', 'joining_instructions'),
				'compare' => 'IN',
			),
		);
		
		remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

		$count_records = get_comments( $args );
		
		if(!$count_records){
			$this->set_pagination_args(array(
				'total_items' => 0,
				'total_pages' => 0,
				'per_page' => $this->get_query_arg('per_page', 12),
			));
			return array();
		}
		
		$args['number'] = $this->get_query_arg('per_page');
		unset($args['count']);
		
		$records = get_comments($args);

		add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
		
		if(!$records){
			$this->set_pagination_args(array(
				'total_items' => 0,
				'total_pages' => 0,
				'per_page' => $this->get_query_arg('per_page', 12),
			));
			return array();
		}
		$this->set_pagination_args(array(
			'total_items' => $count_records,
			'total_pages' => ceil( $count_records / $this->get_query_arg('per_page', 12) ),
			'per_page' => $this->get_query_arg('per_page', 12)
		));
		$records_data = array();
		foreach( $records as $r){
			$records_data[] = array(
				'id'   => (int) $r->comment_ID,
				'date' => $r->comment_date,
				'note' => $r->comment_content,
				'type' => get_comment_meta($r->comment_ID, 'tbs_email_type', true)
			);
		}
		return $records_data;
	}
	
	/**
	 * Prepare items for the table
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		$this->items = $this->get_email_records();
	}
}