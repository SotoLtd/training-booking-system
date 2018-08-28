<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TBS_Trainers_Report extends TBS_Report {
	/**
	 *
	 * @var int Trainer ID
	 */
	private $trainer_id;
	/**
	 * Constructor of 
	 * @param int $trainer_id
	 */
	public function __construct($trainer_id) {
		$trainer = get_post($trainer_id);
		if($trainer){
			$this->object = $trainer;
			$this->trainer_id = $this->object->ID;
			$this->recipient = get_post_meta($this->trainer_id, 'trainer_email', true);
		}
		parent::__construct();
	}
	/**
	 * Get trainer ID
	 * @return int
	 */
	public function get_trainer_id(){
		return $this->trainer_id;
	}
	/**
	 * Get CSV headers
	 * @return array
	 */
	public function get_headers(){
		return array(
			'Course date',
			'Course name',
			'Course location',
			'Course time',
			'Number of Delegates',
			'Name of company delegates are from',
			'Onsite location address',
			'Onsite contact',
			'Onsite special instructions',
		);
	}
	
	/**
	 * Get items/records
	 */
	public function get_items(){
		return $this->items;
	}
	/**
	 * Prepare records/items
	 */
	public function prepare_items(){
		global $wpdb;
		// Prepare and get course dates data
		$datr_sql = $this->prepare_dates_query();
		$course_dates_data = $wpdb->get_results($datr_sql, OBJECT_K);
		if(!$course_dates_data){
			return;
		}
		remove_filter( 'the_title', 'wptexturize'   );
		remove_filter( 'the_title', 'convert_chars' );
		// Get course order dates
		$order_data = $this->prepare_orders_data( array_keys($course_dates_data));
		
		foreach($course_dates_data as $course_date_id => $cd_data) {
			$item_data = array(
				'course_date' => '',
				'course_name' => '',
				'course_location' => '',
				'course_time' => '',
				'course_delegates' => '',
				'delegates_company' => '',
				'onsite_locaion' => '',
				'onsite_contact' => '',
				'onsite_special_instructions' =>'',
			);
			$item_data['course_date'] = date('l d M Y', $cd_data->start_date);
			$item_data['course_name'] = get_the_title($cd_data->course_id);
			if('tbs_custom' == $cd_data->location_id){
				$item_data['course_location'] =  'Onsite';
				$item_data['onsite_locaion'] = !empty($order_data[$course_date_id]['onsite_address']) ? $order_data[$course_date_id]['onsite_address'] : $cd_data->custom_locaion;
			}else{
				$item_data['course_location'] = $cd_data->location_id ? str_replace('–', '-', get_the_title($cd_data->location_id)) : '';
			} 
			
			$item_data['course_time'] = $cd_data->course_time;
			$item_data['course_delegates'] = $this->get_delegates_count($course_date_id);
			if(isset($order_data[$course_date_id]['company'])){
				$item_data['delegates_company'] = $order_data[$course_date_id]['company'];
			}
			if(isset($order_data[$course_date_id]['contact_name_phone'])){
				$item_data['onsite_contact'] = $order_data[$course_date_id]['contact_name_phone'];
			}
			if(isset($order_data[$course_date_id]['speical_instructions'])){
				$item_data['onsite_special_instructions'] = $order_data[$course_date_id]['speical_instructions'];
			}
			$this->items[] = $item_data;
			remove_filter( 'the_title', 'wptexturize'   );
			remove_filter( 'the_title', 'convert_chars' );
		}
	}
	/**
	 * Prepare sql query to retrieve 
	 * @global obj $wpdb
	 */
	public function prepare_dates_query(){
		global $wpdb;
		$time_now = time();
		$time_to = $time_now + 1209600;//2*7*24*60*60 = two weeks
		
		$p_cols = "DISTINCT pcd.ID";
		$p_table = "{$wpdb->posts} AS pcd";
		$p_where = "pcd.post_type = 'product' AND pcd.post_status = 'publish'";
		
		$tr_join = "LEFT JOIN {$wpdb->postmeta} as mt_tr ON pcd.ID = mt_tr.post_id";
		$tr_where = $wpdb->prepare("(mt_tr.meta_key = '_tbs_trainer' AND mt_tr.meta_value='%d')", $this->trainer_id);
		
		$sd_join = "LEFT JOIN {$wpdb->postmeta} as mt_sd ON pcd.ID = mt_sd.post_id";
		$sd_cols = "mt_sd.meta_value AS start_date";
		$sd_where= "(mt_sd.meta_key = '_tbs_start_date' AND mt_sd.meta_value >= {$time_now} AND mt_sd.meta_value < {$time_to})";
		$sd_order = "mt_sd.meta_value ASC";
		
		$c_join = "LEFT JOIN {$wpdb->postmeta} as mt_c ON pcd.ID = mt_c.post_id AND mt_c.meta_key = '_tbs_course'";
		$c_cols = "mt_c.meta_value AS course_id";
		$c_where= "(mt_c.meta_key IS NULL OR mt_c.meta_key = '_tbs_course')";
		
		$l_join = "LEFT JOIN {$wpdb->postmeta} as mt_l ON pcd.ID = mt_l.post_id AND mt_l.meta_key = '_tbs_location'";
		$l_cols = "mt_l.meta_value AS location_id";
		$l_where= "(mt_l.meta_key IS NULL OR mt_l.meta_key = '_tbs_location')";
		
		$cl_join = "LEFT JOIN {$wpdb->postmeta} as mt_cl ON pcd.ID = mt_cl.post_id AND mt_cl.meta_key = '_tbs_custom_location'";
		$cl_cols = "mt_cl.meta_value AS custom_locaion";
		$cl_where= "(mt_cl.meta_key IS NULL OR mt_cl.meta_key = '_tbs_custom_location')";
		
		$t_join = "LEFT JOIN {$wpdb->postmeta} as mt_t ON pcd.ID = mt_t.post_id AND mt_t.meta_key = '_tbs_start_finish_time'";
		$t_cols = "mt_t.meta_value AS course_time";
		$t_where= "(mt_t.meta_key IS NULL OR mt_t.meta_key = '_tbs_start_finish_time')";
		
		$fields = "{$p_cols}, {$sd_cols}, {$c_cols}, {$l_cols}, {$cl_cols}, {$t_cols}";
		
		$joins = "FROM {$p_table} {$tr_join} {$sd_join} {$c_join} {$l_join} {$cl_join} {$t_join}";
		$where = "WHERE (1=1) AND {$p_where} AND {$tr_where} AND {$sd_where}";
		$order = "ORDER BY {$sd_order}";
		
		$sql = "SELECT {$fields} {$joins} {$where} {$order}";
		
		return $sql;
	}
	
	/**
	 * 
	 * @global obj $wpdb
	 * @param array $course_dates
	 * @return array
	 */
	public function prepare_orders_data($course_date_ids = array()){
		global $wpdb;
		$sql = "";
		$sql .= "SELECT DISTINCT o.ID, order_item_meta.meta_value AS course_date_id, order_meta_company.meta_value AS company, order_meta.meta_value AS onsite_data FROM {$wpdb->posts} as o";
		$sql .= " LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON o.ID = order_items.order_id";
		$sql .= " LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id";
		$sql .= " LEFT JOIN {$wpdb->postmeta} AS order_meta_company ON o.ID = order_meta_company.post_id AND order_meta_company.meta_key = '_billing_company'";
		$sql .= " LEFT JOIN {$wpdb->postmeta} AS order_meta ON o.ID = order_meta.post_id AND order_meta.meta_key = 'mbs_onsite_data'";
		$sql .= " WHERE (1 = 1)";
		$sql .= " AND o.post_type = 'shop_order' AND o.post_status = 'wc-completed'";
		$sql .= " AND (order_items.order_item_type = 'line_item')";
		$sql .= " AND (order_item_meta.meta_key = '_product_id')";
		$sql .= " AND order_item_meta.meta_value IN (". implode( ',', $course_date_ids ) .")";
		$sql .= " AND (order_meta_company.meta_key IS NULL OR order_meta_company.meta_key = '_billing_company')";
		$sql .= " AND (order_meta.meta_key IS NULL OR order_meta.meta_key = 'mbs_onsite_data')";
		$order_data = $wpdb->get_results($sql, OBJECT_K);
		if(!$order_data){
			return array();
		}
		
		$course_date_company = array();
		$course_date_onsite_data = array();
		foreach($order_data as $order_id => $data){
			if(!isset($course_date_company[$data->course_date_id])){
				$course_date_company[$data->course_date_id] = array();
			}
			if($data->company){
				$course_date_company[$data->course_date_id][] = $data->company;
			}
			$onsite_data = maybe_unserialize($data->onsite_data);
			if(!$onsite_data){
				continue;
			}
			foreach($onsite_data as $course_date_id => $osc_data){
				$osc_data = wp_parse_args( $osc_data, array(
					'address' => '',
					'named_contact' => '',
					'named_contact_phone' => '',
					'parking_available' => 'no',
					'location_requirements' => '',
					'quiet_training_room' => 'no',
					'delegates_tables_chairs' => 'no',
					'trainers_laptop_power' => 'no',
				) );
				if(!isset($course_date_onsite_data[$course_date_id])){
					$course_date_onsite_data[$course_date_id] = array(
						'onsite_address' => array(),
						'contact_name_phone' => array(),
						'speical_instructions' => array(),
					);
				}
				$course_date_onsite_data[$course_date_id]['onsite_address'][] = $osc_data['address'];
				$course_date_onsite_data[$course_date_id]['contact_name_phone'][] = $osc_data['named_contact'] . ' - ' . $osc_data['named_contact_phone'];
				$course_date_onsite_data[$course_date_id]['speical_instructions'][] = $osc_data['location_requirements'];
			}
		}
		$return_data = array();
		foreach($course_date_onsite_data as $course_date_id => $cdata){
			if(!isset($return_data[$course_date_id])){
				$return_data[$course_date_id] = array();
			}
			$return_data[$course_date_id]['onsite_address'] = implode(',', $course_date_onsite_data[$course_date_id]['onsite_address']);
			$return_data[$course_date_id]['contact_name_phone'] = implode(',', $course_date_onsite_data[$course_date_id]['contact_name_phone']);
			$return_data[$course_date_id]['speical_instructions'] = implode(',', $course_date_onsite_data[$course_date_id]['speical_instructions']);
		}
		foreach($course_date_company as $course_date_id => $company){
			if(!isset($return_data[$course_date_id])){
				$return_data[$course_date_id] = array();
			}
			$return_data[$course_date_id]['company'] = implode(',', $company);
		}
		return $return_data;
	}
	
	/**
	 * 
	 * @return int
	 */
	public function get_delegates_count($course_date_id){
		$args = array(
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'tbs_course_dates',
					'value' => $course_date_id,
					'compare' => '='
				),
			),
		);
		$delegates_query = new WP_User_Query($args);
		return $delegates_query->get_total();
	}
	/**
	 * 
	 * @param obj $date_data
	 * @return bool
	 */
	public function filter_onsite_dates($date_data) {
		return 'tbs_custom' == $date_data->location_id;
	}
	
	/**
	 * Get report file dir path
	 * @return string
	 */
	public function get_report_temp_path(){
		return WP_CONTENT_DIR . '/uploads/tbs-reports/trainers/';
	}
	/**
	 * Get report file name
	 * @return string
	 */
	public function get_report_file_name(){
		return 'report-' . $this->object->post_name . '-' . date('Ymdhis') . '.csv';
	}
	/**
	 * Get email subject
	 * @return string
	 */
	public function get_email_subject(){
		return 'TrainingSocieti Ltd.: Trainer report ' . date('Y-m-d h:i:s');
	}
	/**
	 * Get email body
	 * @return string
	 */
	public function get_email_body(){
		return 'Please find the attachement for course date report';
	}
	
}