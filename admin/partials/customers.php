<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap tbs-admin-custoemrs">
	<h1 class="wp-heading-inline">Customers</h1>
	<a href="<?php echo self::url('edit'); ?>" class="page-title-action">Add Customer</a>
	<hr class="wp-header-end">
	<?php
	if ( is_array( $this->messages ) && count($this->messages) > 0 ) {
		foreach ( $this->messages as $type => $messages ) {
			foreach($messages as $msg){
				echo '<div class="notice notice-'.$type.' is-dismissible"><p>'. $msg .'</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
			}
		}
	}
	?>
	<div class="tbs-booking-list-wrap">
		<?php $this->list_table->views(); ?>
		<form  method="post" id="tbs-booking-filter">
			<?php $this->list_table->display() ?>
		</form>
	</div>
</div>
