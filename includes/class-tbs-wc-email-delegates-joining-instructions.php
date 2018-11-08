<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'TBS_WC_Email_Delegates_Joining_Instructions', false ) ) :
/**
 * Delegates Joining Instructions Email
 *
 * Joining instructions are sent to delegates email addresses when a booker completes an order
 *
 * @class       TBS_WC_Email_Delegates_Joining_Instructions
 * @version     2.0.0
 * @package     WooCommerce/Classes/Emails
 * @author      WooThemes
 * @extends     WC_Email
 */
class TBS_WC_Email_Delegates_Joining_Instructions extends WC_Email {
	public $course_ji_data = false;
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id             = 'delegates_joining_instructions';
		$this->customer_email = true;
		$this->title          = __( 'Joining instructions', TBS_i18n::get_domain_name() );
		$this->description    = __( 'Joining instructions are sent to delegates email addresses when a booker completes an order', TBS_i18n::get_domain_name() );
		$this->template_html  = 'emails/delegates-joining-instructions.php';
		$this->template_plain = 'emails/plain/delegates-joining-instructions.php';
		$this->placeholders   = array(
			'{site_title}'   => $this->get_blogname(),
			'{course_title}'   => '',
		);

		// Triggers for this email
		add_action( 'woocommerce_order_status_completed_notification', array( $this, 'trigger' ), 10, 2 );

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param int $order_id The order ID.
	 * @param WC_Order $order Order object.
	 */
	public function trigger( $order_id, $order = false ) {
		
		if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		}

		if ( !is_a( $order, 'WC_Order' ) ) {
			return;
		}
		
		$this->object = $order;
		
		$delegates_emails_data = $this->get_delegates_email_data();
		
		if(empty($delegates_emails_data)){
			return;
		}
		
		$this->setup_locale();
		if( !$this->is_enabled() ){
			return;
		}
		foreach($delegates_emails_data as $course_id => $course_ji_data){
			$this->set_ji_data($course_ji_data);
			if (!$this->get_recipient() ) {
				continue;
			}
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		$this->restore_locale();
	}
	/**
	 * Get delegates email data for this order.
	 */
	public function get_delegates_email_data(){
		$order_delegates = get_post_meta($this->object->get_id(), 'delegates', true);
		if( !is_array( $order_delegates ) || 0 === count($order_delegates)){
			return false;
		}
		$data = array();
		foreach($order_delegates as $course_date_id => $d_ids){
			$course_date = new TBS_Course_Date($course_date_id);
			if(!$course_date->exists()){
				continue;
			}
			if(!is_array($d_ids) || count($d_ids) == 0){
				continue;
			}
			$delegates_emails = array();
			foreach ($d_ids as $d_id){
				$delegate = new TBS_Delegate($d_id);
				if(!$delegate->exists()){
					continue;
				}
				$delegates_emails[] = $delegate->get_email();
			}
			if(empty($delegates_emails)){
				continue;
			}
			$data[$course_date_id] = array(
				'delegates_emails' => $delegates_emails,
				'joining_instructions' => $course_date->get_joining_instruction(),
				'course_date_title' => $course_date->get_course_title_with_date(),
				'course_date' => $course_date,
			);
		}
		return $data;
	}
	/**
	 * Set course data
	 * @param type $course_ji_data
	 */
	public function set_ji_data($course_ji_data){
		$this->recipient = implode(',', $course_ji_data['delegates_emails']);
		$this->course_ji_data = $course_ji_data;
		$this->placeholders['{course_title}'] = $course_ji_data['course_date_title'];
	}

	/**
	 * Get the from address for outgoing emails.
	 *
	 * @return string
	 */
	public function get_from_address() {
		$email = trim(tbs_get_settings('joining_instruction_form_email', ''));
		if($email){
			$from_address = $email;
		}else{
			$from_address = apply_filters( 'woocommerce_email_from_address', get_option( 'woocommerce_email_from_address' ), $this );
		}
		
		return sanitize_email( $from_address );
	}

	/**
	 * Get email subject.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_subject() {
		return __( 'Joining instruction for {course_title}', 'woocommerce' );
	}

	/**
	 * Get email heading.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_heading() {
		return __( 'Joining instruction for {course_title}', 'woocommerce' );
	}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false,
			'email'			=> $this,
			'joining_istructions' => $this->course_ji_data['joining_instructions'],
			'course_date' => $this->course_ji_data['course_date'],
		) );
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true,
			'email'			=> $this,
			'joining_istructions' => $this->course_ji_data['joining_instructions'],
			'course_date' => $this->course_ji_data['course_date'],
		) );
	}

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'         => __( 'Enable/Disable', 'woocommerce' ),
				'type'          => 'checkbox',
				'label'         => __( 'Enable this email notification', 'woocommerce' ),
				'default'       => 'yes',
			),
			'subject' => array(
				'title'         => __( 'Subject', 'woocommerce' ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {course_title}</code>' ),
				'placeholder'   => $this->get_default_subject(),
				'default'       => '',
			),
			'heading' => array(
				'title'         => __( 'Email heading', 'woocommerce' ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {course_title}</code>' ),
				'placeholder'   => $this->get_default_heading(),
				'default'       => '',
			),
			'email_type' => array(
				'title'         => __( 'Email type', 'woocommerce' ),
				'type'          => 'select',
				'description'   => __( 'Choose which format of email to send.', 'woocommerce' ),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'       => $this->get_email_type_options(),
				'desc_tip'      => true,
			),
		);
	}
}

endif;
return new TBS_WC_Email_Delegates_Joining_Instructions();