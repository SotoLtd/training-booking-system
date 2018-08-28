<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>

<section class="woocommerce-order-details">
	<h2 class="woocommerce-order-details__title"><?php _e( 'Acredited certificate postal address', 'woocommerce' ); ?></h2>
	<?php
	
	foreach($addresses as $course_date_id => $addr):
		$course_date = new TBS_Course_Date($course_date_id);
		if(!$course_date->exists()){
			continue;
		}
		if(!is_array($addr)){
			continue;
		}
		?>
	<div class="woocommerce-customer-details">
		<h4 class="order-course-date-title"><a href="<?php echo $course_date->get_course_permalink() ?>"><?php echo $course_date->get_course_title_with_date(); ?></a></h4>
		<address>
			<?php echo WC()->countries->get_formatted_address($addr); ?>
		</address>
	</div>
	<?php endforeach; ?>
</section>