<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap tbs-admin-bookings">
	<h1 class="wp-heading-inline"><?php echo $booking_form_setting['title']; ?></h1>
	<?php if($booking_form_setting['new_booking']){echo $booking_form_setting['new_booking'];}  ?>
	<hr class="wp-header-end">
	<div class="tbs-booking-form">
		<div id="tbs-general-settings" class="tbs-booking-general clearfix">
			<div class="tbs-general-data-col booking-status-wrap">
				<label>Booking Status: </label>
				<select id="booking-status">
					<?php
					$wc_order_statuses = wc_get_order_statuses();
					foreach($wc_order_statuses as $status => $status_name):
						$status = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
						if(!in_array($status, array('tbs-draft', 'completed'))){
							continue;
						}
					?>
					<option value="<?php echo $status; ?>"><?php echo $status_name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="tbs-general-data-col  booking-data-entry-complete">
				<label>Campaign Monitor optin: </label>
				<input id="email-optin" type="checkbox" value="1" />
			</div>
			<div class="tbs-general-data-col booking-suppress-order-emails">
				<label>Suppress order emails: </label>
				<input id="suppress-order-emails" type="checkbox" value="1" />
			</div>
			<div class="tbs-general-data-col booking-online-form-url">
				<label>Online form url: </label>
				<input id="online-form-url" type="text" value="" placeholder="Generate url..." />
				<button id="copytoclipboard-online-form-url" class="button">Copy</button>
				<button id="generate-online-form-url" class="button">Generate</button>
			</div>
		</div>
		<div class="tbs-booking-section tbs-bookins-section-customer">
			<h2>Customer details</h2>
			<div class="booking-edit_address">
				<div id="booking-customer-address" class="clearfix">
					<?php 
					$billing_fields = self::get_address_fields();
					foreach ( $billing_fields as $key => $field ) {
						if ( ! isset( $field['type'] ) ) {
							$field['type'] = 'text';
						}
						if ( ! isset( $field['id'] ) ) {
							$field['id'] = '_billing_' . $key;
						}
						if( !empty($field['required']) ){
							$field['class'] = 'booking-address-input tbs-rquired-field';
						}else{
							$field['class'] = 'booking-address-input';
						}
						
						switch ( $field['type'] ) {
							case 'select' :
								tbs_wp_select( $field );
							break;
							default :
								tbs_wp_text_input( $field );
							break;
						}
					}
					?>
				</div>
				<div id="tbs-address-loader" class="modal-loader">
					<div class="tbs-loader"></div>
				</div>
			</div>
		</div>
		<div class="tbs-booking-section tbs-bookins-section-course">
			<h2>Course details</h2>
			<div class="tbs-booking-courses-wrap">
				<div id="tbs-booking-courses">
					<table id="tbs-booking-courses-table">
						<thead>
							<tr>
								<th class="tbs-booking-item">Course Dates</th>
								<th class="tbs-booking-item-cost">Price</th>
								<th class="tbs-booking-item-delegates">Delegates</th>
								<th class="tbs-booking-item-total">Total</th>
								<th class="tbs-booking-item-actions" width="1%"></th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
				<div class="tbs-booking-courses-total clearfix">
					<table id="tbs-booking-totals" class="tbs-booking-totals">
						<tr>
							<td class="tbs-booking-lable">Total:</td>
							<td width="1%"></td>
							<td class="tbs-total"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£</span>0.00</span></td>
						</tr>
					</table>
				</div>
				<div id="tbs-booking-course-buttons" class="clearfix">
					<button class="button booking-add-course-date">Add Course date</button>
					<button class="button button-primary booking-save">Save Booking</button>
					<button class="button booking-recalculate">Recalculate Tax</button>
				</div>
				<div id="tbs-course-loader" class="modal-loader">
					<div class="tbs-loader"></div>
				</div>
			</div>
		</div>
		<div style="clear: both;"></div>
		<div class="tbs-booking-section tbs-bookins-section-delegates">
			<h2>Delegates details</h2>
			<div class="tbs-booking-delegates-wrap">
				<div id="tbs-booking-delgates">

				</div>
				<div id="tbs-delegates-loader" class="modal-loader">
					<div class="tbs-loader"></div>
				</div>
			</div>
		</div>
		<div class="tbs-booking-section tbs-bookins-section-email-records">
			<?php if($this->list_table && is_a( $this->list_table, 'TBS_Email_Records_List_Table' ) && $this->list_table->can_be_shown_manual()): ?>
			<h2>Email records</h2>
			<div class="tbs-email-records-list-wrap">
				<div class="tbs-er-resend-buttons">
					<form method="post" action="<?php echo self::url('', array('action' => 'edit', 'booking_id' => $this->list_table->order->get_id(), )); ?>">
						<?php wp_nonce_field('tbs_resend_emails_' . $this->list_table->order->get_id()); ?>
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
			<?php endif; ?>
		</div>
	</div>
</div>