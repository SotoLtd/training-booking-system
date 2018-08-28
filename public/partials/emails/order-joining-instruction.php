<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?> 
<h2><?php _e( 'Start/finish time', TBS_i18n::get_domain_name() ); ?></h2>
<?php foreach ( $order->get_items() as $item_id => $item ) :
	$course_date = new TBS_Course_Date($item->get_product());
	if(!$course_date->exists()){
		continue;
	}
	?> 
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 15px;" border="1">
		<tbody>
			<tr>
				<td class="td" scope="col" style="text-align: left;vertical-align: top;">
					<a href="<?php echo $course_date->get_course_permalink() ?>"><?php echo $course_date->get_course_title_with_date(); ?></a>
				</td>
				<td class="td" scope="col" style="text-align:left;vertical-align: top;">
					<?php echo $course_date->get_start_finish_time(); ?>
				</td>
			</tr>
		</tbody>
	</table>
<?php endforeach; ?> 

<h2><?php _e( 'Joining instructions', TBS_i18n::get_domain_name() ); ?></h2>
<?php foreach ( $order->get_items() as $item_id => $item ) :
	$course_date = new TBS_Course_Date($item->get_product());
	if(!$course_date->exists()){
		continue;
	}
	?> 
	<h3><a href="<?php echo $course_date->get_course_permalink() ?>"><?php echo $course_date->get_course_title_with_date(); ?></a></h3>
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 15px;" border="1">
		<tbody>
			<tr>
				<td class="td" scope="col" style="text-align:left;">
					<?php echo $course_date->get_joining_instruction(); ?>
				</td>
			</tr>
		</tbody>
	</table>
<?php endforeach; ?> 