<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;
$query = "SELECT COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = 'shop_order' AND post_status = 'wc-completed'";
$total_orders = $wpdb->get_var($query);
?>
<div class="tbs-page-container clearfix">
	<table class="form-table">
		<tr>
			<th scope="row">Reload CRM Accounts</th>
			<td>
				<button class="tbs-crm-reload-accounts button" data-total="<?php echo absint($total_orders); ?>" data-nonce="<?php echo wp_create_nonce('tbs_tools_reload_accounts'); ?>">Reload</button>
				<div class="tbs-crm-reload-account-progress">
					<div class="tbs-crm-reload-account-progress-bar"></div>
					<div class="tbs-crm-reload-account-progress-count"></div>
				</div>

			</td>
		</tr>
	</table>
</div>