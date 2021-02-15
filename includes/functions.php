<?php
function get_adminstrate_courses($params = array()){
    $courses = array();
    if(!class_exists('Administrate')){return $courses;}
    $courses = Administrate::api()->get_course_list( $params);
    
    ksort($courses);
    return $courses;
}

function get_linked_course($course_code=''){
    if(!$course_code){return false;}
    $post = get_posts(array(
        'post_type' => 'courses',
        'meta_key'     => 'training_administrate_course',
	'meta_value'   => $course_code,
    ));
    if($post){
        return array_shift($post);
    }
    return false;
}


function tts_get_acc_logos() {

    $logos = array(
        'logo1' => array('label' => 'Logo 1', 'logo' => get_stylesheet_directory_uri() . '/images/course/logo1.png'),
        'logo2' => array('label' => 'Logo 2', 'logo' => get_stylesheet_directory_uri() . '/images/course/logo2.png'),
        'logo3' => array('label' => 'Logo 2', 'logo' => get_stylesheet_directory_uri() . '/images/course/logo3.png'),
    );

    return $logos;
}

function tts_get_persons_graphics1() {
    $persons = array(
        'alan' => array('label' => 'Alan', 'logo' => get_stylesheet_directory_uri() . '/images/course/person-alan.png'),
        'dave' => array('label' => 'Dave', 'logo' => get_stylesheet_directory_uri() . '/images/course/person-dave.png'),
    );

    return $persons;
}

function tts_get_persons_graphics2() {
    return tts_get_persons_graphics1();
}

function tts_get_stikers() {

    $stikers = array(
        'sticker_certificate' => array(
            'label' => 'Ceritificate',
            'logo' => get_stylesheet_directory_uri() . '/images/course/sticker-certificate.png',
            'info' => 'Cirficate Hover Text'
        ),
        'sticker_duration-1d' => array(
            'label' => 'One Day Duration',
            'logo' => get_stylesheet_directory_uri() . '/images/course/sticker-duration-1d.png',
            'info' => 'One day dureation'
        ),
        'sticker_price_175' => array(
            'label' => 'Price - $175',
            'logo' => get_stylesheet_directory_uri() . '/images/course/sticker-price-175.png',
            'info' => '$175 with vat'
        ),
        'sticker_delegates_1_12' => array(
            'label' => 'Delegates 1min-12max',
            'logo' => get_stylesheet_directory_uri() . '/images/course/sticker-delegates-1-12.png',
            'info' => '1 min 12 Max delegates.'
        ),
    );

    return $stikers;
}

function tts_array_ksort_merge($main_array, $key_array) {
    if (empty($key_array)) {
        return $main_array;
    }
    $sorted_arrray = array();
    foreach ($key_array as $k) {
        if (isset($main_array[$k])) {
            $sorted_arrray[$k] = $main_array[$k];
            unset($main_array[$k]);
        }
    }
    return array_merge($sorted_arrray, $main_array);
}

function tts_check_stickers_array($stikers = array(), $save = false) {
    if (empty($stikers) || !is_array($stikers)) {
        if ($save)
            return '';
        return array(
            0 => array('normal' => '', 'hover' => '')
        );
    }
    $checked_sticker = array();
    $k = 0;
    foreach ($stikers as $st) {
        if (!is_array($st) || empty($st['normal']) || empty($st['hover'])) {
            continue;
        }
        $checked_sticker[$k]['normal'] = $st['normal'];
        $checked_sticker[$k]['hover'] = $st['hover'];
        $k++;
    }
    return $checked_sticker;
}

