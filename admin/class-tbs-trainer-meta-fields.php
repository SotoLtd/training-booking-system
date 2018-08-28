<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TBS_Trainer_Meta_Fields {
	/**
	 * Display metbox
	 * @param type $post
	 */
	public static function display_meta_fields($post){
		wp_nonce_field( 'save_trainer_data', '_tbs_trainer_nonce' );
		self::do_meta_fields( $post->ID );
	}
	/**
	 * Display individual meta fields
	 * @param type $post_id
	 */
	static function do_meta_fields( $post_id = false ) {
		$fields = self::get_fields($post_id);
		foreach ( $fields as $meta_key => $meta_label ) {
			if ( method_exists( __CLASS__, $meta_key ) ) {
				call_user_func( array(__CLASS__, $meta_key), $meta_key, $meta_label, $post_id );
			}
		}
	}
	/**
	 * Get trainer fields
	 * @param type $post_id
	 * @return array
	 */
	public static function get_fields($post_id){
		$fields = array(
			'trainer_email' => 'Email address',
		 );
		return $fields;
	}
	/**
	 * Save location data
	 * @param type $post_id
	 * @return type
	 */
	public static function save_fields( $post_id ) {
		if ( !isset( $_POST['_tbs_trainer_nonce'] ) ) {
			return;
		}
		if ( !wp_verify_nonce( $_POST['_tbs_trainer_nonce'], 'save_trainer_data' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		/**
		 * @todo Create a new capability - manage_course
		 */
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = self::get_fields($post_id);
		
		foreach ( $fields as $meta_key => $label ) {
			if ( isset( $_POST[$meta_key] ) ) {
				if ( method_exists( 'TBS_Trainer_Meta_Fields', 'save_' . $meta_key ) ) {
					call_user_func( array("TBS_Trainer_Meta_Fields", 'save_' . $meta_key), $meta_key, $_POST[$meta_key], $post_id );
				} else {
					update_post_meta( $post_id, $meta_key, $_POST[$meta_key] );
				}
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}
	}
	
    /**
	 * Display Meta: 
	 * @param type $meta_key
	 * @param type $meta_label
	 * @param type $post_id
	 */
	static function trainer_email( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field">
				<input type="email" name="<?php echo $meta_key ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
				<p class="description">Enter trainer email address.</p>
			</div>
		</div>
		<?php
	}
}