<?php

class TBS_Delegate {
	protected $id;
	protected $data = array(
		'first_name' => '',
		'last_name'  => '',
		'email'      => '',
		'notes'      => '',
	);
	protected $courses = array();
	protected $courses_db = array();
	protected $course_dates = array();
	protected $course_dates_db = array();
	protected $customers = array();
	protected $customers_db = array();
	protected $empty_email = false;


	public function __construct($delegate = 0) {
		if ( $delegate instanceof WP_User ) {
			$this->load($delegate->data);
		} elseif ( is_numeric( $delegate ) ) {
			$this->load(WP_User::get_data_by('id', $delegate));
		}elseif( is_string( $delegate )){
			$this->load(WP_User::get_data_by('email', $delegate));
			$this->set_email( $delegate );
		}
	}
	public function load($data){
		if(!$data){
			$this->id = 0;
			return;
		}
		$this->id = (int)$data->ID;
		$this->set_first_name( get_user_meta($this->id, 'first_name', true));
		$this->set_last_name( get_user_meta($this->id, 'last_name', true));
		$this->set_email( $data->user_email );
		$this->set_notes( get_user_meta($this->id, 'notes', true));
		$this->load_courses();
		$this->load_course_dates();
		$this->load_customer();
		
		$this->empty_email = 'yes' == get_user_meta($this->id, '_tbs_empty_email', true);
	}
	
	public function exists(){
		return !empty($this->id);
	}
	
	
	public function get_data($key){
		if(!isset($this->data[$key])){
			return false;
		}
		return $this->data[$key];
	}
	
	protected function set_data($key, $val){
		if(isset($this->data[$key])){
			$this->data[$key] = $val;
		}
	}
	
	public function get_id(){
		return $this->id;
	}

	public function get_email($cickable = false){
		$email = $this->get_data('email');
		if($cickable){
			$email = '<a href="mailto:'. esc_url( $email ) .'">' . $email . '</a>';
		}
		return $email;
	}
	
	public function get_first_name(){
		return $this->get_data('first_name');
	}
	
	public function get_last_name(){
		return $this->get_data('last_name');
	}
	
	public function get_company(){
		if(!$this->customers && !is_array($this->customers)){
			return '';
		}
		$companies = array();
		foreach($this->customers as $customer_id){
			$customer_company = get_user_meta($customer_id, 'billing_company', true);
			if($customer_company && !in_array($customer_company, $companies)){
				$companies[] = $customer_company;
			}
		}
		return implode( ',', $companies );
	}
	
	public function get_notes(){
		return $this->get_data('notes');
	}
	
	public function has_email(){
		return !$this->empty_email;
	}
	
	public function set_empty_email(){
		$this->empty_email = true;
	}
	
	public function set_first_name($value){
		$this->set_data('first_name', $value);
	}
	
	public function set_last_name($value){
		$this->set_data('last_name', $value);
	}
	
	public function get_full_name(){
		return $this->get_first_name() . ' ' . $this->get_last_name();
		
	}
	
	public function get_course_dates(){
		return $this->course_dates;
	}
	
	public function set_email($value){
		$this->set_data('email', $value);
	}
	
	public function set_notes($value){
		$this->set_data('notes', $value);
	}
	
	public function add_course($course_id){
		if(!in_array( $course_id, $this->courses )) {
			$this->courses[] = $course_id;
		}
	}
	
	public function add_course_date($course_date_id){
		if(!in_array( $course_date_id, $this->course_dates )) {
			$this->course_dates[] = $course_date_id;
		}
	}
	
	public function add_customer($custmer_id){
		if(!in_array( $custmer_id, $this->customers )) {
			$this->customers[] = $custmer_id;
		}
	}
	
	public function remove_course($course_id){
		if($key = array_search( $course_id, $this->courses !== false)) {
			unset($this->courses[$key]);
		}
	}
	
	public function remove_course_dates($course_date_id){
		if($key = array_search( $course_date_id, $this->course_dates !== false)) {
			unset($this->course_dates[$key]);
		}
	}
	
	public function remove_customer($custmer_id){
		if($key = array_search( $custmer_id, $this->customers !== false)) {
			unset($this->customers[$key]);
		}
	}
	
	protected function load_courses(){
		$this->courses = $this->courses_db = get_user_meta($this->id, 'tbs_courses', false);
	}
	
	protected function load_course_dates(){
		$this->course_dates = $this->course_dates_db = get_user_meta($this->id, 'tbs_course_dates', false);
	}
	
	protected function load_customer(){
		$this->customers = $this->customers_db = get_user_meta($this->id, 'tbs_customers', false);
	}

	protected function save_courses(){
		extract(tbs_analysis_array_merge($this->courses_db, $this->courses));
		foreach($removed as $r_v){
			delete_user_meta($this->id, 'tbs_courses', $r_v);
		}
		foreach($new as $n_v){
			add_user_meta($this->id, 'tbs_courses', $n_v);
		}
		$this->courses_db = $this->courses;
	}
	protected function save_course_dates(){
		extract(tbs_analysis_array_merge($this->course_dates_db, $this->course_dates));
		foreach($removed as $r_v){
			delete_user_meta($this->id, 'tbs_course_dates', $r_v);
		}
		foreach($new as $n_v){
			add_user_meta($this->id, 'tbs_course_dates', $n_v);
		}
		$this->course_dates_db = $this->course_dates;
	}
	protected function save_customer(){
		extract(tbs_analysis_array_merge($this->customers_db, $this->customers));
		foreach($removed as $r_v){
			delete_user_meta($this->id, 'tbs_customers', $r_v);
		}
		foreach($new as $n_v){
			add_user_meta($this->id, 'tbs_customers', $n_v);
		}
		$this->customers_db = $this->customers;
	}


	public function get_wp_insert_user_data(){
		$user_data = array(
			'user_email' => $this->get_email(),
			'user_login' => $this->get_email(),
			'dispaly_name' => $this->get_full_name(),
			'first_name' => $this->get_first_name(),
			'last_name' => $this->get_last_name(),
			'notes' => $this->get_notes(),
			'show_admin_bar_front' => false,
		);
		if($this->exists()){
			$user_data['ID'] = $this->id;
		}else{
			$user_data['role'] = 'delegate';
			$user_data['user_pass'] = wp_generate_password(12, true, true);
		}
		return $user_data;
	}
	
	public function save(){
		$user_data = $this->get_wp_insert_user_data();
		$user_id = wp_insert_user($user_data);
		if( is_wp_error( $user_id )){
			return false;
		}
		
		$this->id = $user_id;
		update_user_meta($user_id, 'notes', $user_data['notes']);
		$this->save_courses();
		$this->save_course_dates();
		$this->save_customer();
		if($this->empty_email){
			update_user_meta($user_id, '_tbs_empty_email', 'yes');
		}else{
			delete_user_meta($user_id, '_tbs_empty_email');
		}
	}
	
}