function tts_get_course_locations() {
    return array(
        "Aberdeen",
        "Aintree (Liverpool)",
        "Birmingham",
        "Bracknell",
        "Caldicot (South Wales)",
        "Cambridge",
        "Canning Town",
        "Cardiff",
        "Chelmsford",
        "Coatbridge (Glasgow)",
        "Con Hill",
        "Derby",
        "Doncaster",
        "Durham",
        "Edinburgh",
        "Essex",
        "Exeter",
        "Gatwick",
        "Gatwick (London)",
        "Glasgow",
        "Hull",
        "Kent",
        "Leeds",
        "Leicester",
        "Lincoln",
        "Liskeard",
        "Liverpool",
        "London",
        "London East",
        "London – East Thurrock",
        "London – Purfleet ",
        "London – Uxbridge ",
        "Luton",
        "Manchester",
        "Manchester Norwich",
        "Middlesbrough",
        "Milton Keynes",
        "Newcastle",
        "North Wales (Deeside) Peterborough",
        "Norwich",
        "Nottingham",
        "Nottingham (Newstead)",
        "Penzance",
        "Plymouth",
        "Portsmouth",
        "Rainham",
        "Reading",
        "Ringwood",
        "Roche (Cornwall)",
        "Salford",
        "Sheffield",
        "Southampton",
        "St Albans",
        "Swansea",
        "Swindon",
        "Taunton",
        "Uxbridge",
        "Walsall",
        "Warrington",
        "Wembley",
        "Weston",
        "Wimborne",
        "Worcester",
        "York"
    );
}

function tbs_header_cart_summery(){
	tbs_get_template_part('cart/cart-summery');
}

function tbs_get_delegates_field(){
	return array(
		'first_name' => array(
			'label' => __('First Name', TBS_i18n::get_domain_name()),
			'required' => false,
			'cd_required' => true,
			'type' => 'text',
			'class' => array( 'form-row-first', ),
			'input_class' => array( 'df-first-name', 'cd_required_field' ),
		),
		'last_name' => array(
			'label' => __('Last Name', TBS_i18n::get_domain_name()),
			'required' => false,
			'cd_required' => true,
			'type' => 'text',
			'class' => array( 'form-row-last', ),
			'input_class' => array( 'df-last-name', 'cd_required_field' ),
		),
		'email' => array(
			'label'        => __( 'Email address', TBS_i18n::get_domain_name() ),
			'required'     => false,
			'type'         => 'email',
			'class'		   => array('form-row-wide'),
			'input_class'=> array('df-email'),
			'cd_required' => false,
			'validate'     => array( 'email' ),
			'autocomplete' => 'no' === get_option( 'woocommerce_registration_generate_username' ) ? 'email' : 'email username',
			'description' => 'If not known please leave blank, details will be sent to the Booker for forwarding',
		),
		'notes' => array(
			'type'        => 'textarea',
			'class'       => array( 'notes', 'form-row-wide' ),
			'cd_required' => false,
			'label'       => __( 'Notes', TBS_i18n::get_domain_name() ),
			'placeholder' => esc_attr__( 'Any special dietary requirements?  Do they need help with reading / writing? Etc.', TBS_i18n::get_domain_name() ),
		),
	);
}
function tbs_get_address_fields($key_base){
	$address_fields = WC()->countries->get_address_fields( '', $key_base . '_' );
	foreach ( $address_fields as $key => $field ) {
		if(!empty($field['required'])){
			$field['required'] = false;
			$field['cd_required'] = true;
			$field['class'] = is_array($field['class'])?array_merge($field['class'], array('cd_required_field')):array('cd_required_field');
		}else{
			$field['cd_required'] = false;
		}
		$field['fieldset_key'] = $key_base;
		if ( isset( $field['country_field'], $fields[ $field['country_field'] ] ) ) {
			$field['country'] = WC()->checkout()->get_value( $field['country_field'] );
		}
		if(empty($field['type'])){
			$field['type'] = 'text';
		}
		if(empty($field['label'])){
			$field['label'] = $field['placeholder'];
		}
		$address_fields[$key] = $field;
	}
	return $address_fields;
}
/**
 * 
 * @param string $key key to find
 * @param array $arr Input array
 * @param mixed $default
 * @return mixed|boolean
 */
function tbs_arr_get($key, $arr, $default = ''){
	if(!isset($arr[$key])){
		return $default;
	}
	if($arr[$key] === 'true'){
		return true;
	}
	if($arr[$key] === 'false'){
		return false;
	}
	return $arr[$key];
}

function tbs_analysis_array_merge($array1, $array2){
	$existing = array_intersect($array1, $array2);
	$removed = array_diff($array1, $array2);
	$new = array_diff($array2, $existing);
	return array('existing' => $existing, 'removed' => $removed, 'new' => $new);
}

function tbs_array_insert_item( $items, $new_items, $after ) {
	// Search for the item position and +1 since is after the selected item key.
	$position = array_search( $after, array_keys( $items ) ) + 1;

	// Insert the new item.
	$array = array_slice( $items, 0, $position, true );
	$array += $new_items;
	$array += array_slice( $items, $position, count( $items ) - $position, true );

    return $array;
}


