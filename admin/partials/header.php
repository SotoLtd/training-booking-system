<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?> 
<div class="wrap">
	<?php if(isset($page_title)) : ?>
	<h1><?php echo $page_title; ?></h1>
	<?php endif; ?> 
	
	<?php
	if ( isset($this->messages) && is_array( $this->messages ) && count($this->messages) > 0 ) {
		foreach ( $this->messages as $type => $messages ) {
			foreach($messages as $msg){
				echo '<div class="notice notice-'.$type.' is-dismissible"><p>'. $msg .'</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
			}
		}
	}
	?>
	
	<?php if(isset($tabs)): ?> 
	<div <?php if($tabs_container_id){echo ' id="'. $tabs_container_id .'"'; } ?> class="tbs-settings-tab clearfix">
		<ul class="subsubsub">
			<?php 
			$tab_num = 0;
			foreach($tabs as $tab): 
				$tab_num++;
			?> 
			<li>
				<a <?php if($current_tab_key == $tab['id']){echo 'class="current"';} ?> href="<?php echo self::url($tab['id']); ?>"><?php echo $tab['title']; ?></a>
				<?php if($tab_num < count($tabs)){echo ' | ';} ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?> 