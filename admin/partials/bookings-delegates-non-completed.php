<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php 
$order_delegates_data = array();

foreach($order_delegates as $order_delegate_data){
	if(isset($order_delegate_data['booker_is_delegate']) && 'yes' == $order_delegate_data['booker_is_delegate']){
		$order_delegates_data[$order_delegate_data['course_date_id']][] = array(
			'first_name' => $order->get_billing_first_name(),
			'last_name' => $order->get_billing_last_name(),
			'email' => $order->get_billing_email(),
			'notes' => $order_delegate_data['notes'],
		);
	}else{
		$order_delegates_data[$order_delegate_data['course_date_id']][] = array(
			'first_name' => $order_delegate_data['first_name'],
			'last_name' => $order_delegate_data['last_name'],
			'email' => $order_delegate_data['email'],
			'notes' => $order_delegate_data['notes'],
		);
	}
}

?> 
<?php if(empty($show_view_only)): ?>
<div id="tbs-delegate-details-view">
<?php endif;?>
<?php
foreach($order_delegates_data as $course_date_id => $delegates_data):
	$course_date = new TBS_Course_Date($course_date_id);
	if(!$course_date->exists()){
		continue;
	}
	if(!is_array($delegates_data) || count($delegates_data) == 0){
		continue;
	}
	?>
<h4 class="order-course-date-title"><a href="<?php echo $course_date->get_course_permalink() ?>"><?php echo $course_date->get_course_title_with_date(); ?></a></h4>
<table class="woocommerce-table woocommerce-table--order-details shop_table order_details order-delegates-table">
	<thead>
		<tr>
			<th style="width:15px;">&nbsp;</th>
			<th>Name</th>
			<th>Email</th>
			<th>Notes</th>
		</tr>
	</thead>
	<tbody>
	<?php 
	$serial_no = 0;
	foreach ($delegates_data as $delegate):
		$serial_no++;
	?> 
	<tr>
		<td style="width:15px;"><?php echo $serial_no; ?>.</td>
		<td><?php echo esc_html($delegate['first_name'] . ' ' . $delegate['last_name']); ?></td>
		<td><a href="<?php echo esc_url($delegate['email']); ?>"><?php echo $delegate['email']; ?></a></td>
		<td><?php echo esc_html($delegate['notes']); ?></td>
	</tr>
	<?php endforeach;?> 
	</tbody>
</table>
<?php endforeach; ?>
<?php if(empty($show_view_only)): ?>
</div>
<?php endif; ?>
<?php if(empty($show_view_only)): ?>
<div id="tbs-delegate-details-edit" class="tbs-inactive">
	<form action="" method="post">
	<?php	
		foreach($order_delegates_data as $course_date_id => $delegates_data):
			$course_date = new TBS_Course_Date($course_date_id);
			if(!$course_date->exists()){
				continue;
			}
			if(!is_array($delegates_data) || count($delegates_data) == 0){
				continue;
			}
			?>
		<h4 class="order-course-date-title"><a href="<?php echo $course_date->get_course_permalink() ?>"><?php echo $course_date->get_course_title_with_date(); ?></a></h4>
		<div class="tbs-cd-delegates-fields">
			<?php 
			$serial_no = 0;
			foreach ($delegates_data as $delegate):
				
			?> 
			<div class="tbs-cd-delegate-field clearfix">
				<h4>Delegate <?php echo $serial_no+1; ?></h4>
				<p class="form-field delegate_first_name_field">
					<label for="delegate_<?php echo $course_date_id . '_' . $serial_no; ?>_first_name">First name</label>
					<input type="text" id="delegate_<?php echo $course_date_id . '_' . $serial_no; ?>_first_name" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][first_name]" class="delegate-field tbs-rquired-field" value="<?php echo esc_attr($delegate['first_name']); ?>"/>
				</p>
				<p class="form-field delegate_last_name_field">
					<label for="delegate_<?php echo $serial_no; ?>_last_name">Last name</label>
					<input type="text" id="delegate_<?php echo $course_date_id . '_' . $serial_no; ?>_last_name" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][last_name]" class="delegate-field tbs-rquired-field" value="<?php echo esc_attr($delegate['last_name']); ?>"/>
				</p>
				<p class="form-field delegate_email_field">
					<label for="delegate_<?php echo $serial_no; ?>_email">Email address</label>
					<input type="email" id="delegate_<?php echo $course_date_id . '_' . $serial_no; ?>_email" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][email]" class="delegate-field" value="<?php echo esc_attr($delegate['email']); ?>"/>
					<span class="description">If not known please leave blank, details will be sent to the Booker for forwarding</span>
				</p>
				<p class="form-field delegate_notes_field last">
					<label for="delegate_<?php echo $serial_no; ?>_notes">Notes</label>
					<input type="text" id="delegate_<?php echo $course_date_id . '_' . $serial_no; ?>_notes" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][notes]" class="delegate-field" value="<?php echo esc_attr($delegate['notes']); ?>"/>
				</p>
				<input type="hidden" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][course_date_id]" value="<?php echo $course_date_id; ?>"/>
				<input type="hidden" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][course_id]" value="<?php echo $course_date->get_course_id(); ?>"/>
			</div>
			<?php 
			$serial_no++;
			endforeach;
			?> 
		</div>
		<?php endforeach; ?>
		<div class="tbs-submit-row">
			<input type="hidden" name="order_id" value="<?php echo $order->get_id(); ?>"/>
			<input type="hidden" name="action" value="tbs_save_online_booking_delegates_details"/>
			<?php 
			wp_nonce_field('online-booking-save-delegate-detials-'.$order->get_id(), '_tbsnonce');
			submit_button('Save');
			?>
		</div>
	</form>
	<div id="tbs-delegate-details-loader" class="modal-loader">
		<div class="tbs-loader"></div>
	</div>
</div>
<?php endif; ?>