/**
 * Campaign Monitor API call to add subscribers
 * @param type $sbuscriber_data
 * @param type $order
 * @return boolean
 */
function tbs_campaign_monitor_import($sbuscriber_data, $order = false){
	try {
		$course_settings = get_option('tbs_settings');

		$ca_apikey = isset($course_settings['ca_apikey'])?$course_settings['ca_apikey']:'';
		$ca_clientid = isset($course_settings['ca_clientid'])?$course_settings['ca_clientid']:'';
		$ca_list_id = isset($course_settings['ca_list_id'])?$course_settings['ca_list_id']:'';

		if(!$ca_apikey || !$ca_clientid || !$ca_list_id){
			return false;
		}
		require_once tbs_get_libarary('campaignmonitor-createsend/csrest_subscribers.php');
		$wrap = new CS_REST_Subscribers($ca_list_id, array('api_key' => $ca_apikey));
		$result = $wrap->import($sbuscriber_data, true, true, true);

		$notes = array();
		if($result->was_successful()) {
			$notes[] = __('Successfully imported to campaign monitor.');
		} else {
			if($result->response->ResultData->TotalExistingSubscribers > 0) {
				echo 'Updated '.$result->response->ResultData->TotalExistingSubscribers.' existing subscribers in the list';        
			} else if($result->response->ResultData->TotalNewSubscribers > 0) {
				echo 'Added '.$result->response->ResultData->TotalNewSubscribers.' to the list';
			} else if(count($result->response->ResultData->DuplicateEmailsInSubmission) > 0) { 
				echo $result->response->ResultData->DuplicateEmailsInSubmission.' were duplicated in the provided array.';
			}
			if(is_array($result->response->ResultData->FailureDetails) && count($result->response->ResultData->FailureDetails) > 0){
				$failed_msg = '';
				foreach($result->response->ResultData->FailureDetails as $failed_email){

					$failed_msg .= "{$failed_email->EmailAddress}: {$failed_email->Message}\n";
				}
				$notes[] = 'Failed to import to campaign monitor:' . "\n".$failed_msg;
			}else{
				$notes[] = __('Successfully imported to campaign monitor.');
			}
		}
		$order->add_order_note(implode( "\n", $notes ));
		return true;
	}  catch (Exception $e) { 
		return false;
	}
}
/**
 * Get coupon course dates IDs
 * @param type $coupon_id
 * @return type
 */
function tbs_get_course_dates_for_coupon($coupon_id){
	$coupon_courses = get_post_meta($coupon_id, 'tbs_coupon_courses', true);
	if(!$coupon_courses){
		return array();
	}
	
	$course_date_ids = array();
	$meta_query = array();
	$meta_query['relation'] = 'AND';
	$meta_query[] = array(
		'key' => '_is_tbs_course',
		'value' => 'yes',
		'compare' => '=',
	);
	$meta_query[] = array(
		'key' => '_tbs_course',
		'value' => $coupon_courses,
		'compare' => 'IN',
	);
	$query_args = array(
		'post_type' => 'product',
		'posts_per_page' => -1,
		'meta_query' => $meta_query,
		'orderby' => 'title',
		'order' => 'ASC',
	);
	$course_date_query = new WP_Query($query_args);
	if(!$course_date_query->have_posts()){
		$course_date_ids = array();
	}else{
		while($course_date_query->have_posts()){
			$course_date_query->the_post();
			$course_date_ids[] = (int)get_the_ID();
		}
		wp_reset_postdata();
	}
	return $course_date_ids;
}

function tbs_get_availabilyt_offset($time_of_day){
		/**
		 * @todo Calculate time offset
		 */
		return 0;
	}
/**
 * Get setting
 * @param type $key
 * @param type $default
 * @return mix
 */
function tbs_get_settings($key = '', $default = ''){
	$settings = get_option('tbs_settings');
	if( !is_array($settings) ){
		return $default;
	}
	if(!$key){
		return $settings;
	}
	return tbs_arr_get($key, $settings, $default);
}
/**
 * Get course dates
 * @return \TBS_Course_Date
 */
