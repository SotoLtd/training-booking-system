<?php
$course_cat = get_queried_object();
$course_cat_title = get_term_meta( $course_cat->term_id, '_title', true );
$course_cat_description = get_term_meta( $course_cat->term_id, '_description', true );
?>
<div class="entry-title"><?php echo $course_cat->name; ?></div>

<?php if($course_cat_title || $course_cat_description){
?>
<div class="course-cat-header">
    <?php if($course_cat_title){ ?><h1><?php echo $course_cat_title; ?></h1><?php } ?>
    <?php if($course_cat_description){ ?><div class="course-cate-desc"><?php echo $course_cat_description; ?></div><?php } ?>
</div>

<?php } ?>
