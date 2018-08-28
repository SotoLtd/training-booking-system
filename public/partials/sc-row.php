<?php
//'id' => '',
//'classes' => '',
//$content

$classes = 'tts-row ' . $classes . ' clearfix';


?>
<div <?php if($id){echo 'id="' . $id . '"';} ?> class="<?php echo $classes; ?>"> 
    <?php echo do_shortcode($content); ?>
</div>

