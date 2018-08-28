<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<h3>Bookings</h3>
<div class="course-dates-bookings-list">
	<?php $this->list_table->views(); ?>
	<form  method="post" id="tbs-booking-filter">
		<?php $this->list_table->display(); ?>
	</form>
</div>
