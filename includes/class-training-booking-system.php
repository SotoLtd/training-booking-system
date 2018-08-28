<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://mhmasum.me/
 * @since      1.0.0
 *
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/includes
 * @author     TTS <mmhasaneee@gmail.com>
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Training_Booking_System {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      TBS_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TBS_VERSION' ) ) {
			$this->version = TBS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'tbs';

		$this->load_dependencies();
		$this->set_locale();
		$this->register_post_types();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - TBS_Loader. Orchestrates the hooks of the plugin.
	 * - TBS_i18n. Defines internationalization functionality.
	 * - TBS_Admin. Defines all hooks for the admin area.
	 * - TBS_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tbs-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tbs-i18n.php';

		/**
		 * Utility functions of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';

		/**
		 * The class responsible for defining all post types.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tbs-custom-types.php';
		/**
		 * The class responsible for defining user roles and capabilities.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tbs-user-roles.php';
		
		/**
		 * Models of various entity
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tbs-course.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tbs-course-date.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tbs-booker.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tbs-delegate.php';
		
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tbs-report.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tbs-trainers-report.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/admin-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-admin-courses.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-admin-manual-bookings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-admin-online-bookings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-admin-course-date-info.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-admin-customers.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-admin-reports.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-admin-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-admin-wc-coupon.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-course-meta-fields.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-course-category-fields.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-location-meta-fields.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tbs-trainer-meta-fields.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tbs-public.php';

		$this->loader = new TBS_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the TBS_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new TBS_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all custom post types and taxonomy
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function register_post_types(){
		$custom_types = TBS_Custom_Types::instance();
		$this->loader->add_action('init', $custom_types, 'register');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new TBS_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		// Settings page
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu', 9 );
		$this->loader->add_action('admin_head', $plugin_admin, 'remove_booking_syestem_submenu');
		$this->loader->add_filter('menu_order', $plugin_admin, 'menu_order');
		
		// Course Meta Boxes
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'meta_boxes', 1, 2);
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_meta_fields');
		
		$this->loader->add_action('admin_footer', $plugin_admin, 'js_templates');
		
		// Ajax Handler
		$this->loader->add_action('wp_ajax_tbs_get_course_date_edit_form', $plugin_admin, 'ajax_get_course_date_edit_form');
		$this->loader->add_action('wp_ajax_tbs_add_course_date', $plugin_admin, 'ajax_add_course_date');
		$this->loader->add_action('wp_ajax_tbs_update_course_date', $plugin_admin, 'update_course_date');
		$this->loader->add_action('wp_ajax_tbs_delete_course_date', $plugin_admin, 'delete_course_date');
		
		$this->loader->add_action('wp_ajax_tbs_load_booking', $plugin_admin->get_manual_booking_handler(), 'ajax_load_booking');
		$this->loader->add_action('wp_ajax_tbs_save_booking', $plugin_admin->get_manual_booking_handler(), 'ajax_save_booking');
		$this->loader->add_action('wp_ajax_tbs_booking_get_course_dates', $plugin_admin->get_manual_booking_handler(), 'ajax_get_course_dates_models');
		$this->loader->add_action('wp_ajax_tbs_booking_get_items', $plugin_admin->get_manual_booking_handler(), 'ajax_booking_get_items_models');
		$this->loader->add_action('wp_ajax_tbs_generate_online_url', $plugin_admin->get_manual_booking_handler(), 'ajax_generate_online_url');
		$this->loader->add_action('wp_ajax_tbs_save_online_booking_customer_details', $plugin_admin->get_online_booking_handler(), 'ajax_save_online_booking_customer_details');
		$this->loader->add_action('wp_ajax_tbs_save_online_booking_delegates_details', $plugin_admin->get_online_booking_handler(), 'ajax_tbs_save_online_booking_delegates_details');
		
		
		$this->loader->add_action('wp_trash_post', $plugin_admin, 'trash_post');
		$this->loader->add_action('untrashed_post', $plugin_admin, 'untrashed_post');
		
		
		$this->loader->add_filter('set-screen-option', $plugin_admin, 'set_screen_option', 10, 3);
		
		// Term metafields
		$course_taxonomy_name = TBS_Custom_Types::get_course_category_data('type');
		$this->loader->add_action( $course_taxonomy_name . '_add_form_fields', $plugin_admin, 'course_category_add_fields', 10);
		$this->loader->add_action( $course_taxonomy_name . '_edit_form_fields', $plugin_admin, 'course_category_edit_fields', 10, 2);
		$this->loader->add_action( 'created_term', $plugin_admin, 'save_course_category_fields', 10, 3);
		$this->loader->add_action( 'edit_term', $plugin_admin, 'save_course_category_fields', 10, 3);
		
		// Woocomerce Coupon
		$this->loader->add_filter('woocommerce_coupon_data_tabs', $plugin_admin->get_wc_coupon_handler(), 'data_tabs');
		$this->loader->add_action('woocommerce_coupon_data_panels', $plugin_admin->get_wc_coupon_handler(), 'data_panels', 10, 2);
		$this->loader->add_action('woocommerce_coupon_options_save', $plugin_admin->get_wc_coupon_handler(), 'save', 10, 2);
		// User settings
        $this->loader->add_action('personal_options', $plugin_admin, 'display_user_extra_fields');
        $this->loader->add_action('personal_options_update', $plugin_admin, 'save_user_extra_fields');
        $this->loader->add_action('edit_user_profile_update', $plugin_admin, 'save_user_extra_fields');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new TBS_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'template_redirect', $plugin_public, 'template_redirect' );
		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_filter( 'pre_get_posts', $plugin_public, 'category_posts_per_page' );
		$this->loader->add_action('pre_get_posts', $plugin_public, 'exclude_private_courses');
		
		$this->loader->add_action( 'init', $plugin_public, 'init' );
		
		$this->loader->add_action( 'template_include', $plugin_public, 'load_template' );
		$this->loader->add_filter( 'body_class', $plugin_public, 'body_class' );
		
		//$this->loader->add_filter('comments_open', $plugin_public, 'course_comments_enable', 99, 2);
		
		// Woocomerce Hooks
		$this->loader->add_action( 'plugins_loaded', $plugin_public, 'wc_custom_gateways' );
		
		$this->loader->add_filter('wc_order_statuses', $plugin_public, 'wc_order_statuses');
		$this->loader->add_filter('woocommerce_cart_item_name', $plugin_public, 'woocommerc_get_cart_item_name', 10, 3);
		$this->loader->add_filter('woocommerce_cart_item_removed_title', $plugin_public, 'woocommerc_get_cart_item_removed_title', 10, 2);
		$this->loader->add_filter('woocommerce_cart_product_subtotal', $plugin_public, 'cart_product_subtotal_private_course', 10, 4);
		$this->loader->add_filter('woocommerce_add_to_cart_fragments', $plugin_public, 'header_mini_cart_fragment');
		$this->loader->add_filter('woocommerce_coupon_is_valid', $plugin_public, 'validate_coupon_course_ids', 20, 3);
		$this->loader->add_action('woocommerce_before_calculate_totals', $plugin_public, 'woocommerce_before_calculate_totals');
		$this->loader->add_action('woocommerce_after_calculate_totals', $plugin_public, 'woocommerce_after_calculate_totals');
		
		$this->loader->add_filter('woocommerce_checkout_fields', $plugin_public, 'wc_checkout_additional_booking_fields');
		$this->loader->add_action('woocommerce_checkout_after_customer_details', $plugin_public, 'course_additional_fields');
		$this->loader->add_action('woocommerce_checkout_process', $plugin_public, 'checkout_field_validation');
		$this->loader->add_action('woocommerce_checkout_update_order_meta', $plugin_public, 'save_additional_booking_fields', 10, 2);
		$this->loader->add_action('woocommerce_checkout_order_processed', $plugin_public, 'process_checkout_fields', 10,3);
		$this->loader->add_action('woocommerce_thankyou', $plugin_public, 'wc_auto_complete_order');
		
		$this->loader->add_action('init', $plugin_public, 'add_endpoints');
		$this->loader->add_filter('query_vars', $plugin_public, 'add_query_vars');
		
		$this->loader->add_action('woocommerce_order_status_completed', $plugin_public, 'wc_maybe_create_order_delegates', 1, 2);
		$this->loader->add_action('woocommerce_order_status_changed', $plugin_public, 'wc_manage_order_delegates_on_status_change', 10, 4);
		
		$this->loader->add_action('woocommerce_order_details_after_order_table', $plugin_public, 'acredoted_address_details');
		$this->loader->add_action('woocommerce_order_details_after_order_table', $plugin_public, 'order_delegates_details');
		$this->loader->add_action('woocommerce_email_after_order_table', $plugin_public, 'email_order_delegates_details');
		
		$this->loader->add_filter('woocommerce_email_classes', $plugin_public, 'wc_admin_email_recipent', 10, 1);
		$this->loader->add_filter('woocommerce_email_classes', $plugin_public, 'wc_joining_instructions_email', 10, 1);
		$this->loader->add_filter('woocommerce_email_from_address', $plugin_public, 'woocommerce_email_from_address', 20, 2);
		$this->loader->add_filter('woocommerce_email_from_name', $plugin_public, 'wc_add_email_record', 20, 2);
		$this->loader->add_filter('woocommerce_email_recipient_joining_instructions', $plugin_public, 'wc_add_copy_email_address', 10, 2);
		$this->loader->add_filter('woocommerce_email_recipient_customer_completed_order', $plugin_public, 'wc_add_copy_email_address', 10, 2);
		$this->loader->add_filter('woocommerce_email_recipient_customer_new_account', $plugin_public, 'wc_add_copy_email_address', 10, 2);
		//$this->loader->add_filter('woocommerce_email_recipient_customer_new_account', $plugin_public, 'wc_add_customer_new_account_copy_email', 10, 2);
		$this->loader->add_filter('woocommerce_email_recipient_customer_invoice', $plugin_public, 'wc_customer_invoice_email_finance_contact', 10, 2);
		
		$this->loader->add_action('woocommerce_checkout_order_review', $plugin_public, 'email_optin_field', 15);
		
		$this->loader->add_filter('woocommerce_account_menu_items', $plugin_public, 'woocommerce_account_menu_items', 10, 1);
		$this->loader->add_filter('woocommerce_order_item_name', $plugin_public, 'woocommerce_order_item_name', 10, 3);
		
		$this->loader->add_filter('woocommerce_account_my-courses_endpoint', $plugin_public, 'woocommerce_mycourses_content', 10, 3);
		$this->loader->add_filter('woocommerce_get_shop_page_permalink', $plugin_public, 'woocommerce_return_to_shop_redirect');
		
		$this->loader->add_filter('woocommerce_order_item_get_name', $plugin_public, 'woocommerce_order_item_get_name', 10, 2);
		$this->loader->add_filter('woocommerce_order_amount_item_total', $plugin_public, 'order_item_total_private_course', 10, 5);
		
		$this->loader->add_action('wp_head', $plugin_public, 'header_meta_tags', 20);
		$this->loader->add_action('wp_footer', $plugin_public, 'js_templates', 10, 5);
		
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    TBS_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
