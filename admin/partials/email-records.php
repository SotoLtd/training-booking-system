<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap tbs-admin-email-records">
	<h1><?php
		/* translators: 1: order type 2: order number */
		printf(
			esc_html__( 'Email records for %1$s #%2$s', 'woocommerce' ),
			'booking',
			$order->get_order_number()
		);
	?></h1>
	<hr class="wp-header-end">
	<div class="tbs-email-records-list-wrap">
		<div class="tbs-er-resend-buttons">
			<form method="post" action="<?php echo self::url('', array('action' => 'view_email_records', 'booking_id' => $order->get_id(), )); ?>">
				<?php wp_nonce_field('tbs_resend_emails_' . $order->get_id()); ?>
				<input type="hidden" name="tbs_resend_email" value="1"/>
				<select name="tbs_order_email_type">
					<option value="">Select an email type</option>
					<option value="booking_confirmation">Booking confirmation</option>
					<option value="joining_instructions">Joining instructions</option>
				</select>
				<button class="button button-primary">Resend</button>
			</form>
		</div>
		<?php //$this->list_table->views(); ?>
		<form  method="post" id="tbs-email-records-filter">
			<?php $this->list_table->display() ?>
		</form>
	</div>
</div>
