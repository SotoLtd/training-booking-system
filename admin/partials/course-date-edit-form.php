<?php 
$form_course_date_id = $form_is_private = $form_course_id = $form_start_date = $form_end_date = $form_duration = $form_price = $form_max_delegates = $form_available_places = $form_trainer_id = $form_joining_instruction = '';

extract($form_data);
?>
<div class="tbs-container tbs-no-padding">
	<?php if('edit' == $form_type):  ?>
	<div style="display: none;">
		<input type="hidden" id="cd-course-id" value="<?php echo $form_course_date_id; ?>"/>
	</div>
	<?php endif;?>
	<div class="tbs-row">
		<div class="tbs-col-sm-6">
			<div class="tbs-form-group">
				<label class="inline-checkbox-label" for="cd-is-private">This is a private course</label>
				<input type="checkbox" class="custom-control-input" id="cd-is-private" value="1" <?php checked($form_is_private, true); ?> />
			</div>
		</div>
		<div class="tbs-col-sm-6">
			<div class="tbs-form-group">
				<label for="cd-duration" class="tbs-input-label">Duration</label>
				<input type='text' class="tbs-form-control " id="cd-duration" value="<?php echo $form_duration; ?>" />
			</div>
		</div>
	</div>
	<div class="tbs-row">
		<div class="tbs-col-sm-6">
			<div class="tbs-form-group">
				<label for="cd-start-date" class="tbs-input-label">Start Date</label>
				<input type='text' class="tbs-form-control tbs-date-field tbs-dtf-linked-max" data-maxfield="#cd-end-date" id="cd-start-date" value="<?php echo $form_start_date; ?>" />
			</div>
		</div>
		<div class="tbs-col-sm-6">
			<div class="tbs-form-group">
				<label for="cd-end-date" class="tbs-input-label">End Date</label>
				<input type='text' class="tbs-form-control tbs-date-field tbs-dtf-linked-min" data-minfield="#cd-start-date" id="cd-end-date" value="<?php echo $form_end_date; ?>" />
			</div>
		</div>
	</div>
	<div class="tbs-row">
		<div class="tbs-col-sm-6">
			<div class="tbs-form-group">
				<label for="cd-price" class="tbs-input-label">Price (£)</label>
				<input type='text' class="tbs-form-control" id="cd-price"  value="<?php echo $form_price; ?>"/>
			</div>
		</div>
		<div class="tbs-col-sm-6">
			<div class="tbs-form-group">
				<label for="cd-stock" class="tbs-input-label">Maximum number of delegates</label>
				<input type='text' class="tbs-form-control" id="cd-stock"  value="<?php echo $form_max_delegates; ?>"/>
			</div>
		</div>
	</div>
	<div class="tbs-row">
		<div class="tbs-col-sm-6">
			<div class="tbs-form-group">
				<label for="cd-trainer" class="tbs-input-label">Trainer</label>
				<?php
				$trainers	 = get_posts(array(
					'post_type' => TBS_Custom_Types::get_trainer_data('type'),
					'numberposts' => -1,
					'orderby' => 'title',
					'order' => 'ASC',
				));
				if(!$trainers || is_wp_error($trainers)){
					$trainers = array();
				}
				?>
				<select class="tbs-select" id="cd-trainer">
					<?php foreach($trainers as $trainer): ?> 
						<option value="<?php echo esc_attr( $trainer->ID ); ?>" <?php selected( $trainer->ID, $form_trainer_id, true ); ?>><?php echo get_the_title($trainer->ID); ?></option>
					<?php endforeach; ?> 
				</select>
			</div>
		</div>
		<div class="tbs-col-sm-6">
			<div class="tbs-form-group">
				<label for="cd-location" class="tbs-input-label">Location</label>
				<?php
				$locations = get_posts(array(
					'post_type' => TBS_Custom_Types::get_location_data('type'),
					'numberposts' => -1,
					'orderby' => 'title',
					'order' => 'ASC',
				));
				if(!$trainers || is_wp_error($trainers)){
					$trainers = array();
				}
				?>
				<select class="tbs-select" id="cd-location">
					<option value="">Select a location...</option>
					<option value="tbs_custom" <?php selected( 'tbs_custom', $form_location_id, true ); ?>>Custom location</option>
					<?php foreach($locations as $location): ?> 
						<option value="<?php echo esc_attr( $location->ID ); ?>" <?php selected( $location->ID, $form_location_id, true ); ?>><?php echo get_the_title($location->ID); ?></option>
					<?php endforeach; ?> 
				</select>
			</div>
		</div>
	</div>
	<div class="tbs-row <?php if('tbs_custom' == $form_location_id){echo 'tbs-active';} ?>" id="custom-location-field-wrap">
		<div class="tbs-col-sm-12">
			<div class="tbs-form-group">
				<label for="cd-custom-location" class="tbs-input-label">Custom Location</label>
				<textarea class="tbs-textarea" id="cd-custom-location" rows="6"><?php echo esc_textarea( $form_custom_location ); ?></textarea>
			</div>
		</div>
	</div>
	<div class="tbs-row">
		<div class="tbs-col-sm-6">
			<div class="tbs-form-group">
				<label for="cd-map" class="tbs-input-label">Map</label>
				<input type='text' class="tbs-form-control" id="cd-map"  value="<?php echo $form_map; ?>"/>
				<a href="#" title="Select file from media library" class="tts-add-media">Open media library</a>
			</div>
		</div>
		<div class="tbs-col-sm-6">
			<div class="tbs-form-group">
				<label for="cd-start-finish-time" class="tbs-input-label">Start/finish time</label>
				<input type='text' class="tbs-form-control" id="cd-start-finish-time"  value="<?php echo $form_start_finish_time; ?>"/>
			</div>
		</div>
	</div>
	<div class="tbs-row">
		<div class="tbs-col-sm-12">
			<div class="tbs-form-group">
				<label for="cdjoininginstruction" class="tbs-input-label">Joining instructions</label>
				<div class="tbs-wp-editor-holder">
					<?php 
					add_filter( 'wp_default_editor', 'tts_default_editor_visual');
					wp_editor( $form_joining_instruction, 'cdjoininginstruction');
                    remove_filter( 'wp_default_editor', 'tts_default_editor_visual');
					?>
				</div>
			</div>
		</div>
	</div>
	
</div>