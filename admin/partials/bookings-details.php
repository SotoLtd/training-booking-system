<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
	$order = new WC_Order();
}

$finance_contact_email = get_post_meta($order->get_id(), 'finance_contact_email', true);
?>
<div class="wrap">
	<h2><?php
		/* translators: 1: order type 2: order number */
		printf(
			esc_html__( '%1$s #%2$s details', 'woocommerce' ),
			'Booking',
			$order->get_order_number()
		);
	?></h2>
	<div class="tbs-online-booking-details">
		<div class="tbs-booking-data-panel tbs-booking-data-general">
			<h3 class="tbs-booking-data-panel-title">
				<?php _e( 'General details', TBS_i18n::get_domain_name() ); ?>
			</h3>
			<div class="tbs-booking-data-panel-content">
				<p><strong><?php _e( 'Order date:', TBS_i18n::get_domain_name() ) ?></strong>
				<?php echo date_i18n( 'l d M Y H:i', strtotime( $order->get_date_created() ) ); ?>
				 | <strong><?php _e( 'Order Status:', TBS_i18n::get_domain_name() ) ?></strong>
				<?php echo wc_get_order_status_name( $order->get_status() ); ?></p>
				<?php if($finance_contact_email): ?>
				<p><strong>Finance contact email:</strong> <a href="mailto:<?php echo esc_attr($finance_contact_email); ?>"><?php echo esc_html($finance_contact_email); ?></a></p>
				<?php endif; ?>
			</div>
			<div class="tbs-bdp-empty-space"></div>
			<h3 class="tbs-booking-data-panel-title">
				<?php _e( 'Customer billing address', TBS_i18n::get_domain_name() ); ?>
				<a id="tbs-edit-customer-details" href="#" class="tbs-booking-data-panel-edit-button"><span class="dashicons dashicons-edit"></span></a>
			</h3>
			<div class="tbs-booking-data-panel-content">
				<div id="tbs-booking-details-view">
					<h4>Address:</h4>
					<address>
						<?php echo ( $address = $order->get_formatted_billing_address() ) ? $address : __( 'N/A', 'woocommerce' ); ?>
					</address>
					<?php if ( $order->get_billing_email() ) : ?>
						<h4>Email address:</h4>
						<p class="woocommerce-customer-details--email"><a href="mailto:<?php echo esc_attr($order->get_billing_email()); ?>"><?php echo  $order->get_billing_email(); ?></a></p>
					<?php endif; ?>
					<?php if ( $order->get_billing_phone() ) : ?>
						<h4>Phone:</h4>
						<p class="woocommerce-customer-details--phone"><?php echo wc_make_phone_clickable(esc_html( $order->get_billing_phone() )); ?></p>
					<?php endif; ?>
				</div>
				<div id="tbs-booking-details-edit" class="booking-edit_address tbs-inactive clearfix">
					<form action="" method="post">
					<?php 
					$billing_fields = TBS_Admin_Manual_Bookings::get_address_fields();
					foreach ( $billing_fields as $key => $field ) {
						if ( ! isset( $field['type'] ) ) {
							$field['type'] = 'text';
						}
						if ( ! isset( $field['id'] ) ) {
							$field['id'] = '_billing_' . $key;
						}
						if(empty($field['class'])){
							$field['class'] = '';
						}
						if( !empty($field['required']) ){
							$field['class'] .= ' booking-address-input tbs-rquired-field';
						}else{
							$field['class'] .= ' booking-address-input';
						}
						$field_name = 'billing_' . $key;

						if ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
							$field['value'] = $order->{"get_$field_name"}( 'edit' );
						} else {
							$field['value'] = $order->get_meta( '_' . $field_name );
						}
						
						switch ( $field['type'] ) {
							case 'select' :
								tbs_wp_select( $field );
							break;
							default :
								tbs_wp_text_input( $field );
							break;
						}
					}
					?>
						<div class="tbs-submit-row">
							<input type="hidden" name="order_id" value="<?php echo $order->get_id(); ?>"/>
							<input type="hidden" name="action" value="tbs_save_online_booking_customer_details"/>
							<?php 
							wp_nonce_field('online-booking-save-customer-detials-'.$order->get_id(), '_tbsnonce');
							submit_button('Save'); 
							?>
						</div>
					</form>
					<div id="tbs-billing-address-loader" class="modal-loader">
						<div class="tbs-loader"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="tbs-booking-data-panel tbs-booking-data-courses">
			<h3 class="tbs-booking-data-panel-title"><?php _e( 'Course details', TBS_i18n::get_domain_name() ); ?></h3>
			<div class="tbs-booking-data-panel-content">
				<?php 
				$order_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
				?> 
				<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

					<thead>
						<tr>
							<th class="woocommerce-table__product-name product-name"><?php _e( 'Course', 'woocommerce' ); ?></th>
							<th class="woocommerce-table__product-table product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
						</tr>
					</thead>

					<tbody>
						<?php
							foreach ( $order_items as $item_id => $item ) {
								$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
								$purchase_note = $product ? $product->get_purchase_note() : ''
								?>
								<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">

									<td class="woocommerce-table__product-name product-name tbswc-course-name">
										<?php
											$is_visible        = $product && $product->is_visible();
											//$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
											$product_permalink = TBS_Admin_Course_Date_Info::url('view', array('course_date_id' => $product->get_id(),));

											echo $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name();
											echo apply_filters( 'woocommerce_order_item_quantity_html', ' <br/><span class="product-quantity">Delegates: ' . sprintf( '%s', $item->get_quantity() ) . '</span>', $item );

											do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

											wc_display_item_meta( $item );

											do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
										?>
									</td>

									<td class="woocommerce-table__product-total product-total">
										<?php echo $order->get_formatted_line_subtotal( $item ); ?>
									</td>

								</tr>

								<?php if (  $purchase_note ) : ?>

								<tr class="woocommerce-table__product-purchase-note product-purchase-note">

									<td colspan="2"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></td>

								</tr>

								<?php endif; ?>
								<?php
								
							}
						?>
						<?php do_action( 'woocommerce_order_items_table', $order ); ?>
					</tbody>

					<tfoot>
						<?php
							foreach ( $order->get_order_item_totals() as $key => $total ) {
								?>
								<tr>
									<th scope="row"><?php echo $total['label']; ?></th>
									<td><?php echo $total['value']; ?></td>
								</tr>
								<?php
							}
						?>
					</tfoot>
				</table>
				<?php if ( $order->get_customer_note() ) : ?> 
				<div class="tbs-bdp-empty-space"></div>
				<table class="tbs-bd-order-note">
					<tbody>
						<tr>
							<th><?php _e( 'Note:', 'woocommerce' ); ?></th>
							<td><?php echo wptexturize( $order->get_customer_note() ); ?></td>
						</tr>
					</tbody>
				</table>
				<?php endif; ?>
			</div>
			
		</div>
		<?php 
		if('completed' == $order->get_status()){
			$order_delegates = get_post_meta($order->get_id(), 'delegates', true);
		}else{
			$order_delegates = get_post_meta($order->get_id(), 'online_delegates_data', true);
		}
		
		if( is_array( $order_delegates ) && count($order_delegates) > 0 ){
		?>
		<div class="tbs-booking-data-panel tbs-booking-data-delegates">
			<h3 class="tbs-booking-data-panel-title">
				<?php _e( 'Delegates details', TBS_i18n::get_domain_name() ); ?>
				<a id="tbs-edit-delegate-details" href="#" class="tbs-booking-data-panel-edit-button"><span class="dashicons dashicons-edit"></span></a>
			</h3>
			<div class="tbs-booking-data-panel-content">
				<?php 
				if('completed' == $order->get_status()){
					include_once( dirname( __FILE__ ) . '/bookings-delegates-completed.php' );
				}else{
					include_once( dirname( __FILE__ ) . '/bookings-delegates-non-completed.php' );
				}
				?>
			</div>
		</div>
		<?php  } ?>
		<?php 
		$accr_addresses = get_post_meta($order->get_id(), 'certificate_addresses', true);
		if( is_array( $accr_addresses ) && count($accr_addresses) > 0 ){
		?>
		<div class="tbs-booking-data-panel tbs-booking-data-accredited-address">
			<h3 class="tbs-booking-data-panel-title"><?php _e( 'Accredited certificate addresses', TBS_i18n::get_domain_name() ); ?></h3>
			<div class="tbs-booking-data-panel-content">
				<?php
	
				foreach($accr_addresses as $course_date_id => $addr):
					$course_date = new TBS_Course_Date($course_date_id);
					if(!$course_date->exists()){
						continue;
					}
					if(!is_array($addr)){
						continue;
					}
					?>
				<div class="woocommerce-customer-details">
					<h4 class="order-course-date-title"><a href="<?php echo $course_date->get_course_permalink() ?>"><?php echo $course_date->get_course_title_with_date(); ?></a></h4>
					<address>
						<?php echo WC()->countries->get_formatted_address($addr); ?>
					</address>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php } ?>

		<div class="tbs-booking-data-panel tbs-booking-data-ji">
			<h3 class="tbs-booking-data-panel-title"><?php _e( 'Joining instructions', TBS_i18n::get_domain_name() ); ?></h3>
			<div class="tbs-booking-data-panel-content">
				<?php foreach ( $order->get_items() as $item_id => $item ) :
				$course_date = new TBS_Course_Date($item->get_product());
				if(!$course_date->exists()){
					continue;
				}
				?> 
				<div class="tbs-bdp-ji-content">
					<h4><a href="<?php echo $course_date->get_course_permalink() ?>"><?php echo $course_date->get_course_title_with_date(); ?></a></h4>
					<?php echo $course_date->get_joining_instruction(); ?>
				</div>
			<?php endforeach; ?> 
			</div>
		</div>
		
		<div class="tbs-booking-data-panel tbs-booking-data-er">
			<h3 class="tbs-booking-data-panel-title"><?php _e( 'Email records', TBS_i18n::get_domain_name() ); ?></h3>
				<div class="tbs-email-records-list-wrap">
					<div class="tbs-er-resend-buttons">
						<form method="post" action="<?php echo self::url('', array('action' => 'details', 'booking_id' => $this->list_table->order->get_id(), )); ?>">
							<?php wp_nonce_field('tbs_resend_emails_' . $this->list_table->order->get_id()); ?>
							<input type="hidden" name="tbs_resend_email" value="1"/>
							<select name="tbs_order_email_type">
								<option value="">Select an email type</option>
								<option value="booking_confirmation">Booking confirmation</option>
								<option value="joining_instructions">Joining instructions</option>
							</select>
							<button class="button button-primary">Resend</button>
						</form>
					</div>
					<?php //$this->list_table->views(); ?>
					<form  method="post" id="tbs-email-records-filter">
						<?php $this->list_table->display() ?>
					</form>
				</div>
		</div>
	</div>
</div>