<article id="post-<?php $the_course->id; ?>" <?php post_class('', $the_course->id); ?>>
    <div class="course-title"><h1><a href="<?php echo get_permalink($the_course->id); ?>" title=""><?php echo get_the_title($the_course->id); ?></a></h1></div>
    <div class="course-normal-text"><?php echo $the_course->short_description(); ?></div>
    <div class="course-normal-text"><?php echo $the_course->content; ?></div>
    <div><a class="tts-course-readmore" href="<?php echo get_permalink($the_course->id); ?>" title="">Read More</a></div>
</article>