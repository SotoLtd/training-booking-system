<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap tbs-admin-bookings">
	<h1 class="wp-heading-inline">Bookings</h1>
	<a href="<?php echo TBS_Admin_Manual_Bookings::url('edit'); ?>" class="page-title-action">Add Booking</a>
	<hr class="wp-header-end">
	<div class="tbs-booking-list-wrap">
		<?php $this->list_table->views(); ?>
		<form  method="post" id="tbs-booking-filter">
			<?php $this->list_table->display() ?>
		</form>
	</div>
</div>
