<?php

class TBS_Course_Date {
	protected $woo_product;
	protected $id;
	protected $course_id;
	protected $is_private;
	protected $start_date;
	protected $duration;
	protected $start_finish_time;
	protected $end_date;
	protected $price;
	protected $max_delegates;
	protected $delegate_stock;
	protected $trainer;
	protected $location;
	protected $custom_location;
	protected $joining_instruction;
	protected $map;
	protected $extra_data = array();




	public function __construct($course_date) {
		$this->woo_product = wc_get_product($course_date);
		if($this->woo_product && $this->woo_product->get_meta('_tbs_course', true)){
			$this->set_course_data();
		}
	}
	private function set_course_data(){
		$this->id = $this->woo_product->get_id();
		$this->course_id = $this->woo_product->get_meta('_tbs_course', true);
		$this->is_private = $this->woo_product->get_meta('_tbs_is_private', true);
		$this->start_date = $this->woo_product->get_meta('_tbs_start_date', true);
		$this->end_date = $this->woo_product->get_meta('_tbs_end_date', true);
		$this->duration = $this->woo_product->get_meta('_tbs_duration', true);
		$this->start_finish_time = $this->woo_product->get_meta('_tbs_start_finish_time', true);
		$this->trainer = $this->woo_product->get_meta('_tbs_trainer', true);
		$this->location = $this->woo_product->get_meta('_tbs_location', true);
		$this->custom_location = $this->woo_product->get_meta('_tbs_custom_location', true);
		$this->joining_instruction = $this->woo_product->get_meta('_tbs_joining_instruction', true);
		$this->map = $this->woo_product->get_meta('_tbs_map', true);
		$this->price = $this->woo_product->get_price();
		$this->max_delegates = $this->woo_product->get_meta('_tbs_max_delegates');
		$this->delegate_stock = $this->woo_product->get_stock_quantity();
	}
	private function reset_course_data(){
		$this->id = null;
		$this->course_id = null;
		$this->is_private = null;
		$this->start_date = null;
		$this->end_date = null;
		$this->duration = null;
		$this->start_finish_time = null;
		$this->trainer = null;
		$this->location = null;
		$this->custom_location = null;
		$this->joining_instruction = null;
		$this->map = null;
		$this->price = null;
		$this->max_delegates = null;
		$this->delegate_stock = null;
	}
	public function reload(){
		$this->woo_product = wc_get_product($this->id);
		if($this->woo_product && $this->woo_product->get_meta('_tbs_course', true)){
			$this->set_course_data();
		}else{
			$this->reset_course_data();
		}
	}
	public function exists(){
		return (bool) $this->id;
	}
	public function get_json_model(){
		return array(
			'id'=> $this->get_id(),
			'courseID' => $this->get_course_id(),
			'isPrivate' => $this->is_private(),
			'startDate' => $this->get_start_date_raw(),
			'endDate' => $this->get_end_date_raw(),
			'durtation'	=> $this->get_duration(),
			'price'		=> $this->get_price_formatted(),
			'priceVal'	=> $this->get_price(),
			'maxDelegates' => $this->get_max_delegates(),
			'places'	=> $this->get_places(),
			'isAccridated' => $this->is_accredited(),
			'title'		=> $this->get_date_formatted(),
			'courseDateTitle'=> $this->get_course_title_with_date(),
			'coursePermalink'	=> $this->get_course_permalink(),
			'trainerName' => $this->get_trainers_name(),
			'trainerID' => $this->get_trainer_id(),
			'location' => $this->get_location_short_name(),
		);
	}
	public function get_woo_porduct(){
		return $this->woo_product;
	}
	public function get_id(){
		return (int)$this->id;
	}
	public function get_product_obj(){
		return $this->woo_product;
	}
	public function get_course_id(){
		return (int)$this->course_id;
	}
	public function get_course_permalink(){
		return get_permalink($this->course_id);
	}
	public function get_course_title(){
		return get_the_title($this->course_id);
	}
	public function get_course_title_with_date($show_private = false){
		return get_the_title($this->course_id) . ': ' . $this->get_date_formatted($show_private ) ;
	}
	public function get_start_date_raw(){
		return (int)$this->start_date;
	}
	
