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
class TBS_Course_Date_Info_List_Table extends WP_List_Table {
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
		$this->query_data['per_page'] = $this->get_items_per_page('course_items_per_page', 12);
		$this->query_data['current_page'] = $this->get_pagenum();
		if(isset($_REQUEST['orderby'])){
			$this->query_data['orderby'] = trim($_REQUEST['orderby']);
		}else{
			$this->query_data['orderby'] = 'start_date';
		}
		if(isset($_REQUEST['order'])){
			$this->query_data['order'] = $_REQUEST['order'];
		}else{
			$this->query_data['order'] = 'ASC';
		}
		if(isset($_REQUEST['course_id'])){
			$this->query_data['course_id'] = $_REQUEST['course_id'];
		}else{
			$this->query_data['course_id'] = false;
		}
		if(isset($_REQUEST['location_id'])){
			$this->query_data['location_id'] = $_REQUEST['location_id'];
		}else{
			$this->query_data['location_id'] = false;
		}
		if(isset($_REQUEST['trainer_id'])){
			$this->query_data['trainer_id'] = $_REQUEST['trainer_id'];
		}else{
			$this->query_data['trainer_id'] = false;
		}
		if(isset($_REQUEST['cdm'])){
			$this->query_data['cdm'] = $_REQUEST['cdm'];
		}else{
			$this->query_data['cdm'] = '';
		}
		if(isset($_REQUEST['course_dates'])){
			$this->query_data['course_dates'] = $_REQUEST['course_dates'];
		}else{
			$this->query_data['course_dates'] = '';
		}
	}
	/**
	 * Get the query args for filtered actions
	 * @return array
	 */
	public function get_filter_actions_query(){
		$course_id = $this->get_query_arg('course_id', false);
		$location_id = $this->get_query_arg('location_id', false);
		$trainer_id = $this->get_query_arg('trainer_id', false);
		$course_month = $this->get_query_arg('cdm', false);
		$course_dates = $this->get_query_arg('course_dates', false);
		
		$query_args = array();
		if($course_id){
			$query_args['course_id'] = $course_id;
		}else{
			$query_args['course_id'] = false;
		}
		if($location_id){
			$query_args['location_id'] = $location_id;
		}
		if($trainer_id){
			$query_args['trainer_id'] = $trainer_id;
		}
		if($course_month){
			$query_args['cdm'] = $course_month;
		}
		if($course_dates){
			$query_args['course_dates'] = $course_dates;
		}
		return $query_args;
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
			'title' => __("Date", TBS_i18n::get_domain_name()),
			'course_title' => __("Course", TBS_i18n::get_domain_name()),
			'location' => __("Location", TBS_i18n::get_domain_name()),
			'trainer' => __("Trainer", TBS_i18n::get_domain_name()),
			'delegates_count' => __("Delegates", TBS_i18n::get_domain_name()),
			'reserves' => __("Reserves", TBS_i18n::get_domain_name()),
			'remaining_places' => __("Remaining Places", TBS_i18n::get_domain_name()),
			'capacity' => __("Capacity", TBS_i18n::get_domain_name()),
		);
	}
	/**
	 * 
	 */
	function column_title ($course_date){
		
		$actions = array(
			'view' => sprintf('<a href="%s">View details</a>', TBS_Admin_Course_Date_Info::url('view', array('course_date_id' => $course_date->get_id(),) ) ),
		);
		
		echo '<strong><a href="'.TBS_Admin_Course_Date_Info::url('view', array('course_date_id' => $course_date->get_id(),) ).'">' . $course_date->get_date_formatted(true). '</a></strong>';
		echo $this->row_actions( $actions );
	}
	function column_reserves ($course_date) {
		$reserves_count = $course_date->get_reserves_count();
		echo $reserves_count ? $reserves_count : '-';
	}
	/**
	 * Get column output
	 * @param type $item
	 * @param type $column_name
	 * @return type
	 */
	public function column_default( $course_date, $column_name ) {
		switch($column_name){
			case 'course_title':
				return $course_date->get_course_title();
			case 'location':
				return $course_date->get_location_short_name();
			case 'trainer':
				return $course_date->get_trainers_name();
			case 'delegates_count':
				return $course_date->get_delegates_count();
			case 'remaining_places':
				return $course_date->get_places();
			case 'capacity':
				return $course_date->get_max_delegates();
			default: 
				return is_callable(array($course_date, 'get_' . $column_name)) ? $course_date->{"get_$column_name"}() : '';
				
		}
	}

	/**
	 * Columns to make sortable.
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'title'    => 'start_date',
			//'date'     => array( 'date', true )
		);
	}
	
	/**
	 * Print column headers, accounting for hidden and sortable columns.
	 *
	 * @since 3.1.0
	 *
	 * @staticvar int $cb_counter
	 *
	 * @param bool $with_id Whether to set the id attribute or not
	 */
	public function print_column_headers( $with_id = true ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( 'paged', $current_url );
		$submitted_filters = $this->get_filter_actions_query();
		$current_url = add_query_arg($submitted_filters, $current_url);

		if ( isset( $_GET['orderby'] ) ) {
			$current_orderby = $_GET['orderby'];
		} else {
			$current_orderby = '';
		}

		if ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) {
			$current_order = 'desc';
		} else {
			$current_order = 'asc';
		}

		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;
			$columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
				. '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
			$cb_counter++;
		}

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );

			if ( in_array( $column_key, $hidden ) ) {
				$class[] = 'hidden';
			}

			if ( 'cb' === $column_key )
				$class[] = 'check-column';
			elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
				$class[] = 'num';

			if ( $column_key === $primary ) {
				$class[] = 'column-primary';
			}

			if ( isset( $sortable[$column_key] ) ) {
				list( $orderby, $desc_first ) = $sortable[$column_key];

				if ( $current_orderby === $orderby ) {
					$order = 'asc' === $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
			}

			$tag = ( 'cb' === $column_key ) ? 'td' : 'th';
			$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
			$id = $with_id ? "id='$column_key'" : '';

			if ( !empty( $class ) )
				$class = "class='" . join( ' ', $class ) . "'";

			echo "<$tag $scope $id $class>$column_display_name</$tag>";
		}
	}
	
	public function get_course_dates(){
		$this->set_query_data();
		
		$meta_query = array();
		$meta_query['relation'] = 'AND';
		$meta_query[] = array(
			'key' => '_is_tbs_course',
			'value' => 'yes',
			'compare' => '=',
		);
		
		$meta_query['start_date'] = array(
			'key' => '_tbs_start_date',
			'compare' => 'EXISTS',
		);
		
		$course_id = $this->get_query_arg('course_id');
		if($course_id){
			$meta_query['course'] = array(
				'key' => '_tbs_course',
				'value' => $course_id,
				'compare' => '=',
			);
		}
		if($this->get_query_arg('location_id')){
			$meta_query['location'] = array(
				'key' => '_tbs_location',
				'value' => $this->get_query_arg('location_id'),
				'compare' => '=',
			);
		}
		if($this->get_query_arg('trainer_id')){
			$meta_query['trainer'] = array(
				'key' => '_tbs_trainer',
				'value' => $this->get_query_arg('trainer_id'),
				'compare' => '=',
			);
		}
		
		$date_filter = $this->get_query_arg('course_dates');
		if($date_filter){
			$date_filter = explode('-', $date_filter);
			$date_filter = array_map('trim', $date_filter);
			if( count($date_filter) === 2){
				$now = time();
				$meta_query['form_date'] = array(
					'key' => '_tbs_start_date',
					'value' => strtotime($date_filter[0]),
					'compare' => '>=',
				);
				$meta_query['to_date'] = array(
					'key' => '_tbs_start_date',
					'value' => strtotime($date_filter[1]) + 86400,
					'compare' => '<',
				);
			}
		}
		
		$orderby_clauses = array(
			$this->get_query_arg( 'orderby') => $this->get_query_arg( 'order')
		);
		
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => $this->get_query_arg('per_page', 12),
			'paged' => $this->get_query_arg( 'current_page', 1 ),
			//'order' => $this->get_query_arg( 'order'),
			//'orderby' => $this->get_query_arg( 'orderby'),
			'meta_query' => $meta_query,
			'orderby' => $orderby_clauses,
		);
		$m = $this->get_query_arg('cdm', '');
		if($m){
			add_filter('posts_join', array($this, 'months_join_clause'), 10, 2);
			add_filter('posts_where', array($this, 'months_where_clause'), 10, 2);
			
		}
		
		$date_query = new WP_Query($args);
		if($m){
			
			remove_filter('posts_join', array($this, 'months_join_clause'), 10, 2);
			remove_filter('posts_where', array($this, 'months_where_clause'), 10, 2);
			
		}
		
		if(!$date_query->have_posts()){
			$this->set_pagination_args(array(
				'total_items' => 0,
				'total_pages' => 0,
				'per_page' => $this->get_query_arg('per_page', 12),
			));
			return array();
		}
		$this->set_pagination_args(array(
			'total_items' => $date_query->found_posts,
			'total_pages' => $date_query->max_num_pages,
			'per_page' => $this->get_query_arg('per_page', 12)
		));
		$course_dates = array();
		while($date_query->have_posts()){
			$date_query->the_post();
			$course_dates[] = new TBS_Course_Date( get_the_ID());
		}
		wp_reset_postdata();
		return $course_dates;
	}
	
	/**
	 * Prepare items for the table
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		$this->items = $this->get_course_dates();
	}
	
	public function months_join_clause($join, $query){
		global $wpdb;
		$join .= " INNER JOIN {$wpdb->postmeta} AS cdm ON cdm.post_id={$wpdb->posts}.ID";
		return $join;
	}
	
	public function months_where_clause($where, $query){
		global $wpdb;
		$m = $this->get_query_arg('cdm', '');
		if($m){
			$where .= " AND YEAR(FROM_UNIXTIME(cdm.meta_value))=" . substr($m, 0, 4);
			$where .= " AND MONTH(FROM_UNIXTIME(cdm.meta_value))=" . substr($m, 4, 2);
		}
		return $where;
	}
	/**
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' === $which && !is_singular() ) {
			ob_start();
			$this->courses_dropdown();
			$this->location_dropdown();
			$this->trainer_dropdown();
			//$this->months_dropdown();
			$this->date_selector();
			$output = ob_get_clean();

			if ( ! empty( $output ) ) {
				echo '<div class="alignleft actions">' . $output . '</div>';
				submit_button( __( 'Filter' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
			}
		}
	}
	/**
	 * Get courses dropdown filter
	 */
	public function courses_dropdown(){
		$course_id = (int)$this->get_query_arg('course_id');
		$courses = get_posts(array(
			'posts_per_page'=> -1,
			'post_type'     => TBS_Custom_Types::get_course_data( 'type' ),
			'orderby'       => 'title',
			'order'         => 'ASC',
		));
		$html = '';
		$html .= '<label for="filter-by-course" class="screen-reader-text">' . __( 'Filter by course', TBS_i18n::get_domain_name() ) . '</label>';
		$html .= '<select name="course_id" id="filter-by-course">';
			$html .= '<option ' . selected( $course_id, 0, false ) .' value="0">' . __( 'All courses', TBS_i18n::get_domain_name() ) . '</option>';
			
			foreach($courses as $course){
				$html .= '<option value="'. $course->ID .'" '. selected( $course_id, $course->ID, false ) .'>'. $course->post_title .'</option>';
			}
			$html .= '</select>';
		echo $html;
		
	}
	/**
	 * Get location dropdown filter
	 */
	public function location_dropdown(){
		$location_id = (int)$this->get_query_arg('location_id');
		$locations = get_posts(array(
			'posts_per_page'=> -1,
			'post_type'     => TBS_Custom_Types::get_location_data( 'type' ),
			'orderby'       => 'title',
			'order'         => 'ASC',
		));
		$html = '';
		$html .= '<label for="filter-by-location" class="screen-reader-text">' . __( 'Filter by location', TBS_i18n::get_domain_name() ) . '</label>';
		$html .= '<select name="location_id" id="filter-by-location">';
			$html .= '<option ' . selected( $location_id, 0, false ) .' value="0">' . __( 'All locations', TBS_i18n::get_domain_name() ) . '</option>';
			
			foreach($locations as $location){
				$html .= '<option value="'. $location->ID .'" '. selected( $location_id, $location->ID, false ) .'>'. $location->post_title .'</option>';
			}
			$html .= '</select>';
		echo $html;
	}
	/**
	 * Get location dropdown filter
	 */
	public function trainer_dropdown(){
		$trainer_id = (int)$this->get_query_arg('trainer_id');
		$trainers = get_posts(array(
			'posts_per_page'=> -1,
			'post_type'     => TBS_Custom_Types::get_trainer_data( 'type' ),
			'orderby'       => 'title',
			'order'         => 'ASC',
		));
		$html = '';
		$html .= '<label for="filter-by-trainer" class="screen-reader-text">' . __( 'Filter by trainer.', TBS_i18n::get_domain_name() ) . '</label>';
		$html .= '<select name="trainer_id" id="filter-by-location">';
			$html .= '<option ' . selected( $trainer_id, 0, false ) .' value="0">' . __( 'All trainers', TBS_i18n::get_domain_name() ) . '</option>';
			
			foreach($trainers as $trainer){
				$html .= '<option value="'. $trainer->ID .'" '. selected( $trainer_id, $trainer->ID, false ) .'>'. $trainer->post_title .'</option>';
			}
			$html .= '</select>';
		echo $html;
	}
	public function date_selector(){
		$course_dates = $this->get_query_arg('course_dates');
		$html = '';
		$html .= '<label for="filter-by-date" class="screen-reader-text">' . __('Filter by dates:', TBS_i18n::get_domain_name()) . ' </label>';
		$html .= '<input id="filter-by-date" name="course_dates" type="text" placeholder="All dates" data-date-format="yyyy/mm/dd" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here" data-toggle-selected="false" value="'. esc_attr($course_dates) .'"/>';
	
		echo $html;
	}
	

	/**
	 * Display a monthly dropdown for filtering items
	 *
	 * @since 3.1.0
	 *
	 * @global wpdb      $wpdb
	 * @global WP_Locale $wp_locale
	 *
	 * @param string $post_type
	 */
	protected function months_dropdown($post_type='product') {
		global $wpdb, $wp_locale;

		$extra_checks = "AND p.post_status != 'auto-draft'";
		if ( ! isset( $_REQUEST['post_status'] ) || 'trash' !== $_REQUEST['post_status'] ) {
			$extra_checks .= " AND post_status != 'trash'";
		} elseif ( isset( $_REQUEST['post_status'] ) ) {
			$extra_checks = $wpdb->prepare( ' AND p.post_status = %s', $_REQUEST['post_status'] );
		}
		
		$sql = "SELECT DISTINCT YEAR(FROM_UNIXTIME(mt.meta_value)) AS year, MONTH(FROM_UNIXTIME(mt.meta_value)) AS month 
			FROM {$wpdb->posts} as p
			INNER JOIN {$wpdb->postmeta} AS mt ON mt.post_id=p.ID
			WHERE p.post_type = 'product'
			AND mt.meta_key='_tbs_start_date'
			{$extra_checks}
			ORDER BY mt.meta_value DESC";
			
		$months = $wpdb->get_results($sql);

		$month_count = count( $months );

		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;

		$m = isset( $_REQUEST['cdm'] ) ? (int) $_REQUEST['cdm'] : 0;
		?>
				<label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
				<select name="cdm" id="filter-by-date">
					<option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All months' ); ?></option>
		<?php
				foreach ( $months as $arc_row ) {
					if ( 0 == $arc_row->year )
						continue;

					$month = zeroise( $arc_row->month, 2 );
					$year = $arc_row->year;

					printf( "<option %s value='%s'>%s</option>\n",
						selected( $m, $year . $month, false ),
						esc_attr( $arc_row->year . $month ),
						/* translators: 1: month name, 2: 4-digit year */
						sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
					);
				}
		?>
				</select>
		<?php
	}
}