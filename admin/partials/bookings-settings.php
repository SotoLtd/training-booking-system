<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div class="wrap">
	<h1>Booking System Settings</h1>
	<?php
	if ( is_array( $this->messages ) ) {
		foreach ( $this->messages as $message ) {
			echo $message;
		}
	}
	?>
	<div class="tbs-settings-tab">
		<ul class="subsubsub">
			<?php 
			$tab_num = 0;
			foreach($tabs as $tab): 
				$tab_num++;
			?> 
			<li>
				<a <?php if($current_tab_key == $tab['id']){echo 'class="current"';} ?> href="<?php echo self::url($tab['id']); ?>"><?php echo $tab['title']; ?></a>
				<?php if($tab_num < count($tabs)){echo ' | ';} ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<form novalidate="novalidate" action="" method="post">
		<?php wp_nonce_field( 'save_tbs_settings', '_tbsnonce' ); ?>
		<input type="hidden" value="1" name="tbs_settings">
		<input type="hidden" value="<?php echo esc_attr($current_tab_key); ?>" name="tab">
		<table class="form-table">
			<?php foreach($current_tab_fields as $field): ?> 
			<tr>
				<th scope="row">
					<?php echo $field['label']; ?>
				</th>
				<td>
					<?php 
					switch($field['type']){
						case 'textarea_html':
							wp_editor( $field['value'], $field['id'], $field['args']);
							break;
						case 'text':
							echo '<input type="text" class="widefat" name="' . $field['id'] . '" value="' . $field['value'] . '"/>';
							break;
						case 'select':
							echo '<select id="' . $field['id'] . '" name="' . $field['id'] . '">';
							foreach($field['options'] as $option_key => $option_value){
								echo '<option value="' . $option_key .'" ' . selected($option_key, $field['value'], false) . '>' . $option_value . '</option>';
							}
							echo '</select>';
							break;
						case 'camp_monitor_list':
							if($ca_lists->was_successful()){
								echo '<select id="' . $field['id'] . '" name="' . $field['id'] . '">';
									echo '<option value="">Select a list</option>';
									foreach($ca_lists->response as $list){
										echo '<option value="' . $list->ListID . '" ' . selected( $list->ListID, $field['value'], true ) .'>' . $list->Name. '</option>';
									}
								echo '</select>';
							}else{
								echo __( 'Failed to connect to Campaign monitor. Please check the client ID and API Key.', TBS_i18n::get_domain_name() );
							}
							break;
						case 'select_page':
							$args = array(
								'name'             => $field['id'],
								'id'               => $field['id'],
								'sort_column'      => 'title',
								'sort_order'       => 'ASC',
								'show_option_none' => esc_attr__( 'Select a page&hellip;', TBS_i18n::get_domain_name() ),
								'echo'             => false,
								'selected'         => absint($field['value']),
							);
							echo wp_dropdown_pages( $args );
							break;
							
					}
					if(!empty($field['description'])){
						echo'<p class="description">' . $field['description'] . '</p>';
					}
					?>
				</td>
			</tr>
			<?php endforeach;?>
		</table>
		<p class="submit">
			<?php submit_button(); ?>
		</p>
	</form>
</div>