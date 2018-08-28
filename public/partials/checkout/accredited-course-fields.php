<?php

if( ! defined( 'ABSPATH' ) ){
	exit();
}

$key_base = 'cd_' . $course_date->get_id();
$label = __("We will send certificates to your billing address.  Please tick here if you'd like to provide an alternative address", TBS_i18n::get_domain_name());



?>

<div class="accredited-checkout-fields">
	<p class="form-row woocommerce-validated" id="<?php echo $key_base . '_dif_address'; ?>_field" data-priority="">
		<label class="checkbox ">
			We will send certificates to your billing address.  Please tick here if you'd like to provide an alternative address 
			<input style="margin-left: 8px;" class="input-checkbox cd-accr-addr-enable" name="<?php echo $key_base . '_dif_address'; ?>" id="<?php echo $key_base . '_enable'; ?>" value="1" type="checkbox" <?php checked( WC()->checkout()->get_value( $key_base . '_enable' ), 1 ) ?>>
		</label>
	</p>
	<div class="accredited-address-fields-group">
		<?php
			$address_fields = tbs_get_address_fields($key_base);
			foreach ( $address_fields as $key => $field ) {
				woocommerce_form_field( $key, $field, WC()->checkout()->get_value( $key ) );
			}
		?>
	</div>
</div>