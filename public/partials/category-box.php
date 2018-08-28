<?php
if(!empty($course_cat_id)){
    if(empty($course_cat_name)){
        $course_cat_name = get_term_field('name', $course_cat_id, 'course_category');
    }
    $featured_image = '';
    $featured_image = absint(get_term_meta( $course_cat_id, '_featured_image_id', true ) );
    if ( $featured_image )
        $featured_image = wp_get_attachment_thumb_url( $featured_image );
    else
        $featured_image = get_stylesheet_directory_uri().'/images/placeholder.png';
	
    $category_courses = get_posts(array(
        'posts_per_page'=> 3,
        'post_type'     => 'courses',
        'orderby'       => 'id',
        'order'         => 'DESC',
		'suppress_filters' => false,
		'meta_query'	=> array(
			'relation' => 'OR',
			array(
				'key' => 'is_private',
				'value' => 'yes',
				'compare' => '!=',
			),
			array(
				'key' => 'is_private',
				'value' => 'yes',
				'compare' => 'NOT EXISTS',
			)
		),
        'tax_query'     => array(
            array(
                'taxonomy'  => 'course_category',
                'field'     => 'id',
                'terms'     => array($course_cat_id)
            )
        )
    ));
?>
<div class="course-box course-box-2">
   
    <div class="course-box-content clearfix">
        <div class="course-box-thumb">
            <a href="<?php echo esc_url(get_term_link($course_cat_id, 'course_category')) ?>" title=""><img src="<?php echo esc_url($featured_image); ?>" alt="" /></a>
        </div>
        <div class="course-box-detials">
            <h2 class="course-box-title"><a href="<?php echo esc_url(get_term_link($course_cat_id, 'course_category')) ?>" title=""><?php echo $course_cat_name; ?></a></h2>
            <?php
            if($category_courses){
                echo '<ul>';
                foreach($category_courses as $cc){
                    echo '<li><a href="'. esc_url(get_permalink($cc->ID)) .'" title="">'. get_the_title($cc->ID) .'<span class="tts-icon">&gt;</span></a></li>';
                }
                echo '</ul>';
            }
                
            ?>
        </div>
    </div>
    <div class="course-box-more">
        <a href="<?php echo esc_url(get_term_link($course_cat_id, 'course_category')) ?>" title="">Full list of our  <?php echo $course_cat_name; ?> courses<span class="raquo">&gt;&gt;<span></a>
    </div>
</div>
<?php
}