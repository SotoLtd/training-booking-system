<?php

class TBS_Course {

	public $id;
	public $accreditation_logos;
	public $person_graphic_1;
	public $quote;
	public $stickers;
	public $short_description;
	public $person_graphic_2;
	public $benefits;
	public $right_hand_long_graphic;
	public $available_at_training_centre;
	public $trianing_center_map_graphic;
	public $training_center_administrate_link;
	public $available_at_customer_site;
	public $customer_site_map_graphic;
	public $customer_site_instruction;
	public $linked_course_code;
	public $training_hide_book_button;
	public $training_hide_events_table;
	public $price;
	public $price_includes;
	public $location;
	public $course_location;
	public $duration_text;
	public $duration;
	public $max_delegates;
	public $delegates;
	public $certification_logo;
	public $certification_text;
	public $who_needs_section_title;
	public $who_needs_to_do_text;
	public $trainer;
	public $offer;
	public $cbenefits;
	public $testimonials_section_title;
	public $testimonials;
	public $terms_condition;
	public $faqs;
	public $loc_britol = false;
	public $joining_instruction;
	public $is_accredited;
	public $is_private;
	public $course_time;
	public $course_map;

	public function __construct( $post_id ) {
		$course = get_post( $post_id );
		if ( ! $course ) {
			$this->id = false;
		} else {
			$this->id                                = $post_id;
			$this->post_title                        = $course->post_title;
			$this->post_name                         = $course->post_name;
			$this->accreditation_logos               = $this->accreditation_logos();
			$this->person_graphic_1                  = $this->person_graphic_1();
			$this->person_graphic_2                  = $this->person_graphic_2();
			$this->quote                             = get_post_meta( $post_id, 'quote', true );
			$this->stickers                          = $this->stickers();
			$this->short_description                 = get_post_meta( $post_id, 'short_description', true );
			$this->content                           = apply_filters( 'the_content', $course->post_content );
			$this->benefits                          = get_post_meta( $post_id, 'benefits', true );
			$this->right_hand_long_graphic           = get_post_meta( $post_id, 'right_hand_long_graphic', true );
			$this->available_at_training_centre      = (bool) get_post_meta( $post_id, 'available_at_training_centre', true );
			$this->trianing_center_map_graphic       = get_post_meta( $post_id, 'trianing_center_map_graphic', true );
			$this->training_center_administrate_link = get_post_meta( $post_id, 'training_center_administrate_link', true );
			$this->available_training_centers        = $this->available_training_centers();
			$this->available_at_customer_site        = (bool) get_post_meta( $post_id, 'available_at_customer_site', true );
			$this->customer_site_map_graphic         = get_post_meta( $post_id, 'customer_site_map_graphic', true );
			$this->customer_site_instruction         = get_post_meta( $post_id, 'customer_site_instruction', true );
			$this->excerpt                           = $course->post_excerpt;
			$this->training_hide_book_button         = (bool) get_post_meta( $post_id, 'training_hide_book_button', true );


			$this->linked_course_code         = get_post_meta( $post_id, 'training_administrate_course', true );
			$this->training_hide_events_table = (bool) get_post_meta( $post_id, 'training_hide_events_table', true );
			$this->price                      = get_post_meta( $post_id, 'price', true );
			$this->price_includes             = $this->price_includes();
			$this->location                   = $this->location();
			$this->course_location            = $this->course_location();
			$this->duration                   = get_post_meta( $post_id, 'duration', true );
			$this->duration_text              = get_post_meta( $post_id, 'duration_text', true );
			$this->max_delegates              = get_post_meta( $post_id, 'max_delegates', true );
			$this->delegates                  = get_post_meta( $post_id, 'delegates', true );
			$this->certification_logo         = get_post_meta( $post_id, 'certification_logo', true );
			$this->certification_text         = get_post_meta( $post_id, 'certification_text', true );
			$this->trainer                    = $this->trainer();
			$this->who_needs_section_title    = get_post_meta( $post_id, 'who_needs_section_title', true );
			$this->who_needs_to_do_text       = get_post_meta( $post_id, 'who_needs_to_do_text', true );
			$this->offer                      = get_post_meta( $post_id, 'offer', true );
			$this->cbenefits                  = $this->cbenefits();
			$this->testimonials_section_title = get_post_meta( $post_id, 'testimonials_section_title', true );
			$this->testimonials               = $this->testimonials();
			$this->faqs                       = $this->faqs();
			$this->terms_condition            = get_post_meta( $post_id, 'terms_condition', true );
			$this->joining_instruction        = get_post_meta( $post_id, 'joining_instruction', true );
			$this->course_time                = get_post_meta( $post_id, 'course_time', true );
			$this->course_map                 = get_post_meta( $post_id, 'course_map', true );

			$this->is_accredited = 'yes' == get_post_meta( $post_id, 'is_accredited', true );
			$this->is_private    = 'yes' == get_post_meta( $post_id, 'is_private', true );
		}
	}

