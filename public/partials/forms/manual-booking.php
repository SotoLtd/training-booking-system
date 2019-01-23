<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$order_id = $order->get_id();
?>  
<?php 
global $tbs_manual_booking_online_form_error;
$msg = '';
if(isset( $_GET['status'] )){
	switch( $_GET['status'] ){
		case 'saved': 
			$msg = '<div class="tbs-mbf-messages woocommerce-message">Your booking data saved successfully.</div>';
			break;
		case 'submitted': 
			$msg = '<div class="tbs-mbf-messages woocommerce-message"><p>Thank you very much, your booking form has been successfully submitted.</p>';
			$msg .= '<p><strong>What happens next?</strong></p>';
			$msg .= '<p>We will send you over an invoice with instructions of how to pay.  Once that is paid, we will send you over your joining instructions. Please allow 1 working day for us to process your booking (unless your course is tomorrow!).</p>';
			$msg .= '<p>Please note that for official 30 day credit account customers we will send your joining instructions as soon as possible.</p>';
			$msg .= '</div>';
			break;
		case 'falied': 
			$msg = '<div class="tbs-mbf-messages woocommerce-error">Failed! Please try again.</div>';
			break;
		default:
			$msg = '';
	}
}
if($tbs_manual_booking_online_form_error){
	$msg = '<div class="tbs-mbf-messages woocommerce-message">'.$tbs_manual_booking_online_form_error.'</div>';
}
if($msg){
	echo  $msg ;
}
?> 
<div style="margin-bottom: 25px;">
	<p><?php if($order->get_billing_first_name()){echo 'Dear ' . $order->get_billing_first_name();}  ?></p>
	<p>Thank you very much for your booking today with The Training Societi Ltd.</p>
	<h3 class="mbs-section-title">How to confirm your booking:</h3>
	<p>Please:</p>
	<ol>
		<li><strong>Fill in</strong> your booking details below</li>
		<li>Tick the box [] I agree to accept the terms and conditions</li>
		<li>Submit Booking</li>
	</ol>
	<p>If you prefer to complete the booking offline:-</p>
	<ol>
		<li><strong>Fill in</strong> as many of your details as you wish online</li>
		<li>Download and <strong>print</strong> PDF</li>
		<li>Check details and <strong>sign</strong></li>
	</ol>
	<p>Then return it to us by either:-</p>
	<ul style="list-style: disc outside;padding-left: 15px;">
		<li>Faxing to 0117 981 1344</li>
		<li>Scanning and emailing to <a href="mailto:bookings@thetrainingsocieti.co.uk">bookings@thetrainingsocieti.co.uk</a></li>
		<li>Posting to The Training Societi Ltd, 1 Riverside Business Centre, St Annes, Bristol, BS4 4ED (Please note this may not always be the course venue)</li>
	</ul>
	<p>*If you prefer, you can Download PDF at any time and complete the booking details by hand, before signing and returning it as above.</p>
	<p>If you have any questions about this booking please contact us on 0117 971 1892 option 2, we are always very happy to help you.</p>
	<p>PLEASE NOTE YOUR SPACE IS NOT GUARANTEED UNTIL WE RECEIVE FULL PAYMENT (Unless you are an official account customer.)</p>
