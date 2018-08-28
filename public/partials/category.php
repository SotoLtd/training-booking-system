<?php
get_header(); 
?>

<main>
    <div class="center">
        <div id="tts-course-wrap" class="col col1">
            <?php tbs_get_template_part('category-header'); ?>
            <div class="course-box-row clearfix">
                <?php 
                if ( have_posts() ) while ( have_posts() ) : the_post();
                $featured_image = '';
                if (has_post_thumbnail() ){
                    $featured_image = wp_get_attachment_thumb_url( get_post_thumbnail_id() );
                }else{
                    $featured_image = get_stylesheet_directory_uri().'/images/placeholder.png';
                }
                $the_course = new TBS_Course(get_the_ID());
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('course-box course-box-2'); ?>>
                    <div class="course-box-content clearfix">
                        <div class="course-box-thumb">
                            <a href="<?php echo the_permalink(); ?>" title=""><img src="<?php echo esc_url($featured_image); ?>" alt="" /></a>
                        </div>
                        <div class="course-box-detials">
                            <h2 class="course-box-title"><a href="<?php echo the_permalink(); ?>" title=""><?php the_title(); ?></a></h2>
                            <div class="course-box-st">
                                <?php echo $the_course->excerpt; ?>
                            </div>
                            <div class="course-box-more">
                                <a href="<?php echo the_permalink(); ?>" title="">Full course detail…</a>
                            </div>
                        </div>
                    </div>
                </article><!-- #post-## -->

                <?php endwhile; // end of the loop. ?>
                
            </div>
        </div>
        <div class="col col2">
            <?php get_sidebar('right'); ?>
        </div>
        <div class="clear"></div>
    </div>
</main>

<?php get_footer(); ?>