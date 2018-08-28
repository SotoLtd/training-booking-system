<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

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

<section class="woocommerce-order-details">
	<h2 class="woocommerce-order-details__title"><?php _e( 'Delegates details', 'woocommerce' ); ?></h2>
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
</section>