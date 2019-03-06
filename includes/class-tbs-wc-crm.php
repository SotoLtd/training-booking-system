<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
class TBS_WC_CRM {

	private static $order_processed = false;

	/**
	 * Core singleton class
	 * @var self - pattern realization
	 */
	private static $_instance;

	private function __construct() {
		add_action('wc_crm_loaded', array($this, 'init'));
	}

	/**
	 * Get the instance of CR_VCE_Manager
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( !( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function init() {
		//add_action('save_post', array($this, 'update_shop_order'), 999, 2);
		add_action('woocommerce_new_order', array($this, 'tbh_crm_booking_update'));
		add_action('woocommerce_update_order', array($this, 'tbh_crm_booking_update'));
		add_action('tbh_wc_crm_booking_update_delegates', array($this, 'booking_update_delegates'));

		add_action('wp_ajax_tbh_crm_reload_accounts', array($this, 'tbh_crm_reload_accounts'));
	}
	public function tbh_crm_reload_accounts(){
		check_admin_referer('tbs_tools_reload_accounts', '_tbsnonce');
		if(!current_user_can('manage_bookings')){
			wp_die( "You don't have sufficient permission.", __( 'WordPress Failure Notice' ), 403 );
		}
		$current = !empty($_POST['current']) ? $_POST['current'] : 0;
		$limit = !empty($_POST['limit']) ? $_POST['limit'] : 0;
		$current = absint($current);
		$limit = absint($limit);
		if($limit <= 0){
			wp_send_json(array(
				'status' => 'NOTOK',
			));
		}
		$offset = ($current - 1) * $limit;
		global $wpdb;
		$query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'shop_order' AND post_status = 'wc-completed' ORDER BY ID ASC LIMIT %d OFFSET %d", $limit, $offset);
		$order_ids = $wpdb->get_col($query);
		foreach ($order_ids as $order_id){
			$this->process_order($order_id);
		}
		wp_send_json(array(
			'status' => 'OK',
		));
	}
	public function booking_update_delegates($booking_id) {
		// $post_id and $post are required
		if (empty($booking_id)) {
			return;
		}
		self::$order_processed = true;
		$this->process_order($booking_id);
	}
	public function tbh_crm_booking_update($booking_id) {
		// $post_id and $post are required
		if (empty($booking_id) || self::$order_processed) {
			return;
		}
		$this->process_order($booking_id);
	}
	public function update_shop_order($post_id, $post) {
		// $post_id and $post are required
		if (empty($post_id) || empty($post)) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if (defined('DOING_AUTOSAVE') || is_int(wp_is_post_revision($post)) || is_int(wp_is_post_autosave($post))) {
			return;
		}

		// Check the nonce
		if (empty($_POST['woocommerce_meta_nonce']) || !wp_verify_nonce($_POST['woocommerce_meta_nonce'], 'woocommerce_save_data')) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if (empty($_POST['post_ID']) || $_POST['post_ID'] != $post_id) {
			return;
		}

		$this->process_order($post_id);
	}

	public function process_order($order_id){
		$order = wc_get_order( $order_id );
		$customer_id = $order->get_customer_id();
		$company = $order->get_billing_company();
		if(!$customer_id || !$company){
			return false;
		}
		$crm_customer = wc_crm_get_customer($customer_id, 'user_id');
		if(!$crm_customer){
			return false;
		}
		$account_id = $this->maybe_create_account($company);
		if($account_id){
			$this->update_customer_account_meta($crm_customer->c_id, $account_id);

			$delegates_ids = $this->get_booking_delegates_id($order_id);
			foreach($delegates_ids as $del_id){
				$crm_del = wc_crm_get_customer($del_id, 'user_id');
				$this->update_customer_account_meta($crm_del->c_id, $account_id);
			}
		}
		wc_crm_clear_transient();
	}
	public function update_customer_account_meta($customer_id, $account_id){
		$customer_account_id = wc_crm_get_customer_account($customer_id);
		if($customer_account_id != $account_id ){
			delete_post_meta($customer_account_id, '_wc_crm_customer_id', $customer_id);
			add_post_meta($account_id, '_wc_crm_customer_id', $customer_id);
		}
	}
	public function get_booking_delegates_id($order_id){
		$order_delegates_ids = get_post_meta($order_id, 'delegates', true);

		if( !is_array( $order_delegates_ids ) || 0 === count($order_delegates_ids)){
			$order_delegates_ids = array();
		}
		$ids = array();
		foreach($order_delegates_ids as $course_date_id => $d_ids){
			if(!is_array($d_ids) || count($d_ids) == 0){
				continue;
			}
			foreach ($d_ids as $d_id){
				$ids[] = $d_id;
			}
		}
		return $ids;
	}
	public function maybe_create_account($name) {
		$account_id = $this->get_account_id_by_name($name);
		if($account_id){
			return $account_id;
		}
		$account_id = wp_insert_post(array(
			'post_type' => 'wc_crm_accounts',
			'post_title' => $name,
			'post_status' => 'publish'
		));
		return $account_id;
	}
	public function get_account_id_by_name($name){
		global $wpdb;
		$sql = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'wc_crm_accounts' AND post_status = 'publish' AND post_title=%s", $name);
		return $wpdb->get_var($sql);
	}

}


TBS_WC_CRM::get_instance();