<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class TBS_Course_Meta_Fields {
	
	public static function display_meta_fields($post){
		wp_nonce_field( 'save_cfg', '_cfgnonce' );
		self::do_meta_fields( $post->ID );
	}

	static function do_meta_fields( $post_id = false ) {
		$fields = self::get_fields($post_id);
		foreach ( $fields as $meta_key => $meta_label ) {
			if ( method_exists( __CLASS__, $meta_key ) ) {
				call_user_func( array(__CLASS__, $meta_key), $meta_key, $meta_label, $post_id );
			}
		}
	}
	
	public static function get_fields($post_id){
		$course_template = '';
		if($post_id){
			$course_template = get_post_meta($post_id, 'course_template', true);
		}
		if(!$course_template){
			$course_settings = get_option('tbs_settings');
			$course_template = isset($course_settings['course_template'])?$course_settings['course_template']:'';
		}
		if( 'new' == $course_template ){
		   $fields = array(
			   'is_private' => 'This is a private course',
			   'is_accredited' => 'This is a accredited course.',
				'course_template' => 'Template',
				'trainer' => 'Trainer',
				// Sidebar data
				'price' => 'Price (£)',
				'price_includes' => 'Price Includes',
				'location' => 'Location',
				'course_location' => 'Course Location',
				'duration' => 'Duration',
				'duration_text' => 'Duration Text',
				'course_time' => 'Start/finish time',
				'max_delegates' => 'Maximum number of delegates',
				'delegates' => 'Delegates Text',
				'certification_logo' => 'Certification Logo',
				'certification_text' => 'Certification Text',
				'joining_instruction' => 'Joining instructions',
				'course_map' => 'Course Map',
				// Main Content
				'who_needs_to_do_text' => 'Who needs to do this course text',
				'offer' => 'Offer',
				'cbenefits' => 'Benefits',
				'testimonials' => 'Testimonials',
				'faqs' => 'FAQs',
				'terms_condition' => 'Terms &amp; Conditions',

			); 
		}else{
			$fields = array(
				//'accreditation_logos'   => 'Accreditation Logos',
				'course_template' => 'Template',
				'person_graphic_1'					 => 'Person Graphic 1',
				'quote'								 => 'Quote',
				'stickers'							 => 'Stickers',
				'short_description'					 => 'Short Description',
				'person_graphic_2'					 => 'Person Graphic 2',
				'who_needs_to_do_text'				 => 'Who needs to do this course text',
				'benefits'							 => 'Benefits',
				'right_hand_long_graphic'			 => 'Right Hand Long Graphic',
				'available_at_training_centre'		 => 'Available at Training Centre',
				'trianing_center_map_graphic'		 => 'Training Center Map Graphic',
				'training_administrate_course'		 => 'Link to Administrate Course',
				'training_hide_book_button'			 => 'Hide "View Dates &amp; Book Now" Button',
				'training_hide_events_table'		 => 'Hide Administrate Events Table',
				'training_center_administrate_link'	 => 'Training Administrate Link',
				'available_training_centers'		 => 'Available Training Centres',
				'available_at_customer_site'		 => 'Available at Customer Site',
				'customer_site_map_graphic'			 => 'Customer Site Map Graphic',
				'customer_site_instruction'			 => 'Customer Site Instruction',
				'testimonials'						 => 'Testimonials'
			);
		}
		return $fields;
	}
	public static function save_fields( $post_id ) {
		if ( !isset( $_POST['_cfgnonce'] ) ) {
			return;
		}
		if ( !wp_verify_nonce( $_POST['_cfgnonce'], 'save_cfg' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = self::get_fields($post_id);
		foreach ( $fields as $meta_key => $label ) {
			if ( isset( $_POST[$meta_key] ) ) {
				if ( method_exists( 'TBS_Course_Meta_Fields', 'save_' . $meta_key ) ) {
					call_user_func( array("TBS_Course_Meta_Fields", 'save_' . $meta_key), $meta_key, $_POST[$meta_key], $post_id );
				} else {
					update_post_meta( $post_id, $meta_key, $_POST[$meta_key] );
				}
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}
	}

	static function course_template( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $meta_value ) ) {
			$course_template = '';
			if($post_id){
				$course_template = get_post_meta($post_id, 'course_template', true);
			}
			if(!$course_template){
				$course_settings = get_option('tbs_settings');
				$course_template = isset($course_settings['course_template'])?$course_settings['course_template']:'';
			}
			$meta_value = $course_template;
		}
		?>
		<div class="tts-mb-field-wrap">
                    <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                    <div class="tts-mb-field tts-select">
                        <select id="<?php echo $meta_key ?>" name="<?php echo $meta_key ?>">
                            <option value="old">Template 1</option>
                            <option value="new" <?php selected($meta_value, 'new', true); ?> >Template 2</option>
                        </select>
                        <p class="description">Select course template.</p>
                    </div>
		</div>
		<?php
	}

	static function is_private( $meta_key, $meta_label, $post_id = false ){
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?> 
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-field tts-checkbox-group">
			<label><strong style="margin-right: 8px;"><?php echo $meta_label; ?></strong> <input type="checkbox" name="<?php echo $meta_key ?>" value="1" <?php checked( 'yes', $meta_value ) ?>/></label>
			</div>
		</div>
		<?php
	}
	static function save_is_private( $meta_key, $meta_value, $post_id ) {
		if ( $meta_value ) {
			update_post_meta( $post_id, $meta_key, 'yes' );
		} else {
			delete_post_meta( $post_id, $meta_key );
		}
	}

	static function is_accredited( $meta_key, $meta_label, $post_id = false ){
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?> 
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-field tts-checkbox-group">
			<label><strong style="margin-right: 8px;"><?php echo $meta_label; ?></strong> <input type="checkbox" name="<?php echo $meta_key ?>" value="1" <?php checked( 'yes', $meta_value ) ?>/></label>
			</div>
		</div>
		<?php
	}
	static function save_is_accredited( $meta_key, $meta_value, $post_id ) {
		if ( $meta_value ) {
			update_post_meta( $post_id, $meta_key, 'yes' );
		} else {
			delete_post_meta( $post_id, $meta_key );
		}
	}

	static function accreditation_logos( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $meta_value ) ) {
			$meta_value = array();
		}
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><strong><?php echo $meta_label; ?></strong></div>
			<div class="tts-mb-field tts-checkbox-group">
				<?php
				$acc_logos = tts_get_acc_logos();
				foreach ( $acc_logos as $key => $logo ) {
					?> 
					<label><strong><?php echo $logo['label']; ?></strong>  <input type="checkbox" name="<?php echo $meta_key ?>[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( true, in_array( $key, $meta_value ), true ) ?> /></label>

					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	static function person_graphic_1( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $meta_value ) ) {
			$meta_value = array();
		}
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><strong><?php echo $meta_label; ?></strong></div>
			<div class="tts-mb-field tts-checkbox-group">
				<?php
				$acc_persons = tts_get_persons_graphics1();
				foreach ( $acc_persons as $key => $logo ) {
					?> 
					<label><strong><?php echo $logo['label']; ?></strong>  <input type="checkbox" name="<?php echo $meta_key ?>[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( true, in_array( $key, $meta_value ), true ) ?> /></label>

					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	static function quote( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field tts-textarea">
				<textarea id="<?php echo $meta_key ?>" name="<?php echo $meta_key ?>"><?php echo esc_textarea( $meta_value ); ?></textarea>
			</div>
		</div>
		<?php
	}

	static function stickers( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $meta_value ) ) {
			$meta_value = array();
		}
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><strong><?php echo $meta_label; ?></strong></div>
			<div class="tts-mb-field">
				<ul class="tts-stikers-ul clearfix">
					<?php
					$acc_stickers = tts_check_stickers_array( $meta_value );
					foreach ( $acc_stickers as $key => $st ) {
						?> 
						<li>
							<p class="ttsad-control"><span class="tts-sahandle dashicons dashicons-menu"></span><span class="tts-delete-sticker dashicons dashicons-no-alt"></span></p>

							<p class="small-stickers">
								<input type="hidden" name="<?php echo $meta_key . '[' . $key . ']'; ?>[normal]" value="<?php echo esc_attr( $st['normal'] ); ?>"/>
								<a class="tts-add-media tss-has-prev" href="#">Add small sticker</a>
								<span class="tts-media-prev"><?php if ( $st['normal'] ) { ?><img src="<?php echo esc_attr( $st['normal'] ); ?>"/><?php } ?></span>
							</p>

							<p class="hover-stickers">
								<input type="hidden" name="<?php echo $meta_key . '[' . $key . ']'; ?>[hover]" value="<?php echo esc_attr( $st['hover'] ); ?>"/>
								<a class="tts-add-media tss-has-prev" href="#">Add hover sticker</a>
								<span class="tts-media-prev"><?php if ( $st['hover'] ) { ?><img src="<?php echo esc_attr( $st['hover'] ); ?>"/><?php } ?></span>
							</p>
						</li>
						<?php
					}
					?>
				</ul>
				<a href="#" class="tts-add-stiker"><span class="dashicons dashicons-plus"></span></a>
			</div>
		</div>
		<?php
	}

	static function save_stickers( $meta_key, $meta_value, $post_id ) {
		$acc_stickers = tts_check_stickers_array( $meta_value, true );
		if ( $acc_stickers ) {
			update_post_meta( $post_id, $meta_key, $acc_stickers );
		} else {
			delete_post_meta( $post_id, $meta_key );
		}
	}

	static function short_description( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field tts-textarea">

				<?php
				wp_editor( $meta_value, 'ttscourse' . $meta_key, array(
					'wpautop'		 => true,
					'media_buttons'	 => false,
					'textarea_name'	 => $meta_key,
					'textarea_rows'	 => 5,
					'teeny'			 => true
				) );
				?>
			</div>
		</div>
		<?php
	}

	static function person_graphic_2( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $meta_value ) ) {
			$meta_value = array();
		}
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><strong><?php echo $meta_label; ?></strong></div>
			<div class="tts-mb-field tts-checkbox-group">
				<?php
				$acc_persons = tts_get_persons_graphics2();
				foreach ( $acc_persons as $key => $logo ) {
					?> 
					<label><strong><?php echo $logo['label']; ?></strong>  <input type="checkbox" name="<?php echo $meta_key ?>[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( true, in_array( $key, $meta_value ), true ) ?> /></label>

					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	static function benefits( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field tts-textarea">

				<?php
				wp_editor( $meta_value, 'ttscourse' . $meta_key, array(
					'wpautop'		 => true,
					'media_buttons'	 => false,
					'textarea_name'	 => $meta_key,
					'textarea_rows'	 => 5,
					'teeny'			 => true
				) );
				?>
			</div>
		</div>
		<?php
	}

	static function right_hand_long_graphic( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field tts-media-add">
				<input type="text" name="<?php echo $meta_key ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
				<a href="#" title="Add image using medoa library" class="tts-add-media">Add Image</a>
			</div>
		</div>
		<?php
	}

	static function available_training_centers( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $meta_value ) ) {
			$meta_value = array();
		}
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><strong><?php echo $meta_label; ?></strong></div>
			<div class="tts-mb-field tts-checkbox-group">
				<ul class="clearfix tts-mb-tcchecklists">
					<?php
					$training_centers = tts_get_course_locations();
					foreach ( $training_centers as $tc ) {
						?> 
						<li>
							<label><input type="checkbox" name="<?php echo $meta_key ?>[]" value="<?php echo esc_attr( $tc ); ?>" <?php checked( true, in_array( $tc, $meta_value ) ) ?>/><strong><?php echo $tc; ?></strong></label>
						</li>
			<?php
		}
		?>
				</ul>
			</div>
		</div>
		<?php
	}

	static function available_at_training_centre( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = (bool) get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label><strong><?php echo $meta_label; ?></strong> <input type="checkbox" name="<?php echo $meta_key ?>" value="1" <?php checked( 1, $meta_value, true ); ?> /></label></div>
		</div>
		<?php
	}

	static function trianing_center_map_graphic( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field tts-media-add">
				<input type="text" name="<?php echo $meta_key ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
				<a href="#" title="Add image using medoa library" class="tts-add-media">Add Image</a>
			</div>
		</div>
		<?php
	}
	
	static function joining_instruction($meta_key, $meta_label, $post_id = false){
		if ( !metadata_exists( 'post', $post_id, $meta_key ) ) {
			$meta_value = "";
		} else {
			$meta_value = get_post_meta( $post_id, $meta_key, true );
		}
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field tts-textarea">

				<?php
				wp_editor( $meta_value, 'ttscourse' . $meta_key, array(
					'textarea_name'	 => $meta_key,
				) );
				?>
			</div>
		</div>
		<?php
	}

	static function customer_site_instruction( $meta_key, $meta_label, $post_id = false ) {
		if ( !metadata_exists( 'post', $post_id, $meta_key ) ) {
			$meta_value = "<strong>We can deliver this training anywhere in the South of England, click above to view the area we cover on a map.</strong>";
		} else {
			$meta_value = get_post_meta( $post_id, $meta_key, true );
		}
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field tts-textarea">

				<?php
				wp_editor( $meta_value, 'ttscourse' . $meta_key, array(
					'wpautop'		 => true,
					'media_buttons'	 => false,
					'textarea_name'	 => $meta_key,
					'textarea_rows'	 => 5,
					'teeny'			 => true
				) );
				?>
			</div>
		</div>
		<?php
	}

	static function training_center_administrate_link( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field">
				<input type="text" name="<?php echo $meta_key ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
			</div>
		</div>
		<?php
	}

	static function available_at_customer_site( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = (bool) get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label><strong><?php echo $meta_label; ?></strong> <input type="checkbox" name="<?php echo $meta_key ?>" value="1" <?php checked( 1, $meta_value, true ); ?> /></label></div>
		</div>
		<?php
	}

	static function training_administrate_course( $meta_key, $meta_label, $post_id = false ) {
		if ( !class_exists( 'Administrate' ) ) {
			return '';
		}
		$meta_value	 = get_post_meta( $post_id, $meta_key, true );
		$ad_courses	 = get_adminstrate_courses();
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field">
				<select name="<?php echo $meta_key; ?>">
					<option value="">Select a course</option>
					<?php
					foreach ( $ad_courses as $corse_code => $course ) {
						?>
						<option value="<?php echo esc_attr( $corse_code ); ?>" <?php selected( $corse_code, $meta_value, true ); ?>><?php echo $course->get_title(); ?></option>
			<?php
		}
		?>
				</select>
			</div>
		</div>
		<?php
	}

	static function save_training_administrate_course( $meta_key, $meta_value = '', $post_id = '' ) {
		if ( !class_exists( 'Administrate' ) ) {
			return '';
		}
		update_post_meta( $post_id, $meta_key, $meta_value );
	}

	static function training_hide_book_button( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = (bool) get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label><strong><?php echo $meta_label; ?></strong> <input type="checkbox" name="<?php echo $meta_key ?>" value="1" <?php checked( 1, $meta_value, true ); ?> /></label></div>
		</div>
		<?php
	}

	static function training_hide_events_table( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = (bool) get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label><strong><?php echo $meta_label; ?></strong> <input type="checkbox" name="<?php echo $meta_key ?>" value="1" <?php checked( 1, $meta_value, true ); ?> /></label></div>
		</div>
		<?php
	}

	static function customer_site_map_graphic( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field tts-media-add">
				<input type="text" name="<?php echo $meta_key ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
				<a href="#" title="Add image using medoa library" class="tts-add-media">Add Image</a>
			</div>
		</div>
		<?php
	}
        
	static function price( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
                    <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                    <div class="tts-mb-field">
                        <input type="text" name="<?php echo $meta_key ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
                        <p class="description">For public course enter price person. For private course this will be whole price.</p>
                    </div>
		</div>
		<?php
	}
        
	static function price_includes( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
                    <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                    <div class="tts-mb-field tts-textarea">
                        <textarea type="text" name="<?php echo $meta_key ?>"><?php echo esc_textarea($meta_value); ?></textarea>
                        <p class="description">Enter each item in a line.</p>
                    </div>
		</div>
		<?php
	}
        

	static function location( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $meta_value ) ) {
			$meta_value = array();
		}
		$location_gropus = get_terms(array(
			'taxonomy'	 => TBS_Custom_Types::get_location_group_data('type'),
			'orderby'	 => 'name',
			'order'		 => 'ASC',
			'hide_empty' => false,
			'parent'	 => 0
		));
		?>
		<div class="tts-mb-field-wrap">
                    <div class="tts-mb-label"><strong><?php echo $meta_label; ?></strong></div>
                    <div class="tts-mb-field tts-checkbox-group">
                        <ul class="clearfix tts-mb-tcchecklists">
							<?php foreach($location_gropus as $group_obj): ?>
								<li><label><input type="checkbox" name="<?php echo $meta_key ?>[]" value="<?php echo $group_obj->slug; ?>" <?php checked( true, in_array( $group_obj->slug, $meta_value ) ) ?>/><strong><?php echo $group_obj->name; ?></strong></label></li>
							<?php endforeach; ?>
                        </ul>
                        <p class="description">Select location of the course.</p>
                    </div>
		</div>
		<?php
	}
        
	static function duration( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
                    <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                    <div class="tts-mb-field tts-textarea">
						<input type="text" name="<?php echo $meta_key ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
                        <p class="description">Enter course duration.</p>
                    </div>
		</div>
		<?php
	}
        
	static function duration_text( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
                    <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                    <div class="tts-mb-field tts-textarea">
                        <textarea type="text" name="<?php echo $meta_key ?>"><?php echo esc_textarea($meta_value); ?></textarea>
                        <p class="description">Enter course duration text.</p>
                    </div>
		</div>
		<?php
	}
        
	static function course_time( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
                    <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                    <div class="tts-mb-field tts-textarea">
						<input type="text" name="<?php echo $meta_key ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
                        <p class="description">Enter course start/finish time.</p>
                    </div>
		</div>
		<?php
	}
        
	static function max_delegates( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
                    <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                    <div class="tts-mb-field tts-textarea">
                        <input type="number" name="<?php echo $meta_key ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
                        <p class="description">Enter course maximum number of delegates.</p>
                    </div>
		</div>
		<?php
	}
        
	static function delegates( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
                    <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                    <div class="tts-mb-field tts-textarea">
                        <textarea type="text" name="<?php echo $meta_key ?>"><?php echo esc_textarea($meta_value); ?></textarea>
                        <p class="description">Enter course delegates text.</p>
                    </div>
		</div>
		<?php
	}

	static function certification_logo( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field tts-media-add">
				<input type="text" name="<?php echo $meta_key ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
				<a href="#" title="Add image using media library" class="tts-add-media">Add Logo</a>
                                
                            <p class="description">Add course certification logo.</p>
			</div>
		</div>
		<?php
	}

	static function course_map( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field tts-media-add">
				<input type="text" name="<?php echo $meta_key ?>" value="<?php echo esc_attr( $meta_value ); ?>" />
				<a href="#" title="Select file from media library" class="tts-add-media">Open media library</a>
                <p class="description">Add course map photo.</p>
			</div>
		</div>
		<?php
	}
        
	static function certification_text( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
                    <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                    <div class="tts-mb-field tts-textarea">
                        <textarea type="text" name="<?php echo $meta_key ?>"><?php echo esc_textarea($meta_value); ?></textarea>
                        <p class="description">Enter course certification text.</p>
                    </div>
		</div>
		<?php
	}
        

	static function who_needs_to_do_text( $meta_key, $meta_label, $post_id = false ) {
            $meta_value = get_post_meta( $post_id, $meta_key, true );
            ?>
            <div class="tts-mb-field-wrap">
                <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                <div class="tts-mb-field tts-textarea">

                    <?php
                    wp_editor( $meta_value, 'ttscourse' . $meta_key, array(
                        'wpautop'		 => true,
                        'media_buttons'	 => false,
                        'textarea_name'	 => $meta_key,
                        'textarea_rows'	 => 5,
                        'teeny'			 => true
                    ) );
                    ?>
                </div>
            </div>
            <?php
	}

        
	static function offer( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
                    <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                    <div class="tts-mb-field tts-textarea">
                        <textarea type="text" name="<?php echo $meta_key ?>"><?php echo esc_textarea($meta_value); ?></textarea>
                        <p class="description">Enter course offer text.</p>
                    </div>
		</div>
		<?php
	}
	
	static function course_location( $meta_key, $meta_label, $post_id = false ){
		$meta_value	 = get_post_meta( $post_id, $meta_key, true );
		$locations	 = get_posts(array(
			'post_type' => TBS_Custom_Types::get_location_data( 'type' ),
			'numberposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		));
		if(!$locations || is_wp_error($locations)){
			$locations = array();
		}
		?>
		<div class="tts-mb-field-wrap">
			<div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
			<div class="tts-mb-field">
				<select name="<?php echo $meta_key; ?>">
					<option value="">Select a Location</option>
					<?php
					foreach ( $locations as $location ) {
						?>
						<option value="<?php echo esc_attr( $location->ID ); ?>" <?php selected( $location->ID, $meta_value, true ); ?>><?php echo get_the_title($location->ID); ?></option>
					<?php
		}
		?>
				</select>

				<p class="description">Select location of the course.</p>
			</div>
		</div>
		<?php
	}


	static function trainer( $meta_key, $meta_label, $post_id = false ) {
            $meta_value	 = get_post_meta( $post_id, $meta_key, true );
            $trainers	 = get_posts(array(
                'post_type' => 'trainer',
                'numberposts' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
            ));
            if(!$trainers || is_wp_error($trainers)){
                $trainers = array();
            }
            ?>
            <div class="tts-mb-field-wrap">
                <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                <div class="tts-mb-field">
                    <select name="<?php echo $meta_key; ?>">
                        <option value="">Select a trainer</option>
                        <?php
                        foreach ( $trainers as $trainer ) {
                            ?>
                            <option value="<?php echo esc_attr( $trainer->ID ); ?>" <?php selected( $trainer->ID, $meta_value, true ); ?>><?php echo get_the_title($trainer->ID); ?></option>
                        <?php
            }
            ?>
                    </select>
                    
                    <p class="description">Select trainer of the course.</p>
                </div>
            </div>
            <?php
	}
        
        static function cbenefits( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $meta_value ) ) {
			$meta_value = array(0 => '');
		} else {
			$meta_value = array_values( $meta_value );
		}
		?>
		<div class="tts-mb-field-wrap ttsmb-cloneable-fieldset">
                    <div class="tts-mb-label"><strong><?php echo $meta_label; ?></strong></div>
                    <div class="tts-mb-clone-wrap">

		<?php foreach ( $meta_value as $k => $value ) { ?>
                        <div class="tts-mb-field-group tts-field-cloneable">
                            <span class="tts-mb-clone-delete dashicons dashicons-dismiss"></span>
                            <span class="tts-mb-clone-sahandle dashicons dashicons-sort"></span>
                            <div class="tts-mb-field-wrap ttsmb-inline">
                                <label for="<?php echo $meta_key . '-' . $k . '-text' ?>">Benefit <span class="tts-label-num"><?php echo $k+1; ?></span> </label>
                                <div class="tts-mb-field tts-textarea">
                                    <textarea id="<?php echo $meta_key . '-' . $k . '-text' ?>" type="text" name="<?php echo $meta_key . '[]' ?>" ><?php echo esc_textarea( $value ); ?></textarea>
                                </div>
                            </div>
                        </div>
		<?php } ?>
			</div>
			<span class="tts-mb-clone-add dashicons dashicons-plus-alt"></span>
		</div>
		<?php
	}
        
	static function testimonials( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $meta_value ) ) {
			$meta_value = array(
				0 => array(
					'name'			 => '',
					'job_title'		 => '',
					'company_name'	 => '',
					'city'			 => '',
					'photo'			 => '',
					'testimonial'	 => ''
				)
			);
		} else {
			$meta_value = array_values( $meta_value );
		}
		?>
		<div class="tts-mb-field-wrap ttsmb-cloneable-fieldset">
			<div class="tts-mb-label"><strong><?php echo $meta_label; ?></strong></div>
			<div class="tts-mb-clone-wrap">

		<?php foreach ( $meta_value as $k => $value ) { ?>
					<div class="tts-mb-field-group tts-field-cloneable">
						<span class="tts-mb-clone-delete dashicons dashicons-dismiss"></span>
						<span class="tts-mb-clone-sahandle dashicons dashicons-sort"></span>
						<div class="tts-mb-field-wrap ttsmb-inline">
							<label for="<?php echo $meta_key . '-' . $k . '-name' ?>">Name</label>
							<div class="tts-mb-field">
								<input id="<?php echo $meta_key . '-' . $k . '-name' ?>" type="text" name="<?php echo $meta_key . '[' . $k . '][name]' ?>" value="<?php echo esc_attr( $value['name'] ); ?>"/>
							</div>
						</div>
						<div class="tts-mb-field-wrap ttsmb-inline">
							<label for="<?php echo $meta_key . '-' . $k . '-job_title' ?>">Job Title</label>
							<div class="tts-mb-field">
								<input id="<?php echo $meta_key . '-' . $k . '-job_title' ?>" type="text" name="<?php echo $meta_key . '[' . $k . '][job_title]' ?>" value="<?php echo esc_attr( $value['job_title'] ); ?>"/>
							</div>
						</div>
						<div class="tts-mb-field-wrap ttsmb-inline">
							<label for="<?php echo $meta_key . '-' . $k . '-company_name' ?>">Company Name</label>
							<div class="tts-mb-field">
								<input id="<?php echo $meta_key . '-' . $k . '-company_name' ?>" type="text" name="<?php echo $meta_key . '[' . $k . '][company_name]' ?>" value="<?php echo esc_attr( $value['company_name'] ); ?>"/>
							</div>
						</div>
						<div class="tts-mb-field-wrap ttsmb-inline">
							<label for="<?php echo $meta_key . '-' . $k . '-city' ?>">City</label>
							<div class="tts-mb-field">
								<input id="<?php echo $meta_key . '-' . $k . '-city' ?>" type="text" name="<?php echo $meta_key . '[' . $k . '][city]' ?>" value="<?php echo esc_attr( $value['city'] ); ?>"/>
							</div>
						</div>
						<div class="tts-mb-field-wrap ttsmb-inline">
							<label for="<?php echo $meta_key . '-' . $k . '-photo' ?>">Photo</label>
							<div class="tts-mb-field tts-media-add">
								<input id="<?php echo $meta_key . '-' . $k . '-photo' ?>" type="text" name="<?php echo $meta_key . '[' . $k . '][photo]' ?>" value="<?php echo esc_attr( $value['photo'] ); ?>"/>
								<a href="#" title="Add image using medoa library" class="tts-add-media">Add Image</a>
							</div>
						</div>
						<div class="tts-mb-field-wrap ttsmb-inline">
							<label for="<?php echo $meta_key . '-' . $k . '-testimonial' ?>">Testimonial</label>
							<div class="tts-mb-field tts-textarea">
								<textarea id="<?php echo $meta_key . '-' . $k . '-testimonial' ?>" type="text" name="<?php echo $meta_key . '[' . $k . '][testimonial]' ?>" ><?php echo esc_textarea( $value['testimonial'] ); ?></textarea>
							</div>
						</div>
					</div>
		<?php } ?>
			</div>
			<span class="tts-mb-clone-add dashicons dashicons-plus-alt"></span>
		</div>
		<?php
	}
        
        static function faqs( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $meta_value ) ) {
                    $meta_value = array(
                        0 => array(
                            'q' => '',
                            'a' => '',
                        )
                    );
		} else {
                    $meta_value = array_values( $meta_value );
		}
		?>
		<div class="tts-mb-field-wrap ttsmb-cloneable-fieldset">
                    <div class="tts-mb-label"><strong><?php echo $meta_label; ?></strong></div>
                    <div class="tts-mb-clone-wrap">

		<?php foreach ( $meta_value as $k => $value ) { ?>
                        <div class="tts-mb-field-group tts-field-cloneable">
                            <span class="tts-mb-clone-delete dashicons dashicons-dismiss"></span>
                            <span class="tts-mb-clone-sahandle dashicons dashicons-sort"></span>
                            <div class="tts-mb-field-wrap ttsmb-inline">
                                <label for="<?php echo $meta_key . '-' . $k . '-q' ?>">Question <span class="tts-label-num"><?php echo $k+1; ?></span></label>
                                <div class="tts-mb-field">
                                    <input id="<?php echo $meta_key . '-' . $k . '-q' ?>" type="text" name="<?php echo $meta_key . '[' . $k . '][q]' ?>" value="<?php echo esc_attr( $value['q'] ); ?>"/>
                                </div>
                            </div>
                            <div class="tts-mb-field-wrap ttsmb-inline">
                                <label for="<?php echo $meta_key . '-' . $k . '-a' ?>">Answer <span class="tts-label-num"><?php echo $k+1; ?></span> </label>
                                <div class="tts-mb-field tts-textarea">
                                    <textarea id="<?php echo $meta_key . '-' . $k . '-a' ?>" class="sd-visual-editor" type="text" name="<?php echo $meta_key . '[' . $k . '][a]' ?>" ><?php echo esc_textarea( $value['a'] ); ?></textarea>
                                </div>
                            </div>
                        </div>
		<?php } ?>
			</div>
			<span class="tts-mb-clone-add dashicons dashicons-plus-alt"></span>
		</div>
		<?php
	}

        
	static function terms_condition( $meta_key, $meta_label, $post_id = false ) {
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		?>
		<div class="tts-mb-field-wrap">
                    <div class="tts-mb-label"><label for="<?php echo $meta_key ?>"><strong><?php echo $meta_label; ?></strong></label></div>
                    <div class="tts-mb-field tts-textarea">
                        <textarea type="text" name="<?php echo $meta_key ?>"><?php echo esc_textarea($meta_value); ?></textarea>
                        <p class="description">Enter course terms &amp; conditions text.</p>
                    </div>
		</div>
		<?php
	}
        
        
}
