<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap tbs-admin-cm-courses">
	<h1 class="wp-heading-inline"><?php echo $form_data['form_title']; ?></h1>
	<?php
	if ( is_array( $this->messages ) && count($this->messages) > 0 ) {
		foreach ( $this->messages as $type => $messages ) {
			foreach($messages as $msg){
				echo '<div class="notice notice-'.$type.' is-dismissible"><p>'. $msg .'</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
			}
		}
	}
	?>
	<form method="post" action="<?php echo $form_data['form_url']; ?>">
		<div id="tbs-customer-fields">
			<table class="form-table">
				<tr>
					<th><label for="account-type"><?php echo __('Account type', TBS_i18n::get_domain_name()); ?></label></th>
					<td>
						<fieldset>
							<label for="normal-account">
								<input name="account_type" type="radio" id="normal-account" value="normal" <?php checked($customers_data['account_type'], 'normal'); ?> />
								<?php _e('Normal account', TBS_i18n::get_domain_name()); ?>
							</label><br />
							<label for="credit-account">
								<input name="account_type" type="radio" id="credit-account" value="credit" <?php checked($customers_data['account_type'], 'credit'); ?> />
								<?php _e('Credit account', TBS_i18n::get_domain_name()); ?>
							</label><br />
						</fieldset>
					</td>
				</tr>
				<?php
				$customer_fields = $this->get_customer_fields();
				foreach ( $customer_fields as $key => $field ) :
					$field_value = array_key_exists($key, $customers_data) ? $customers_data[$key] : '';
					?>
					<tr>
						<th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
						<td>
							<?php if ( ! empty( $field['type'] ) && 'select' === $field['type'] ) : ?>
								<select name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $field['class'] ); ?>" style="width: 25em;">
									<?php
										foreach ( $field['options'] as $option_key => $option_value ) : ?>
										<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $field_value, $option_key, true ); ?>><?php echo esc_attr( $option_value ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php elseif ( ! empty( $field['type'] ) && 'checkbox' === $field['type'] ) : ?>
								<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="1" class="<?php echo esc_attr( $field['class'] ); ?>" <?php checked( $field_value, 1, true ); ?> />
							<?php elseif ( ! empty( $field['type'] ) && 'button' === $field['type'] ) : ?>
								<button id="<?php echo esc_attr( $key ); ?>" class="button <?php echo esc_attr( $field['class'] ); ?>"><?php echo esc_html( $field['text'] ); ?></button>
							<?php else : ?>
								<input type="text" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $field_value ); ?>" class="<?php echo ( ! empty( $field['class'] ) ? esc_attr( $field['class'] ) : 'regular-text' ); ?>" />
							<?php endif; ?>
							<br/>
							<span class="description"><?php echo wp_kses_post( $field['description'] ); ?></span>
						</td>
					</tr>
					<?php
				endforeach;
				?> 
				<?php if('add_new' == $form_data['form_action']): ?>
					
					<tr class="user-pass1-wrap">
						<th scope="row">
							<label for="pass1">
								<?php _e( 'Password' ); ?>
							</label>
						</th>
						<td>
							<input class="hidden" value=" " /><!-- #24364 workaround -->
							<button type="button" class="button wp-generate-pw hide-if-no-js"><?php _e( 'Show password' ); ?></button>
							<div class="wp-pwd hide-if-js">
								<?php $initial_password = wp_generate_password( 24 ); ?>
								<span class="password-input-wrapper">
									<input type="password" name="pass1" id="pass1" class="regular-text" autocomplete="off" data-reveal="1" data-pw="<?php echo esc_attr( $initial_password ); ?>" aria-describedby="pass-strength-result" />
								</span>
								<button type="button" class="button wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Hide password' ); ?>">
									<span class="dashicons dashicons-hidden"></span>
									<span class="text"><?php _e( 'Hide' ); ?></span>
								</button>
								<button type="button" class="button wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Cancel password change' ); ?>">
									<span class="text"><?php _e( 'Cancel' ); ?></span>
								</button>
								<div style="display:none" id="pass-strength-result" aria-live="polite"></div>
							</div>
						</td>
					</tr>
					<tr class="form-field form-required user-pass2-wrap hide-if-js">
						<th scope="row"><label for="pass2"><?php _e( 'Repeat Password' ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
						<td>
						<input name="pass2" type="password" id="pass2" autocomplete="off" />
						</td>
					</tr>
					<tr class="pw-weak">
						<th><?php _e( 'Confirm Password' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="pw_weak" class="pw-checkbox" />
								<?php _e( 'Confirm use of weak password' ); ?>
							</label>
						</td>
					</tr>
				<?php else: ?>
					<tr id="password" class="user-pass1-wrap">
						<th><label for="pass1"><?php _e( 'New Password' ); ?></label></th>
						<td>
							<input class="hidden" value=" " /><!-- #24364 workaround -->
							<button type="button" class="button wp-generate-pw hide-if-no-js"><?php _e( 'Generate Password' ); ?></button>
							<div class="wp-pwd hide-if-js">
								<span class="password-input-wrapper">
									<input type="password" name="pass1" id="pass1" class="regular-text" value="" autocomplete="off" data-pw="<?php echo esc_attr( wp_generate_password( 24 ) ); ?>" aria-describedby="pass-strength-result" />
								</span>
								<button type="button" class="button wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Hide password' ); ?>">
									<span class="dashicons dashicons-hidden"></span>
									<span class="text"><?php _e( 'Hide' ); ?></span>
								</button>
								<button type="button" class="button wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Cancel password change' ); ?>">
									<span class="text"><?php _e( 'Cancel' ); ?></span>
								</button>
								<div style="display:none" id="pass-strength-result" aria-live="polite"></div>
							</div>
						</td>
					</tr>
					<tr class="user-pass2-wrap hide-if-js">
						<th scope="row"><label for="pass2"><?php _e( 'Repeat New Password' ); ?></label></th>
						<td>
						<input name="pass2" type="password" id="pass2" class="regular-text" value="" autocomplete="off" />
						<p class="description"><?php _e( 'Type your new password again.' ); ?></p>
						</td>
					</tr>
					<tr class="pw-weak">
						<th><?php _e( 'Confirm Password' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="pw_weak" class="pw-checkbox" />
								<span id="pw-weak-text-label"><?php _e( 'Confirm use of potentially weak password' ); ?></span>
							</label>
						</td>
					</tr>
				<?php endif; ?>
			</table>
		</div>
		<div id="tbs-cm-submit" class="tbs-section">
			<?php wp_nonce_field( $form_data['nonce_action'], '_tbsnonce' ); ?>
			<input type="hidden" name="tbs_customer_form_action" value="<?php echo $form_data['form_action']; ?>"/>
			<?php 
			if('add_new' == $form_data['form_action']){
				submit_button( __( 'Add New Customer', TBS_i18n::get_domain_name() ), 'primary', 'createuser', true, array( 'id' => 'createusersub' ) ); 
			}else{
				submit_button(  __('Update Customer', TBS_i18n::get_domain_name()) ); 
			}
			?>
		</div>
	</form>
</div>