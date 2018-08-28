<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>
<div class="tbs-email-optin-field">
	<?php 

	woocommerce_form_field( 
			"tbs_email_optin",
			array(
				'label' => __('Opt-in to maling list', TBS_i18n::get_domain_name()),
				'type' => 'checkbox',
			),
			1
		);
	?>
</div>