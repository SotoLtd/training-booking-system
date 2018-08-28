<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>

<section class="woocommerce-order-details">
	<h2 class="woocommerce-order-details__title"><?php _e( 'Delegates details', 'woocommerce' ); ?></h2>
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
			<td><?php if($delegate->has_email()): ?><a href="<?php echo esc_url($delegate->get_email()); ?>"><?php echo $delegate->get_email(); ?></a><?php endif; ?></td>
			<td><?php echo esc_html($delegate->get_notes()); ?></td>
		</tr>
		<?php endforeach;?> 
		</tbody>
	</table>
	<?php endforeach; ?>
</section>