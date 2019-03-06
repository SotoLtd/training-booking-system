<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<script id="tts-tpl-added-to-cart-lightbox" type="text/template">
<div id="tts-atc-lightbox-wrap">
	<span id="tts-atclb-overlay" class="tts-atc-ligtbox-close"></span>
	<div id="tts-atc-lightbox-inner">
		<div id="tts-atc-lightbox-content">
			<p class="tts-atc-lightbox-title">It's in the basket - would you like to...</p>
			<div class="tts-atc-lightbox-butons clearfix">
				<a class="tts-button  button-with-arrow tts-button tts-atc-ligtbox-close" href="#">Continue shopping</a>
				<a class="tts-button button-with-arrow ts-button tts-atc-view-cart" href="<?php echo wc_get_cart_url(); ?>">View basket</a>
			</div>
		</div>
	</div>
</div>
</script>