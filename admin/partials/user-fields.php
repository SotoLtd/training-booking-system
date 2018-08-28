<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php if(user_can( $profileuser, 'manage_options' )): ?> 
<tr class="tbs-wc-emails-enable">
	<th scope="row"><?php _e( 'Woocommerce Emails', TBS_i18n::get_domain_name() ); ?></th>
	<td>
		<fieldset>
			<legend class="screen-reader-text"><span><?php _e('Woocommerce Emails', TBS_i18n::get_domain_name() ) ?></span></legend>
			<?php 
			$mailer = WC()->mailer();
			$emails = $mailer->get_emails();
			foreach ( $emails as $email_key => $email ):
				if($email->is_customer_email()){
					continue;
				}
				$user_settings_key = 'tbs_' . strtolower($email_key);
				$user_setting_value = 'true' === get_user_meta($profileuser->ID, $user_settings_key, true);
			?>
			<label for="<?php echo $user_settings_key; ?>">
				<input name="<?php echo $user_settings_key; ?>" type="checkbox" id="<?php echo $user_settings_key; ?>" value="1"<?php checked($user_setting_value); ?> />
				<?php echo $email->get_title(); ?>
			</label><br />
			
			<?php endforeach; ?>
		</fieldset>
	</td>
</tr>
<?php endif; ?>
