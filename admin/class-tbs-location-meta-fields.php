<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TBS_Location_Meta_Fields {
	/**
	 * Display metbox
	 * @param type $post
	 */
	public static function display_meta_fields($post){
		wp_nonce_field( 'save_location_data', '_tbs_location_nonce' );
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
	 * Get location fields
	 * @param type $post_id
	 * @return array
	 */
	public static function get_fields($post_id){
		$fields = array(
			'dispay_frontend' => 'Display on front end',
			'map_url' => 'Map Url',
		 );
		return $fields;
	}
	/**
	 * Save location data
	 * @param type $post_id
	 * @return type
	 */
	public static function save_fields( $post_id ) {
		if ( !isset( $_POST['_tbs_location_nonce'] ) ) {
			return;
		}
		if ( !wp_verify_nonce( $_POST['_tbs_location_nonce'], 'save_location_data' ) ) {
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
				if ( method_exists( 'TBS_Location_Meta_Fields', 'save_' . $meta_key ) ) {
					call_user_func( array("TBS_Location_Meta_Fields", 'save_' . $meta_key), $meta_key, $_POST[$meta_key], $post_id );
				} else {
					update_post_meta( $post_id, $meta_key, $_POST[$meta_key] );
				}
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}
	}
	
	/**
	 * Display meta: display on front end
	 * @param type $meta_key
	 * @param type $meta_label
	 * @param type $post_id
	 */
	static function dispay_frontend( $meta_key, $meta_label, $post_id = false ){
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?> 
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-field tts-checkbox-group">
			<label><strong style="margin-right: 8px;"><?php echo $meta_label; ?></strong> <input type="checkbox" name="<?php echo $meta_key ?>" value="1" <?php checked( 'yes', $meta_value ) ?>/></label>
			</div>
		</div>
		<?php
	}
	/**
	 * Save Meta: Display
	 * @param type $meta_key
	 * @param type $meta_value
	 * @param type $post_id
	 */
	static function save_dispay_frontend( $meta_key, $meta_value, $post_id ) {
		if ( $meta_value ) {
			update_post_meta( $post_id, $meta_key, 'yes' );
		} else {
			delete_post_meta( $post_id, $meta_key );
		}
	}
	
    /**
	 * Display Meta: 
	 * @param type $meta_key
	 * @param type $meta_label
	 * @param type $post_id
	 */
	static function map_url( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field">
				<input type="text" name="<?php echo $meta_key ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
				<p class="description">Enter map url.</p>
			</div>
		</div>
		<?php
	}
}