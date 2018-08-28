<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TBS_Admin_WC_Coupon {
	/**
	 * Main admin handler object
	 * @access   private
	 * @var obj
	 */
	private $admin;
	
	/**
	 * Constructor of this class
	 * @param TBS_Admin $admin
	 */
	public function __construct(TBS_Admin $admin) {
		$this->admin = $admin;
	}
	/**
	 * Hook to coupons edit screen tabs
	 * @param array $tabs
	 */
	public function data_tabs($coupon_data_tabs){
		$coupon_data_tabs['course_usage_restriction'] = array(
			'label'  => __( 'Course restriction', TBS_i18n::get_domain_name() ),
			'target' => 'course_usage_restriction_coupon_data',
			'class'  => '',
		);
		return $coupon_data_tabs;
	}
	/**
	 * Hook to coupon panel
	 * @param type $coupon_id
	 * @param type $coupon
	 */
	public function data_panels($coupon_id, $coupon){
		include $this->admin->get_partial('wc-coupon-course-panel');
	}
	/**
	 * Save coupon data
	 * @param type $coupon_id
	 * @param type $coupon
	 */
	public function save($coupon_id, $coupon){
		$courses = isset( $_POST['tbs_coupon_courses'] ) ? (array) $_POST['tbs_coupon_courses'] : array();
		$courses = array_filter( array_map( 'intval', $courses ) );
		update_post_meta($coupon_id, 'tbs_coupon_courses', $courses);
	}
}