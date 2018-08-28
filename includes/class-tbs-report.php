<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class TBS_Report {
	/**
	 * Type of the report - CSV
	 * @var string Report type
	 */
	protected $export_type = "csv";
	/**
	 * Store items or rows of the report
	 * @var array 
	 */
	protected $items;
	/**
	 *
	 * @var type 
	 */
	protected $object;
	/**
	 *
	 * @var string
	 */
	protected $recipient;
	
	protected $messages;
	
	/**
	 * Constructor of the class
	 */
	public function __construct() {
		
	}
	/**
	 * 
	 * @param string $type Message type
	 * @param string $message Message
	 */
	public function add_message($type, $message){
		if(!isset($this->messages[$type])){
			$this->messages[$type] = array();
		}
		$this->messages[$type][] = $message;
	}
	/**
	 * Get all messages
	 * @return array
	 */
	public function get_messages(){
		return $this->messages;
	}
	/**
	 * Check if has any messages
	 * @return bool
	 */
	public function has_message(){
		return $this->messages && count($this->messages) > 0;
	}
	/**
	 * Prepare records items
	 */
	abstract public function prepare_items();
	/**
	 * Get items
	 */
	abstract public function get_items();
	/**
	 * Check if the object is found
	 * @return bool
	 */
	public function object_found(){
		return !empty($this->object);
	}
	/**
	 * Check if any records found
	 * @return bool
	 */
	public function has_items(){
		return count($this->items) > 0;
	}
	/**
	 * Check if any email recipiens set
	 * @return bool
	 */
	public function has_recipients(){
		return !empty($this->recipient);
	}
	/**
	 * Download the report file
	 * @return bool
	 */
	public function download(){
		if(!$this->has_items()){
			$this->add_message('error', 'No records found.');
			return false;
		}
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment;filename="' . $this->get_report_file_name() . '";');
		$f = fopen('php://output', 'w');
		fputcsv($f, $this->get_headers());
		foreach ($this->get_items() as $line) {
			fputcsv($f, $line);
		}
		fclose($f);
		return true;
	}
	/**
	 * Email the report file
	 * @return boolean
	 */
	public function email(){
		if(!$this->has_items()){
			$this->add_message('error', 'No records found.');
			return false;
		}
		if(!$this->has_recipients()){
			$this->add_message('error', 'No recipients set.');
			return false;
		}
		if(!wp_mkdir_p($this->get_report_temp_path())){
			$this->add_message('error', 'Failed to create temporary file.');
			return false;
		}
		$file_name = $this->get_report_full_file_name();
		$f = fopen($file_name, 'w');
		fputcsv($f, $this->get_headers());
		foreach ($this->get_items() as $line) {
			fputcsv($f, $line);
		}
		fclose($f);
		$subject = $this->get_email_subject();
		$message = $this->get_email_body();
		wp_mail(
			$this->recipient,
			$subject,
			$message,
			'',
			array($file_name)
		);
		@unlink($file_name);
		$this->add_message('success', 'Mail sent successfully.');
			
	}
	/**
	 * Get report file dir path
	 * @return string
	 */
	public function get_report_temp_path(){
		return WP_CONTENT_DIR . '/uploads/tbs-reports/';
	}
	/**
	 * Get report file name
	 * @return string
	 */
	public function get_report_file_name(){
		return 'report-' . date('Ymdhis') . '.csv';
	}
	/**
	 *  Get report file full path
	 * @return stirng
	 */
	public function get_report_full_file_name(){
		return $this->get_report_temp_path() . $this->get_report_file_name();
	}
	/**
	 * Get email subject
	 * @return string
	 */
	public function get_email_subject(){
		return 'TrainingSocieti Ltd.: Report ' . date('Y-m-d h:i:s');
	}
	/**
	 * Get email body
	 * @return string
	 */
	public function get_email_body(){
		return 'Please find the attachement for report';
	}
	
	
}