	public function get_end_date_raw(){
		return (int)$this->end_date;
	}
	public function get_months_until_start(){
		$start = new DateTime(date('Y-m-d 00:00:00', $this->start_date));
		$now = new DateTime( date('Y-m-d', time()));
		$diff  = $now->diff($start);
		$months = $diff->y * 12 + $diff->m;
		if($diff->d > 0){
			$months += 1;
		}
		return $months;
	}
	public function get_start_finish_time(){
		$time = $this->start_finish_time;
		if(!$time){
			$time = get_post_meta($this->course_id, 'course_time', true);
		}
		return $time;
	}
	public function get_permalink(){
		return trailingslashit( $this->get_course_permalink() ) . 'date/' . $this->id . '/#course-dates-list';
	}
	public function get_date_formatted($show_private = false){
		$date =  date('D d M Y', $this->start_date);
		if($this->duration > 1 && $this->end_date){
			$date .= ' to ' . date('D d M Y', $this->end_date);
		}
		if($show_private && $this->is_private()){
			$date .= ' [Private]';
		}
		return $date;
	}
	public function get_duration(){
		return $this->duration ? $this->duration : get_post_meta($this->course_id, 'duration', true);
	}
	public function get_duration_formatted(){
		return sprintf(_n('%s day', '%s days', $this->get_duration(), TBS_i18n::get_domain_name()), $this->get_duration());
	}
	public function get_price(){
		return (float)$this->price;
	}
	public function get_price_formatted(){
		return $this->woo_product->get_price_html() . __(' + VAT', TBS_i18n::get_domain_name());
	}
	public function get_places_formatted(){
		return $this->delegate_stock . '/' . $this->max_delegates;
	}
	public function get_places(){
		return (int)$this->delegate_stock;
	}
	public function get_max_delegates(){
		return (int)$this->max_delegates;
	}
	public function is_sold_out(){
		return !$this->woo_product->is_in_stock();
	}
	public function is_private(){
		return (bool) $this->is_private;
	}
	public function get_trainer(){
		/**
		 * @todo Create Model class for Trainer and return the instance of this class
		 */
		return get_post($this->trainer);
	}
	public function get_trainer_id(){
		return (int)$this->trainer;
	}
	public function get_trainers_name(){
		return get_the_title($this->trainer);
	}
	public function is_accredited(){
		return 'yes' == get_post_meta( $this->course_id, 'is_accredited', true );
	}
	public function is_onsite(){
		return $this->has_custom_address();
	}
	public function get_location(){
		/**
		 * @todo Create Model class for Trainer and return the instance of this class
		 */
		$location_post =  get_post($this->get_location_id());
		if ( !$location_post || is_wp_error( $location_post ) ) {
			return false;
		}
		$tobj		 = new stdClass();
		$tobj->ID	 = $location_post->ID;
		$tobj->short_name	 = $location_post->post_title;
		$tobj->full_address	 = $location_post->post_content;
		if ( has_post_thumbnail( $location_post ) ) {
			$post_thumbnail_id	 = get_post_thumbnail_id( $location_post );
			$tobj->photo		 = wp_get_attachment_image( $location_post, 'full', false );
		} else {
			$tobj->photo = '';
		}
		$tobj->is_public = 'yes' == get_post_meta($location_post->ID, 'dispay_frontend', true);
		$tobj->map_url = get_post_meta($location_post->ID, 'map_url', true);
		return $tobj;
	}
	public function get_location_id(){
		$location_id = $this->location;
		if(!$location_id){
			$location_id = get_post_meta($this->course_id, 'course_location', true);
		}
		return (int)$location_id;
	}
	public function get_location_groups(){
		$location_id = $this->get_location_id();
		if(!$location_id){
			return false;
		}
		$location_groups = get_the_terms($location_id, TBS_Custom_Types::get_location_group_data('type'));
		if(!$location_groups || is_wp_error($location_groups)) {
			return false;
		}
		$lgs = array();
		foreach($location_groups as $lg) {
			$lgs[$lg->slug] = array(
				'id' => $lg->term_id,
				'title' => $lg->name,
				'slug' => $lg->slug,
			);
		}
		return $lgs;
	}
	public function has_custom_address(){
		return 'tbs_custom' === $this->location;
	}
	public function get_location_short_name(){
		if('tbs_custom' == $this->location){
			return 'Onsite';
		} 
		return $this->location ? get_the_title($this->location) : '-';
	}
	public function get_custom_location(){
		return $this->custom_location;
	}
	public function get_joining_instruction(){
		$joining_instruction = $this->joining_instruction;
		if(!$joining_instruction){
			$joining_instruction = get_post_meta($this->course_id, 'joining_instruction', true);
		}
		return $joining_instruction;
	}
	public function get_map(){
		$map = $this->map;
		if(!$map){
			$map = get_post_meta($this->course_id, 'course_map', true);
		}
		return $map;
	}
	public function get_delegates_count(){
		$args = array(
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'tbs_course_dates',
					'value' => $this->get_id(),
					'compare' => '='
				),
			),
		);
		$delegates_query = new WP_User_Query($args);
		//var_dump($delegates_query);
		return $delegates_query->get_total();
	}
	public function get_reserves_count(){
		$non_completed_status = wc_get_order_statuses();
		unset($non_completed_status['wc-completed']);
		unset($non_completed_status['wc-cancelled']);
		unset($non_completed_status['wc-refunded']);
		unset($non_completed_status['wc-failed']);
		$non_completed_status = array_keys($non_completed_status);
		$booking_ids = $this->get_bookigs_ids(array(
			'status' => $non_completed_status
		));
		
		$reserves_count = 0;
		if(count($booking_ids) < 0) {
			return 0;
		}
		
		foreach($booking_ids as $booking_id){
			$order = wc_get_order($booking_id);
			$reserves_count += tbs_order_course_delegates_count( $order, $this->get_id() );
		}
		
		return $reserves_count;
	}
	public function get_bookigs_ids($args = array()){
		global $wpdb;
		$args = wp_parse_args($args, array(
			'status' => array_keys(wc_get_order_statuses()),
			'type' => '',
		));
		
		if($args['type']){
			$course_date_ids =  tbs_get_course_dates(array(
				'type' => 'upcoming',
				'ids_only' => true,
			));
			
			$sql = "";
			$sql .= " SELECT DISTINCT order_items.order_id FROM {$wpdb->prefix}woocommerce_order_items as order_items";
			$sql .= " LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id";
			$sql .= " LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID";
			$sql .= " WHERE (1 = 1)";
			$sql .= " AND posts.post_type = 'shop_order'";
			if( is_array($args['status']) && count($args['status']) > 0 ){
				$sql .= " AND posts.post_status IN ( '" . implode( "','", $args['status'] ) . "' )";
			}
			$sql .= " AND (order_items.order_item_type = 'line_item')";
			$sql .= " AND (order_item_meta.meta_key = '_product_id')";
			$sql .= " AND order_item_meta.meta_value IN (". implode( ',', $course_date_ids ) .")";
			$exclude_bookings_ids = $wpdb->get_col($sql);
		}
		//SQL_CALC_FOUND_ROWS
		$sql = "";
		$sql .= " SELECT DISTINCT order_items.order_id FROM {$wpdb->prefix}woocommerce_order_items as order_items";
		$sql .= " LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id";
		$sql .= " LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID";
		$sql .= " WHERE (1 = 1)";
		$sql .= " AND posts.post_type = 'shop_order'";
		if( is_array($args['status']) && count($args['status']) > 0 ){
			$sql .= " AND posts.post_status IN ( '" . implode( "','", $args['status'] ) . "' )";
		}
		$sql .= " AND (order_items.order_item_type = 'line_item')";
		$sql .= " AND (order_item_meta.meta_key = '_product_id')";
		$sql .= " AND order_item_meta.meta_value = '%d'";
		if($args['type'] && 'past' == $args['type']){
			$sql .= " AND order_items.order_id NOT IN (". implode( ',', $exclude_bookings_ids ) .")";
		}
		if($args['type'] && 'upcoming' == $args['type']){
			$sql .= " AND order_items.order_id IN (". implode( ',', $exclude_bookings_ids ) .")";
		}
		//echo $wpdb->prepare($sql, $this->id);die();
		$bookings_ids = $wpdb->get_col($wpdb->prepare($sql, $this->id));
		$bookings_ids = array_map('absint', $bookings_ids);
		return $bookings_ids;
	}
	public function set_extra_data($key, $value) {
		$this->extra_data[$key] = $value;
	}
	public function get_extra_data($key){
		if(isset($this->extra_data[$key])){
			return $this->extra_data[$key];
		}
		return '';
	}
	public function get_edit_form_data(){
		$data = array(
			'form_course_date_id' => $this->id,
			'form_is_private' => (bool) $this->is_private,
			'form_course_id'	=> $this->course_id,
			'form_start_date' => date('Y-m-d', $this->start_date),
			'form_end_date' => date('Y-m-d', $this->end_date),
			'form_duration' => $this->get_duration(),
			'form_start_finish_time' => $this->get_start_finish_time(),
			'form_price' => $this->price,
			'form_max_delegates' => $this->max_delegates,
			'form_available_places' => $this->delegate_stock,
			'form_trainer_id' => $this->trainer,
			'form_location_id' => $this->location,
			'form_joining_instruction' => $this->joining_instruction,
			'form_joining_instruction' => $this->joining_instruction,
			'form_custom_location' => $this->custom_location,
			'form_map' => $this->get_map(),
		);
		return $data;
	}
}