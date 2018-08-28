<?php
get_header(); 
?>

<main>
    <div class="center">
        <div id="tts-course-wrap" class="col col1">
            <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
            <?php
            $the_course = new TTS_Course(get_the_ID());
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="course-row">
                    <div class="course-col course-col3">
                        <div class="course-title"><h1><?php the_title(); ?></h1></div>
                        <div class="course-accrediation-logos">
                            <img alt="" src="<?php echo get_stylesheet_directory_uri() ?>/images/course/by-training-logo.png" />
                            <?php
                            foreach($the_course->accreditation_logos as $accl){
                                echo '<img alt="" src="'. $accl .'" />';
                            }
                            unset($accl);
                            ?>
                        </div>
                        <div class="course-qoute"><?php echo $the_course->quote; ?></div>
                    </div>
                    <div class="course-col course-col1">
                        <?php
                        foreach($the_course->person_graphic_1 as $pg1){
                            echo '<img alt="" src="'. $pg1 .'" />';
                        }
                        unset($pg1);
                        ?>
                    </div>
                </div>
                <div class="course-row">
                    <div class="course-col course-colfull course-stikers-row">
                        <div class="course-stickers">
                            <?php
                            foreach($the_course->stickers as $stiker){
                                ?>
                                <div class="course-stiker">
                                    <img class="course-stiker-img" alt="<?php echo $stiker['label'] ?>" src="<?php echo $stiker['logo'] ?>" />
                                    <p style="display:none;"><?php echo $stiker['info'] ?></p>
                                </div>
                                <?php
                            }
                            unset($accl);
                            ?>
                        </div>
                    </div>
                    <p class="course-sticker-hover">...Hover over stickers for extra info!</p>
                </div>
                <div class="course-row">
                    <div class="course-col course-col3">
                        <div class="course-normal-text"><?php echo $the_course->short_description(); ?></div>
                        
                        <?php if($the_course->who_needs_to_do_text){ ?>
                        <h3 class="course-section-title">Who Needs to do this Course?</h3>
                        <div class="course-normal-text">
                            <?php echo $the_course->who_needs_to_do_text(); ?>
                        </div>
                        <?php } ?>
                        
                        <h3 class="course-section-title">Course Content</h3>
                        <div class="course-normal-text">
                            <?php the_content(); ?>
                        </div>
                        
                        
                        <?php if($the_course->benefits){ ?>
                        <h3 class="course-section-title">Benefits</h3>
                        <div class="course-normal-text">
                            <?php echo $the_course->benefits(); ?>
                        </div>
                        <?php } ?>
                        
                        <h3 class="course-section-title">Venues</h3>
                        
                    </div>
                    <div class="course-col course-col1">
                        <div class="course-person-graphinc-2">
                            <?php
                            foreach($the_course->person_graphic_2 as $pg1){
                                echo '<img alt="" src="'. $pg1 .'" />';
                            }
                            unset($pg1);
                            ?>
                        </div>
                    </div>
                </div>
            </article><!-- #post-## -->

            <?php endwhile; // end of the loop. ?>
        </div>
        <div class="col col2">
            <?php get_sidebar('right'); ?>
        </div>
        <div class="clear"></div>
    </div>
</main>

<?php get_footer(); ?>