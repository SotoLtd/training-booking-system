<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<h2>General information</h2>
<div class="tbs-panel-wrap">
	<div id="ci-course-date-data" class="tbs-panel tbd-panel-half">
		<h3 class="tbs-panel-title">Course Date Data</h3>
		<div class="tbs-panel-content">
			<table>
				<tbody>
					<tr>
						<th>Duration:</th>
						<td><?php echo $this->course_date->get_duration_formatted(); ?></td>
					</tr>
					<tr>
						<th>Start/End time:</th>
						<td><?php echo $this->course_date->get_start_finish_time(); ?></td>
					</tr>
					<tr>
						<th>Max no. of spaces:</th>
						<td><?php echo $this->course_date->get_max_delegates(); ?></td>
					</tr>
					<tr>
						<th>Available spaces:</th>
						<td><?php echo $this->course_date->get_places(); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div id="ci-location" class="tbs-panel tbs-panel-half">
		<h3 class="tbs-panel-title">Location</h3>
		<div class="tbs-panel-content">
			<?php 
			if($this->course_date->has_custom_address()){
				?> 
				<h5 class="ci-location-title"><?php echo $this->course_date->get_location_short_name(); ?></h4>
				<div class="ci-location-full-addresss"><?php echo wpautop($this->course_date->get_custom_location()); ?></div>
				<?php
			}elseif( $location = $this->course_date->get_location()) {
				?> 
				<h5 class="ci-location-title"><?php echo $location->short_name; ?></h4>
				<div class="ci-location-full-addresss"><?php echo wpautop($location->full_address); ?></div>
				<?php
			}
			?>
		</div>
	</div>
	<div id="ci-joining-instructions" class="tbs-panel tbd-panel-half">
		<h3 class="tbs-panel-title">Joining instruction</h3>
		<div class="tbs-panel-content">
			<?php echo wpautop( $this->course_date->get_joining_instruction() ); ?>
		</div>
	</div>
	<div id="cii-traininer" class="tbs-panel tbd-panel-half">
		<h3 class="tbs-panel-title">Trainer</h3>
		<div class="tbs-panel-content">
			<?php 
			$trainer = $this->course_date->get_trainer();
			if($trainer){
				?>  
				<div class="ci-trianer-details">
					<?php if( has_post_thumbnail($trainer)): ?>
						<div class="ci-trainer-photo">
							<?php echo get_the_post_thumbnail($trainer); ?>
						</div>
					<?php endif; ?>
					<div class="ci-trainer-text">
						<h5 class="ci-trainer-name"><?php echo get_the_title($trainer); ?></h5>
						<?php echo wpautop($trainer->post_content); ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>