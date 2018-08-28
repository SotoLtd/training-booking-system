<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$booking_key = !empty($_GET['booking_manual_key']) ? trim($_GET['booking_manual_key']) : '';
$booking = get_booking_by_booking_key( $booking_key );
if(isset($_GET['show_pdf_html'])){
	tbs_get_template_part('forms/manual-booking-pdf', true, array('order' => $booking));
	die();
}
?> 

<?php get_header(); ?>

<main class="manual-booking-form">
	<div class="center">
        <div class="col col1">
			<div class="manual-booking-form-inner woocommerce">
				<?php
				if(!$booking){
					echo '<div class="tbs-mbf-messages woocommerce-error">No booking found!</div>';
				}elseif($booking->get_meta('tbs_data_entry_complete', true)){
					echo '<div class="tbs-mbf-messages woocommerce-error">Not allowed to edit!</div>';
				}else{
					tbs_get_template_part('forms/manual-booking', true, array('order' => $booking));
				}
				?> 
			</div>
        </div>
        <div class="clear"></div>
    </div>
</main>

<?php get_footer(); ?>