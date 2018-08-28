<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<h3>Delegates</h3>
<div class="course-dates-delegates-list">
	<form  method="post" id="tbs-delegates-filter">
		<?php $this->list_table->display(); ?>
	</form>
</div>
