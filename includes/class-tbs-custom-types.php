<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TBS_Custom_Types {/**
	 * The single instance of the class.
	 *
	 * @var WC_Checkout|null
	 */
	protected static $instance = null;
	private $text_domain_name;
	private static $types_data;/**
	 * Gets the main WC_Checkout Instance.
	 *
	 * @since 2.1
	 * @static
	 * @return WC_Checkout Main instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( ) {
		$this->text_domain_name = TBS_i18n::get_domain_name();
		self::$types_data = array(
			'course' => array('type' => 'courses', 'slug' => 'course'),
			'course_category' => array('type' => 'course_category', 'slug' => 'category'),
			'trainer' => array('type' => 'trainer', 'slug' => false),
			'location' => array('type' => 'location', 'slug' => false),
		);
	}
	
	/**
	 * Post type register: Course
	 */
	private function course(){
		$type_name = self::get_course_data('type');
		$type_slug = self::get_course_data('slug');
		$course_type_name = self::get_course_data('type');
		$labels = array(
			'name'				 => _x( 'Course', 'post type general name', $this->text_domain_name ),
			'singular_name'		 => _x( 'Course', 'post type singular name', $this->text_domain_name ),
			'menu_name'			 => _x( 'Course', 'admin menu', $this->text_domain_name ),
			'name_admin_bar'	 => _x( 'Course', 'add new on admin bar', $this->text_domain_name ),
			'add_new'			 => _x( 'Add New Course', $this->text_domain_name ),
			'add_new_item'		 => __( 'Add New ' . 'Course', $this->text_domain_name ),
			'new_item'			 => __( 'New course', $this->text_domain_name ),
			'edit_item'			 => __( 'Edit course', $this->text_domain_name ),
			'view_item'			 => __( 'View course', $this->text_domain_name ),
			'all_items'			 => __( 'All course', $this->text_domain_name ),
			'search_items'		 => __( 'Search course', $this->text_domain_name ),
			'parent_item_colon'	 => __( 'Parent course', $this->text_domain_name ),
			'not_found'			 => __( 'No course found.', $this->text_domain_name ),
			'not_found_in_trash' => __( 'No course found in Trash.', $this->text_domain_name )
		);
		$args = array(
			'labels'				 => $labels,
			'public'				 => true,
			'publicly_queryable'	 => true,
			'show_ui'				 => true,
			'show_in_menu'			 => true,
			'query_var'				 => true,
			'rewrite'				 => array('slug' => $type_slug),
			'capability_type'		 => 'post',
			'has_archive'			 => false,
			'show_in_nav_menus'		 => true,
			'hierarchical'			 => false,
			'menu_position'			 => 25.1,
			'supports'				 => array('title', 'editor', 'thumbnail', 'excerpt'),
			'taxonomies'			 => array($course_type_name),
			'menu_icon'				 => 'dashicons-welcome-learn-more',
		);

		register_post_type( $type_name, $args );		
	}
	
	/**
	 * Custom Taxonomy register: Course Category
	 */
	private function course_category(){
		$type_name = self::get_course_category_data('type');
		$type_slug = self::get_course_category_data('slug');
		$course_type_name = self::get_course_data('type');
		$course_type_slug = self::get_course_data('slug');
		$labels	 = array(
			'name'						 => _x( 'Course Categories', 'taxonomy general name', $this->text_domain_name ),
			'singular_name'				 => _x( 'Course  Category', 'taxonomy singular name', $this->text_domain_name ),
			'search_items'				 => __( 'Search Course Categories', $this->text_domain_name ),
			'popular_items'				 => __( 'Popular Course Categories', $this->text_domain_name ),
			'all_items'					 => __( 'All Course Categories', $this->text_domain_name ),
			'parent_item'				 => null,
			'parent_item_colon'			 => null,
			'edit_item'					 => __( 'Edit Course Category', $this->text_domain_name ),
			'update_item'				 => __( 'Update Course Category', $this->text_domain_name ),
			'add_new_item'				 => __( 'Add New Course Category', $this->text_domain_name ),
			'new_item_name'				 => __( 'New Course Category', $this->text_domain_name ),
			'separate_items_with_commas' => __( 'Separate Course Categories with commas', $this->text_domain_name ),
			'add_or_remove_items'		 => __( 'Add or remove Course Categories', $this->text_domain_name ),
			'choose_from_most_used'		 => __( 'Choose from the most used Course Categories', $this->text_domain_name ),
			'not_found'					 => __( 'No Course  found.', $this->text_domain_name ),
			'menu_name'					 => __( 'Categories', $this->text_domain_name ),
		);
		$args	 = array(
			"labels"			 => $labels,
			'public'			 => true,
			'hierarchical'		 => true,
			'show_ui'			 => true,
			'show_in_nav_menus'	 => true,
			'show_admin_column'	 => true,
			'args'				 => array('orderby' => 'term_order'),
			'query_var'			 => true,
			'rewrite'			 => array('slug' => $course_type_slug . '/' . $type_slug, 'with_front' => false),
		);
		register_taxonomy( $type_name, $course_type_name, $args );
	}
	
	/**
	 * Post type register: Trainer
	 */
	private function trainer(){
		$type_name = self::get_trainer_data('type');
		$labels = array(
			'name'				 => _x( 'Trainer', 'post type general name', $this->text_domain_name ),
			'singular_name'		 => _x( 'Trainer', 'post type singular name', $this->text_domain_name ),
			'menu_name'			 => _x( 'Trainers', 'admin menu', $this->text_domain_name ),
			'name_admin_bar'	 => _x( 'Trainer', 'add new on admin bar', $this->text_domain_name ),
			'add_new'			 => _x( 'Add New','trainer', $this->text_domain_name ),
			'add_new_item'		 => __( 'Add New Trainer', $this->text_domain_name ),
			'new_item'			 => __( 'New Trainer', $this->text_domain_name ),
			'edit_item'			 => __( 'Edit Trainer', $this->text_domain_name ),
			'view_item'			 => __( 'View Trainer', $this->text_domain_name ),
			'all_items'			 => __( 'All Trainers', $this->text_domain_name ),
			'search_items'		 => __( 'Search Trainers', $this->text_domain_name ),
			'parent_item_colon'	 => __( 'Parent Trainers', $this->text_domain_name ),
			'not_found'			 => __( 'No Trainers found.', $this->text_domain_name ),
			'not_found_in_trash' => __( 'No trainers found in Trash.', $this->text_domain_name )
		);
		$args = array(
			'labels'				 => $labels,
			'public'				 => false,
			'publicly_queryable'	 => false,
			'show_ui'				 => true,
			'show_in_menu'			 => true,
			'query_var'				 => false,
			'rewrite'				 => false,
			'capability_type'		 => 'post',
			'has_archive'			 => false,
			'show_in_nav_menus'		 => false,
			'hierarchical'			 => false,
			'menu_position'			 => 25.2,
			'supports'               => array('title', 'editor', 'thumbnail'),
			'menu_icon'				 => 'dashicons-businessman',
		);
		register_post_type( $type_name, $args );
	}
	/**
	 * Post type register: Location
	 */
	private function location(){
		$show_in_menu = true;
		$type_name = self::get_location_data('type');
		$labels = array(
			'name'				 => _x( 'Location', 'post type general name', $this->text_domain_name ),
			'singular_name'		 => _x( 'Location', 'post type singular name', $this->text_domain_name ),
			'menu_name'			 => _x( 'Locations', 'admin menu', $this->text_domain_name ),
			'name_admin_bar'	 => _x( 'Location', 'add new on admin bar', $this->text_domain_name ),
			'add_new'			 => _x( 'Add New','location', $this->text_domain_name ),
			'add_new_item'		 => __( 'Add New Location', $this->text_domain_name ),
			'new_item'			 => __( 'New Location', $this->text_domain_name ),
			'edit_item'			 => __( 'Edit Location', $this->text_domain_name ),
			'view_item'			 => __( 'View Location', $this->text_domain_name ),
			'all_items'			 => __( 'All Locations', $this->text_domain_name ),
			'search_items'		 => __( 'Search Locations', $this->text_domain_name ),
			'parent_item_colon'	 => __( 'Parent Location', $this->text_domain_name ),
			'not_found'			 => __( 'No Locations found.', $this->text_domain_name ),
			'not_found_in_trash' => __( 'No locations found in Trash.', $this->text_domain_name )
		);
		$args = array(
			'labels'				 => $labels,
			'public'				 => false,
			'publicly_queryable'	 => false,
			'show_ui'				 => true,
			'show_in_menu'			 => $show_in_menu,
			'query_var'				 => false,
			'rewrite'				 => false,
			'capability_type'		 => $type_name,
			'map_meta_cap'			 => true,
			'has_archive'			 => false,
			'show_in_nav_menus'		 => false,
			'hierarchical'			 => false,
			'menu_position'			 => 25.2,
			'supports'               => array('title', 'editor', 'thumbnail'),
			'menu_icon'				 => 'dashicons-location-alt',
		);
		register_post_type( $type_name, $args );
	}
	/**
	 * Post Status Register: WC Manual Draft
	 */
	public function post_statuses (){
		register_post_status('wc-tbs-draft', array(
			'label' => _x( 'Manual Draft', 'Order status', $this->text_domain_name ),
			'public' => false,
			'exclude_from_search' => true,
		));
	}

	/**
	 * Get Course data
	 * @param string $key
	 * @return string
	 */
	public static function get_course_data($key = ''){
		if($key && isset(self::$types_data['course'][$key])){
			return self::$types_data['course'][$key];
		}
		return self::$types_data['course'];
	}
	
	/**
	 * Get Course Category data
	 * @param string $key
	 * @return string
	 */
	public static function get_course_category_data($key = ''){
		if($key && isset(self::$types_data['course_category'][$key])){
			return self::$types_data['course_category'][$key];
		}
		return self::$types_data['course_category'];
	}
	
	/**
	 * Get Trianer data
	 * @param string $key
	 * @return string
	 */
	public static function get_trainer_data($key = ''){
		if($key && isset(self::$types_data['trainer'][$key])){
			return self::$types_data['trainer'][$key];
		}
		return self::$types_data['trainer'];
	}
	
	/**
	 * Get Location data
	 * @param string $key
	 * @return string
	 */
	public static function get_location_data($key = ''){
		if($key && isset(self::$types_data['location'][$key])){
			return self::$types_data['location'][$key];
		}
		return self::$types_data['location'];
	}
	/**
	 * Register each custom types
	 */
	public function register(){
		$this->course_category();
		$this->course();
		$this->trainer();
		$this->location();
		$this->post_statuses();
	}
}