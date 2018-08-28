<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?> 

<?php 

foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item){
	$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
	if ( !$_product || !$_product->exists() || $cart_item['quantity'] < 1 || !apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
		continue;
	}
	$course_date = new TBS_Course_Date($cart_item['product_id']);
	if(!$course_date->exists()){
		continue;
	}

	?> 
	<div class="tbs-checkout-section">
		<h3>Additional details for <?php echo $course_date->get_course_title_with_date();?></h3>
		<?php 
		woocommerce_form_field( 
			"cd_{$course_date->get_id()}_po",
			array(
				'label' => __('Purchase Order', TBS_i18n::get_domain_name()),
				'required' => false,
				'type' => 'text',
			),
			WC()->checkout()->get_value( "cd_{$course_date->get_id()}_po" ) 
		);
		
		if($course_date->is_accredited()){
			tbs_get_template_part('checkout/accredited-course-fields', true, array('course_date' => $course_date));
		}
		?>
		<div class="checkout-delegates-fields-wrap">
			<p class="form-row woocommerce-validated" id="<?php echo "booker_is_delegate_" . $course_date->get_id(); ?>_field" data-priority="">
				<label class="checkbox ">
					Booker is also delegate 
					<input style="margin-left: 8px;" class="input-checkbox booker_is_delegate" name="<?php echo "cd_{$course_date->get_id()}_booker_is_delegate"; ?>" id="<?php echo "cd_{$course_date->get_id()}_booker_is_delegate"; ?>" value="1" type="checkbox" <?php checked( WC()->checkout()->get_value( "cd_{$course_date->get_id()}_booker_is_delegate" ), 1 ) ?>>
				</label>
			</p>		
			<?php
			for($delegate_no = 0; $delegate_no < $cart_item['quantity']; $delegate_no++){
				$data = array(
					'delegate_no' => $delegate_no,
					'cart_item' => $cart_item,
					'course_date' => $course_date,
				);
				tbs_get_template_part('checkout/individual-delegates-field', true, $data);
			}
			?> 
		</div>
	</div>
	<?php
}