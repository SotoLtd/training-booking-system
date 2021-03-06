<?php 
//$course_date = new TBS_Course_Date( $course_date );
if(!isset($show_private)){
	$show_private = false;
}
$location_group_name = '';
if(!empty($_GET['location'])) {
	$location_group_term = get_term_by('slug', $_GET['location'], TBS_Custom_Types::get_location_group_data('type'));
	if($location_group_term){
		$location_group_name = $location_group_term->name;
	}
}
$rows = '';
$locations = array();
ob_start();
?>

<?php 
foreach ($course_dates as $course_date){
	$course_location_ids = $course_date->get_location_groups();
	if($course_location_ids){
		$locations = array_merge($locations, $course_location_ids);
	}
	?> 
		<tr>
			<td class="tbs-hide"><?php echo $course_date->get_months_until_start(); ?></td>
			<td class="tbs-hide"><?php echo $course_date->get_course_id(); ?></td>
			<td class="tbs-hide"><?php echo implode( ',', array_keys($course_location_ids )); ?></td>
			<td class="course-date-col-date" data-order="<?php echo $course_date->get_start_date_raw(); ?>"><?php echo $course_date->get_date_formatted(); ?></td>
			<td class="course-date-col-duration" data-order="<?php echo $course_date->get_duration(); ?>"><?php echo $course_date->get_duration();?></td>
			<td class="course-date-col-title" data-order="<?php echo $course_date->get_course_title(); ?>">
				<a href="<?php echo $course_date->get_course_permalink(); ?>" target="_blank"><?php echo $course_date->get_course_title(); ?></a>
			</td>
			<td class="course-date-col-duration" data-order="<?php echo $course_date->get_location_short_name(); ?>"><?php echo $course_date->get_location_short_name();?></td>
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
<?php 
} 
$rows = ob_get_clean();
?> 

<div class="course-date-table-wrap course-dates-list">
	<div class="course-table-cotrolls">
		<div>
			Show 
			<?php 
			if(empty($_GET['course_id'])) {
				echo tbs_courses_dropdown(array('show_private' => $show_private,'select_classes' => 'course-table-filter-course', 'first_option' => __('All Events', TBS_i18n::get_domain_name())));
			}else{
				echo 'course dates';
			}
			?>
			 in the next 
			<select class="course-table-filter-months">
				<option value="3">3</option>
				<option value="6" selected="selected">6</option>
				<option value="12">12</option>
			</select> months in 
			<?php if(!empty($_GET['location'])) { ?>
			<span class="tts-orrange-styled" style="margin-right: 20px;"><?php echo $location_group_name; ?></span> <a href="<?php echo get_page_link(361); ?>" title="">Show all Courses &gt;</a>
			<?php }else{ ?>
			<select class="course-table-filter-locgroup">
				<option value="">All Locations</option>
				<?php foreach($locations as $locgroup): ?>
				<option value="<?php echo $locgroup['slug']; ?>" <?php selected( $locgroup['title'], $location_group_name ) ?>><?php echo $locgroup['title']; ?></option>
				<?php endforeach; ?>
			</select>
			<?php } ?>
		</div>
	</div>
	<table class="course-date-table" data-domtype="tip">
		<thead>
			<tr>
				<th class="tbs-hide"></th>
				<th class="tbs-hide"></th>
				<th class="tbs-hide"></th>
				<th class="course-date-col-date">From</th>
				<th class="course-date-col-duration">Days</th>
				<th class="course-date-col-title">Title</th>
				<th class="course-date-col-title">Location</th>
				<th class="course-date-col-price">Price *</th>
				<th class="course-date-col-status">Status</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $rows; ?>
		</tbody>
	</table>
</div>