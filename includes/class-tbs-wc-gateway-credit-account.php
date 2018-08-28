<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class TBS_WC_Gateway_Credit_Acount extends WC_Payment_Gateway {
	public function __construct() {
		$this->id = 'tbs_credit_acount';
		$this->has_fields = false;
		$this->method_title = __('Credit Account', TBS_i18n::get_domain_name());
		$this->method_description = __('Allow credit account customer to checkout without payment.', TBS_i18n::get_domain_name());
		
		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();
		
		// Get setting values.
		$this->title                   = $this->get_option( 'title' );
		$this->description             = $this->get_option( 'description' );
		$this->enabled                 = $this->get_option( 'enabled' );
		
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}
	/**
	 * Initialise Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title' => __( 'Enable/Disable', 'woocommerce' ),
				'type' => 'checkbox',
				'label' => __( 'Enable Credit Account Payment', 'woocommerce' ),
				'default' => 'yes'
			),
			'title' => array(
				'title' => __( 'Title', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default' => __( 'Credit Account Payment', 'woocommerce' ),
				'desc_tip'      => true,
			),
			'description' => array(
				'title' => __( 'Customer Message', 'woocommerce' ),
				'type' => 'textarea',
				'default' => 'Checkout with your credit account'
			)
		);
	}
	function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );
		// Allow woocomerce to hanld the completed payment
		$order->add_order_note(__('Customer choosed Credit Account to process chekcout.', TBS_i18n::get_domain_name() ) );
		$order->payment_complete();
		// Remove cart.
		WC()->cart->empty_cart();
		// Return thank you page redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
	
}