	public function exists() {
		return (bool) $this->id;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_dates( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'type'                     => 'all', // all, current, expired, upcoming
			'orderby'                  => 'start_date',
			'order'                    => 'ASC', // ASC, DESC
			'trainer'                  => '',
			'location'                 => '',
			'posts_per_page'           => - 1,
			'date_ids'                 => '',
			'show_private'             => true,
			'availability_offset_time' => 0,
			'json_model'               => false,
		) );
		extract( $args );
		$meta_query               = array();
		$meta_query['relation']   = 'AND';
		$meta_query[]             = array(
			'key'     => '_is_tbs_course',
			'value'   => 'yes',
			'compare' => '=',
		);
		$meta_query[]             = array(
			'key'     => '_tbs_course',
			'value'   => $this->id,
			'compare' => '=',
		);
		$meta_query['start_date'] = array(
			'key'     => '_tbs_start_date',
			'compare' => 'EXISTS',
		);
		$availability_offset      = $this->get_availabilyt_offset( $availability_offset_time );
		$now                      = time() + $availability_offset;
		if ( 'upcoming' == $type ) {
			$meta_query['start_date'] = array(
				'key'     => '_tbs_start_date',
				'value'   => $now,
				'compare' => '>',
			);
		}
		if ( ! $show_private ) {
			$meta_query['is_private'] = array(
				'key'     => '_tbs_is_private',
				'value'   => 'yes',
				'compare' => '!=',
			);
		}

		$orderby_clauses = array( $orderby => $order );
		//		post__in 
		if ( is_array( $date_ids ) && count( $date_ids ) > 0 ) {
			$query_args = array(
				'post_type'      => 'product',
				'posts_per_page' => - 1,
				'post__in'       => $date_ids,
				'orderby'        => 'post__in',
			);
		} else {
			$query_args = array(
				'post_type'      => 'product',
				'posts_per_page' => - 1,
				'meta_query'     => $meta_query,
				'orderby'        => $orderby_clauses,
			);
		}

		//$woo_products = wc_get_products($query_args);
		$date_query = new WP_Query( $query_args );
		if ( ! $date_query->have_posts() ) {
			return array();
		}
		$course_dates = array();
		while ( $date_query->have_posts() ) {
			$date_query->the_post();
			$course_date = new TBS_Course_Date( get_the_ID() );
			if ( $json_model ) {
				$course_dates[] = $course_date->get_json_model();
			} else {
				$course_dates[] = $course_date;
			}
		}
		wp_reset_postdata();

