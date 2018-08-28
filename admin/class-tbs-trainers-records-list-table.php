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
class TBS_Trainers_Records_List_Table extends WP_List_Table {
	private $query_data = array();
	private $base_url;
	/**
	 * Constructor
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		parent::__construct(array(
			'singular'	=> __('Trainer record', TBS_i18n::get_domain_name()),
			'plural'	=> __('Trainer records', TBS_i18n::get_domain_name()),
			'ajax'		=> false,
		));
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
		$this->query_data['per_page'] = $this->get_items_per_page('trainers_records_per_page', 12);
		$this->query_data['current_page'] = $this->get_pagenum();
		
		if(isset($_REQUEST['orderby'])){
			$this->query_data['orderby'] = trim($_REQUEST['orderby']);
		}else{
			$this->query_data['orderby'] = 'start_date';
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
		_e( 'No records found.', TBS_i18n::get_domain_name() );
	}
	/**
	 * Associative array of columns
	 * @return array
	 */
	public function get_columns() {
			return array(
				'start_date' => __("Course Date", TBS_i18n::get_domain_name()),
				'title' => __("Course Name", TBS_i18n::get_domain_name()),
				'location' => __("Course Location", TBS_i18n::get_domain_name()),
				'time' => __("Course Time", TBS_i18n::get_domain_name()),
				'delegates' => __("Delegates", TBS_i18n::get_domain_name()),
				'company' => __("Company", TBS_i18n::get_domain_name()),
				'onsite_address' => __("Onsite Address", TBS_i18n::get_domain_name()),
				'onsite_contact' => __("Onsite Contact", TBS_i18n::get_domain_name()),
				'onsite_instructions' => __("Onsite ", TBS_i18n::get_domain_name()),
			);
	}
	/**
	 * Columns to make sortable.
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'start_date'    => 'start_date',
			'title'    => 'title',
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
	 * Get column output
	 * @param type $item
	 * @param type $column_name
	 * @return type
	 */
	public function column_default( $user, $column_name ) {
		switch($column_name){
			case 'first_name': 
				return $user['first_name'];
			case 'last_name': 
				return $user['last_name'];
			case 'email': 
				return '<a href="mailto:'. esc_url($user['email']) .'">'. $user['email'] .'</a>';
			default: 
				return '';
				
		}
	}
	/**
	 * Get bookings
	 */
	public function get_records(){
		return array();
	}
	
	/**
	 * Prepare items for the table
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		$this->items = $this->get_records();
	}
	
}