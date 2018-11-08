<?php
get_header(); 
$course_settings = get_option('tbs_settings');
$course_page_nottice = isset($course_settings['course_page_nottice'])?$course_settings['course_page_nottice']:'';
//<div class="course-sbelement course-price"></div>		
?>

<main id="course-single">
    <div class="center">
        <div id="tts-course-wrap" class="clearfix">
            <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
            <?php
            $the_course = new TBS_Course(get_the_ID());
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h1 class="course-title"><?php the_title(); ?></h1>
                <div class="course-row">
                    <div class="course-sidebar">
                        <div class="course-sidebar-inner">
                            <?php if($the_course->certification_text || $the_course->certification_logo){ ?>
                            <div class="course-sbelement course-certification">
                                <h4>Certification</h4>
                                <div class="course-sbelement-content">
                                    <?php if($the_course->certification_logo){ ?>
                                    <p class="course-certification-logo"><img src="<?php echo esc_url($the_course->certification_logo); ?>" alt=""/></p>
                                    <?php } ?>
                                    <?php if($the_course->certification_text){ ?>
                                    <?php echo do_shortcode(wpautop($the_course->certification_text)); ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } ?>
                            
                            <?php if($the_course->duration_text){ ?>
                            <div class="course-sbelement course-duration">
                                <h4><?php echo do_shortcode(wpautop($the_course->duration_text)); ?></h4>
                            </div>
                            <?php } ?>
                            <?php if($the_course->price){ ?>
                            <div class="course-sbelement course-price">
                                <h4>
									<?php 
									add_filter('woocommerce_price_trim_zeros', '__return_true');
									echo $the_course->price_formatted(); 
									remove_filter('woocommerce_price_trim_zeros', '__return_true');
									?>
								</h4>
                                <?php if($the_course->price_includes){ ?>
                                <div class="course-sbelement-content course-includes">
                                    <h5>Includes:</h5>
                                    <?php echo $the_course->price_includes; ?>
                                </div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            
                            <?php if($the_course->location){ ?>
                            <div class="course-sbelement course-location">
                                <h4>Location</h4>
                                <?php 
                                echo '<ul>';
                                foreach($the_course->location as $cl){
									$location_group_term = get_term_by('slug', $cl, TBS_Custom_Types::get_location_group_data('type'));
                                    echo '<li>'.  $location_group_term->name . '</li>';
                                }
                                echo '</ul>';
                                ?>
                            </div>
                            <?php } ?>
                            <?php if($the_course->delegates){ ?>
                            <div class="course-sbelement course-delegates">
                                <h4>Delegates</h4>
                                <div class="course-sbelement-content">
                                    <?php echo do_shortcode(wpautop($the_course->delegates)); ?>
                                </div>
                            </div>
                            <?php } ?>
                            <?php 
							foreach($the_course->location as $cl){
								if( in_array($cl, array('onsite', 'nationaltc') )){
									continue;
								}
								$location_group_term = get_term_by('slug', $cl, TBS_Custom_Types::get_location_group_data('type'));
								?>
								<div class="course-sbelement course-book-in-bristol">
									<a title="" href="<?php echo get_page_link(361) . '?course_id=' . $the_course->id . '&location=' . $location_group_term->slug;?>">BOOK IN <?php echo $location_group_term->name; ?></a>
								</div>
							<?php
							}
							?>
                        </div>
                        <div class="course-enquiry-form">
                            <h5 class="course-ef-title">Enquire</h5>
                            <div class="course-efwrap">
                                <?php echo do_shortcode('[contact-form-7 id="2057" title="Course Quick Enquiry"]'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="course-content">
                        <div class="course-content-inner">
                            <div class="course-description">
                                <?php the_content(); ?>
                            </div>
                            
                            <?php if($the_course->who_needs_to_do_text){ ?>
                            <div class="course-normal-text course-who-needs">
                                <h3>Who Needs <?php the_title(); ?> </h3>
                                <?php echo $the_course->who_needs_to_do_text(); ?>
                            </div>
                            <?php } ?>
                            <?php if($the_course->offer){ ?>
                            <div class="course-normal-text course-offer">
                                <h3><strong>OFFER:</strong> <?php echo $the_course->offer; ?></h3>
                            </div>
                            <?php } ?>
                            <?php if($the_course->trainer){ ?>
                            <div class="course-normal-text course-trainer">
                                <h3>Trainer</h4>
                                <div class="course-trainer-details clearfix">
                                    <div class="course-trainer-photo">
                                        <?php echo $the_course->trainer->photo; ?>
                                    </div>
                                    <div class="course-trainer-description">
                                        <h5><?php echo $the_course->trainer->name; ?></h5>
                                        <div class="course-trainer-bio"><?php echo wpautop($the_course->trainer->bio); ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            
                            <div class="course-normal-text course-traingin-status">
                                <h3 id="course-dates-list">Course Dates</h3>
									<?php
									$queried_date = absint( get_query_var('date', 0) );
									if($queried_date){
										$date_args = array(
											'date_ids' => array($queried_date)
										);
									}else{
										$date_args = array(
											'type' => 'upcoming',
											'orderby' => 'start_date',
											'order' => 'ASC',
											'posts_per_page' => -1,
											'show_private' => false,
										);
									}
									$course_dates = $the_course->get_dates($date_args);

									if( count( $course_dates > 0)){
										tbs_get_template_part('course-date-loop', true, array('course' => $the_course, 'course_dates' => $course_dates, 'hide_table_filter' => (bool)$queried_date ));
									}
									?>
                            </div>
                            
							
                            <?php if($the_course->faqs){ ?> 
                            <div class="course-normal-text course-faqs">
                                <h3>FAQs</h3>
                                <ol class="course-faqs-list">
                                    <?php foreach($the_course->faqs as $faq){  ?>
                                    <li>
                                        <h4>Q: <?php echo $faq['q']; ?></h4>
                                        <p>A: <?php echo $faq['a']; ?></p>
                                    </li>
                                    <?php }?>
                                </ol>
                            </div>
                            <?php } ?>
                            
                            <?php if($the_course->testimonials){ ?> 
							<h3>Feedback on <?php the_title(); ?> Course</h3>
                            <div class="course-normal-text course-testimonials flexslider">
                                <div class="course-testimonials-wrap">
                                <?php foreach($the_course->testimonials as $testimonial){ ?> 
                                <div class="course-testimonial">
                                    <span class="tts-qoute-icon"></span>
                                    <?php if(!empty($testimonial['testimonial'])){ ?> 
                                    <div class="course-testimonial-text">
                                        <p><?php echo $testimonial['testimonial']; ?></p>
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
                                    <div class="course-testimonial-author-image"><?php if(!empty($testimonial['photo'])){ echo '<img alt="" src="'. esc_url($testimonial['photo']) .'" />'; }?></div>
                                    <?php } ?>  
                                    <span class="tts-qoute-icon-2"></span>
                                </div>
                                <?php }?> 
                                </div>
                            </div>
                            <?php } ?> 
                            
                            <?php if($the_course->terms_condition){ ?>
                            <div class="course-sbelement course-terms-conditions">
                                <h4>TERMS &amp; CONDITIONS</h4>
                                <div class="course-sbelement-content">
                                    <?php echo do_shortcode(wpautop($the_course->terms_condition)); ?>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </article><!-- #post-## -->

            <?php endwhile; // end of the loop. ?>
        </div>
    </div>
</main>
<?php 
add_action('wp_footer', 'tts_yanda_metrica'); 
function tts_yanda_metrica(){
?>
<script type="text/javascript"> (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter42342714 = new Ya.Metrika({ id:42342714, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/42342714" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<script type="text/javascript">
	function ttsSendQuotesYanda(){
		yaCounter42342714 && yaCounter42342714.reachGoal('Quick Quote sent');
	}
</script>
<?php
}
?>
<?php get_footer(); ?>