		return $course_dates;
	}

	public function add_date( $args ) {
		$defaults = array(
			'course_date_id'      => 0,
			'start_date'          => '',
			'end_date'            => '',
			'duration'            => '',
			'is_private'          => '',
			'max_delegates'       => '',
			'price'               => '',
			'trainer'             => '',
			'location'            => '',
			'custom_location'     => '',
			'joining_instruction' => '',
			'start_finish_time'   => '',
			'map'                 => '',
		);
		$args     = wp_parse_args( $args, $defaults );
		extract( $args );
		if ( $is_private ) {
			$is_private = 'yes';
		}
		$max_delegates = absint( $max_delegates );
		if ( ! $start_date || ! $end_date || ! $duration || ! $price || ! $trainer || ! $location ) {
			return false;
		}
		$start_date_format = date( 'F d Y', $start_date );
		$product_name      = $this->post_title . ' - ' . $start_date_format;
		$product_slug      = $this->post_name . '-' . date( 'Ymd', $start_date );

		$product = new WC_Product_Simple( $course_date_id );
		$product->set_name( $product_name );
		$product->set_status( 'publish' );
		$product->set_slug( $product_slug );
		$product->set_regular_price( $price );
		$product->set_manage_stock( true );

		$stock_quantity = 0;
		if ( $max_delegates && ! $course_date_id ) {
			$stock_quantity = $max_delegates;
		}
		if ( $max_delegates && $course_date_id && $product->exists() ) {
			$previous_max_delegates = $product->get_meta( '_tbs_max_delegates' );
			$stock_change           = $max_delegates - $previous_max_delegates;
			$stock_quantity         = $product->get_stock_quantity() + $stock_change;
			$stock_quantity         = max( $stock_quantity, 0 );
		}

		$product->set_stock_quantity( wc_stock_amount( $stock_quantity ) );

		if ( ! $course_date_id ) {
			$product->update_meta_data( '_is_tbs_course', 'yes' );
			$product->update_meta_data( '_tbs_course', $this->id );
		}

		$product->update_meta_data( '_tbs_start_date', $start_date );
		$product->update_meta_data( '_tbs_end_date', $end_date );
		$product->update_meta_data( '_tbs_duration', $duration );
		$product->update_meta_data( '_tbs_is_private', $is_private );
		$product->update_meta_data( '_tbs_trainer', $trainer );
		$product->update_meta_data( '_tbs_location', $location );
		$product->update_meta_data( '_tbs_custom_location', $custom_location );
		$product->update_meta_data( '_tbs_max_delegates', $max_delegates );
		$product->update_meta_data( '_tbs_joining_instruction', $joining_instruction );
		$product->update_meta_data( '_tbs_start_finish_time', $start_finish_time );
		$product->update_meta_data( '_tbs_map', $map );
		if ( $prduct_id = $product->save() ) {
			return new TBS_Course_Date( $prduct_id );
		}

		return false;
	}

	public function is_private() {
		return $this->is_private;
	}

	public function is_accredited() {
		return $this->is_accredited;
	}

	public function price_formatted() {
		return wc_price( $this->price ) . __( ' + VAT per person', TBS_i18n::get_domain_name() );
	}

	public function price_includes() {
		$price_includes = get_post_meta( $this->id, 'price_includes', true );
		$price_includes = trim( $price_includes );
		if ( ! $price_includes ) {
			return '';
		}
		$price_includes = explode( "\n", $price_includes );
		if ( ! $price_includes ) {
			return '';
		}
		$incs = '';
		foreach ( $price_includes as $prci ) {
			if ( ! trim( $prci ) ) {
				continue;
			}
			$incs .= "<li>{$prci}</li>";
		}
		if ( $incs ) {
			$incs = '<ul class="no-bullet">' . $incs . '</ul>';
		}

		return $incs;
	}

	public function location() {
		$location = get_post_meta( $this->id, 'location', true );
		if ( ! $location && ! is_array( $location ) ) {
			return '';
		}
		$locs = array();
		foreach ( $location as $lc ) {
			if ( 'bristol' == $lc ) {
				$this->loc_britol = true;
			}
			$locs[] = $lc;
		}

		return $locs;
	}

	public function get_course_location_id() {
		$location = get_post_meta( $this->id, 'course_location', true );
		if ( ! $location ) {
			return false;
		}

		return absint( $location );
	}

	public function course_location() {
		$location = get_post_meta( $this->id, 'course_location', true );
		if ( ! $location ) {
			return false;
		}
		$location_post = get_post( $location );
		if ( ! $location_post || is_wp_error( $location_post ) ) {
			return false;
		}
		$tobj               = new stdClass();
		$tobj->ID           = $location_post->ID;
		$tobj->short_name   = $location_post->post_title;
		$tobj->full_address = $location_post->post_content;
		if ( has_post_thumbnail( $location_post ) ) {
			$post_thumbnail_id = get_post_thumbnail_id( $location_post );
			$tobj->photo       = wp_get_attachment_image( $post_thumbnail_id, 'full', false );
		} else {
			$tobj->photo = '';
		}
		$tobj->is_public = 'yes' == get_post_meta( $location_post->ID, 'dispay_frontend', true );
		$tobj->map_url   = get_post_meta( $location_post->ID, 'map_url', true );

		return $tobj;
	}

	public function get_trainer_id() {
		$trainer = get_post_meta( $this->id, 'trainer', true );
		if ( ! $trainer ) {
			return false;
		}

		return absint( $trainer );
	}

	public function trainer() {
		$trainer = get_post_meta( $this->id, 'trainer', true );
		if ( ! $trainer ) {
			return false;
		}
		$trainer_post = get_post( $trainer );
		if ( ! $trainer_post || is_wp_error( $trainer_post ) ) {
			return false;
		}
		$tobj       = new stdClass();
		$tobj->ID   = $trainer_post->ID;
		$tobj->name = $trainer_post->post_title;
		$tobj->bio  = $trainer_post->post_content;
		if ( has_post_thumbnail( $trainer_post ) ) {
			$post_thumbnail_id = get_post_thumbnail_id( $trainer_post );
			$tobj->photo       = wp_get_attachment_image( $post_thumbnail_id, 'full', false );
		} else {
			$tobj->photo = '';
		}

		return $tobj;
	}

	public function accreditation_logos() {
		$logos = get_post_meta( $this->id, 'accreditation_logos', true );
		if ( empty( $logos ) ) {
			return array();
		}
		$r         = array();
		$acc_logos = tts_get_acc_logos();
		foreach ( $logos as $key => $value ) {
			if ( isset( $acc_logos[ $value ] ) ) {
				$r[] = $acc_logos[ $value ]['logo'];
			}
		}

		return $r;
	}

	public function person_graphic_1() {
		$persons = get_post_meta( $this->id, 'person_graphic_1', true );
		if ( empty( $persons ) ) {
			return array();
		}
		$r   = array();
		$dps = tts_get_persons_graphics1();
		foreach ( $persons as $key => $value ) {
			if ( isset( $dps[ $value ] ) ) {
				$r[] = $dps[ $value ]['logo'];
			}
		}

		return $r;
	}

	public function person_graphic_2() {
		$persons = get_post_meta( $this->id, 'person_graphic_2', true );
		if ( empty( $persons ) ) {
			return array();
		}
		$r   = array();
		$dps = tts_get_persons_graphics2();
		foreach ( $persons as $key => $value ) {
			if ( isset( $dps[ $value ] ) ) {
				$r[] = $dps[ $value ]['logo'];
			}
		}

		return $r;
	}

	public function short_description() {
		echo wpautop( $this->short_description );
	}

	public function who_needs_to_do_text() {
		echo wpautop( $this->who_needs_to_do_text );
	}

	public function benefits() {
		echo wpautop( $this->benefits );
	}

	public function stickers() {
		$stickers = get_post_meta( $this->id, 'stickers', true );
		$stickers = tts_check_stickers_array( $stickers );

		return $stickers;
	}

	public function available_training_centers() {
		$locations = get_post_meta( $this->id, 'available_training_centers', true );
		if ( ! $locations || ! is_array( $locations ) ) {
			return array();
		}

		return $locations;
	}

	public function cbenefits() {
		$meta_value = get_post_meta( $this->id, 'cbenefits', true );
		if ( empty( $meta_value ) ) {
			$meta_value = array();
		} else {
			$meta_value = array_values( $meta_value );
		}

		return $meta_value;
	}

	public function testimonials() {
		$meta_value = get_post_meta( $this->id, 'testimonials', true );
		if ( empty( $meta_value ) ) {
			$meta_value = array();
		} else {
			$meta_value = array_values( $meta_value );
		}

		return $meta_value;
	}

	public function faqs() {
		$meta_value = get_post_meta( $this->id, 'faqs', true );
		if ( empty( $meta_value ) ) {
			$meta_value = array();
		} else {
			$meta_value = array_values( $meta_value );
		}
		if ( ! is_array( $meta_value ) ) {
			return array();
		}
		$faqs = array();
		foreach ( $meta_value as $f ) {
			if ( empty( $f['q'] ) ) {
				continue;
			}
			$faqs[] = $f;
		}

		return $faqs;
	}

	public function adminstrate_linked_courese() {
		if ( ! $this->linked_course_code ) {
			return false;
		}
		$cc = $this->linked_course_code;
	}

	public function get_availabilyt_offset( $time_of_day ) {
		/**
		 * @todo Calculate time offset
		 */
		return 0;
	}

	public function get_review_count() {
		return 0;
	}

}
