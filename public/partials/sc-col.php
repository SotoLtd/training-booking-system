<?php
//'classes' => '',
//'size' => '1/2',
//$content

$size_class = '';
switch($size){
    case '1/1':
        $size_class = 'tts-col-1-1';
        break;
    case '1/2':
        $size_class = 'tts-col-1-2';
        break;
    case '1/3':
        $size_class = 'tts-col-1-3';
        break;
    case '1/4':
        $size_class = 'tts-col-1-4';
        break;
    case '1/5':
        $size_class = 'tts-col-1-5';
        break;
    case '1/6':
        $size_class = 'tts-col-1-6';
        break;
    default:
        $size_class = 'tts-col-1-2';
        break;
}
if('yes' == $last){
    $size_class .= ' tts-col-last';
}

$classes = 'tts-col ' . $size_class . $classes;


?>
<div class="<?php echo $classes; ?>"> 
    <?php echo do_shortcode($content); ?>
</div>

