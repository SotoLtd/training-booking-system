<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?> 
<?php if(empty($show_view_only)): ?>
<div id="tbs-delegate-details-view">
<?php endif;?>
<?php 
foreach($order_delegates as $course_date_id => $d_ids):
	$course_date = new TBS_Course_Date($course_date_id);
	if(!$course_date->exists()){
		continue;
	}
	if(!is_array($d_ids) || count($d_ids) == 0){
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
	foreach ($d_ids as $d_id):
		$delegate = new TBS_Delegate($d_id);
		if(!$delegate->exists()){
			continue;
		}
		$serial_no++;
	?> 
	<tr>
		<td style="width:15px;"><?php echo $serial_no; ?>.</td>
		<td><?php echo esc_html($delegate->get_full_name()); ?></td>
		<td>
			<?php if($delegate->has_email()): ?>
			<a href="<?php echo esc_url($delegate->get_email()); ?>"><?php echo $delegate->get_email(); ?></a>
			<?php endif; ?>
		</td>
		<td><?php echo esc_html($delegate->get_notes()); ?></td>
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
	foreach($order_delegates as $course_date_id => $d_ids):
		$course_date = new TBS_Course_Date($course_date_id);
		if(!$course_date->exists()){
			continue;
		}
		if(!is_array($d_ids) || count($d_ids) == 0){
			continue;
		}
		?>
	<h4 class="order-course-date-title"><a href="<?php echo $course_date->get_course_permalink() ?>"><?php echo $course_date->get_course_title_with_date(); ?></a></h4>
	<div class="tbs-cd-delegates-fields">
		<?php 
		$serial_no = 0;
		foreach ($d_ids as $d_id):
			$delegate = new TBS_Delegate($d_id);
			if(!$delegate->exists()){
				continue;
			}
			
		?> 
		<div class="tbs-cd-delegate-field clearfix">
			<h4>Delegate <?php echo $serial_no+1; ?></h4>
			<p class="form-field delegate_first_name_field">
				<label for="delegate_<?php echo $course_date_id . '_' . $serial_no; ?>_first_name">First name</label>
				<input type="text" id="delegate_<?php echo $course_date_id . '_' . $serial_no; ?>_first_name" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][first_name]" class="delegate-field tbs-rquired-field" value="<?php echo esc_attr($delegate->get_first_name()); ?>"/>
			</p>
			<p class="form-field delegate_last_name_field">
				<label for="delegate_<?php echo $serial_no; ?>_last_name">Last name</label>
				<input type="text" id="delegate_<?php echo $course_date_id . '_' . $serial_no; ?>_last_name" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][last_name]" class="delegate-field tbs-rquired-field" value="<?php echo esc_attr($delegate->get_last_name()); ?>"/>
			</p>
			<p class="form-field delegate_email_field">
				<label for="delegate_<?php echo $serial_no; ?>_email">Email address</label>
				<input type="email" id="delegate<?php echo $course_date_id . '_' . $serial_no; ?>_email" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][email]" class="delegate-field" value="<?php echo $delegate->has_email() ? esc_attr($delegate->get_email(false)) : ''; ?>"/>
				<span class="description">If not known please leave blank, details will be sent to the Booker for forwarding</span>
			</p>
			<p class="form-field delegate_notes_field last">
				<label for="delegate_<?php echo $serial_no; ?>_notes">Notes</label>
				<input type="text" id="delegate_<?php echo $course_date_id . '_' . $serial_no; ?>_notes" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][notes]" class="delegate-field" value="<?php echo esc_attr($delegate->get_notes()); ?>"/>
			</p>
			<input type="hidden" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][course_date_id]" value="<?php echo $course_date_id; ?>"/>
			<input type="hidden" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][course_id]" value="<?php echo $course_date->get_course_id(); ?>"/>
			<input type="hidden" name="delegates[<?php echo $course_date_id; ?>][<?php echo $serial_no; ?>][ID]" value="<?php echo $d_id; ?>"/>
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