function tbs_get_course_dates($args){
	$args = wp_parse_args($args, array(
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
		'ids_only' => false,
	));
	extract($args);
	$meta_query = array();
	$meta_query['relation'] = 'AND';
	$meta_query[] = array(
		'key' => '_is_tbs_course',
		'value' => 'yes',
		'compare' => '=',
	);

	if( is_array($course_ids) && count($course_ids) > 0){
		$course_ids  = array_map('absint', $course_ids);
		$meta_query['course'] = array(
			'key' => '_tbs_course',
			'value' => $course_ids,
			'compare' => 'IN',
		);
	}
	if( is_array($locations) && count($locations) > 0){
		$course_ids  = array_map('absint', $locations);
		$meta_query['location'] = array(
			'key' => '_tbs_location',
			'value' => $locations,
			'compare' => 'IN',
		);
	}
	$meta_query['start_date'] = array(
		'key' => '_tbs_start_date',
		'compare' => 'EXISTS',
	);
	$availability_offset = tbs_get_availabilyt_offset($availability_offset_time);
	$now = time() + $availability_offset;
	if('upcoming' == $type){
		$meta_query['start_date'] = array(
			'key' => '_tbs_start_date',
			'value' => $now,
			'compare' => '>',
		);
	}
	if('past' == $type){
		$meta_query['start_date'] = array(
			'key' => '_tbs_start_date',
			'value' => $now,
			'compare' => '<=',
		);
	}
	if(!$show_private){
		$meta_query['is_private'] = array(
			'key' => '_tbs_is_private',
			'value' => 'yes',
			'compare' => '!=',
		);
	}

	$orderby_clauses = array($orderby => $order);
	//		post__in 
	if(is_array( $date_ids ) && count($date_ids) > 0 ){
		$query_args = array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'post__in' => $date_ids,
			'orderby' => 'post__in',
		);
		if($type){
			$query_args['meta_query'] = array(
				'start_date' => $meta_query['start_date'],
			);
		}
	}else{
		$query_args = array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'meta_query' => $meta_query,
			'orderby' => $orderby_clauses,
		);
	}

	//$woo_products = wc_get_products($query_args);
	$date_query = new WP_Query($query_args);
	if(!$date_query->have_posts()){
		return array();
	}
	$course_dates = array();
	while($date_query->have_posts()){
		$date_query->the_post();
		if($ids_only){
			$course_dates[] = absint(get_the_ID());
			continue;
		}
		$course_date = new TBS_Course_Date( get_the_ID());
		if($json_model){
			$course_dates[] = $course_date->get_json_model();
		}else{
			$course_dates[] = $course_date;
		}
	}
	wp_reset_postdata();
	return $course_dates;
}


