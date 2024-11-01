<?php

/**
 * Fired during plugin uninstall
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/includes
 */

/**
 * Fired during plugin uninstall.
 *
 * This class defines all code necessary to run during the plugin's uninstall.
 *
 * @since      1.0.0
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/includes
 * @author     Jai Awasthi <jay.awasthi@gmail.com>
 */
class Wireless_Butler_Uninstall {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {
		/**
		 * Delete plugin options from DB
		 */
		delete_option( 'wireless_butler_form_1_step_1_greeting');
		delete_option( 'wireless_butler_form_1_step_1_heading');
		delete_option( 'wireless_butler_form_1_step_1_label');
		delete_option( 'wireless_butler_form_1_step_1_account_holder');
		delete_option( 'wireless_butler_form_1_step_1_smartphone_heading');
		delete_option( 'wireless_butler_form_1_step_1_button_text');

		//Delete form 1 step 2 options
		delete_option( 'wireless_butler_form_1_step_2_heading');
		delete_option( 'wireless_butler_form_1_step_2_chepest_plan_text');
		delete_option( 'wireless_butler_form_1_step_2_total_bill');
		delete_option( 'wireless_butler_form_1_step_2_latest_month_bill');
		delete_option( 'wireless_butler_form_1_step_2_past_due');
		delete_option( 'wireless_butler_form_1_step_2_total_plan_charges');
		delete_option( 'wireless_butler_form_1_step_2_gb_of_data_used');
		delete_option( 'wireless_butler_form_1_step_2_gb_in_your_plan');
		delete_option( 'wireless_butler_form_1_step_2_reach_out_text');
		delete_option( 'wireless_butler_form_1_step_2_button_text');

		//Delete Email Notification Options
		delete_option( 'wireless_butler_email_to_user_subject');
		delete_option( 'wireless_butler_email_to_user_content');
		delete_option( 'wireless_butler_notification_email');
		delete_option( 'wireless_butler_form_notification_template');

		delete_option( 'wireless_butler_admin_name');
		delete_option( 'wireless_butler_admin_mail');

		$data = array(
			'action' 			=> 'plugin_activation_sync',
			'sourceDomain' 		=> get_site_url(),
			'wirelessButler' 	=> 'uninstall',
		);
		$result = wp_remote_post('https://wirelessbutlerserver.com/wp/wp-admin/admin-post.php', 
			array(
				'method' 		=> 'POST',
				'timeout'     	=> 45,
				'httpversion' 	=> '1.0',
				'sslverify' 	=> false,
				'body' 			=> $data
			)
		);
	}

	/**
	 * Remove plugin table on uninstall
	 */
	public static function dropPluginTable()
	{
		global $table_prefix, $wpdb;

		$tblname = 'wireless_butler_carrier';
		$wp_table = $table_prefix . $tblname;
		$wpdb->query( "DROP TABLE IF EXISTS ".$wp_table );

		$tblname = 'wireless_butler_customer';
		$wp_table = $table_prefix . $tblname;
		$wpdb->query( "DROP TABLE IF EXISTS ".$wp_table );
		
		$tblname = 'wireless_butler_regex';
		$wp_table = $table_prefix . $tblname;
		$wpdb->query( "DROP TABLE IF EXISTS ".$wp_table );

		$tblname = 'wireless_butler_plan_database';
		$wp_table = $table_prefix . $tblname;
		$wpdb->query( "DROP TABLE IF EXISTS ".$wp_table );

		$tblname = 'wireless_butler_recommendation';
		$wp_table = $table_prefix . $tblname;
		$wpdb->query( "DROP TABLE IF EXISTS ".$wp_table );
		
		delete_option("wireless_butler_db_version");
	}
}
