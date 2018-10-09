<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://mhmasum.me/
 * @since      1.0.0
 *
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/public
 * @author     TTS <mmhasaneee@gmail.com>
 */
class TBS_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name	 = $plugin_name;
		$this->version		 = $version;
	}
	/**
	 * Init public
	 */
	public function init(){
		$this->shortcodes();
		$this->rewrite_endpoints();
		if(isset($_POST['manual_booking_form_save'])){
			$this->save_manual_booking_form();
		}
	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'tipped', $this->get_assets_url( 'css/tipped.css' ) );
		wp_enqueue_style( 'datatables', $this->get_assets_url( 'library/DataTables/datatables.min.css' ) );
		if( tbs_is_manual_booking_form_page() ){
			wp_enqueue_style( 'manual-bookig-print', $this->get_assets_url( 'css/print/manual-booking-form.css' ), array(), null, 'print' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		//if ( is_singular( TBS_Custom_Types::get_course_data( 'type' ) ) ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'tipped', $this->get_assets_url( 'js/tipped.min.js' ), array( 'jquery' ) );
			wp_enqueue_script( 'datatables', $this->get_assets_url( 'library/DataTables/datatables.min.js' ), array( 'jquery' ) );
			wp_enqueue_script( 'tts-course-js', $this->get_assets_url( 'js/frontend.js' ), array( 'jquery', 'tipped', 'datatables' ) );
		//}
		if( tbs_is_manual_booking_form_page() ){
			wp_enqueue_script( 'manual-bookig-js', $this->get_assets_url( 'js/manual-booking-form.js' ), array( 'jquery' ) );
		}
	}

	/**
	 * Change Posts Per Page for course category archive
	 * 
	 * @param object $query data
	 *
	 */
	public function category_posts_per_page( $query ) {
		if ( $query->is_main_query() && !is_admin() && is_tax( TBS_Custom_Types::get_course_category_data('type') ) ) {
			$query->set( 'posts_per_page', 12 );
		}
	}
	/**
	 * Exclude private courses by default from course category archive pages
	 * @param type $query
	 * @return type
	 */
	public function exclude_private_courses($query){
		if(is_search()){
			$meta_query = $query->get('meta_query', array());
			$meta_query[] = array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					array(
						'key' => 'is_private',
						'value' => 'yes',
						'compare' => '!=',
					),
					array(
						'key' => 'is_private',
						'value' => 'yes',
						'compare' => 'NOT EXISTS',
					),
				),
				array(
					'relation' => 'OR',
					array(
						'key' => '_tbs_is_private',
						'value' => 'yes',
						'compare' => '!=',
					),
					array(
						'key' => '_tbs_is_private',
						'value' => 'yes',
						'compare' => 'NOT EXISTS',
					)
				)
			);
			$query->set('meta_query', $meta_query);
			return;
		}
		if( is_admin() || !$query->is_main_query() || !is_tax(TBS_Custom_Types::get_course_category_data( 'type' ))){
			return;
		}
		$meta_query = $query->get('meta_query', array());
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key' => 'is_private',
				'value' => 'yes',
				'compare' => '!=',
			),
			array(
				'key' => 'is_private',
				'value' => 'yes',
				'compare' => 'NOT EXISTS',
			)
		);
		$query->set('meta_query', $meta_query);
	}
	/**
	 * Add rewrite endpoints
	 */
	public function rewrite_endpoints(){
		add_rewrite_endpoint( 'date', EP_PERMALINK );
	}

	// register shortcodes herer
	public function shortcodes() {
		add_shortcode( 'tts_courses', array( $this, 'sc_courses' ) );
		add_shortcode( 'tts_course_dates', array( $this, 'sc_course_dates' ) );
		add_shortcode( 'tts_courses_category_boxes', array( $this, 'sc_courses_category_boxes' ) );
		add_shortcode( 'tts_row', array( $this, 'sc_row' ) );
		add_shortcode( 'tts_col', array( $this, 'sc_col' ) );
	}

	public function sc_courses( $atts, $content = "" ) {
		$courses = get_posts( array(
			'post_type'		 => TBS_Custom_Types::get_course_data( 'type' ),
			'numberposts'	 => -1,
		) );
		if ( empty( $courses ) ) {
			return '';
		}
		ob_start();
		echo '<div class="tts-course-list">';
		foreach ( $courses as $course ) {
			$the_course = new TBS_Course( $course->ID );
			include $this->get_partial('loop');
		}
		echo '</div>';
		return ob_get_clean();
	}

	public function sc_course_dates( $atts, $content = "" ) {
		$atts = shortcode_atts( array(
			'classes'	 => '',
		), $atts, 'tts_course_dates' );
		// Build course date query args
		$query_args = shortcode_atts( array(
			'posts_per_page' => -1,
			'type' => 'upcoming', // all, current, expired, upcoming
			'orderby' => 'start_date',
			'order'	=> 'ASC', // ASC, DESC
			'trainer' => '',
			'locations' => '',
			'course_ids' => '',
			'date_ids' => '',
			'show_private' => false,
			'availability_offset_time' => 0,
			'json_model' => false,
		), $atts );
		$meta_query = array();
		if($query_args['show_private']){
			$meta_query['is_private'] = array(
				'key' => 'is_private',
				'value' => 'yes',
				'compare' => '!=',
			);
		}
		if(count($meta_query) > 0){
			$meta_query['relation'] = 'AND';
			$query_args['meta_query'] = $meta_query;
		}
		if(!empty($_GET['course_id'])) {
			$query_args['course_ids'] = array(absint($_GET['course_id']));
		}
		if(!empty($_GET['location'])) {
			$locations	 = get_posts(array(
				'post_type' => TBS_Custom_Types::get_location_data( 'type' ),
				'numberposts' => -1,
				'fields' => 'ids',
				'tax_query' => array(
					array(
						'taxonomy' => TBS_Custom_Types::get_location_group_data('type'),
						'field' => 'slug',
						'terms' => $_GET['location'],
					),
				),
				
			));
			if($locations){
				$query_args['locations'] = $locations;
			}else{
				return '';
			}
		}
		// Get course dates
		$course_dates = tbs_get_course_dates($query_args);
		if ( empty( $course_dates ) ) {
			return '';
		}
		ob_start();
		tbs_get_template_part('course-dates-list', true, array( 'course_dates' => $course_dates, 'show_private' =>  $query_args['show_private']));
		return ob_get_clean();
	}

	function sc_row( $atts, $content = "" ) {
		$atts = shortcode_atts( array(
			'id'		 => '',
			'classes'	 => '',
		), $atts, 'tts_row' );

		extract( $atts );
		ob_start();
		include $this->get_partial('sc-row');
		return ob_get_clean();
	}

	/**
	 * Sortcode for tts_col
	 * 
	 */
	function sc_col( $atts, $content = "" ) {
		$atts = shortcode_atts( array(
			'classes'	 => '',
			'size'		 => '1/2',
			'last'		 => 'no',
		), $atts, 'tts_col' );

		extract( $atts );
		ob_start();
		include $this->get_partial('sc-col');
		return ob_get_clean();
	}

	public function sc_courses_category_boxes( $atts, $content = "" ) {
		$course_taxonomy_name = TBS_Custom_Types::get_course_category_data('type');
		$atts = shortcode_atts( array( 
			'top_listed' => '', 
			'exclude' => '' 
		), $atts, 'tts_courses_category_boxes' );
		extract( $atts );
		if ( $atts[ 'top_listed' ] ) {
			$atts[ 'top_listed' ] = explode( ',', $atts[ 'top_listed' ] );
		}
		if ( $exclude ) {
			$exclude = explode( ',', $atts[ 'exclude' ] );
		}else{
			$exclude = array();
		}
		$excluded_cats = array();
		foreach ( $exclude as $exc ) {
			$excluded_cats[] = trim( $exc );
		}


		$args		 = array(
			'orderby'	 => 'id',
			'order'		 => 'ASC',
			'hide_empty' => false,
			'fields'	 => 'id=>name',
			'slug'		 => '',
			'parent'	 => 0
		);
		$top_listed	 = array();
		if ( is_array( $atts[ 'top_listed' ] ) && count( $atts[ 'top_listed' ] ) > 0 ) {
			$excluded_cats = array_merge( $excluded_cats, $atts[ 'top_listed' ] );
			foreach ( $atts[ 'top_listed' ] as $tlc_id ) {
				$tlc_id	 = (int) trim( $tlc_id );
				$tlc_cat = get_term( $tlc_id, $course_taxonomy_name, OBJECT, 'raw' );
				if ( !$tlc_cat || is_wp_error( $tlc_cat ) ) {
					continue;
				}
				$top_listed[ $tlc_id ] = $tlc_cat->name;
			}
		}
		if ( count( $excluded_cats ) > 0 ) {
			$args[ 'exclude' ] = $excluded_cats;
		}


		$course_cats = get_terms( $course_taxonomy_name, $args );

		if ( !$course_cats || is_wp_error( $course_cats ) ) {
			return '';
		}

		ob_start();
		echo '<div class="course-box-row clearfix">';
		foreach ( $top_listed as $course_cat_id => $course_cat_name ) {
			include $this->get_partial('category-box');
		}
		foreach ( $course_cats as $course_cat_id => $course_cat_name ) {
			include $this->get_partial('category-box');
		}
		echo '</div>';
		return ob_get_clean();
	}
	public function body_class($classes){
		if( tbs_is_manual_booking_form_page() ){
			$classes[] = 'manual-booking-from-page';
		}
		return $classes;
	}
	public function template_redirect(){
		global $wp_query, $wp;
		if( is_shop()){
			wp_redirect( tbs_get_course_dates_listing_url());
			exit;
		}
		if( is_product()){
			$redirect_url = '';
			$course_date = new TBS_Course_Date(get_queried_object_id());
			if($course_date->exists()){
				$redirect_url = $course_date->get_permalink();
			}else{
				$redirect_url = tbs_get_course_dates_listing_url();
			}
			wp_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * 
	 * @param type $template
	 * @return type
	 */
	public function load_template( $template ) {
		if(isset($_GET['mm_clear_order_dels'])){
			$this->clean_online_order_delegates();
		}
		$partial_path = '';
		$template_theme = '';
		if(!empty($_GET['booking_manual_key'])){
			$template		 = $this->get_partial( 'manual-booking-online' );
		} elseif ( is_tax(TBS_Custom_Types::get_course_category_data('type')) ) {
			$template_theme	 = locate_template( 'courses/category.php' );
			$template		 = $this->get_partial( 'category' );
		} elseif ( is_singular( TBS_Custom_Types::get_course_data( 'type' ) ) ) {
			$course_template = get_post_meta( get_the_ID(), 'course_template', true );
			if ( !$course_template ) {
				$course_settings = get_option( 'tbs_settings' );
				$course_template = isset( $course_settings[ 'course_template' ] ) ? $course_settings[ 'course_template' ] : '';
			}
			if ( 'new' == $course_template ) {
				$template_theme	 = locate_template( 'courses/single-new.php' );
				$template		 = $this->get_partial( 'single-new' );
			} else {
				$template_theme	 = locate_template( 'courses/single.php' );
				$template		 = $this->get_partial( 'single' );
			}
		}
		if ( $template_theme ) {
			return $template_theme;
		}
		return $template;
	}
	
	public function course_comments_enable($open, $post_id){
		return true;
		if(TBS_Custom_Types::get_course_data('type') != get_post_type($post_id)){
			var_dump($open);
			return $open;
		}
		return true;
	}
	
	public function allow_admin_access_for_course_manager($prevent_access){
		$has_cap = false;
		foreach ( TBS_User_Roles::get_courser_manger_caps() as $access_cap ) {
			if ( current_user_can( $access_cap ) ) {
				$has_cap = true;
				break;
			}
		}
		if($has_cap){
			return true;
		}
		return $prevent_access;
	}
	
	public function wc_order_statuses($order_statuses){
		if(!is_array($order_statuses)){
			$order_statuses = array();
		}
		$order_statuses['wc-tbs-draft'] = _x( 'Manual Draft', 'Order status', TBS_i18n::get_domain_name() );
		return $order_statuses;
	}
	public function woocommerc_get_cart_item_name($name, $cart_item, $cart_item_key){
		$course_date = new TBS_Course_Date($cart_item['product_id']);
		$course_link = $course_date->get_course_permalink() . '#course-dates-list';
		$formatted_course_name = $course_date->get_course_title_with_date();
		return sprintf( '<a href="%s">%s</a>', esc_url( $course_link ), $formatted_course_name );
	}
	public function woocommerc_get_cart_item_removed_title($name, $cart_item){
		$course_date = new TBS_Course_Date($cart_item['product_id']);
		$course_link = $course_date->get_course_permalink() . '#course-dates-list';
		$formatted_course_name = $course_date->get_course_title_with_date();
		return sprintf( '<a href="%s">%s</a>', esc_url( $course_link ), $formatted_course_name );
	}
	public function order_item_total_private_course($total, $order, $item, $inc_tax, $round){
		$product = $item->get_product();
		if(!$product){
			return $total;
		}
		$price = $product->get_price();
		if( 'yes' != get_post_meta($product->get_id(), '_tbs_is_private', true) ){
			return $total;
		}
		
		if ( is_callable( array( $item, 'get_total' ) ) ) {
			if ( $inc_tax ) {
				$total = $item->get_total() + $item->get_total_tax();
			} else {
				$total = floatval( $item->get_total() );
			}

			$total = $round ? round( $total, wc_get_price_decimals() ) : $total;
		}
		
		return $total;
	}
	public function cart_product_subtotal_private_course($product_subtotal, $product, $quantity, $wc_cart){
		$price = $product->get_price();
		if( 'yes' != get_post_meta($product->get_id(), '_tbs_is_private', true) ){
			return $product_subtotal;
		}
		if ( $product->is_taxable() ) {

			if ( 'excl' === $wc_cart->tax_display_cart ) {

				$row_price        = wc_get_price_excluding_tax( $product, array( 'qty' => 1 ) );
				$product_subtotal = wc_price( $row_price );

				if ( wc_prices_include_tax() && $wc_cart->get_subtotal_tax() > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}
			} else {

				$row_price        = wc_get_price_including_tax( $product, array( 'qty' => 1 ) );
				$product_subtotal = wc_price( $row_price );

				if ( ! wc_prices_include_tax() && $wc_cart->get_subtotal_tax() > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
			}
		} else {
			$row_price        = $price;
			$product_subtotal = wc_price( $row_price );
		}
		return $product_subtotal;
	}
	public function woocommerce_before_calculate_totals($cart_object){
		foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
			if( 'yes' != get_post_meta($cart_item['data']->get_id(), '_tbs_is_private', true) ){
				continue;
			}
			$cart_object->cart_contents[ $cart_item_key ]['tbs_org_quantity'] = $cart_item['quantity'];
			$cart_object->set_quantity($cart_item_key, 1, false);
		}
	}
	public function woocommerce_after_calculate_totals($cart_object){
		foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
			if( 'yes' != get_post_meta($cart_item['data']->get_id(), '_tbs_is_private', true) ){
				continue;
			}
			$cart_object->set_quantity($cart_item_key, $cart_object->cart_contents[ $cart_item_key ]['tbs_org_quantity'], false);
			unset($cart_object->cart_contents[ $cart_item_key ]['tbs_org_quantity']);
		}
	}
	
	public function woocommerce_order_item_name($name, $item, $is_visible){
		$course_date = new TBS_Course_Date($item->get_product());
		$course_link = $course_date->get_course_permalink() . '#course-dates-list';
		$formatted_course_name = $course_date->get_course_title_with_date();
		return sprintf( '<a href="%s">%s</a>', esc_url( $course_link ), $formatted_course_name );
	}

	public function header_mini_cart_fragment($fragments){
		ob_start();
		tbs_header_cart_summery();
		$fragments['div.tbs-header-cart-summery'] = ob_get_clean();
		return $fragments;
		
	}
	/**
	 * Validate coupon for courses
	 * @param type $valid
	 * @param type $coupon
	 * @param type $discount
	 */
	public function validate_coupon_course_ids($valid, $coupon, $discount){
		$course_date_ids = tbs_get_course_dates_for_coupon($coupon->get_id());
		if ( count( $course_date_ids ) > 0 ) {
			$valid = false;

			foreach ( $discount->get_items() as $item ) {
				if ( $item->product && in_array( $item->product->get_id(), $course_date_ids, true ) || in_array( $item->product->get_parent_id(), $course_date_ids, true ) ) {
					$valid = true;
					break;
				}
			}

			if ( ! $valid ) {
				throw new Exception( __( 'Sorry, this coupon is not applicable to selected courses.', TBS_i18n::get_domain_name() ), 109 );
			}
		}

		return true;
	}
	/**
	 * Add additional fields on checkout page for bookings
	 * @param array $fields
	 * @return array
	 */
	public function wc_checkout_additional_booking_fields($fields){
		if(empty($fields['order'])){
			$fields['order'] = array();
		}
		$fields['order']['finance_contact_email'] = array(
			'type'         => 'email',
			'label'        => __( 'Finance contact email', 'woocommerce' ),
			'required'     => false,
			'validate'     => array( 'email' ),
		);
		return $fields;
	}
	/**
	 * Save additional booking fields.
	 * @param int $order_id
	 * @param array $data
	 */
	public function save_additional_booking_fields($order_id, $data){
		if(!empty($data['finance_contact_email'])){
			add_post_meta($order_id, 'finance_contact_email', $data['finance_contact_email']);
		}
	}
	public function course_additional_fields(){
		tbs_get_template_part('checkout/course-fields');
	}
	
	public function email_optin_field(){
		tbs_get_template_part('checkout/email-optin-field');
	}
	
	public function get_chekcout_addition_fields($include_addresses = false){
		$fields = array();
		foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item){
			$course_date = new TBS_Course_Date($cart_item['product_id']);
			if(!$course_date->exists()){
				continue;
			}
			
			$course_date_title = $course_date->get_course_title_with_date();
			$key_base = "cd_{$cart_item['product_id']}";
			$fields[$key_base . '_po'] = array(
				'label' => sprintf(__('Purchase Order for %s', TBS_i18n::get_domain_name()),$course_date_title),
				'required' => false,
				'type' => 'text',
			);
			
			if($include_addresses && !empty($_POST[$key_base. '_dif_address'])){
				$addresses_fields = tbs_get_address_fields($key_base);
				foreach ($addresses_fields as $a_key => $a_field){
					$fields[$a_key] = array(
						'label' => sprintf(__('%s for %s', TBS_i18n::get_domain_name()), $a_field['label'], $course_date_title),
						'required' => $a_field['cd_required'],
						'type' => $a_field['type'],
					);
				}
			}
			
			$booker_is_delegate = tbs_arr_get($key_base. '_booker_is_delegate', $_POST, false);

			for($delegate_no = 0; $delegate_no < $cart_item['quantity']; $delegate_no++){
				$delegate_base = "{$key_base}_delegate_{$delegate_no}_";
				$delegates_fields = tbs_get_delegates_field();
				foreach($delegates_fields as $key => $field){
					$fields[$delegate_base . $key] = array(
						'label' => sprintf(__('Delegate %d %s for %s', TBS_i18n::get_domain_name()), $delegate_no + 1, $field['label'], $course_date_title),
						'required' =>  $field['cd_required'] && (!$booker_is_delegate || $delegate_no > 0 ),
						'type' => $field['type'],
						'delegate_email_field' => 'email' == $field['type'],
					);
					if(0 ===  $delegate_no && $booker_is_delegate){
						$fields[$delegate_base . $key]['bypass_validation'] = true;
					}
				}
			}
		}
		return $fields;
	}
	public function get_chekcout_course_dates_data(){
		$course_dates_data = array();
		// Loop through Cart items
		foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item){
			$date_data = array();
			// Try to get the Course Date Obj for cart item porduct
			$course_date = new TBS_Course_Date($cart_item['product_id']);
			// Check if it is valid Course date
			if(!$course_date->exists()){
				continue;
			}
			// Fildd data for this course
			$date_data['course_date_id'] = $cart_item['product_id'];
			$date_data['course_id'] = $course_date->get_course_id();
			
			$key_base = "cd_{$cart_item['product_id']}";
			
			$date_data['purchase_order'] = tbs_arr_get($key_base. '_po', $_POST);
			
			$date_data['different_certificate_address'] = tbs_arr_get($key_base. '_dif_address', $_POST);
			$address = array();
			if($date_data['different_certificate_address']){
				$address_fields = tbs_get_address_fields($key_base);
				foreach($address_fields as $a_key => $a_field){
					$address[ str_replace($key_base . '_', '', $a_key)] = tbs_arr_get($a_key, $_POST);
				}
				$date_data['certificate_address'] = $address;
			}else{
				$date_data['certificate_address'] = '';
			}
			
			$date_data['booker_is_delegate'] = tbs_arr_get($key_base. '_booker_is_delegate', $_POST);
			$delegates_data = array();
			for($delegate_no = 0; $delegate_no < $cart_item['quantity']; $delegate_no++){
				$delegate_base = "{$key_base}_delegate_{$delegate_no}_";
				$delegates_fields = tbs_get_delegates_field();
				$df = array();
				foreach($delegates_fields as $key => $field){
					$df[$key] = tbs_arr_get($delegate_base . $key, $_POST);
				}
				if($date_data['booker_is_delegate'] && 0 == $delegate_no){
					$df['booker_is_delegate'] = 'yes';
				}
				$delegates_data[] = $df;
			}
			$date_data['delegates'] = $delegates_data;
			$course_dates_data[$cart_item['product_id']] = $date_data;
		}
		return $course_dates_data;
	}
	public function checkout_delegates_email_fields_filter($field){
		return !empty($field['delegate_email_field']);
	}
	public function checkout_field_validation(){
		$checkout_fields = $this->get_chekcout_addition_fields(true);
		$errors = new WP_Error();
		foreach($checkout_fields as $key => $field){
			if(!empty($field['bypass_validation'])){
				continue;
			}
			if($field['required'] && empty($_POST[$key])){
				$errors->add('validation', sprintf( __( '%s is a required field.', TBS_i18n::get_domain_name() ), '<strong>' . esc_html( $field['label'] ) . '</strong>' ) );
				continue;
			}
			$format = array_filter( isset( $field['validate'] ) ? (array) $field['validate'] : array() );
			
			if ( in_array( 'postcode', $format ) ) {
				$country      = isset( $_POST[$field['fieldset_key'].'_country' ] ) ? $_POST['country_key'] : WC()->customer->{"get_{$field['fieldset_key']}_country"}();
				$_POST[ $key ] = wc_format_postcode( $_POST[ $key ], $country );

				if ( '' !== $_POST[ $key ] && ! WC_Validation::is_postcode( $_POST[ $key ], $country ) ) {
					$errors->add( 'validation', sprintf(__( '%s is not a valid postcode / ZIP.', 'woocommerce' ),'<strong>' . esc_html( $field['label'] ) . '</strong>') );
				}
			}
			if ( in_array( 'phone', $format ) ) {
				$_POST[ $key ] = wc_format_phone_number( $_POST[ $key ] );

				if ( '' !== $_POST[ $key ] && ! WC_Validation::is_phone( $_POST[ $key ] ) ) {
					/* translators: %s: phone number */
					$errors->add( 'validation', sprintf( __( '%s is not a valid phone number.', 'woocommerce' ), '<strong>' . esc_html( $field['label'] ) . '</strong>' ) );
				}
			}

			if ( '' !== $_POST[ $key ] && in_array( 'state', $format ) ) {
				$country      = isset( $_POST[ $field['fieldset_key'] . '_country' ] ) ? $_POST[ $field['fieldset_key'] . '_country' ] : WC()->customer->{"get_{$field['fieldset_key']}_country"}();
				$valid_states = WC()->countries->get_states( $country );

				if ( ! empty( $valid_states ) && is_array( $valid_states ) && sizeof( $valid_states ) > 0 ) {
					$valid_state_values = array_map( 'wc_strtoupper', array_flip( array_map( 'wc_strtoupper', $valid_states ) ) );
					$_POST[ $key ]       = wc_strtoupper( $_POST[ $key ] );

					if ( isset( $valid_state_values[ $_POST[ $key ] ] ) ) {
						// With this part we consider state value to be valid as well, convert it to the state key for the valid_states check below.
						$_POST[ $key ] = $valid_state_values[ $_POST[ $key ] ];
					}

					if ( ! in_array( $_POST[ $key ], $valid_state_values ) ) {
						/* translators: 1: state field 2: valid states */
						$errors->add( 'validation', sprintf( __( '%1$s is not valid. Please enter one of the following: %2$s', 'woocommerce' ), '<strong>' . esc_html( $field['label'] ) . '</strong>', implode( ', ', $valid_states ) ) );
					}
				}
			}
			
			if('email' == $field['type'] && !empty($_POST[$key]) && ! is_email( $_POST[$key] )){
				$errors->add( 'validation', sprintf( __( '%s is not a valid email address.', 'woocommerce' ), '<strong>' . esc_html( $field['label'] ) . '</strong>' ) );
				continue;
			}
		}
		$delegates_email_fields = array_filter($checkout_fields, array($this, 'checkout_delegates_email_fields_filter'));
		$delegates_email_duplicates = array();
		foreach($delegates_email_fields as $key => $delegates_email_field){
			if(empty($_POST[$key])){
				continue;
			}
			$delegates_email_duplicates[$_POST[$key]][] = $delegates_email_field['label'];
		}
		foreach ($delegates_email_duplicates as $duplicate_fields){
			if(count($duplicate_fields) < 2) {
				continue;
			}
			$errors->add( 'validation', __( 'Delegates must not have the same email address', TBS_i18n::get_domain_name() ) );
				
		}
		//var_dump($delegates_email_fields);die();
		
		
		foreach ( $errors->get_error_messages() as $message ) {
			wc_add_notice( $message, 'error' );
		}
	}
	public function process_checkout_fields($order_id, $posted_data, $order){
		//$booker = new TBS_Booker($order->get_customer_id());
		$course_date_data = $this->get_chekcout_course_dates_data();
		$order_delegates_data = array();
		$order_addresses = array();
		$camp_monitor_subscribers = array();
		$is_optin = tbs_arr_get('tbs_email_optin', $_POST, false);
		
		if($is_optin){
			$camp_monitor_subscribers[] = array(
				'EmailAddress' => $order->get_billing_email(),
				'Name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
			);
		}
		
		foreach($course_date_data as $course_date_id => $date_data){
			// Add purchase order meta
			add_post_meta($order_id, 'course_id', $date_data['course_id']);
			add_post_meta($order_id, 'course_date_id', $date_data['course_date_id']);
			add_post_meta($order_id, 'purchase_order_' . $course_date_id, $date_data['purchase_order']);
			add_post_meta($order_id, 'different_certificate_address_' . $course_date_id, $date_data['different_certificate_address']);
			add_post_meta($order_id, 'booker_is_delegate_' . $course_date_id, $date_data['booker_is_delegate']);
			
			if($date_data['different_certificate_address']){
				if($date_data['certificate_address']){
					$order_addresses[$course_date_id] =$date_data['certificate_address'];
				}
			}

			foreach($date_data['delegates'] as $delegate_data){
				// Prepare delegates data to save as meta
				// This data will be used to create delegates account if orer is completed
				if(isset($delegate_data['booker_is_delegate']) && 'yes' == $delegate_data['booker_is_delegate']){
					$order_delegates_data[] = array(
						'booker_is_delegate' => 'yes',
						'notes' => $delegate_data['notes'],
						'course_id' => $date_data['course_id'],
						'course_date_id' => $date_data['course_date_id'],
					);
				}else{
					$order_delegates_data[] = array(
						'first_name' => $delegate_data['first_name'],
						'last_name' => $delegate_data['last_name'],
						'email' => $delegate_data['email'],
						'notes' => $delegate_data['notes'],
						'course_id' => $date_data['course_id'],
						'course_date_id' => $date_data['course_date_id'],
					);
				}
				// Prepare data for Campaign Monitor subscription
				if($is_optin && !empty($delegate_data['email'])){
					$camp_monitor_subscribers[] = array(
						'EmailAddress' => $delegate_data['email'],
						'Name' => $delegate_data['first_name'] . ' ' . $delegate_data['last_name'],
					);
				}
			}
		}
		add_post_meta($order_id, 'online_delegates_data', $order_delegates_data);
		if($order_addresses){
			add_post_meta($order_id, 'certificate_addresses', $order_addresses);
		}
		if($is_optin && count($camp_monitor_subscribers) > 0){
			tbs_campaign_monitor_import($camp_monitor_subscribers, $order);
		}
	}
	public function wc_maybe_create_order_delegates($order_id, $order){
		if('tbs_manual_booking' == $order->get_created_via()){
			return;
		}
		$saved_delegates_data = get_post_meta($order_id, 'online_delegates_data', true);
		if(!$saved_delegates_data || !is_array( $saved_delegates_data ) || count($saved_delegates_data) < 1){
			return;
		}
		$order_delegates = array();
		foreach($saved_delegates_data as $delegate_data){
			$empty_email = false;
			if(isset($delegate_data['booker_is_delegate']) && 'yes' == $delegate_data['booker_is_delegate']){
				$delegate_data['email'] = $order->get_billing_email();
				$delegate_data['first_name'] = $order->get_billing_first_name();
				$delegate_data['last_name'] = $order->get_billing_last_name();
			}else if(empty($delegate_data['email'])){
				$delegate_data['email'] = random_uqniq_user_email();
				$empty_email = true;
			}
			$delegate = new TBS_Delegate($delegate_data['email']);
			$delegate->set_first_name($delegate_data['first_name']);
			$delegate->set_last_name($delegate_data['last_name']);
			$delegate->set_notes($delegate_data['notes']);
			$delegate->add_course($delegate_data['course_id']);
			$delegate->add_course_date($delegate_data['course_date_id']);
			$delegate->add_customer($order->get_customer_id());
			if($empty_email){
				$delegate->set_empty_email();
			}
			$delegate->save();
			if($delegate->exists()){
				$order_delegates[$delegate_data['course_date_id']][] = $delegate->get_id();
			}
		}
		update_post_meta($order_id, 'delegates', $order_delegates);
	}
	
	public function wc_manage_order_delegates_on_status_change($order_id, $status_from, $status_to, $order){
		if($status_from == 'completed'){
			$order_delegates = get_post_meta($order->get_id(), 'delegates', true);

			$order_delegates_data = array();
			$delegates_id = array();
			if( is_array( $order_delegates ) && count($order_delegates) > 0 ){
				foreach($order_delegates as $course_date_id => $d_ids){
					$course_date = new TBS_Course_Date($course_date_id);
					if(!$course_date->exists()){
						continue;
					}
					if(!is_array($d_ids) || count($d_ids) == 0){
						continue;
					}
					$serial_no = 0;
					foreach($d_ids as $d_id){
						$delegate = new TBS_Delegate($d_id);
						if(!$delegate->exists()){
							continue;
						}
						$serial_no++;
						$delegates_id[] = $d_id;
						delete_user_meta($d_id, 'tbs_course_dates', $course_date_id);
						if('tbs_manual_booking' != $order->get_created_via()){
							$order_delegates_data[] = array(
								'first_name' => $delegate->get_first_name(),
								'last_name' => $delegate->get_last_name(),
								'email' => $delegate->has_email() ? $delegate->get_email() : '',
								'notes' => $delegate->get_notes(),
								'course_id' => $course_date->get_course_id(),
								'course_date_id' => $course_date_id,
							);
						}
					}
				}
			}
			if($order_delegates_data && 'tbs_manual_booking' != $order->get_created_via()){
				update_post_meta($order->get_id(), 'online_delegates_data', $order_delegates_data);
			}
			delete_post_meta($order->get_id(), 'delegates');
			
		}
		if( in_array($status_to, array('cancelled', 'refunded', 'failed')) && !in_array( $status_from, array('cancelled', 'refunded', 'failed') )){
			$order_items = $order->get_items();

			if ( ! empty( $order_items ) ) {
				foreach ( $order_items as $item_id => $order_item ) {
					$_product = $order_item->get_product();
					if ( $_product && $_product->exists() && $_product->managing_stock() ) {
						$old_stock    = $_product->get_stock_quantity();
						$order_item_qty = $order_item->get_quantity();
						$stock_change = apply_filters( 'woocommerce_restore_order_stock_quantity', $order_item_qty, $item_id );
						$new_quantity = wc_update_product_stock( $_product, $stock_change, 'increase' );
						$item_name    = $_product->get_sku() ? $_product->get_sku() : $_product->get_id();
						$note         = sprintf( __( 'Item %1$s stock increased from %2$s to %3$s.', 'woocommerce' ), $item_name, $old_stock, $new_quantity );
						$order->add_order_note( $note );
					}
				}
				do_action( 'woocommerce_restore_order_stock', $order );
			}
		}
		if( in_array($status_from, array('cancelled', 'refunded', 'failed')) && !in_array( $status_to, array('cancelled', 'refunded', 'failed') )){
			
			$order_items = $order->get_items();

			if ( ! empty( $order_items ) ) {
				foreach ( $order_items as $item_id => $order_item ) {
					$_product = $order_item->get_product();
					if ( $_product && $_product->exists() && $_product->managing_stock() ) {
						$old_stock    = $_product->get_stock_quantity();
						$order_item_qty = $order_item->get_quantity();
						$stock_change = apply_filters( 'woocommerce_restore_order_stock_quantity', $order_item_qty, $item_id );
						$new_quantity = wc_update_product_stock( $_product, $stock_change, 'decrease' );
						$item_name    = $_product->get_sku() ? $_product->get_sku() : $_product->get_id();
						$note         = sprintf( __( 'Item %1$s stock decreased from %2$s to %3$s.', 'woocommerce' ), $item_name, $old_stock, $new_quantity );
						$order->add_order_note( $note );
					}
				}
				do_action( 'woocommerce_restore_order_stock', $order );
			}
		}
	}
	
	public function clean_online_order_delegates(){
		$orders = wc_get_orders(array(
			'limit' => -1,
			'created_via' => 'checkout',
		));
		if( !function_exists( 'wp_delete_user') ){
			require_once ABSPATH . '/wp-admin/includes/user.php';
		}
		foreach($orders as $order){
			$order_delegates = get_post_meta($order->get_id(), 'delegates', true);
			
			$order_delegates_data = array();
			$delegates_id = array();
			if( is_array( $order_delegates ) && count($order_delegates) > 0 ){	
				foreach($order_delegates as $course_date_id => $d_ids){
					$course_date = new TBS_Course_Date($course_date_id);
					if(!$course_date->exists()){
						continue;
					}
					if(!is_array($d_ids) || count($d_ids) == 0){
						continue;
					}
					foreach($d_ids as $d_id){
						$delegate = new TBS_Delegate($d_id);
						if(!$delegate->exists()){
							continue;
						}
						$delegates_id[] = $d_id;
						$order_delegates_data[] = array(
							'first_name' => $delegate->get_first_name(),
							'last_name' => $delegate->get_last_name(),
							'email' => $delegate->get_email(),
							'notes' => $delegate->get_notes(),
							'course_id' => $course_date->get_course_id(),
							'course_date_id' => $course_date_id,
						);
					}
				}
			}
			if($order_delegates_data){
				update_post_meta($order->get_id(), 'online_delegates_data', $order_delegates_data);
			}
			if('completed' == $order->get_status()){
				// Do for completed...
			}else{
				delete_post_meta($order->get_id(), 'delegates');
				foreach($delegates_id as $did){
					if($did != $order->get_customer_id()){
						wp_delete_user($did);
					}
				}
			}
		}
		
	}
	
	public function acredoted_address_details($order){
		$addresses = get_post_meta($order->get_id(), 'certificate_addresses', true);
		if( !is_array( $addresses ) || 0 === count($addresses)){
			return;
		}
		
		$partials_data = array(
			'addresses' => $addresses,
			'order' => $order,
		);
		
		$this->get_partial('order/order-accredited-address', false, $partials_data);
	}
	
	public function order_delegates_details($order){
		if('completed' == $order->get_status()){
			$order_delegates = get_post_meta($order->get_id(), 'delegates', true);
			$template = 'order/order-delegates-details';
		}else{
			$order_delegates = get_post_meta($order->get_id(), 'online_delegates_data', true);
			$template = 'order/order-delegates-details-non-complete';
		}
		
		if( !is_array( $order_delegates ) || 0 === count($order_delegates)){
			$order_delegates = array();
		}
		
		$partials_data = array(
			'order_delegates' => $order_delegates,
			'order' => $order,
		);
		
		$this->get_partial($template, false, $partials_data);
	}
	
	public function wc_auto_complete_order($order_id) {
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		$order->update_status( 'completed' );
	}
	
	public function email_order_delegates_details($order){
		if('completed' == $order->get_status()){
			$order_delegates = get_post_meta($order->get_id(), 'delegates', true);
			$template = 'emails/order-delegates-details';
		}else{
			$order_delegates = get_post_meta($order->get_id(), 'online_delegates_data', true);
			$template = 'emails/order-delegates-details-non-complete';
		}
		
		if( !is_array( $order_delegates ) || 0 === count($order_delegates)){
			$order_delegates = array();
		}
		
		$partials_data = array(
			'order_delegates' => $order_delegates,
			'order' => $order,
		);
		
		$this->get_partial($template, false, $partials_data);
	}
	public function email_joining_instructions($order, $sent_to_admin, $plain_text, $email){
		if('customer_completed_order' != $email->id){
			return;
		}
		$partials_data = array(
			'sent_to_admin' => $sent_to_admin,
			'order' => $order,
		);
		
		$this->get_partial('emails/order-joining-instruction', false, $partials_data);
	}
	public function email_attachments($attachments, $status, $order){
		if ( ! is_object( $order ) || ! isset( $status ) ) {
			return $attachments;
		}
		if ( empty( $order ) ) {
			return $attachments;
		}
		if( !in_array($status, array('joining_instructions')) ){
			return $attachments;
		}
		//$attachments[] = str_replace( content_url(), WP_CONTENT_DIR, $download['file'] );
		foreach ( $order->get_items() as $item_id => $item ) {
			$course_date = new TBS_Course_Date($item->get_product());
			if(!$course_date->exists()){
				continue;
			}
			$map_url = $course_date->get_map();
			if(!$map_url){
				continue;;
			}
			$attachments[] = str_replace( content_url(), WP_CONTENT_DIR, $map_url );
		}
		return $attachments;
	}
	
	public function wc_admin_email_recipent($emails){
		foreach ( $emails as $email_key => $email ){
			if($email->is_customer_email()){
				continue;
			}
			$email->recipient = tbs_wc_get_emails_recipient($email_key, $email);
			$emails[$email_key] = $email;
		}
		
		return $emails;
	}
	public function wc_joining_instructions_email($emails){
		$emails['TBS_WC_Email_Joining_Instructions'] = include( tbs_plugin_root_path() . 'includes/class-tbs-wc-email-joining-instructions.php' );
		return $emails;
	}
	public function woocommerce_email_from_address($form_email, $email_handler) {
		if('customer_new_account' == $email_handler->id){
			$email = trim(tbs_get_settings('new_customer_form_email', ''));
			if($email){
				$form_email = $email;
			}
		}
		return $form_email;
	}
	public function wc_add_email_record($form_name, $email_handler){
		$note = '';
		$record_type = '';
		if ( is_a( $email_handler->object, 'WC_Order' ) ) {
			switch($email_handler->id){
				case 'customer_completed_order':
					$note = 'Booking confirmation email sent on ' . date('d.m.Y H:i');
					$record_type = 'booking_confirmation';
					break;
				case 'joining_instructions':
					$note = 'Joining instructions for '. $email_handler->course_ji_data['course_date_title'] .' email sent on ' . date('d.m.Y H:i');
					$record_type = 'joining_instructions';
					break;
				default: 
					$note = $email_handler->title .' email sent on ' . date('d.m.Y H:i');
					$record_type = $email_handler->id;
					break;
			}
		}else{
			$note = $email_handler->title .' email sent on ' . date('d.m.Y H:i');
			$record_type = $email_handler->id;
		}
		if(!$email_handler->is_customer_email()){
			return $form_name;
		}
		if($note && $record_type){
			tbs_add_order_email_record($note, $email_handler->object, $record_type);
		}
		
		return $form_name;
	}
	public function wc_add_copy_email_address($recipient, $object){
		$cc_email = tbs_get_settings('copy_email', '');
		if(!$cc_email){
			return $recipient;
		}
		$cc_email = explode(',', $cc_email);
		$recipients = array_map( 'trim', explode( ',', $recipient ) );
		$recipients = array_merge($recipients, $cc_email);
		return implode(',', $recipients);
	}
	public function wc_add_customer_new_account_copy_email($recipient, $object){
		return $recipient . ',trainingsocieti@rowenahicks.co.uk';
	}
	public function wc_customer_invoice_email_finance_contact($recipient, $object){
		$finance_contact_email = get_post_meta($object->get_id(), 'finance_contact_email', true);
		if(!$finance_contact_email){
			return $recipient;
		}
		$recipients = array_map( 'trim', explode( ',', $recipient ) );
		$recipients[] = $finance_contact_email;
		return implode(',', $recipients);
	}
	public function add_endpoints(){
		add_rewrite_endpoint( 'my-courses', EP_ROOT | EP_PAGES );
	}
	
	public function add_query_vars($query_vars){
		$query_vars['my-courses'] = 'my-courses';
		return $query_vars;
	}
	
	public function woocommerce_account_menu_items($items){//
		if(isset($items['downloads'])){
			unset($items['downloads']);
		}
		if(isset($items['payment-methods'])){
			unset($items['payment-methods']);
		}
		$new_items = array();
		$new_items['my-courses'] = __('My Courses', TBS_i18n::get_domain_name());
		
		$items = tbs_array_insert_item($items, $new_items, 'dashboard');
		
		return $items;
	}
	
	public function woocommerce_mycourses_content(){
		$courses = array();
		$delegates = array();
		$current_page    = empty( $current_page ) ? 1 : absint( $current_page );
		$customer_orders = wc_get_orders( apply_filters( 'woocommerce_my_account_my_orders_query', array( 'customer' => get_current_user_id(), 'page' => $current_page, 'paginate' => true ) ) );
		if($customer_orders){
			$order_delegates = array();
			foreach($customer_orders->orders as $customer_order){
				$order = wc_get_order($customer_order);
				$order_delegates = get_post_meta($order->get_id(), 'delegates', true);
			}
			if(!is_array($order_delegates)){
				$order_delegates = array();
			}
			foreach($order_delegates as $course_date_id => $delegates_ids){
				$delegates[$course_date_id] = isset($delegates[$course_date_id]) ? array_merge($delegates[$course_date_id], $delegates_ids) : $delegates_ids;
			}
		}
		//$booker = new TBS_Booker(get_current_user_id());
		
		wc_get_template(
			'myaccount/my-courses.php',
			array(
				'current_page' => absint( $current_page ),
				'courses' => $courses,
				'delegates' => $delegates,
				'has_courses' => 0 < count( $courses),
				'has_delegates' => 0 < count( $delegates),
			)
		);
	}
	
	public function woocommerce_order_item_get_name($name, $item){
		$course_date = new TBS_Course_Date( $item->get_product() );
		return $course_date->get_course_title_with_date(true);
	}
	
	public function woocommerce_return_to_shop_redirect($url){
		$course_date_listing_page = tbs_get_settings('course_date_list_page_id');
		if(!$course_date_listing_page){
			return $url;
		}
		return get_permalink($course_date_listing_page);
	}
	
	public function save_manual_booking_form(){
		$order_id = tbs_arr_get( 'fe_manual_booking_id', $_POST, false );
		if(!$order_id){
			return;
		}
		$order = wc_get_order($order_id);
		if( !$order || is_wp_error( $order )){
			return;
		}
		$online_form_key = $order->get_meta( '_tbs_online_form_id', true);
		
		if(!$online_form_key){
			wp_redirect(add_query_arg(array('booking_manual_key' => $online_form_key, 'status' => 'expired'), site_url()));
			exit();
		}
		
		$data_entry_complete = (bool)$order->get_meta('tbs_data_entry_complete', true);
		
		if($data_entry_complete){
			wp_redirect(add_query_arg(array('booking_manual_key' => $online_form_key, 'status' => 'completed'), site_url()));
			exit();
		}
		global $tbs_manual_booking_online_form_error;
		$is_submit_confirm = 'Submit Booking' == tbs_arr_get('save_type', $_POST, '');
		if($is_submit_confirm && empty($_POST['terms_conditions'])){
			$tbs_manual_booking_online_form_error = 'You must accept our Terms & Conditions.';
			return;
		}
		// Save Purchase Order
		$line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
		foreach($line_items as $line_item_id => $line_item){
			$course_date = new TBS_Course_Date($line_item->get_product());
			if(!$course_date->exists()){
				continue;
			}
			$delegates = absint($line_item->get_quantity());
			// Remove the item if no delegates is set
			if($delegates < 1){
				continue;
			}
			if( isset($_POST['purchase_orders'][$course_date->get_id()]) ){
				update_post_meta($order_id, 'purchase_order_' . $course_date->get_id(), $_POST['purchase_orders'][$course_date->get_id()]);
			}
		}
		$customer_address_data = array();
		$cusomer_fields = WC()->countries->get_address_fields( $order->get_billing_country(), 'billing_' );
		$cusomer_fields = array_keys($cusomer_fields);
		foreach($cusomer_fields as $cf_key){
			$customer_address_data[ str_replace('billing_', '', $cf_key)] = tbs_arr_get($cf_key, $_POST, '');
		}
		$customer_address_data = wp_parse_args($customer_address_data, array(
			'first_name' => '',
			'last_name' => '',
			'company' => '',
			'address_1' => '',
			'address_2' => '',
			'city' => '',
			'postcode' => '',
			'coungtry' => '',
			'email' => '',
			'phone' => '',
		));
		
		// Add address components to order data
		foreach($customer_address_data as $ad_key => $ad_value){
			$ad_key = 'billing_' . $ad_key;
			if ( is_callable( array( $order, "set_{$ad_key}" ) ) ) {
				$order->{"set_{$ad_key}"}( $ad_value );

			// Store custom fields prefixed with wither shipping_ or billing_. This is for backwards compatibility with 2.6.x.
			} else{
				$order->update_meta_data( '_' . $ad_key, $ad_value );
			}
		}
		$delegates_posted_data = tbs_arr_get('mbs_delegates', $_POST, array());
		update_post_meta($order_id, 'tbs_draft_delegates_data', $delegates_posted_data);
		
		if(isset($_POST['mbs_onsite_data'])){
			update_post_meta($order_id, 'mbs_onsite_data', $_POST['mbs_onsite_data']);
		}
		
		$order_id = $order->save();
		$is_pdf_download = 'Download PDF' == tbs_arr_get('save_type', $_POST, '');
		if($order_id && $is_pdf_download){
			$pdf_file_name  ='booking_details_' . $order_id .'_'.$online_form_key.'_'. time() .'.pdf';
			tbs_generate_pdf( tbs_get_template_part( 'forms/manual-booking-pdf', false ), array('order' => $order), $pdf_file_name);
			exit;
		}
		
		if($order_id && $is_submit_confirm){
			
			if(wp_mkdir_p(WP_CONTENT_DIR . '/uploads/tbs-pdfs/' .$order_id)){
				$pdf_file_name = WP_CONTENT_DIR . '/uploads/tbs-pdfs/' .$order_id .'/booking_details_' . $order_id .'_'.$online_form_key.'_'. time() .'.pdf';

				tbs_generate_pdf( tbs_get_template_part( 'forms/manual-booking-pdf', false ), array('order' => $order), $pdf_file_name, 'file');
				
				$subject = 'Manual booking confirmation by ' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
				$message = 'Manual booking confirmation by ' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
				$course_dates_titles = array();
				
				$line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
				foreach($line_items as $line_item_id => $line_item){
					$course_date = new TBS_Course_Date($line_item->get_product());
					if(!$course_date->exists()){
						continue;
					}
					$delegates = absint($line_item->get_quantity());
					// Remove the item if no delegates is set
					if($delegates < 1){
						continue;
					}
					$course_dates_titles[] = ' ' . $course_date->get_course_title_with_date();
				}
				if(count($course_dates_titles) > 0){
					$subject .= ' for ' . join(',', $course_dates_titles);
					$message .=  "\r\nCourses:\r\n" . join("\,r\n", $course_dates_titles);
				}
				
				
				
				$from_email = tbs_get_settings('online_form_manual_email', '');
				if($from_email){
					wp_mail(
						$from_email,
						$subject,
						$message,
						'',
						array($pdf_file_name)
					);
				}
				wp_redirect(add_query_arg(array('booking_manual_key' => $online_form_key, 'status' => 'submitted'), site_url()), 200);
				exit;
			}
		}
		
		if($order_id){
			wp_redirect(add_query_arg(array('booking_manual_key' => $online_form_key, 'status' => 'saved'), site_url()), 200);
			exit;
		}else{
			wp_redirect(add_query_arg(array('booking_manual_key' => $online_form_key, 'status' => 'failed'), site_url()), 200);
			exit;
		}
		
	}
	
	public function wc_custom_gateways(){
		if( class_exists( 'TBS_WC_Gateway_Credit_Acount' )){
			return;
		}
		require_once tbs_plugin_root_path() . 'includes/class-tbs-wc-gateway-credit-account.php';
		add_filter('woocommerce_payment_gateways', array($this, 'add_gateways'));
		add_filter('woocommerce_available_payment_gateways', array($this, 'process_conditional_gateways'));
	}
	public static function add_gateways($methods){
		$methods[] = 'TBS_WC_Gateway_Credit_Acount';
		return $methods;
	}
	
	public function process_conditional_gateways($_available_gateways){
		$remove_credit_account = true;
		if(is_user_logged_in() && 'credit' == get_user_meta( get_current_user_id(), 'account_type', true)){
			$remove_credit_account = false;
		}
		if($remove_credit_account && isset($_available_gateways['tbs_credit_acount'])){
			unset($_available_gateways['tbs_credit_acount']);
		}
		return $_available_gateways;
	}
	/**
	 * Display different meta tags on header
	 */
	public function header_meta_tags(){
		$block_robot = false;
		$object_id = get_queried_object_id();
		
		if(is_singular(TBS_Custom_Types::get_course_data('type'))){
			if('yes' == get_post_meta( $object_id, 'is_private', true )){
				$block_robot = true;
			}elseif( ($queried_date = absint( get_query_var('date', 0) )) && ('yes' == get_post_meta($queried_date, '_tbs_is_private', true)) ){
				$block_robot = true;
			}
		}
		if($block_robot){
			echo '<meta name="robots" content="noindex,nofollow" />';
		}
	}
	
	public function js_templates(){
		tbs_get_template_part('cart/added-to-cart-lightbox.tpl');
	}
	public function get_partial( $name, $return = true, $data = array() ) {
		$partial_path = plugin_dir_path( __FILE__ ) . 'partials/' . $name . '.php';

		if ( $return ) {
			return $partial_path;
		}
		if ( !file_exists( $partial_path ) ) {
			return;
		}
		if ( is_array( $data ) ) {
			extract( $data );
			unset( $data );
		}
		include $partial_path;
	}
	public function get_assets_url($path = ''){
		return plugin_dir_url(__FILE__) . 'assets/' . $path;
	}

}
