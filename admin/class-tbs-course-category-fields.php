<?php

class TBS_Course_Category_Fields {

	public static function add_fields() {
		?>
		<div class="form-field">
			<?php wp_nonce_field( 'save_course_cat_fields', '_tbs_ccn' ); ?>
			<label><?php _e( 'Featured Images', TBS_i18n::get_domain_name() ); ?></label>
			<div id="course_cat_featured_image" style="float:left;margin-right:10px;"><img src="<?php echo tbs_assets_url('admin/assets/images/placeholder.png'); ?>" width="60px" height="60px" /></div>
			<div style="line-height:60px;">
				<input type="hidden" id="course_cat_featured_image_id" name="_featured_image_id" />
				<button type="button" class="course-cat-ubutton button"><?php _e( 'Upload/Add image', TBS_i18n::get_domain_name() ); ?></button>
				<button type="button" class="course-cat-rbutton button"><?php _e( 'Remove image', TBS_i18n::get_domain_name() ); ?></button>
			</div>
			<script type="text/javascript">
		        // Only show the "remove image" button when needed
		        if ( !jQuery( '#course_cat_featured_image_id' ).val() ){
		            jQuery( '.course-cat-rbutton' ).hide();
				}
		        // Uploading files
		        var file_frame;
		        jQuery( document ).on( 'click', '.course-cat-ubutton', function ( event ) {
		            event.preventDefault();
		            // If the media frame already exists, reopen it.
		            if ( file_frame ) {
		                file_frame.open();
		                return;
		            }
		            // Create the media frame.
		            file_frame = wp.media.frames.downloadable_file = wp.media( {
		                title: '<?php _e( 'Choose an image', TBS_i18n::get_domain_name() ); ?>',
		                button: {
		                    text: '<?php _e( 'Use image', TBS_i18n::get_domain_name() ); ?>',
		                },
		                multiple: false
		            } );
		            // When an image is selected, run a callback.
		            file_frame.on( 'select', function () {
		                attachment = file_frame.state().get( 'selection' ).first().toJSON();

		                jQuery( '#course_cat_featured_image_id' ).val( attachment.id );
		                jQuery( '#course_cat_featured_image img' ).attr( 'src', attachment.url );
		                jQuery( '.course-cat-rbutton' ).show();
		            } );

		            // Finally, open the modal.
		            file_frame.open();
		        } );
		        jQuery( document ).on( 'click', '.course-cat-rbutton', function ( event ) {
		            jQuery( '#course_cat_featured_image img' ).attr( 'src', '<?php echo tbs_assets_url('admin/assets/images/placeholder.png'); ?>' );
		            jQuery( '#course_cat_featured_image_id' ).val( '' );
		            jQuery( '.course-cat-rbutton' ).hide();
		            return false;
		        } );
			</script>
			<div class="clear"></div>
		</div>
		<?php
	}

