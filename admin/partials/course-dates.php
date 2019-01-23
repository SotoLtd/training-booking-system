<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap tbs-admin-courses">
	<h1 class="wp-heading-inline">Course Dates</h1>
	<hr class="wp-header-end">
	<div class="tbs-courses-list-wrap">
		<form  method="get" action="<?php echo TBS_Admin_Course_Date_Info::url(); ?>" id="tbs-course-filter">
            <input type="hidden" name="page" value="tbs-course-date-info" />
			<?php $this->list_table->display() ?>
		</form>
	</div>
</div>
