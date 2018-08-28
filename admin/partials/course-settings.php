<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div class="wrap">
	<h1>Course Settings</h1>
	<?php
	if ( is_array( $this->messages ) ) {
		foreach ( $this->messages as $message ) {
			echo $message;
		}
	}
	?>
	<form novalidate="novalidate" action="" method="post">
		<input type="hidden" value="1" name="tts_course_settings">
		<?php wp_nonce_field( 'save_course_settings', '_ttscsnonce' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="ttscoursepagenotice">Course Page Notice</label></th>
				<td>
					<?php
					wp_editor( $course_page_nottice, 'ttscoursepagenotice', array(
						'wpautop'		 => true,
						'media_buttons'	 => false,
						'textarea_name'	 => 'tts_course_page_nottice',
						'textarea_rows'	 => 5,
						'teeny'			 => true
					) );
					?>

				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ttscoursedatepagetext">Course Date Page Text</label></th>
				<td>
					<?php
					wp_editor( $course_date_page_text, 'ttscoursedatepagetext', array(
						'wpautop'		 => true,
						'media_buttons'	 => false,
						'textarea_name'	 => 'course_date_page_text',
						'textarea_rows'	 => 5,
						'teeny'			 => true
					) );
					?>

				</td>
			</tr>
			<tr>
				<th scope="row"><label for="tts-course-template">Course Template</label></th>
				<td>
					<select id="tts-course_template" name="tts_course_template">
						<option value="old">Template 1</option>
						<option value="new" <?php selected( $course_template, 'new', true ); ?> >Template 2</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="tts-course-template">Campaign Monitor Client ID</label></th>
				<td>
					<input type="text" class="widefat" name="tts_ca_clientid" value="<?php echo $ca_clientid; ?>"/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="tts-course-template">Campaign Monitor API Key</label></th>
				<td>
					<input type="text" class="widefat" name="tts_ca_apikey" value="<?php echo $ca_apikey; ?>"/>
				</td>
			</tr>
			<?php
			if($ca_lists->was_successful()):
			?> 
			<tr>
				<th>Campaign Monitor List</th>
				<td>
					<select id="tts-course_template" name="tts_ca_list_id">
						<option value="">Select a list</option>
						<?php foreach($ca_lists->response as $list):?> 
							<option value="<?php echo $list->ListID; ?>" <?php selected( $list->ListID, $ca_list_id, true ); ?> ><?php echo $list->Name; ?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<?php else: ?>
			<tr>
				<th></th>
				<td>
					Failed to connect to Campaign monitor. Please check the client ID and API Key.
				</td>
			</tr>
			<?php endif; ?>
		</table>
		<p class="submit">
			<?php submit_button(); ?>
		</p>
	</form>
</div>