	public static function edit_fields( $term, $taxonomy ) {
		$title				 = get_term_meta( $term->term_id, '_title', true );
		$description		 = get_term_meta( $term->term_id, '_description', true );
		$disable_quote_sb	 = get_term_meta( $term->term_id, '_disable_quotes_sb', true );
		$image				 = '';
		$thumbnail_id		 = absint( get_term_meta( $term->term_id, '_featured_image_id', true ) );
		if ( $thumbnail_id )
			$image				 = wp_get_attachment_thumb_url( $thumbnail_id );
		else
			$image				 = tbs_assets_url('admin/assets/images/placeholder.png');
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Disable Qoutes Sidebar', TBS_i18n::get_domain_name() ); ?></label></th>
			<td>
				<div id="course_cate_description">
					<?php wp_nonce_field( 'save_course_cat_fields', '_tbs_ccn' ); ?>
					<input class="widefat" type="checkbox" name="cc_dis_quote_sb" value="1" <?php checked( $disable_quote_sb, 'yes', true ); ?>/>
				</div>
				<p class="description">To hide quotes sidebar.</p>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Featured Text Title', TBS_i18n::get_domain_name() ); ?></label></th>
			<td>
				<div id="course_cate_description">
					<input class="widefat" type="text" name="cc_title" value="<?php echo esc_attr( $title ); ?>"/>
				</div>
				<p class="description">This title will be shown the title on single category page.</p>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Featured Text', TBS_i18n::get_domain_name() ); ?></label></th>
			<td>
				<div id="course_cate_description">
					<?php
					wp_editor( $description, 'coursecatdescription', array(
						'media_buttons'	 => false,
						'textarea_rows'	 => 6,
						'teeny'			 => true
					) );
					?>
				</div>
				<p class="description">This text will be shown bellow the title and above the course list on single category page.</p>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Featured Image', TBS_i18n::get_domain_name() ); ?></label></th>
			<td>
				<div id="course_cat_featured_image" style="float:left;margin-right:10px;"><img src="<?php echo $image; ?>" width="60px" height="60px" /></div>
				<div style="line-height:60px;">
					<input type="hidden" id="course_cat_featured_image_id" name="_featured_image_id" value="<?php echo $thumbnail_id; ?>" />
					<button type="submit" class="course-cat-ubutton button"><?php _e( 'Upload/Add image', TBS_i18n::get_domain_name() ); ?></button>
					<button type="submit" class="course-cat-rbutton button"><?php _e( 'Remove image', TBS_i18n::get_domain_name() ); ?></button>
				</div>
				<script type="text/javascript">

		            // Uploading files
		            var file_frame;

		            jQuery( document ).on( 'click', '.course-cat-ubutton', function ( event ) {

		                event.preventDefault();

		                // If the media frame already exists, reopen it.
		                if ( file_frame ) {
		                    file_frame.open();
		                    return;
		                }

		                // Create the media frame.
		                file_frame = wp.media.frames.downloadable_file = wp.media( {
		                    title: '<?php _e( 'Choose an image', TBS_i18n::get_domain_name() ); ?>',
		                    button: {
		                        text: '<?php _e( 'Use image', TBS_i18n::get_domain_name() ); ?>',
		                    },
		                    multiple: false
		                } );

		                // When an image is selected, run a callback.
		                file_frame.on( 'select', function () {
		                    attachment = file_frame.state().get( 'selection' ).first().toJSON();

		                    jQuery( '#course_cat_featured_image_id' ).val( attachment.id );
		                    jQuery( '#course_cat_featured_image img' ).attr( 'src', attachment.url );
		                    jQuery( '.course-cat-rbutton' ).show();
		                } );

		                // Finally, open the modal.
		                file_frame.open();
		            } );

		            jQuery( document ).on( 'click', '.course-cat-rbutton', function ( event ) {
		                jQuery( '#course_cat_featured_image img' ).attr( 'src', '<?php echo tbs_assets_url('admin/assets/images/placeholder.png'); ?>' );
		                jQuery( '#course_cat_featured_image_id' ).val( '' );
		                jQuery( '.winter-thumb-rbutton' ).hide();
		                return false;
		            } );

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	public static function save_fields( $term_id, $tt_id, $taxonomy ) {
		//<?php wp_nonce_field( 'save_course_cat_fields', '_tbs_ccn' );
		if(empty($_POST['_tbs_ccn']) || !wp_verify_nonce($_POST['_tbs_ccn'], 'save_course_cat_fields')){
			return;
		}
		if ( isset( $_POST[ '_featured_image_id' ] ) ) {
			update_term_meta( $term_id, '_featured_image_id', absint( $_POST[ '_featured_image_id' ] ) );
		} else {
			delete_term_meta( $term_id, '_featured_image_id' );
		}
		if ( !empty( $_POST[ 'cc_dis_quote_sb' ] ) ) {
			update_term_meta( $term_id, '_disable_quotes_sb', 'yes' );
		} else {
			delete_term_meta( $term_id, '_disable_quotes_sb' );
		}

		if ( isset( $_POST[ 'cc_title' ] ) ) {
			update_term_meta( $term_id, '_title', $_POST[ 'cc_title' ] );
		} else {
			delete_term_meta( $term_id, '_title' );
		}

		if ( isset( $_POST[ 'coursecatdescription' ] ) ) {
			update_term_meta( $term_id, '_description', $_POST[ 'coursecatdescription' ] );
		} else {
			delete_term_meta( $term_id, '_description' );
		}
	}

}
