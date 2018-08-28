<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>

<div class="tbs-header-cart-summery">
	<a class="tbs-cart-summery" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>">
		<?php echo sprintf ( _n( '%d course', '%d courses', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?> - 
			<?php echo WC()->cart->get_cart_total(); ?>
	</a>
</div>