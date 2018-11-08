<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$order_id = $order->get_id();
?> 
<html>
	<head>
		<style>
			body {
				color: #000;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 10px;
				line-height: 16px;
			}
			strong {
				font-weight: bold;
			}
			div, p, ul, ol, li, h1, h2, h3, h4, h5,h6 {
				margin: 0px;
				padding: 0;
			}
			table {
				border: 0.1mm solid #000000;
				width: 100%;
				border-collapse: collapse;
				text-align: left;
				margin-bottom: 25px;
				font-size: 10px;
				line-height: 16px;
			}
			td, th { 
				border: 1px solid #aaa;
				padding: 5px 10px;
				vertical-align: top; 
				font-weight: normal;
				text-align: left;
			}
			p {
				margin-bottom: 5px;
			}
			ul {
				list-style: outside disc;
			}
			ul, ol {
				padding-left: 30px;
				margin-bottom: 5px;
			}
			li {
				margin-bottom: 3px;
			}
			a {
				color: inherit;
				text-decoration: underline;
			}
			.top-text {
				margin-bottom: 10px;
			}
			.top-text p {
				
			}
			.top-text ul {
			}
			.top-text ul li {
				
			}
			.main-title {
				font-size: 18px;
				line-height: 22px;
				margin-bottom: 10px;
			}
			.manual-booking-section {
				
			}
			.mbs-section-title {
				font-size: 16px;
				line-height: 20px;
				margin-bottom: 10px;
			}
			.mbs-section-title span {
				color: #777;
			}
			.course-date-title {
				font-size: 14px;
				line-height: 20px;
				margin-bottom: 10px;
			}
			.course-date-title  a {
				text-decoration: none;
			}
			.tbs-booking-courses-table {
				
			}
			.tbs-booking-courses-table tr {
				
			}
			.tbs-booking-courses-table th {
				
			}
			.tbs-booking-courses-table td {
				
			}
			.tbs-booking-courses-table .tbs-booking-item {
				
			}
			.tbs-booking-courses-table .tbs-booking-item-cost {
				text-align: right;
			}
			.tbs-booking-courses-table .tbs-booking-item-delegates {
				text-align: right;
			}
			.tbs-booking-courses-table .tbs-booking-item-total {
				text-align: right;
			}
			.tbs-booking-courses-table .product-price{
				text-align: right;
			}
			.tbs-booking-courses-table .product-quantity {
				text-align: right;
			}
			.tbs-booking-courses-table .product-subtotal {
				text-align: right;
			}
			.tbs-booking-courses-table tfoot .label {
				text-align: right;
			}
			.tbs-booking-courses-table tfoot .total {
				text-align: right;
			}
			.product-name table {
				margin-top: 10px;
				width: auto;
				min-width: 520px;
				margin-bottom: 10px;
			}
			.product-name table th {
				width: 140px;
			}
			.product-name table .purchase-order-field {
				width: 140px;
				margin: 0;
				padding: 0;
			}
			#mbs-customer-details th {
				width: 120px;
			}
			.mbs-delegates-details {
				
			}
			.mbs-course-delegate {
				
			}
			.mbs-course-delegate-title {
				font-size: 14px;
				line-height: 14px;
				margin-bottom: 10px;
			}
			.delegate-fields {
				
			}
			.delegate-fields tr {
				
			}
			.delegate-fields th {
				width: 110px;
			}
			.delegate-fields td {
				
			}
			.delegate-fields th.delegate-notes {
				height: 60px;
			}
			.bottom-content {
				margin-bottom: 15px;
			}
			.terms-conditions {
				
			}
			.terms-conditions h5 {
				font-size: 14px;
				line-height: 14px;
				margin-bottom: 15px;
			}
			.terms-conditions table th {
				width: 165px;
			}
			.terms-conditions ul {
				
			}
			.terms-conditions ol {
				
			}
			.terms-conditions li {
				
			}
			
			.mbs-onsite-course-fields table {
				width: 100%;
			}

			.mbs-onsite-course-fields table th, 
			.mbs-onsite-course-fields table td {
				font-weight: 400;
				text-align: left;
				padding: 7px 10px;
				vertical-align: top;
				border: 1px solid #ddd;
			}
			.mbs-onsite-course-fields table th {
				width: 200px;
			}
			.mbs-onsite-course-fields table p {
				margin: 0 0 15px;
			}
			.mbs-onsite-course-fields table ul {
				list-style: disc outside;
				margin-left: 15px;
				margin-bottom: 20px;
			}
			.mbs-onsite-course-fields table ul li {
				margin-bottom: 3px;
			}
			.mbs-onsite-course-fields table ul li span {
				display: inline-block;
				min-width: 220px;
			}
			.mbs-onsite-course-fields table ul li label {
				display: inline-block;
				margin-right: 10px;
			}
			.mbs-onsite-course-fields textarea {
				background-color: transparent;
				border-width: 0;
				border-color: transparent;
			}
		</style>
	</head>
	<body>
		<div style="background-color: #000; padding: 10px; margin-bottom: 15px;">
			<img style="height: 44px;width: auto;" src="<?php echo get_stylesheet_directory(); ?>/images/logo.png"/>
		</div>
		<div class="top-text">
			<p>Hi<?php if($order->get_billing_first_name()){echo ' ' . $order->get_billing_first_name() ;}  ?></p>
			<p>Thank you very much for your booking today with The Training Societi Ltd.</p>
			<h3 class="mbs-section-title">How to confirm your booking:</h3>
			<p>Please:</p>
			<ol>
				<li><strong>Fill in</strong> you booking details below</li>
				<li>Download and <strong>print</strong> PDF</li>
				<li>Check details and <strong>sign</strong></li>
			</ol>
			<p>Then return it to us by either:-</p>
			<ul>
				<li>Faxing to 0117 981 1344</li>
				<li>Scanning and emailing to <a href="mailto:bookings@thetrainingsocieti.co.uk">bookings@thetrainingsocieti.co.uk</a></li>
				<li>Posting to The Training Societi Ltd, 1 Riverside Business Centre, St Annes, Bristol, BS4 4ED (Please note this may not always be the course venue)</li>
			</ul>
			<p>*If you prefer, you can Download PDF at any time and complete the booking details by hand, before signing and returning it as above.</p>
			<p>If you have any questions about this booking please contact us on 0117 971 1892 option 2, we are always very happy to help you.</p>
			<p>PLEASE NOTE YOUR SPACE IS NOT GUARANTEED UNTIL WE RECEIVE FULL PAYMENT (unless you are an official account customer.)</p>
		</div>
		<h1 class="main-title">#<?php echo $order->get_order_number(); ?></h1>
		<div id="mbs-course-order-details" class="manual-booking-section">
			<h3 class="mbs-section-title">Course details</h3>
			<table class="tbs-booking-courses-table">
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
						$purchase_order = get_post_meta($order_id, 'purchase_order_' . $course_date->get_id(), true);
						
					?> 
					<tr>
						<td class="product-name tbswc-course-name">
							<h4 class="course-date-title"><?php echo $course_date->get_course_title_with_date(true); ?></h4>
							<table>
								<tbody>
									<?php if($course_date->get_location_short_name()): ?>
									<tr>
										<th>Venue: </th>
										<td><?php echo $course_date->get_location_short_name(); ?></td>
									</tr>
									<?php endif; ?>
									<?php if($course_date->get_start_finish_time()): ?>
									<tr>
										<th>Start/ Finish time: </th>
										<td><?php echo $course_date->get_start_finish_time(); ?></td>
									</tr>
									<?php endif; ?>
									<tr>
										<th>Purchase Order Number: </th>
										<td><p class="purchase-order-field"><?php echo $purchase_order; ?><p></td>
									</tr>
								</tbody>
							</table>
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
			<table>
				<tbody>
					<?php 
					$cusomer_fields = WC()->countries->get_address_fields( $order->get_billing_country(), 'billing_' );
					foreach ( $cusomer_fields as $key => $field ) {
						if ( isset( $field['country_field'], $fields[ $field['country_field'] ] ) ) {
							$field['country'] = $checkout->get_value( $field['country_field'] );
						}
						if ( is_callable( array( $order, 'get_' . $key ) ) ) {
							$field_value = $order->{"get_$key"}( 'edit' );
						} else {
							$field_value = $order->get_meta( '_' . $key );
						}
						if('billing_state' == $key){
							continue;
						}
						?> 
						<tr>
							<th><?php if(isset($field['label'])) { echo $field['label'];} ?></th>
							<td><?php echo $field_value; ?></td>
						</tr>
						<?php
					}
					?>

				</tbody>
			</table>
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
			echo '<div class="manual-booking-section mbs-delegates-details">';
				echo '<h3 class="mbs-section-title">Delegates details: <span>'. $course_date->get_course_title_with_date() . '</span></h3>';
				echo '<div class="mbs-course-delegates">';
				$course_d_count = 0;
				foreach($course_delegates as $course_delegate){
					$course_d_count++;
					echo '<div class="mbs-course-delegate">';
					echo '<h5 class="mbs-course-delegate-title">Delegate ' . $course_d_count . '</h5>';
					echo '<table class="delegate-fields" autosize="1"><tbody>';
					foreach($course_delegate as $key=>$val){
						$field_name = "mbs_delegates[{$count}][{$key}]";
						if( !array_key_exists($key, $delegates_fields )){
							continue;
						}
						echo '<tr>';
							echo '<th class="delegate-'. $key .'">' . $delegates_fields[$key]['label'] . '</th>';
							echo '<td>' . $val . '</td>';
						echo '</tr>';
					}
					echo '</tbody></table>';
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
			}
			?> 
			<div id="mbs-onsite-details" class="manual-booking-section clearfix">
				<h3 class="mbs-section-title">Onsite venue details: <span><?php echo $course_date->get_course_title_with_date(); ?></span></h3>
				<div class="mbs-onsite-course-fields clearfix">
					<table>
						<tbody>
							<tr>
								<th>Address with postcode:</th>
								<td>
									<textarea rows="8" style="background:none; border: 0 none; width: 100%;display: block;"><?php echo esc_textarea($fields_value['address']); ?></textarea>
								</td>
							</tr>
							<tr>
								<th>Named contact on the day:</th>
								<td><?php echo esc_html($fields_value['named_contact']);?></td>
							</tr>
							<tr>
								<th>Named contact phone number:</th>
								<td><?php echo esc_html($fields_value['named_contact_phone']);?></td>
							</tr>
							<tr>
								<th>Will there be parking available?</th>
								<td>
									<input type="checkbox" <?php checked('yes', $fields_value['parking_available']); ?>/>
								</td>
							</tr>
							<tr>
								<th>Training site location requirements:</th>
								<td>
									<p>Please inform us of any site requirement eg; security bookings, specific requirements for PPE to gain access to site</p>
									<p>Please list requirements below:</p>
									<textarea rows="8" style="background:none; border: 0 none; width: 100%;display: block;"><?php echo esc_textarea($fields_value['location_requirements']); ?></textarea>
								</td>
							</tr>
							<tr>
								<th>Training Requirements:</th>
								<td>
									<p>Please ensure and confirm there is</p>
									<ul style="margin-bottom: 20px;">
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
		<div class="bottom-content">
			<p>We <strong>welcome all delegates from all nationalities</strong> who want to learn and grow with us. However, as per our terms and conditions, please note that it is the <strong>bookers responsibility</strong> to make sure that delegates have a <strong>satisfactory understanding of the English language</strong> in terms of reading, writing, listening and spoken. Translators are not allowed. If you feel an English course is needed we recommend the course "English for Speakers of Other Languages" which can be found at <a href="http://www.cityofbristol.ac.uk.">www.cityofbristol.ac.uk</a>.</p>
		</div>
		<div class="terms-conditions">
			<h5>I agree to accept the <a href="<?php echo get_permalink(268); ?>" target="_blank">terms and conditions.</a></h5>
			<table autosize="1">
				<tbody>
					<tr>
						<th>Customer Signature</th>
						<td></td>
					</tr>
					<tr>
						<th>Date</th>
						<td></td>
					</tr>
					<tr>
						<th>Customer Name (please print).</th>
						<td></td>
					</tr>
					<tr>
						<th>Customer Job Title</th>
						<td></td>
					</tr>
					<tr>
						<th>Customer Company </th>
						<td></td>
					</tr>
				</tbody>
			</table>
			<p><strong>After signing form, Print and Return by any of the following methods:-</strong></p>
			<p><strong>What happens next?</strong></p>
			<ol>
				<li><strong>We will confirm receipt of your booking form</strong> and send you an invoice which is payable <strong>at least five working days</strong> after the booking is made by any of the following payment methods:
			<ul>
				<li><strong>Card</strong> – Please phone our payment line on 0117 971 1892 option 2 to make your secure payment.</li>
				<li><strong>Cheque</strong> – Please make payable to “The Training Societi Ltd” put your invoice number on the back and send to The Training Societi Ltd, 1 Riverside Business Centre, St Annes, Bristol, BS4 4ED</li>
				<li><strong>Internet Banking</strong> – Please make your payment to “The Training Societi Ltd” Sort Code: 08 92 50 Account Number: 68293765. Please put your Invoice Number as your reference.</li>
			</ul>
			</li>
				<li><strong>Once this is paid we will send you confirmation and joining instructions.</strong></li>
			</ol>
			<p>For credit account customers we will send your invoice (payable within 30 days), joining instruction on completion and return of the booking form.</p>
		</div>
    </body>
</html>