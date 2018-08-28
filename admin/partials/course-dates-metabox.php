<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
 * $course_id,
 * $course_dates
 * $admin_handler
 */

?>

<?php if($new_course_page): ?>
	<div class="tbs-section"><p class="tbs-warning">Course Dates can be added after saving the course.</p></div>
<?php else: ?>
	<div id="course-dates-list" class="tbs-section">
		<table>
			<thead>
				<tr>
					<th>Start Date</th>
					<th>Duration</th>
					<th>Price</th>
					<th>Trainer</th>
					<th>Places</th>
					<th>Location</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($course_dates as $course_date){
					include $admin_handler->get_partial('course-date-row', true);
				} ?> 
			</tbody>
		</table>
		<?php if(empty($course_dates)): ?> 
			<p id="tbs-no-course-found" class="tbs-info">No Dates added for this course.</p>
		<?php endif; ?>
	</div>
	<div class="tbs-section"><a id="add-course-date" class="ui-button ui-widget ui-corner-all" href="#">Add Course Date</a></div>
<?php endif; ?>