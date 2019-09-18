<tr>
	<td>
		<a href="<?php echo TBS_Admin_Course_Date_Info::url('view', array('course_date_id' => $course_date->get_id(),) ) ?>" target="_blank">
			<?php if($course_date->is_private()){echo 'Private: ';} ?>
			<?php echo $course_date->get_date_formatted(); ?>
		</a> 
		<?php if($course_date->is_private()): ?>
		 <a class="private-course-permalink" href="<?php echo $course_date->get_permalink(); ?>" target="_blank"><span class="dashicons dashicons-admin-links"> url</span></a>
		<?php endif;?>
	</td>
	<td><?php echo $course_date->get_duration_formatted(); ?></td>
	<td><?php echo $course_date->get_price_formatted(); ?></td>
	<td><?php echo $course_date->get_trainers_name(); ?></td>
	<td><?php echo $course_date->get_places_formatted(); ?></td>
	<td><?php echo $course_date->get_location_short_name(); ?></td>
	<td>
		<a class="tbs-btn-edit-course ui-button ui-widget ui-corner-all" href="#" data-coursedateid="<?php echo $course_date->get_id(); ?>">Edit</a>
		<a class="tbs-btn-delete-course  ui-button ui-widget ui-corner-all" href="#" data-coursedateid="<?php echo $course_date->get_id(); ?>">Delete</a>
	</td>
</tr>