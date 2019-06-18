<?php
/**
 * WooCommerce Meta Box Functions
 *
 * @author      WooThemes
 * @category    Core
 * @package     WooCommerce/Admin/Functions
 * @version     2.3.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Output a text input box.
 *
 * @param array $field
 */
function tbs_wp_text_input( $field,  $post_id = null) {
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $post_id, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
	$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;
	$data_type              = empty( $field['data_type'] ) ? '' : $field['data_type'];

	switch ( $data_type ) {
		case 'price' :
			$field['class'] .= ' wc_input_price';
			$field['value']  = wc_format_localized_price( $field['value'] );
			break;
		case 'decimal' :
			$field['class'] .= ' wc_input_decimal';
			$field['value']  = wc_format_localized_decimal( $field['value'] );
			break;
		case 'stock' :
			$field['class'] .= ' wc_input_stock';
			$field['value']  = wc_stock_amount( $field['value'] );
			break;
		case 'url' :
			$field['class'] .= ' wc_input_url';
			$field['value']  = esc_url( $field['value'] );
			break;

		default :
			break;
	}

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
		echo wc_help_tip( $field['description'] );
	}

	echo '<input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

	if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	echo '</p>';
}

/**
 * Output a hidden input box.
 *
 * @param array $field
 */
function tbs_wp_hidden_input( $field,  $post_id = null) {
	$field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $post_id, $field['id'], true );
	$field['class'] = isset( $field['class'] ) ? $field['class'] : '';

	echo '<input type="hidden" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" /> ';
}

/**
 * Output a textarea input box.
 *
 * @param array $field
 */
function tbs_wp_textarea_input( $field,  $post_id = null) {
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $post_id, $field['id'], true );
	$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['rows']          = isset( $field['rows'] ) ? $field['rows'] : 2;
	$field['cols']          = isset( $field['cols'] ) ? $field['cols'] : 20;

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
		echo wc_help_tip( $field['description'] );
	}

	echo '<textarea class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '"  name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" rows="' . esc_attr( $field['rows'] ) . '" cols="' . esc_attr( $field['cols'] ) . '" ' . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $field['value'] ) . '</textarea> ';

	if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	echo '</p>';
}

/**
 * Output a checkbox input box.
 *
 * @param array $field
 */
function tbs_wp_checkbox( $field,  $post_id = null) {
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'checkbox';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $post_id, $field['id'], true );
	$field['cbvalue']       = isset( $field['cbvalue'] ) ? $field['cbvalue'] : 'yes';
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
		echo wc_help_tip( $field['description'] );
	}

	echo '<input type="checkbox" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['cbvalue'] ) . '" ' . checked( $field['value'], $field['cbvalue'], false ) . '  ' . implode( ' ', $custom_attributes ) . '/> ';

	if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	echo '</p>';
}

/**
 * Output a select input box.
 *
 * @param array $field
 */
function tbs_wp_select( $field,  $post_id = null) {
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $post_id, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
		echo wc_help_tip( $field['description'] );
	}

	echo '<select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" ' . implode( ' ', $custom_attributes ) . '>';

	foreach ( $field['options'] as $key => $value ) {
		echo '<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
	}

	echo '</select> ';

	if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	echo '</p>';
}

/**
 * Output a radio input box.
 *
 * @param array $field
 */
function tbs_wp_radio( $field,  $post_id = null) {
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $post_id, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

	echo '<fieldset class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><legend>' . wp_kses_post( $field['label'] ) . '</legend>';

	if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
		echo wc_help_tip( $field['description'] );
	}

	echo '<ul class="wc-radios">';

	foreach ( $field['options'] as $key => $value ) {

		echo '<li><label><input
				name="' . esc_attr( $field['name'] ) . '"
				value="' . esc_attr( $key ) . '"
				type="radio"
				class="' . esc_attr( $field['class'] ) . '"
				style="' . esc_attr( $field['style'] ) . '"
				' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '
				/> ' . esc_html( $value ) . '</label>
		</li>';
	}
	echo '</ul>';

	if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	echo '</fieldset>';
}