</div>
<h1 class="entry-title">Edit details for booking #<?php echo $order->get_order_number(); ?></h1>
<form method="post" action="<?php echo add_query_arg(array('booking_manual_key' => $order->get_meta( '_tbs_online_form_id', true)), site_url()); ?>">
	<input type="hidden" name="manual_booking_form_save"/>
	<input type="hidden" name="fe_manual_booking_id" value="<?php echo $order_id; ?>"/>
		
	<div id="mbs-course-order-details" class="manual-booking-section woocommerce">
		<h3 class="mbs-section-title">Course details</h3>
		<table id="tbs-booking-courses-table" class="shop_table woocommerce-checkout-review-order-table">
			<thead>
				<tr>
					<th class="tbs-booking-item">Course</th>
					<th class="tbs-booking-item-cost">Price</th>
					<th class="tbs-booking-item-delegates">Delegates</th>
					<th class="tbs-booking-item-total">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
				foreach($line_items as $line_item_id => $line_item):
					$course_date = new TBS_Course_Date($line_item->get_product());
					if(!$course_date->exists()){
						continue;
					}
					$delegates = absint($line_item->get_quantity());
					// Remove the item if no delegates is set
					if($delegates < 1){
						continue;
					}
					if(isset($_POST['purchase_orders'][$course_date->get_id()])){
						$purchase_order = $_POST['purchase_orders'][$course_date->get_id()];
					}else{
						$purchase_order = get_post_meta($order_id, 'purchase_order_' . $course_date->get_id(), true);
					}
				?> 
				<tr>
					<td class="product-name tbswc-course-name">
						<a href="<?php echo $course_date->get_permalink(); ?>"><?php echo $course_date->get_course_title_with_date(true); ?></a>
						<?php if($course_date->get_location_short_name()): ?>
						<p class="venue">Venue: <?php echo $course_date->get_location_short_name(); ?></p>
						<?php endif; ?>
						<?php if($course_date->get_start_finish_time()): ?>
						<p class="start-finish-time">Start/ Finish time: <?php echo $course_date->get_start_finish_time(); ?></p>
						<?php endif; ?>
						<p class="purchase-order-number">Purchase Order Number: <input type="text" name="purchase_orders[<?php echo $course_date->get_id(); ?>]" value="<?php echo esc_attr($purchase_order); ?>" placeholder="Leave blank if you don't use them" /></p>
					</td>
					<td class="product-price">
						<?php echo wc_price( $order->get_item_total( $line_item, false, true ), array( 'currency' => $order->get_currency() ) ); ?>
					</td>
					<td class="product-quantity">
						<?php echo $delegates;?>
					</td>
					<td class="product-subtotal">
						<?php echo wc_price( $line_item->get_total(), array( 'currency' => $order->get_currency() ) ); ?>
					</td>
				</tr>
				
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<?php if ( wc_tax_enabled() ) : ?>
					<?php foreach ( $order->get_tax_totals() as $code => $tax ) : ?>
						<tr>
							<td class="label" colspan="3"><?php echo $tax->label; ?>:</td>
							<td class="total"><?php
								if ( ( $refunded = $order->get_total_tax_refunded_by_rate_id( $tax->rate_id ) ) > 0 ) {
									echo '<del>' . strip_tags( $tax->formatted_amount ) . '</del> <ins>' . wc_price( WC_Tax::round( $tax->amount, wc_get_price_decimals() ) - WC_Tax::round( $refunded, wc_get_price_decimals() ), array( 'currency' => $order->get_currency() ) ) . '</ins>';
								} else {
									echo $tax->formatted_amount;
								}
							?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php do_action( 'woocommerce_admin_order_totals_after_tax', $order->get_id() ); ?>

				<tr>
					<td class="label" colspan="3"><?php _e( 'Total', 'woocommerce' ); ?>:</td>
					<td class="total">
						<?php echo $order->get_formatted_order_total(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<div id="mbs-customer-details" class="manual-booking-section clearfix">
		<h3 class="mbs-section-title">Billing details</h3>
		<?php 
		$cusomer_fields = WC()->countries->get_address_fields( $order->get_billing_country(), 'billing_' );
		foreach ( $cusomer_fields as $key => $field ) {
			if(isset($_POST[$key])){
				$field_value = $_POST[$key];
			}elseif ( is_callable( array( $order, 'get_' . $key ) ) ) {
				$field_value = $order->{"get_$key"}( 'edit' );
			} else {
				$field_value = $order->get_meta( '_' . $key );
			}
			if('billing_state' == $key){
				continue;
			}
			$field['autofocus'] = false;
			woocommerce_form_field( $key, $field, $field_value );
		}
		?>
	</div>

	<?php  
	$data_entry_complete = 'completed' == $order->get_status();
	// Get Delegates
	$course_delegates_data = array();
	if(!$data_entry_complete){
		$defaults = array(
			'id' => 0,
			'first_name' => '',
			'last_name' => '',
			'email' => '',
			'notes' => '',
			'courseDateID' => 0,
			'courseID' => 0,
			'userID' => 0
		);
		$delegates_data = get_post_meta($order_id, 'tbs_draft_delegates_data', true);
		if(!is_array($delegates_data)){
			$delegates_data = array();
		}
		foreach($delegates_data as $key => $delegate){
			if(!isset($course_delegates_data[$delegate['courseDateID']])){
				$course_delegates_data[$delegate['courseDateID']] = array();
			}
			$course_delegates_data[$delegate['courseDateID']][] = $delegate;
		}
	}
	unset($delegates_data, $order_delegates_ids, $serial_no, $course_date_id, $d_ids, $course_date, $delegate);
	$count = 0;
	$delegates_fields = tbs_get_delegates_field();
	foreach( $course_delegates_data as $course_date_id => $course_delegates){
		$course_date = new TBS_Course_Date($course_date_id);
		echo '<div class="manual-booking-section mbs-delegates-details clearfix">';
			echo '<h3 class="mbs-section-title">Delegates details: <span>'. $course_date->get_course_title_with_date() . '</span></h3>';
			echo '<div class="mbs-course-delegates">';
			$course_d_count = 0;
			foreach($course_delegates as $course_delegate){
				$course_d_count++;
				echo '<div class="mbs-course-delegate">';
				echo '<h5 class="mbs-course-delegate-title">Delegate ' . $course_d_count . '</h5>';
				foreach($course_delegate as $key=>$val){
					$field_name = "mbs_delegates[{$count}][{$key}]";
					if(isset($_POST[$field_name])){
						$val = $_POST[$field_name];
					}
					if( array_key_exists($key, $delegates_fields )){
						woocommerce_form_field( $field_name, $delegates_fields[$key], $val );
					}else{
						echo '<input type="hidden" name="'. $field_name .'" value="' . esc_attr($val) . '" />';
					}
				}
				echo '</div>';
				$count++;
			}
			echo '</div>';
		echo '</div>';
	}
	?> 
	<?php
	$line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
	$onsite_data = get_post_meta($order->get_id(), 'mbs_onsite_data', true);
	foreach($line_items as $line_item_id => $line_item):
		$course_date = new TBS_Course_Date($line_item->get_product());
		if(!$course_date->exists() || !$course_date->is_onsite()){
			continue;
		}
		$field_name_base = 'mbs_onsite_data[' . $course_date->get_id() . ']';
		$fields_value = array();
		if(isset($onsite_data[$course_date->get_id()])){
			$fields_value = $onsite_data[$course_date->get_id()];
		}else{
			$fields_value = array();
		}
		$fields_value = wp_parse_args( $fields_value, array(
			'address' => '',
			'named_contact' => '',
			'named_contact_phone' => '',
			'parking_available' => 'no',
			'location_requirements' => '',
			'quiet_training_room' => 'no',
			'delegates_tables_chairs' => 'no',
			'trainers_laptop_power' => 'no',
		) );
		?> 
		<div id="mbs-onsite-details" class="manual-booking-section clearfix">
			<h3 class="mbs-section-title">Onsite venue details: <span><?php echo $course_date->get_course_title_with_date(); ?></span></h3>
			<div class="mbs-onsite-course-fields clearfix">
				<table>
					<tbody>
						<tr>
							<th>
								<label for="od_address_<?php echo $course_date->get_id(); ?>">Address with postcode:</label>
							</th>
							<td>
								<textarea id="od_address_<?php echo $course_date->get_id(); ?>" name="<?php echo $field_name_base; ?>[address]" rows="8"><?php echo esc_textarea($fields_value['address']); ?></textarea>
							</td>
						</tr>
						<tr>
							<th>
								<label for="od_named_contact_<?php echo $course_date->get_id(); ?>">Named contact on the day:</label>
							</th>
							<td>
								<input type="text" id="od_named_contact_<?php echo $course_date->get_id(); ?>" name="<?php echo $field_name_base; ?>[named_contact]" value="<?php echo esc_attr($fields_value['named_contact']); ?>"/>
							</td>
						</tr>
						<tr>
							<th>
								<label for="od_named_contact_phone_<?php echo $course_date->get_id(); ?>">Named contact phone number:</label>
							</th>
							<td>
								<input type="text" id="od_named_contact_phone_<?php echo $course_date->get_id(); ?>" name="<?php echo $field_name_base; ?>[named_contact_phone]" value="<?php echo esc_attr($fields_value['named_contact_phone']); ?>"/>
							</td>
						</tr>
						<tr>
							<th>
								<label for="od_parking_available_<?php echo $course_date->get_id(); ?>">Will there be parking available?  </label>
                                <p style="margin: 6px 0 0;"><strong>*Please note that if parking is not available, any costs the trainer may receive due to parking will be invoiced to you following the courses completion.</strong></p>
							</th>
							<td>
								<input type="checkbox" id="od_parking_available_<?php echo $course_date->get_id(); ?>" name="<?php echo $field_name_base; ?>[parking_available]" value="yes" <?php checked('yes', $fields_value['parking_available']); ?>/>
							</td>
						</tr>
						<tr>
							<th>
								<label for="od_location_requirements_<?php echo $course_date->get_id(); ?>">Training site location requirements:</label>
							</th>
							<td>
								<p>Please inform us of any site requirement eg; security bookings, specific requirements for PPE to gain access to site</p>
								<p><label for="od_location_requirements_<?php echo $course_date->get_id(); ?>">Please list requirements below:</label></p>
								<textarea id="od_location_requirements_<?php echo $course_date->get_id(); ?>" name="<?php echo $field_name_base; ?>[location_requirements]" rows="8"><?php echo esc_textarea($fields_value['location_requirements']); ?></textarea>
							</td>
						</tr>
						<tr>
							<th>
								Training Requirements:
							</th>
							<td>
								<p>Please ensure and confirm there is</p>
								<ul>
									<li>
										<span>a suitable quiet training room:</span>
										<label><input type="radio" name="<?php echo $field_name_base; ?>[quiet_training_room]" value="yes" <?php checked('yes', $fields_value['quiet_training_room']); ?>/> Yes</label>
										<label><input type="radio" name="<?php echo $field_name_base; ?>[quiet_training_room]" value="no" <?php checked('no', $fields_value['quiet_training_room']); ?>/> No</label>
									</li>
									<li>
										<span>tables and chairs for all delegates:</span>
										<label><input type="radio" name="<?php echo $field_name_base; ?>[delegates_tables_chairs]" value="yes" <?php checked('yes', $fields_value['delegates_tables_chairs']); ?>/> Yes</label>
										<label><input type="radio" name="<?php echo $field_name_base; ?>[delegates_tables_chairs]" value="no" <?php checked('no', $fields_value['delegates_tables_chairs']); ?>/> No</label>
									</li>
									<li>
										<span>power for the trainers laptop:</span>
										<label><input type="radio" name="<?php echo $field_name_base; ?>[trainers_laptop_power]" value="yes" <?php checked('yes', $fields_value['trainers_laptop_power']); ?>/> Yes</label>
										<label><input type="radio" name="<?php echo $field_name_base; ?>[trainers_laptop_power]" value="no" <?php checked('no', $fields_value['trainers_laptop_power']); ?>/> No</label>
									</li>
									
								</ul>
								<p>*****We RESERVE THE RIGHT TO CANCEL the training on the day if the training area is not suitable with the above requirements and CANCELATION CHARGES WILL APPLY ******</p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	<?php endforeach; ?>
	<div class="mbs-section-submit clearfix">
		<p>We <strong>welcome all delegates from all nationalities</strong> who want to learn and grow with us. However, as per our terms and conditions, please note that it is the <strong>bookers responsibility</strong> to make sure that delegates have a <strong>satisfactory understanding of the English language</strong> in terms of reading, writing, listening and spoken. Translators are not allowed. If you feel an English course is needed we recommend the course "English for Speakers of Other Languages" which can be found at <a href="http://www.cityofbristol.ac.uk">www.cityofbristol.ac.uk</a>.</p>
		<p>Thank you very much for filling in this form. Please now confirm your Booking.</p>
		<p>
			<label>
				<input id="terms-conditions" type="checkbox" name="terms_conditions" value="1" <?php checked( tbs_arr_get('terms_conditions', false ), 1); ?>/>
				<span>I agree to accept the terms and conditions</span>
			</label>
		</p>
	</div>
	<div class="mbs-section-submit clearfix">
		<div class="form-row form-row-first">
			<input type="submit" name="save_type" class="button alt" value="Submit Booking" id="mbs-submit-form" />
			<?php if(isset( $_GET['status'] ) && 'submitted' == $_GET['status']): ?> 
			<div style="clear: both;display: inline-block;margin-top: 15px;" class="tbs-mbf-messages woocommerce-message">Thank you for submitting your Booking.</div>
			<?php endif; ?>
		</div>
		<div class="form-row form-row-last">
			<input type="submit" name="save_type" class="button alt" value="Save" id="mbs-save-form" />
			<p style="float: right;">Save your data and return to complete the form later.</p>
		</div>
	</div>
	<div class="mbs-section-submit clearfix">
		<p>If you prefer to complete the booking offline:-</p>
		<ol style="list-style: disc outside;padding-left: 15px;">
			<li>Download PDF, print and complete by hand</li>
			<li>Check details and sign</li>
			<li>Return to us as described above</li>
		</ol>
	</div>
	<div class="mbs-section-submit clearfix">
		<input type="submit" name="save_type" class="button alt" value="Download PDF" id="mbs-print-form" />
	</div>
	<div class="form-bottom-content">
		<p><strong>What happens next?</strong></p>
		<ol>
			<li><strong>We will confirm receipt of your booking form</strong> and send you an invoice which is payable <strong>at least five working days</strong> after the booking is made by any of the following payment methods:
		<ul>
			<li><strong>* Card</strong> – Please phone our payment line on 0117 971 1892 option 2 to make your secure payment.</li>
			<li><strong>* Cheque</strong> – Please make payable to “The Training Societi Ltd” put your invoice number on the back and send to The Training Societi Ltd, 1 Riverside Business Centre, St Annes, Bristol, BS4 4ED</li>
			<li><strong>* Internet Banking</strong> – Please make your payment to “The Training Societi Ltd” Sort Code: 08 92 50 Account Number: 68293765. Please put your Invoice Number as your reference.</li>
		</ul>
		</li>
			<li><strong>Once this is paid we will send you confirmation and joining instructions.</strong></li>
		</ol>
		<p>For credit account customers we will send your invoice (payable within 30 days), joining instruction on completion and return of the booking form.</p>
		<p>If you do not receive your Joining Instructions when expected, please check your Junk E-mail folder.</p>
	</div>
</form>