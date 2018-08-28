<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


$delegate_base = "cd_{$course_date->get_id()}_delegate_{$delegate_no}_";
$delegates_fields = tbs_get_delegates_field();
?> 

<div class="delegate-details" data-delegate="<?php echo $delegate_no; ?>">
	<h4>Delegate <?php echo ($delegate_no+1) ?></h4>
	<div class="delegate-fields clearfix">
		<?php 
		foreach($delegates_fields as $key => $field){
			woocommerce_form_field( $delegate_base . $key, $field, WC()->checkout()->get_value( $delegate_base . $key ) );
		}
		?>
	</div>
</div>
