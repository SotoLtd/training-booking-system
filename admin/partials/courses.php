<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap tbs-admin-courses">
	<h1 class="wp-heading-inline">Courses</h1>
	<hr class="wp-header-end">
	<div class="tbs-courses-list-wrap">
		<form  method="post" id="tbs-course-filter">
			<?php $this->list_table->display() ?>
		</form>
	</div>
</div>
