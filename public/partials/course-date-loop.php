<?php 
//$course_date = new TBS_Course_Date( $course_date );

?>
<div class="course-date-table-wrap">
	<?php if(!$hide_table_filter): ?>
	<div class="course-table-cotrolls">
		<div>Show events in the next 
			<select class="course-table-filter-months">
				<option value="3">3</option>
				<option value="6" selected="selected">6</option>
				<option value="12">12</option>
			</select> months
		</div>
	</div>
	<?php endif; ?>
	<table class="course-date-table" data-domtype="<?php if($hide_table_filter){echo 't';}else{echo 'tip';} ?>">
		<thead>
			<tr>
				<th class="tbs-hide"></th>
				<th class="course-date-col-date">Dates</th>
				<th class="course-date-col-duration">Days</th>
				<th class="course-date-col-price">Price per person</th>
				<th class="course-date-col-status">Status</th>
			</tr>
		</thead>
		<?php foreach ($course_dates as $course_date):?> 
		<tr>
			<td class="tbs-hide"><?php echo $course_date->get_months_until_start(); ?></td>
			<td class="course-date-col-date" data-order="<?php echo $course_date->get_start_date_raw(); ?>"><?php echo $course_date->get_date_formatted(); ?></td>
			<td class="course-date-col-duration" data-order="<?php echo $course_date->get_duration(); ?>"><?php echo $course_date->get_duration();?></td>
			<td class="course-date-col-price" data-order="<?php echo $course_date->get_price(); ?>"><?php echo $course_date->get_price_formatted(); ?></td>
			<td class="course-date-col-status" data-order="<?php echo $course_date->get_places(); ?>">
				<?php if($course_date->is_sold_out()): ?>
				<span class="course-date-soldout">Sold Out</span>
				<?php else: ?> 
				<p class="course-date-book-now-wrap"><?php tbs_get_template_part('cart/add-to-cart', true, array('course_date' => $course_date)); ?></p>
				<p class="course-date-places"><?php printf( _n( "%s Place Available", "%s Places Available", $course_date->get_places() ), $course_date->get_places()); ?></p>
				<?php endif;?>
			</td>
		</tr>
		<?php endforeach; ?> 
	</table>
</div>