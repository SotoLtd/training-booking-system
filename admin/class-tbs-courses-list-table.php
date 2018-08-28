<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Booking List Table
 * @package    Training_Booking_System
 * @subpackage Training_Booking_System/admin
 * @author     TTS <mmhasaneee@gmail.com>
 */
class TBS_Courses_List_Table extends WP_List_Table {
	private $query_data = array();
	/**
	 * Constructor
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		parent::__construct(array(
			'singular'	=> __('Course', TBS_i18n::get_domain_name()),
			'plural'	=> __('Courses', TBS_i18n::get_domain_name()),
			'ajax'		=> false,
		));
	}
	
	/**
	 * Set query all query args
	 */
	public function set_query_data(){
		$this->query_data['per_page'] = $this->get_items_per_page('bookings_per_page', 12);
		$this->query_data['current_page'] = $this->get_pagenum();
		if(isset($_REQUEST['orderby'])){
			$this->query_data['orderby'] = trim($_REQUEST['orderby']);
		}else{
			$this->query_data['orderby'] = 'date';
		}
		if(isset($_REQUEST['order'])){
			$this->query_data['order'] = $_REQUEST['order'];
		}else{
			$this->query_data['order'] = 'DESC';
		}
	}
	/**
	 * Get a single query args
	 * @param string $key
	 * @param mix $default
	 * @return type
	 */
	public function get_query_arg($key, $default = ''){
		return isset($this->query_data[$key]) ? $this->query_data[$key] : $default;
	}
	/**
	 * Return text for no booking found.
	 */
	public function no_items() {
		_e( 'No courses found.', TBS_i18n::get_domain_name() );
	}
	
	public function get_views(){
		$views = array();
		
		return $views;
	}
	/**
	 * Associative array of columns
	 * @return array
	 */
	public function get_columns() {
		return array(
			'title' => __("Title", TBS_i18n::get_domain_name()),
			'trainer' => __("Trainer", TBS_i18n::get_domain_name()),
			//'course_category' => __("Course Categories", TBS_i18n::get_domain_name()),
			'date' => __("Date", TBS_i18n::get_domain_name()),
		);
	}
	/**
	 * 
	 */
	function column_title ($post){
		
		$actions = array(
			'edit' => sprintf('<a href="%s">Edit</a>', TBS_Admin_Courses::url('edit', array('course_id' => $post->ID,) ) ),
		);
		
		echo '<strong><a href="'.TBS_Admin_Courses::url('edit', array('course_id' => $post->ID,) ).'">' . get_the_title($post->ID) . '</a></strong>';
		echo $this->row_actions( $actions );
	}
	/**
	 * Handles the post date column output.
	 *
	 * @since 4.3.0
	 *
	 * @global string $mode List table view mode.
	 *
	 * @param WP_Post $post The current WP_Post object.
	 */
	public function column_date( $post ) {
		global $mode;

		if ( '0000-00-00 00:00:00' === $post->post_date ) {
			$t_time = $h_time = __( 'Unpublished' );
			$time_diff = 0;
		} else {
			$t_time = get_the_time( __( 'Y/m/d g:i:s a' ) );
			$m_time = $post->post_date;
			$time = get_post_time( 'G', true, $post );

			$time_diff = time() - $time;

			if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
				$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
			} else {
				$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
			}
		}

		if ( 'publish' === $post->post_status ) {
			$status = __( 'Published' );
		} elseif ( 'future' === $post->post_status ) {
			if ( $time_diff > 0 ) {
				$status = '<strong class="error-message">' . __( 'Missed schedule' ) . '</strong>';
			} else {
				$status = __( 'Scheduled' );
			}
		} else {
			$status = __( 'Last Modified' );
		}

		/**
		 * Filters the status text of the post.
		 *
		 * @since 4.8.0
		 *
		 * @param string  $status      The status text.
		 * @param WP_Post $post        Post object.
		 * @param string  $column_name The column name.
		 * @param string  $mode        The list display mode ('excerpt' or 'list').
		 */
		$status = apply_filters( 'post_date_column_status', $status, $post, 'date', $mode );

		if ( $status ) {
			echo $status . '<br />';
		}

		if ( 'excerpt' === $mode ) {
			/**
			 * Filters the published time of the post.
			 *
			 * If `$mode` equals 'excerpt', the published time and date are both displayed.
			 * If `$mode` equals 'list' (default), the publish date is displayed, with the
			 * time and date together available as an abbreviation definition.
			 *
			 * @since 2.5.1
			 *
			 * @param string  $t_time      The published time.
			 * @param WP_Post $post        Post object.
			 * @param string  $column_name The column name.
			 * @param string  $mode        The list display mode ('excerpt' or 'list').
			 */
			echo apply_filters( 'post_date_column_time', $t_time, $post, 'date', $mode );
		} else {

			/** This filter is documented in wp-admin/includes/class-wp-posts-list-table.php */
			echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, 'date', $mode ) . '</abbr>';
		}
	}
	/**
	 * Get column output
	 * @param type $item
	 * @param type $column_name
	 * @return type
	 */
	public function column_default( $item, $column_name ) {
		switch($column_name){
			case 'trainer':
				$trainer_id = get_post_meta($item->ID, 'trainer', true);
				return $trainer_id ? get_the_title($trainer_id) : '';
			default: 
				return isset($item[$column_name]) ? $item[$column_name] : '';
				
		}
	}

	/**
	 * Columns to make sortable.
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'title'    => 'title',
			'date'     => array( 'date', true )
		);
	}
	
	public function get_courses(){
		$this->set_query_data();
		$args = array(
			'post_type'		 => TBS_Custom_Types::get_course_data( 'type' ),
			'posts_per_page' => $this->get_query_arg('per_page'),
			'paged' => $this->get_query_arg( 'current_page', 1 ),
			'order' => $this->get_query_arg( 'order'),
			'orderby' => $this->get_query_arg( 'orderby'),
		);
		$course_query = new WP_Query($args);
		if(!$course_query->have_posts()){
			$this->set_pagination_args(array(
				'total_items' => 0,
				'total_pages' => 0,
				'per_page' => $this->get_query_arg('per_page', 12),
			));
			return array();
		}
		$this->set_pagination_args(array(
			'total_items' => $course_query->found_posts,
			'total_pages' => $course_query->max_num_pages,
			'per_page' => $this->get_query_arg('per_page', 12)
		));
		return $course_query->posts;
	}
	
	/**
	 * Prepare items for the table
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		$this->items = $this->get_courses();
	}
	
}