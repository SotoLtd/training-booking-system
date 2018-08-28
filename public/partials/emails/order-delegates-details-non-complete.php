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
	<h2><?php _e( 'Delegates details', TBS_i18n::get_domain_name() ); ?></h2>
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
	<h3><a href="<?php echo $course_date->get_course_permalink() ?>"><?php echo $course_date->get_course_title_with_date(); ?></a></h3>
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 15px;" border="1">
		<tbody>
		<?php 
		$serial_no = 0;
		foreach ($delegates_data as $delegate):
			$serial_no++;
		?> 
		<tr>
			<td class="td" scope="col" style="width:15px; text-align: center"><?php echo $serial_no; ?>.</td>
			<td class="td" scope="col" style="text-align:left;">
				<p><span>Name: </span> <strong><?php echo esc_html($delegate['first_name'] . ' ' . $delegate['last_name']); ?></strong></p>
				<p><span>Email: </span> <strong><a href="<?php echo esc_url($delegate['email']); ?>"><?php echo $delegate['email']; ?></a></strong></p>
				<?php if($delegate['notes']): ?><p><span>Notes: </span> <strong><?php echo esc_html($delegate['notes']); ?></strong></p><?php endif;?>
			</td>
		</tr>
		<?php endforeach;?> 
		</tbody>
	</table>
	<?php endforeach; ?>