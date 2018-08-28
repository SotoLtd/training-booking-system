<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div class="wrap">
	<h1>Course date info for <strong><?php echo $this->course_date->get_course_title_with_date(); ?></strong></h1>
	<?php
	if ( is_array( $this->messages ) ) {
		foreach ( $this->messages as $message ) {
			echo $message;
		}
	}
	?>
	<div class="tbs-course-info-tab clearfix">
		<ul class="subsubsub">
			<?php 
			$tab_num = 0;
			$tabs = $this->get_course_info_tabs();
			$current_tab_key = $this->get_course_info_current_tab();
			foreach($tabs as $tab_key => $tab_name): 
				$tab_num++;
			?> 
			<li>
				<a <?php if($current_tab_key == $tab_key){echo 'class="current"';} ?> href="<?php echo self::url('view', array('course_date_id' => $this->course_date->get_id(), 'tab' => $tab_key,)); ?>"><?php echo $tab_name; ?></a>
				<?php if($tab_num < count($tabs)){echo ' | ';} ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	
	<div class="tbs-course-info-tab-content">
		<?php include $this->admin->get_partial('course-date-info-tab-' . $current_tab_key); ?>
	</div>
	
</div>