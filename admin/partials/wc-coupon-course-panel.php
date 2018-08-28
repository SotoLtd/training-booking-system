<?php
?>
<div id="course_usage_restriction_coupon_data" class="panel woocommerce_options_panel">
	<div class="options_group">
		<p class="form-field"><label><?php _e( 'Courses', TBS_i18n::get_domain_name() ); ?></label>
		<select id="tbs_courses" name="tbs_coupon_courses[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select courses&hellip;', TBS_i18n::get_domain_name() ); ?>">
				<?php
					$courses = get_posts(array(
						'posts_per_page'=> -1,
						'post_type'     => TBS_Custom_Types::get_course_data('type'),
						'orderby'       => 'title',
						'order'         => 'ASC',
					));
					$coupon_courses = get_post_meta($coupon_id, 'tbs_coupon_courses', true);
					if(!$coupon_courses){
						$coupon_courses = array();
					}
					foreach ( $courses as $course ) {
						echo '<option value="' . esc_attr( $course->ID ) . '"' . selected( in_array( $course->ID, $coupon_courses ), true, false ) . '>' . wp_kses_post( get_the_title($course) ) . '</option>';
					}
				?>
			</select> <?php echo wc_help_tip( __( 'Course that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ) ); ?></p>

	</div>
</div>