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
class TBS_Customers_List_Table extends WP_List_Table {
	private $query_data = array();
	private $base_url;
	/**
	 * Constructor
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		parent::__construct(array(
			'singular'	=> __('Customer', TBS_i18n::get_domain_name()),
			'plural'	=> __('Customers', TBS_i18n::get_domain_name()),
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
		$this->query_data['per_page'] = $this->get_items_per_page('customers_per_page', 12);
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
		if(isset($_REQUEST['view_type'])){
			$this->query_data['view_type'] = $_REQUEST['view_type'];
			switch($_REQUEST['view_type']){
				case 'credit_account':
					$this->query_data['account_type'] = 'credit';
					break;
			}
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
		_e( 'No customers found.', TBS_i18n::get_domain_name() );
	}
	/**
	 * Count bookings
	 */
	public function count_customers(){
		
	}
	public function get_views(){
		$views = array();
		$view_all_class = $view_credit_account_class = '';
		$current_view_type = $this->get_query_arg('view_type', '');
		if(!$current_view_type){
			$view_all_class = ' class="current" ';
		}
		if('credit_account' == $current_view_type){
			$view_credit_account_class = ' class="current" ';
		}
		
		//$count_customers = $this->count_customers();
		//$total = array_count_values($views);
		
		$total = 0;
		$all_inner_html = __('All customers', TBS_i18n::get_domain_name());
		$views['all'] = '<a '. $view_all_class .' href="'. add_query_arg(array('all_customers' => 1), $this->base_url) .'">' . $all_inner_html . '</a>';
		
		
		$credit_account_inner_html = __('All credit customers', TBS_i18n::get_domain_name());
		$views['credit_account'] = '<a '. $view_credit_account_class .' href="'. add_query_arg(array('view_type' => 'credit_account'), $this->base_url) .'">' . $credit_account_inner_html . '</a>';
		return $views;
	}
	/**
	 * Associative array of columns
	 * @return array
	 */
	public function get_columns() {
		if( current_user_can('manage_options')){
			return array(
				'cb' => '<input type="checkbox" />',
				'login' => __("Username", TBS_i18n::get_domain_name()),
				'first_name' => __("First Name", TBS_i18n::get_domain_name()),
				'last_name' => __("Last Name", TBS_i18n::get_domain_name()),
				'email' => __("Email", TBS_i18n::get_domain_name()),
			);
		}else{
			return array(
				'login' => __("Username", TBS_i18n::get_domain_name()),
				'first_name' => __("First Name", TBS_i18n::get_domain_name()),
				'last_name' => __("Last Name", TBS_i18n::get_domain_name()),
				'email' => __("Email", TBS_i18n::get_domain_name()),
			);
		}
	}
	/**
	 * Columns to make sortable.
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'login'    => 'login',
			'first_name'    => 'first_name',
			'last_name'   => 'last_name',
			'email' => 'email',
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
	function column_login ($user){
		$delete_nonce = wp_create_nonce('tbs_delete_customer_' . $user['ID']);
		$actions = array(
			'edit' => sprintf('<a href="%s">Edit</a>', add_query_arg( array('action' => 'edit', 'customer_id' => $user['ID']), $this->base_url) ),
		);
		if( current_user_can('manage_options')){
			$actions['delete'] = sprintf(
				'<a href="%s">Delete</a>', 
				add_query_arg( array('action' => 'delete', 'customer_id' => $user['ID'], '_tbsnonce' => $delete_nonce), $this->base_url) 
			);
		}
		
		echo sprintf('<strong><a href="%s">%s</a></strong>', add_query_arg( array('action' => 'edit', 'customer_id' => $user['ID']), $this->base_url), $user['login'] );
		echo $this->row_actions( $actions );
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
	* Returns an associative array containing the bulk action
	*
	* @return array
	*/
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => 'Delete'
		);
		return $actions;
	}
	/**
	 * Get bookings
	 */
	public function get_customers(){
		$this->set_query_data();
		$meta_query = array();
		
		if($this->get_query_arg('account_type', '')){
			$meta_query[] = array(
				'key' => 'account_type',
				'value' => $this->get_query_arg('account_type', ''),
				'compare' => '='
			);
		}
		
		$args = array(
			'number' => $this->get_query_arg('per_page', 12),
			'paged' => $this->get_query_arg( 'current_page', 1 ),
			'orderby' => $this->get_query_arg( 'orderby'),
			'order' => $this->get_query_arg( 'order'),
			'count_total' => true,
			'role' => 'customer',
		);
		if('first_name' == $this->get_query_arg( 'orderby')){
			$args['meta_key'] = 'first_name';
			$args['orderby'] = 'meta_value';
		}
		if('last_name' == $this->get_query_arg( 'orderby')){
			$args['meta_key'] = 'last_name';
			$args['orderby'] = 'meta_value';
		}
		
		if(count($meta_query) > 0){
			$meta_query['releation'] = 'AND';
			$args['meta_query'] = $meta_query;
		}
		
		$customers_query = new WP_User_Query($args);
		
		if($customers_query->get_total() < 1){
			$this->set_pagination_args(array(
				'total_items' => 0,
				'total_pages' => 0,
				'per_page' => $this->get_query_arg('per_page', 12),
			));
			return array();
		}
		$this->set_pagination_args(array(
			'total_items' => $customers_query->get_total(),
			'total_pages' => ceil( $customers_query->get_total() / $this->get_query_arg('per_page', 12) ),
			'per_page' => $this->get_query_arg('per_page', 12)
		));
		$customers = array();
		foreach($customers_query->get_results() as $user ){
			$customer_data = array(
				'ID'	=> $user->ID,
				'login' => $user->user_login,
				'email' => $user->user_email,
				'first_name' => get_user_meta($user->ID, 'first_name', true),
				'last_name' => get_user_meta($user->ID, 'last_name', true),
			);
			$customers[] = $customer_data;
		}
		return $customers;
	}
	
	/**
	 * Prepare items for the table
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		$this->items = $this->get_customers();
	}
	
}