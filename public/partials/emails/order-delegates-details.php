<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>
	<h2><?php _e( 'Delegates details', TBS_i18n::get_domain_name() ); ?></h2>
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
	<h3><a href="<?php echo $course_date->get_course_permalink() ?>"><?php echo $course_date->get_course_title_with_date(); ?></a></h3>
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 15px;" border="1">
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
			<td class="td" scope="col" style="width:15px; text-align: center"><?php echo $serial_no; ?>.</td>
			<td class="td" scope="col" style="text-align:left;">
				<p><span>Name: </span> <strong><?php echo esc_html($delegate->get_full_name()); ?></strong></p>
				<p><span>Email: </span> <strong>
						<?php if($delegate->has_email()): ?><a href="<?php echo esc_url($delegate->get_email()); ?>"><?php echo $delegate->get_email(); ?></a><?php endif; ?>
					</strong></p>
				<?php if($delegate->get_notes()): ?><p><span>Notes: </span> <strong><?php echo esc_html($delegate->get_notes()); ?></strong></p><?php endif;?>
			</td>
		</tr>
		<?php endforeach;?> 
		</tbody>
	</table>
	<?php endforeach; ?>