function tbs_courses_dropdown($options = array()){
	$first_option = $select_id = $select_classes = $select_name = $select_multiple = '';
	extract(wp_parse_args($options, array(
		'first_option' => '', 
		'select_id' => '', 
		'select_classes' => '', 
		'select_name' => '', 
		'select_multiple' => false,
		'show_private' => false,
		)
	));
	
	$args = array(
		'hide_empty' => true,
		'fields'	 => 'id=>name',
		'hierarchical' => false,
	);
	
	$query_args = array(
		'posts_per_page'=> -1,
		'post_type'     => TBS_Custom_Types::get_course_data( 'type' ),
		'orderby'       => 'title',
		'order'         => 'ASC','posts_per_page'=> -1,
		'post_type'     => TBS_Custom_Types::get_course_data( 'type' ),
		'orderby'       => 'title',
		'order'         => 'ASC',
	);
	if(!$show_private){
		$query_args['meta_query'] = array(
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
	}

	$courses = get_posts($query_args);
	$html = '';
	foreach($courses as $course){
		$html .= '<option value="'. $course->ID .'">'. $course->post_title .'</option>';
	}

	if(!$html && !$first_option){
		return '';
	}
	$attributes = array();
	if($select_id){
		$attributes[] = 'id="'. $select_id .'"';
	}
	if($select_classes){
		$attributes[] = 'class="'. $select_classes .'"';
	}
	if($select_name){
		$attributes[] = 'name="'. $select_name .'"';
	}
	if($select_multiple){
		$attributes[] = 'multiple="multiple"';
	}
	return '<select '. implode(' ', $attributes) .' ><option value="">'.$first_option.'</option>'. $html .'</select>';
}

function is_course_manager(){
	$current_user = wp_get_current_user();
	if( $current_user &&  in_array( 'course_manager', $current_user->roles )){
		return true;
	}
	return false;
}

function tbs_wc_get_emails_recipient($email_key, $email){
	$enabled_users = get_users(array(
		'meta_key' => 'tbs_' . strtolower($email_key),
		'meta_value' => 'true',
		'meta_compare' => '=',
		'fields' => array('ID', 'user_email'),
	));
	$enabled_emails = array();
	foreach($enabled_users as $e_user){
		$enabled_emails[] = $e_user->user_email;
	}
	$existed_recipients = array_map( 'trim', explode( ',', $email->recipient ) );
	$enabled_emails = array_merge($enabled_emails, $existed_recipients);
	$enabled_emails = array_unique($enabled_emails);
	return  implode(',', $enabled_emails);
}
/**
 * 
 * @global obj $wpdb
 * @param string $booking_key
 * @return bool|WC_Order|WC_Refund
 */
function get_booking_by_booking_key( $booking_key ) {
	global $wpdb;
	$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_tbs_online_form_id' AND meta_value = %s", $booking_key ) );
	
	if(!$order_id){
		return false;
	}
	return wc_get_order($order_id);
}
/**
 * Check if current page is for manual booking form
 * @return bool
 */
function tbs_is_manual_booking_form_page(){
	return !empty($_GET['booking_manual_key']);
}

function tbs_generate_pdf($template_file, $template_data, $pdf_file_name = '', $output_type = 'inline'){
	if( !file_exists( $template_file )){
		return false;
	}
	if( !class_exists('\Mpdf\Mpdf')){
		require_once tbs_plugin_root_path() . 'vendor/autoload.php';
	}
	ob_start();
	if($template_data){
		extract($template_data);
	}
	include $template_file;
	$html = ob_get_clean();
	
	$configs = array(
		'margin_left' => 10,
		'margin_right' => 10,
		'margin_top' => 10,
		'margin_bottom' => 5,
		'margin_header' => 0,
		'margin_footer' => 0,
	);
	
	if(!$pdf_file_name){
		$pdf_file_name = 'the-training-societi-'. time() .'.pdf';
		if('file' == $output_type){
			$pdf_file_name = WP_CONTENT_DIR . '/tbs-pdfs/' . $pdf_file_name;
		}
	}
	
	switch ($output_type){
		case 'inline': 
			$dest = \Mpdf\Output\Destination::INLINE;
			break;
		case 'download': 
			$dest = \Mpdf\Output\Destination::DOWNLOAD;
			break;
		case 'file': 
			$dest = \Mpdf\Output\Destination::FILE;
			break;
		case 'string': 
			$dest = \Mpdf\Output\Destination::STRING_RETURN;
			break;
		default: 
			$dest = \Mpdf\Output\Destination::INLINE;
			break;
	}
	
	$mpdf = new \Mpdf\Mpdf($configs);
	$mpdf->SetTitle("The Training Societi");
	$mpdf->SetAuthor("The Training Societi");
	$mpdf->SetWatermarkText("The Training Societi");
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->watermarkTextAlpha = 0.01;
	$mpdf->SetDisplayMode('fullpage');
	//$mpdf->SetHTMLHeader($header_html);
	//$mpdf->setFooter('{PAGENO}');
	$mpdf->WriteHTML($html);
	return $mpdf->Output($pdf_file_name, $dest);
}
/**
 * Add email records for an order
 * @param string $note
 * @param WC_Order $order
 * @param string $record_type
 */
function tbs_add_order_email_record($note, $order, $record_type=''){
	$note_id = $order->add_order_note($note);
	if($note_id && $record_type){
		add_comment_meta( $note_id, 'tbs_email_type', $record_type);
	}
}

function tbs_resend_wc_emails($order, $email_type){
	$mailer = false;
	switch($email_type){
		case 'booking_confirmation':
			$mailer = isset(WC()->mailer()->emails['WC_Email_Customer_Completed_Order']) ? WC()->mailer()->emails['WC_Email_Customer_Completed_Order'] : false;
			break;
		case 'joining_instructions':
			$mailer = isset(WC()->mailer()->emails['TBS_WC_Email_Joining_Instructions']) ? WC()->mailer()->emails['TBS_WC_Email_Joining_Instructions'] : false;
			break;
		default:
			break;
	}
	if(!$mailer){
		return;
	}
	WC()->payment_gateways();
	WC()->shipping();
	$mailer->trigger( $order->get_id(), $order );
}
/**
 * Get course dates listing page url
 * @return strig
 */
function tbs_get_course_dates_listing_url(){
	$course_date_listing_page = tbs_get_settings('course_date_list_page_id');
	if($course_date_listing_page){
		$redirect_url = get_permalink($course_date_listing_page);
	}else{
		$redirect_url = home_url();
	}
	return $redirect_url;
}

function tbs_get_time_based_bookings_id($past_or_upcoming = 'upcoming', $course_date_id = false, $created_via = false){
	global $wpdb;
	$course_date_ids =  tbs_get_course_dates(array(
		'type' => 'upcoming',
		'ids_only' => true,
	));
	
	if(!$course_date_ids){
		return array();
	}
	$status = array_keys(wc_get_order_statuses());
	
	$sql = "";
	$sql .= " SELECT DISTINCT order_items.order_id FROM {$wpdb->prefix}woocommerce_order_items as order_items";
	$sql .= " LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id";
	$sql .= " LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID";
	$sql .= " WHERE (1 = 1)";
	$sql .= " AND posts.post_type = 'shop_order'";
	if( is_array($status) && count($status) > 0 ){
		$sql .= " AND posts.post_status IN ( '" . implode( "','", $status ) . "' )";
	}
	$sql .= " AND (order_items.order_item_type = 'line_item')";
	$sql .= " AND (order_item_meta.meta_key = '_product_id')";
	$sql .= " AND order_item_meta.meta_value IN (". implode( ',', $course_date_ids ) .")";
	$upcoming_booking_ids = $wpdb->get_col($sql);
	
	//SQL_CALC_FOUND_ROWS
	$sql = "";
	$sql .= " SELECT DISTINCT order_items.order_id FROM {$wpdb->prefix}woocommerce_order_items as order_items";
	$sql .= " LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id";
	$sql .= " LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID";
	if($created_via){
		$sql .= " LEFT JOIN {$wpdb->postmeta} as mt ON order_items.order_id = mt.post_id";
	}
	$sql .= " WHERE (1 = 1)";
	$sql .= " AND posts.post_type = 'shop_order'";
	if( is_array($status) && count($status) > 0 ){
		$sql .= " AND posts.post_status IN ( '" . implode( "','", $status ) . "' )";
	}
	if($created_via){
		$sql .= " AND mt.meta_key = '_created_via' AND mt.meta_value = '{$created_via}'";
	}
	if($course_date_id){
		$sql .= " AND (order_items.order_item_type = 'line_item')";
		$sql .= " AND (order_item_meta.meta_key = '_product_id')";
		$sql .= " AND order_item_meta.meta_value = '%d'";
	}
	if('past' == $past_or_upcoming){
		$sql .= " AND order_items.order_id NOT IN (". implode( ',', $upcoming_booking_ids ) .")";
	}elseif('upcoming' == $past_or_upcoming){
		$sql .= " AND order_items.order_id IN (". implode( ',', $upcoming_booking_ids ) .")";
	}
	//echo $wpdb->prepare($sql, $this->id);die();
	if($course_date_id){
		$sql = $wpdb->prepare($sql, $course_date_id);
	}
	$bookings_ids = $wpdb->get_col($sql);
	$bookings_ids = array_map('absint', $bookings_ids);
	return $bookings_ids;
	
	return $booking_ids;
}

function tbs_order_course_delegates_count($order, $course_id){
	$items = $order->get_items('line_item');
	$count = 0;

	foreach ( $items as $item ) {
		$_product = $item->get_product();
		if ( $_product && $_product->exists() && $course_id == $_product->get_id() ) {
			$count += $item->get_quantity();
		}
	}
	
	return $count;
}

function random_uqniq_user_email(){
	$uniq_key = microtime();
	$uniq_key = str_replace('.', '', $uniq_key);
	$uniq_key = explode(' ', $uniq_key);
	$uniq_key = $uniq_key[1] . '_' . $uniq_key[0];
	return 'random_email_' . $uniq_key . '@trainingsocieti.co.uk';
}