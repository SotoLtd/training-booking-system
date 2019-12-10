<?php
get_header(); 
$course_settings = get_option('tbs_settings');
$course_page_nottice = isset($course_settings['course_page_nottice'])?$course_settings['course_page_nottice']:'';
		
?>

<main>
    <div class="center">
        <div id="tts-course-wrap" class="col col1">
            <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
            <?php
            $the_course = new TBS_Course(get_the_ID());
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="course-row">
                    <div class="course-col course-col3">
                        <div class="course-title"><h1><?php the_title(); ?></h1></div>
                        <div class="course-qoute"><?php echo $the_course->quote; ?></div>
                        <div class="course-normal-text"><?php echo $the_course->short_description(); ?></div>
                        <div class="course-row">
                            <div class="course-col course-colfull course-stikers-row">
                                <div class="course-stickers">
                                    <?php
                                    foreach($the_course->stickers as $stiker){
                                        ?>
                                        <div class="course-stiker">
                                            <a href="<?php echo $stiker['hover'] ?>" title="" class="course-sticker-image">
                                                <img alt="" src="<?php echo $stiker['normal'] ?>" />
                                            </a>
                                        </div>
                                    <?php
                                    }
                                    unset($stiker);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php if($the_course->who_needs_to_do_text){ ?>
                        <h3 class="course-section-title">Who Needs to do this Course?</h3>
                        <div class="course-normal-text">
                            <?php echo $the_course->who_needs_to_do_text(); ?>
                        </div>
                        <?php } ?>
                        
                        <h3 class="course-section-title"><?php the_title(); ?> Course Content</h3>
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
                        <?php if($the_course->available_at_training_centre){ ?>
                        <div class="course-tick-graphics">
                            <?php if($the_course->trianing_center_map_graphic){ ?>
                            <a href="<?php echo $the_course->trianing_center_map_graphic; ?>" title="" class="course-sticker-image">
                             <?php } ?>
                            <img src="<?php echo get_stylesheet_directory_uri() ?>/images/course/avattcyes.png" alt=""/>
                            <?php if($the_course->trianing_center_map_graphic){ ?>
                            </a>
                             <?php } ?>
                        </div>
                        <?php } ?>
                        <div class="course-traingin-status">
                            <?php
                            if($the_course->linked_course_code && !$the_course->training_hide_events_table){
                                echo '<div class="course-training-name">Bristol</div>';
                                echo do_shortcode('[administrate_event_table_2 course="'.$the_course->linked_course_code.'"]');
                            } 
                            ?>
                        </div>
                        <?php if($the_course->available_training_centers){ ?>
                        <div class="other-locations">
                            <p>We also have training centres in:</p>
                            <p><strong><?php echo implode(', ', $the_course->available_training_centers); ?></strong></p>
                            <p>These are not available to book online. So please <a class="orrange" href="http://tts.websitestage.co.uk/enquire-here/" title="">enquire here</a> or phone <br/><a class="orrange" href="tel:01179711892" title="">0117 971 1892</a> for up-to-date availability.</p>
                        </div>
                        <?php } ?>
                        <?php if($the_course->available_at_customer_site){ ?>
                        <div class="course-tick-graphics">
                            <?php if($the_course->customer_site_map_graphic){ ?>
                            <a href="<?php echo $the_course->customer_site_map_graphic; ?>" title="" class="course-sticker-image">
                            <?php } ?>
                                <img src="<?php echo get_stylesheet_directory_uri() ?>/images/course/avatcsyes.png" alt=""/>
                            <?php if($the_course->customer_site_map_graphic){ ?>
                            </a>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <?php if(665 != get_the_ID()){ ?>
                        <div class="other-locations">
							<?php if($the_course->customer_site_instruction){ ?>
                            <div class="customer_site_instruction"><?php echo wpautop(do_shortcode($the_course->customer_site_instruction)); ?></div>
							<?php } ?>
                            <p>These are not available to book online. So please <a class="orrange" href="http://tts.websitestage.co.uk/enquire-here/" title="">enquire here</a> or phone <br/><a class="orrange" href="tel:01179711892" title="">0117 971 1892</a> for up-to-date availability.</p>
                        </div>
                        <?php }?>
                        
                        <div class="course-testimonials-section">
                            <h3 class="course-section-title">Testimonials</h3>
                            <div class="course-normal-text">What some of our customers that have done the course say...</div>
                            <?php foreach($the_course->testimonials as $testimonial){ ?>
                            <div class="course-testimonial">
                                <?php if(!empty($testimonial['testimonial'])){ ?>
                                <div class="course-testimonial-text">
                                    <span class="tts-qoute-icon"></span>
                                    <p><?php echo $testimonial['testimonial']; ?></p>
                                </div>
                                <?php } ?>
                                <div class="course-testimonials-autor">
                                    <div class="course-testimonial-author-image"><?php if(!empty($testimonial['photo'])){ echo '<img alt="" src="'. esc_url($testimonial['photo']) .'" />'; }?></div>
                                    <div class="course-testimonial-author-details">
                                        <p class="ctad-author-name-job-title">
                                            <?php if(!empty($testimonial['name'])){ echo $testimonial['name'];}?>
                                            <?php if(!empty($testimonial['job_title'])){ echo ' - '.$testimonial['job_title'];}?>
                                        </p>
                                        <?php if(!empty($testimonial['company_name'])){ ?>
                                        <p class="ctad-author-job-title">
                                            <?php echo $testimonial['company_name'];?>
                                        </p>
                                         <?php }?>
                                        <?php if(!empty($testimonial['city'])){ ?>
                                        <p class="ctad-author-city">
                                            <?php echo $testimonial['city'];?>
                                        </p>
                                         <?php }?>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                        </div>
						<?php if($course_page_nottice){ ?>
						<div class="course-notice"><?php echo do_shortcode(shortcode_unautop(wpautop(convert_chars(convert_smilies(wptexturize($course_page_nottice)))))); ?></div>
						<?php } ?>
                        
                    </div>
                    <div class="course-col course-col1 course-side-images">
                        <?php if($the_course->linked_course_code && !$the_course->training_hide_book_button){ ?>
                        <div class="book-now-pp">
                            <a title="" href="<?php echo get_page_link(361).'?tt_course_code='.$the_course->linked_course_code; ?>"><img alt="" src="<?php echo get_stylesheet_directory_uri(); ?>/images/course/book-now-squre.jpg"></a>
                        </div>
                        <?php }?>
                        <div class="book-now-pp">
                            <a title="" href="http://trainingsocieti.co.uk/6-or-more-delegates/"><img alt="" src="<?php echo get_stylesheet_directory_uri(); ?>/images/course/six-more-delegates-button.png"></a>
                        </div>
                        <div class="course-person-graphinc-1">
                            <?php
                            foreach($the_course->person_graphic_1 as $pg1){
                                echo '<img alt="" src="'. $pg1 .'" />';
                            }
                            unset($pg1);
                            ?>
                        </div>
                        <div class="course-person-graphinc-2">
                            <?php
                            foreach($the_course->person_graphic_2 as $pg1){
                                echo '<img alt="" src="'. $pg1 .'" />';
                            }
                            unset($pg1);
                            ?>
                        </div>
                        <div class="course-long-graphinc">
                            <?php
                            if($the_course->right_hand_long_graphic){
                                echo '<img alt="" src="'. $the_course->right_hand_long_graphic .'" />';
                            }
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