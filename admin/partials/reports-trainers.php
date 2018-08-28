<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$current_trainer_id = isset($_REQUEST['trainer_id']) ? $_REQUEST['trainer_id'] : '';
$current_action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
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

<div class="tbs-page-container clearfix">
	<form class="tbs-trainer-form clearfix" action="<?php echo self::url('trainers');?>" method="post">
		<div class="tbs-trainer-field tbs-trainer-select">
			<label for="tbs-trainer-select">Trainer: </label>
			<select id="tbs-trainer-select" class="tbs-select-woo" name="trainer_id">
				<option value="">Select an trainer</option>
				<?php foreach($trainers as $trainer): ?> 
				<option value="<?php echo $trainer->ID; ?>" <?php selected( $current_trainer_id, $trainer->ID ); ?> ><?php echo get_the_title($trainer->ID); ?></option>
				<?php endforeach; ?> 
			</select>
			<?php  ?>
		</div>
		<div class="tbs-trainer-field tbs-trainer-actions">
			<label for="tbs-trainer-actions">Action: </label>
			<select id="tbs-trainer-actions" class="tbs-select-woo" name="action">
				<option value="">Select an action</option>
				<option value="download" <?php selected( $current_action, 'download' ); ?> >Download</option>
				<option value="email" <?php selected( $current_action, 'email' ); ?> >Email to trainer</option>
			</select>
		</div>
		<div class="tbs-trainer-field tbs-trainer-submit">
			<input class="button button-primary" type="submit" value="Go"/>
		</div>
	</form>
</div>