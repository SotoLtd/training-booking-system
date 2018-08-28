<?php

class TBS_Booker {
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
	protected $delegates = array();
	protected $delegates_db = array();
	
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

	public function get_email(){
		return $this->get_data('email');
	}
	
	public function get_first_name(){
		return $this->get_data('first_name');
	}
	
	public function get_last_name(){
		return $this->get_data('last_name');
	}
	
	public function get_notes(){
		return $this->get_data('notes');
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
	
	public function add_delegate($delegate_id){
		if(!in_array( $delegate_id, $this->delegates )) {
			$this->delegates[] = $delegate_id;
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
	
	public function remove_delegate($delegate_id){
		if($key = array_search( $delegate_id, $this->delegates !== false)) {
			unset($this->delegates[$key]);
		}
	}
	
	protected function load_courses(){
		$this->courses = $this->courses_db = get_user_meta($this->id, 'tbs_courses', false);
	}
	
	protected function load_course_dates(){
		$this->course_dates = $this->course_dates_db = get_user_meta($this->id, 'tbs_course_dates', false);
	}
	
	protected function load_delegate(){
		
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
	protected function save_delegates(){
		/**
		 * @todo Do this on delegate account with course_id, $coure_date_id, booker_id
		 */
		extract(tbs_analysis_array_merge($this->customers_db, $this->customers));
		foreach($removed as $r_v){
			//delete_user_meta($this->id, 'tbs_customers', $r_v);
		}
		foreach($new as $n_v){
			//add_user_meta($this->id, 'tbs_customers', $n_v);
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
			$user_data['role'] = 'customer';
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
	}
	
}