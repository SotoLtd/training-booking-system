<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?> 
<div class="order-delegate-box">
	<div class="order-delegate-box-inner">
		<p class="order-delefate-name">
			<span class="order-delegate-label">Name</span>
			<span class="order-delegate-value"><?php echo esc_html($delegate->get_full_name()); ?></span>
		</p>
		<p class="order-delefate-email">
			<span class="order-delegate-label">Email</span>
			<span class="order-delegate-value"><a href="<?php echo esc_url($delegate->get_email()); ?>"><?php echo $delegate->get_email(); ?></a></span>
		</p>
		<p class="order-delefate-name">
			<span class="order-delegate-label">Notes</span>
			<span class="order-delegate-value"><?php echo esc_html($delegate->get_notes()); ?></span>
		</p>
		
	</div>
</div>