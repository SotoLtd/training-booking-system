<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$course = new TBS_Course($_GET['course_id']);
$course_dates = $course->get_dates(array(
	'type' => 'all' // expired, upcoming, running
));
?>
<div class="wrap tbs-admin-cm-courses">
	<h1 class="wp-heading-inline"><?php echo $form_settings['title']; ?></h1>
	<?php
	if ( is_array( $this->messages ) ) {
		foreach ( $this->messages as $message ) {
			echo $message;
		}
	}
	?>
	<form method="post" action="<?php echo TBS_Admin_Courses::url('edit', array('course_id' => $course->get_id(),) ); ?>">
		<div class="tbs-cm-course-section">
			<h3>Course dates</h3>
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
							include $this->admin->get_partial('course-date-row', true);
						} ?> 
					</tbody>
				</table>
				<?php if(empty($course_dates)): ?> 
					<p id="tbs-no-course-found" class="tbs-info">No Dates added for this course.</p>
				<?php endif; ?>
			</div>
			<div class="tbs-section"><a id="add-course-date" class="ui-button ui-widget ui-corner-all" href="#">Add Course Date</a></div>
		</div>
		
		<div class="tbs-cm-course-section">
			<h3>Course Fields</h3>
			<div id="course-location" class="tbs-section">
				<?php
				$course_location = $course->get_course_location_id();
				$locations	 = get_posts(array(
					'post_type' => TBS_Custom_Types::get_location_data( 'type' ),
					'numberposts' => -1,
					'orderby' => 'title',
					'order' => 'ASC',
				));
				if(!$locations || is_wp_error($locations)){
					$locations = array();
				}
				?>
				<div class="tts-mb-field-wrap">
					<div class="tts-mb-label"><label for="course_location"><strong>Course Location</strong></label></div>
					<div class="tts-mb-field">
						<select name="course_location">
							<option value="">Select a Location</option>
							<?php
							foreach ( $locations as $location ) {
								?>
								<option value="<?php echo esc_attr( $location->ID ); ?>" <?php selected( $location->ID, $course_location, true ); ?>><?php echo get_the_title($location->ID); ?></option>
							<?php
				}
				?>
						</select>

						<p class="description">Select location of the course.</p>
					</div>
				</div>
				<?php
				
				?>
				
			</div>
			<div id="course-joining-instructions" class="tbs-section">
				<div class="tts-mb-field-wrap">
					<div class="tts-mb-label"><label for="joining_instruction"><strong>Joining instructions</strong></label></div>
					<div class="tts-mb-field tts-textarea">

						<?php
						wp_editor( $course->joining_instruction, 'joining_instruction', array(
							'textarea_name'	 => 'joining_instruction',
						) );
						?>
					</div>
				</div>
			</div>
		</div>
		<div id="tbs-cm-submit" class="tbs-section">
			<?php wp_nonce_field( 'save_tbs_cm_course', '_tbsnonce' ); ?>
			<input type="hidden" value="1" name="tbs_cm_course">
			<?php submit_button(); ?>
		</div>
	</form>
</div>