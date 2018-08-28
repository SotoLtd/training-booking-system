<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;
?>
<?php if ( wc_tax_enabled() ) : ?>
	<?php foreach ( $order->get_tax_totals() as $code => $tax ) : ?>
		<tr>
			<td class="label"><?php echo $tax->label; ?>:</td>
			<td width="1%"></td>
			<td class="total"><?php
				if ( ( $refunded = $order->get_total_tax_refunded_by_rate_id( $tax->rate_id ) ) > 0 ) {
					echo '<del>' . strip_tags( $tax->formatted_amount ) . '</del> <ins>' . wc_price( WC_Tax::round( $tax->amount, wc_get_price_decimals() ) - WC_Tax::round( $refunded, wc_get_price_decimals() ), array( 'currency' => $order->get_currency() ) ) . '</ins>';
				} else {
					echo $tax->formatted_amount;
				}
			?></td>
		</tr>
	<?php endforeach; ?>
<?php endif; ?>

<?php do_action( 'woocommerce_admin_order_totals_after_tax', $order->get_id() ); ?>

<tr>
	<td class="label"><?php _e( 'Total', 'woocommerce' ); ?>:</td>
	<td width="1%"></td>
	<td class="total">
		<?php echo $order->get_formatted_order_total(); ?>
	</td>
</tr>