function tbs_grouped_courses_dropdown($options = array()){
	$first_option = $select_id = $select_classes = $select_name = $select_multiple = '';
	extract(wp_parse_args($options, array('first_option' => '', 'select_id' => '', 'select_classes' => '', 'select_name' => '', 'select_multiple')));
	$course_taxonomy_name = TBS_Custom_Types::get_course_category_data('type');
	$course_type_name = TBS_Custom_Types::get_course_data( 'type' );
	$args = array(
		'hide_empty' => true,
		'fields'	 => 'id=>name',
		'hierarchical' => false,
	);
	$course_cats = get_terms( $course_taxonomy_name, $args );
	if ( !$course_cats || is_wp_error( $course_cats ) ) {
		return '';
	}
	$course_cats_id = array();
	$html = '';
	foreach($course_cats as $cat_id=>$cat_name){
		$course_cats_id[] = $cat_id;
		$category_courses = get_posts(array(
			'posts_per_page'=> -1,
			'post_type'     => $course_type_name,
			'orderby'       => 'title',
			'order'         => 'ASC',
			'tax_query'     => array(
				array(
					'taxonomy'  => $course_taxonomy_name,
					'field'     => 'id',
					'terms'     => array($cat_id)
				)
			)
		));
		if(!$category_courses){
			continue;
		}
		$html .= '<optgroup label='. esc_attr($cat_name) .'">';
		foreach($category_courses as $course){
			$html .= '<option value="'. $course->ID .'">'. $course->post_title .'</option>';
		}
		$html .= ' </optgroup>';
	}
	if( count( $course_cats_id )> 0){
		$no_category_courses = get_posts(array(
			'posts_per_page'=> -1,
			'post_type'     => $course_type_name,
			'orderby'       => 'title',
			'order'         => 'ASC',
			'tax_query'     => array(
				array(
					'taxonomy'  => $course_taxonomy_name,
					'field'     => 'id',
					'terms'     => $course_cats_id,
					'operator'  => 'NOT IN'
				)
			)
		));
		if($no_category_courses) {
			$html .= '<optgroup label="Other Courses">';
			foreach($no_category_courses as $nc_course){
				$html .= '<option value="'. $nc_course->ID .'">'. $nc_course->post_title .'</option>';
			}
			$html .= ' </optgroup>';
		}
		
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

function tbs_admin_create_customer($data = array()){
	$data = wp_parse_args($data, array(
		'existing_customer_ID' => false,
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
	if($data['existing_customer_ID']){
		$userdata = WP_User::get_data_by('id', $data['existing_customer_ID']);
	}else{
		$userdata = WP_User::get_data_by('email', $data['email']);
	}

	if(!$userdata){
		// User does not exist
		// Create a new customer
		$customer_id = wc_create_new_customer( $data['email'], $data['email'], wp_generate_password(12, true, true) );

		if ( is_wp_error( $customer_id ) ) {
			throw new Exception( $customer_id->get_error_message() );
		}
	}else{
		$customer_id = $userdata->ID;
	}
	// Save customer data
	$customer = new WC_Customer( $customer_id );

	if($data['existing_customer_ID'] && ($data['email'] != $customer->get_email()) ){
		$customer->set_email($data['email']);
		$customer->set_username($data['email']);
	}

	if ( ! empty( $data['first_name'] ) ) {
			$customer->set_first_name( $data['first_name'] );
	}

	if ( ! empty( $data['last_name'] ) ) {
		$customer->set_last_name( $data['last_name'] );
	}

	$customer->set_display_name( $data['first_name'] . ' ' . $data['last_name'] );
	update_user_meta($customer_id, 'nickname', $data['first_name'] . ' ' . $data['last_name'] );

	foreach ( $data as $key => $value ) {
		// Store custom fields prefixed with billing_.
		if ( is_callable( array( $customer, "set_billing_{$key}" ) ) ) {
			// Use setters where available.
			$customer->{"set_billing_{$key}"}( $value );
		}
	}

	/**
	 * Action hook to adjust customer before save.
	 * @since 3.0.0
	 */
	do_action( 'woocommerce_checkout_update_customer', $customer, $data );

	$customer->save();
	
	return $customer_id;
}