<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/includes
 * @author     Jai Awasthi <jay.awasthi@gmail.com>
 */
class Wireless_Butler_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		/**
		 * Add plugin option in DB
		 */
		add_option( 'wireless_butler_form_1_step_1_greeting', "Hello, I'm the");
		add_option( 'wireless_butler_form_1_step_1_heading', "Wireless Butler®");
		add_option( 'wireless_butler_form_1_step_1_label', "Please fill out the below and upload your bill");
		add_option( 'wireless_butler_form_1_step_1_account_holder', "Select the options that match your account holder.");
		add_option( 'wireless_butler_form_1_step_1_smartphone_heading', "Tell us how many non-smartphones you have on your bill.");
		add_option( 'wireless_butler_form_1_step_1_button_text', "Wireless Butler® Analyze!");

		//Form 1 Step 2
		add_option( 'wireless_butler_form_1_step_2_heading', "Looks like we found a monthly savings of $[MONTHLY_SAVING]!");
		add_option( 'wireless_butler_form_1_step_2_chepest_plan_text', "You're already on the cheapest plan for your usage. Check in with us again for any new offers from your carrier.");
		add_option( 'wireless_butler_form_1_step_2_total_bill', "Total Bill");
		add_option( 'wireless_butler_form_1_step_2_latest_month_bill', "Latest Month Bill");
		add_option( 'wireless_butler_form_1_step_2_past_due', "Past Due");
		add_option( 'wireless_butler_form_1_step_2_total_plan_charges', "Total Plan Charges");
		add_option( 'wireless_butler_form_1_step_2_gb_of_data_used', "GB of Data Used");
		add_option( 'wireless_butler_form_1_step_2_gb_in_your_plan', "GB in your plan");
		add_option( 'wireless_butler_form_1_step_2_reach_out_text', "Our real life Wireless Butlers® will reach out to you shortly with a detailed savings plan");
		add_option( 'wireless_butler_form_1_step_2_device_balance', "Device Balance");
		add_option( 'wireless_butler_form_1_step_2_device_owned', "Device Owned");
		add_option( 'wireless_butler_form_1_step_2_button_text', "Get help from Wireless Butler® now");

		//Email Notification Options
		add_option( 'wireless_butler_email_to_user_subject', "Thanks from Wireless Butler®");
		add_option( 'wireless_butler_email_to_user_content', "Hi [FIRST_NAME],

Thanks for trusting Wireless Butler® with your account! We will review your account online, and if we will reach out to you to confirm before making any changes.

Regards");

		add_option( 'wireless_butler_notification_email', "");
		add_option( 'wireless_butler_form_notification_template', "Hi,

Below are the details:
First Name: [FIRST_NAME]
Last Name: [LAST_NAME]
Email: [EMAIL]
Phone: [PHONE]
Carrier: [CARRIER]
Wireless Bill: [WIRELESS_BILL]
Total Bill: [TOTAL_BILL]
Latest Month Bill: [LATEST_MONTH_BILL]
Past Due: [PAST_DUE]
Total Plan Charge: [TOTAL_PLAN_CHARGES]
Used Data: [USED_DATA]
Total Plan Data: [TOTAL_PLAN_DATA]
Total Savings: [SAVINGS_AMOUNT]
Device Balance: [DEVICE_BALANCE]
Device Owned: [DEVICE_OWNED]

Recommendation URL: [RECOMMENDATION_URL]

Regards");

		//The name that will be used with mail to send info to Customer
		$admin_name = wp_get_current_user();
		$admin_name = $admin_name->display_name;
		add_option( 'wireless_butler_admin_name', $admin_name);

		//The mail that will be used to send info to Customer
		$admin_email = get_option('admin_email');
		add_option( 'wireless_butler_admin_mail', $admin_email);

		$data = array(
			'action' 			=> 'plugin_activation_sync',
			'sourceDomain' 		=> get_site_url(),
			'wirelessButler' 	=> 'activate',
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
	 * Create plugin default tables
	 */
	public static function createPluginTable()
	{
		self::createCustomerDatabase();
		self::createCarrierDatabase();
		self::createRegexDatabase();
		self::createPlanDatabase();
		self::createRecommendationDatabase();
	}

	public static function createCarrierDatabase()
	{
		global $table_prefix, $wpdb;

		$tblname = 'wireless_butler_carrier';
		$wp_table = $table_prefix . $tblname;

		#Check to see if the table exists already, if not, then create it
		if($wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table) 
		{
			$sql = "CREATE TABLE `". $wp_table . "` ( ";
			$sql .= "  `id` int(11) NOT NULL auto_increment, ";
			$sql .= "  `carrier_id` int(11) NOT NULL, ";
			$sql .= "  `name` varchar(128) NOT NULL, ";
			$sql .= "  PRIMARY KEY (`id`)) "; 
			$sql .= "  ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);

			//Insert default carrier
			self::insertDefaultCarrier();
		}
	}

	public static function createCustomerDatabase()
	{
		global $table_prefix, $wpdb;

		$tblname = 'wireless_butler_customer';
		$wp_table = $table_prefix . $tblname;

		#Check to see if the table exists already, if not, then create it
		if($wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table) 
		{
			$sql = "CREATE TABLE `". $wp_table . "` ( ";
			$sql .= "  `id` int(11) NOT NULL auto_increment, ";
			$sql .= "  `billNumber` int(11) DEFAULT NULL, ";
			$sql .= "  `wirelessBillURL` varchar(255) DEFAULT NULL, ";
			$sql .= "  `carrier` int(11) DEFAULT NULL, ";
			$sql .= "  `billDate` date DEFAULT NULL, ";
			$sql .= "  `accountNumber` varchar(255) DEFAULT NULL, ";
			$sql .= "  `totalBill` double(11,6) DEFAULT NULL, ";
			$sql .= "  `extractedTotalBills` varchar(255) DEFAULT NULL, ";
			$sql .= "  `latestMonthBill` double(11,6) DEFAULT NULL, ";
			$sql .= "  `extractedLatestMonthBills` varchar(255) DEFAULT NULL, ";
			$sql .= "  `pastDue` double(11,6) DEFAULT NULL, ";
			$sql .= "  `extractedPastDues` varchar(255) DEFAULT NULL, ";
			$sql .= "  `totalGbUsage` int(11) DEFAULT NULL, ";
			$sql .= "  `extractedTotalGbUsage` varchar(255) DEFAULT NULL, ";
			$sql .= "  `gBAllowance` int(11) DEFAULT NULL, ";
			$sql .= "  `extractedGbAllowance` varchar(255) DEFAULT NULL, ";
			$sql .= "  `lineCount` int(11) DEFAULT NULL, ";
			$sql .= "  `firstName` varchar(255) DEFAULT NULL, ";
			$sql .= "  `lastName` varchar(255) DEFAULT NULL, ";
			$sql .= "  `email` varchar(255) DEFAULT NULL, ";
			$sql .= "  `phoneNumber` varchar(255) DEFAULT NULL, ";
			$sql .= "  `planCost` double(11,6) DEFAULT NULL, ";
			$sql .= "  `lineAccessCost` double(11,6) DEFAULT NULL, ";
			$sql .= "  `averageCost` double(11,6) DEFAULT NULL, ";
			$sql .= "  `totalPlanCost` double(11,6) DEFAULT NULL, ";
			$sql .= "  `extractedTotalPlanCosts` varchar(255) DEFAULT NULL, ";
			$sql .= "  `SmartphoneCount` int(11) DEFAULT NULL, ";
			$sql .= "  `autoPayWithCarrier` enum('0','1') DEFAULT '0', ";
			$sql .= "  `inMilitary` enum('0','1') DEFAULT '0', ";
			$sql .= "  `over55YearOfAge` enum('0','1') DEFAULT '0', ";
			$sql .= "  `manual` enum('0','1') DEFAULT '0', ";
			$sql .= "  `basicPhoneCount` int(11) DEFAULT NULL, ";
			$sql .= "  `tabletCount` int(11) DEFAULT NULL, ";
			$sql .= "  `mhsCount` int(11) DEFAULT NULL, ";
			$sql .= "  `connectedDeviceCount` int(11) DEFAULT NULL, ";
			$sql .= "  `RecommendedPlanName` varchar(255) DEFAULT NULL, ";
			$sql .= "  `RecommendedPlanCost` double(11,6) DEFAULT NULL, ";
			$sql .= "  `RecommendedGbAllowance` int(11) DEFAULT NULL, ";
			$sql .= "  `RecommendedSavings` double(11,6) DEFAULT NULL, ";
			$sql .= "  `IsUpdatedFromUI` enum('0','1') DEFAULT '0', ";
			$sql .= "  `CreatedAt` datetime NOT NULL, ";
			$sql .= "  PRIMARY KEY (`id`)) "; 
			$sql .= "  ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}

		//Add column for  deviceBalance & deviceOwned
		$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
		WHERE table_name = '".$wp_table."' AND column_name = 'deviceBalance'"  );

		if(empty($row)){
			$wpdb->query("ALTER TABLE ".$wp_table." ADD deviceBalance double(11,6) DEFAULT NULL");
			$wpdb->query("ALTER TABLE ".$wp_table." ADD deviceOwned varchar(255) DEFAULT NULL");
		}

		// add column for deviceBalance & deviceOwned
		$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
		WHERE table_name = '".$wp_table."' AND column_name = 'extractedDeviceBalance'"  );

		if(empty($row)){
			$wpdb->query("ALTER TABLE ".$wp_table." ADD extractedDeviceBalance double(11,6) DEFAULT NULL");
			$wpdb->query("ALTER TABLE ".$wp_table." ADD extractedDeviceOwned varchar(255) DEFAULT NULL");
		}
	}

	public static function createRegexDatabase()
	{
		global $table_prefix, $wpdb;

		$tblname = 'wireless_butler_regex';
		$wp_table = $table_prefix . $tblname;

		#Check to see if the table exists already, if not, then create it
		if($wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table) 
		{
			$sql = "CREATE TABLE `". $wp_table . "` ( ";
			$sql .= "  `id` int(11) NOT NULL auto_increment, ";
			$sql .= "  `key` varchar(128) NOT NULL, ";
			$sql .= "  `carrier_id` int(11) NOT NULL, ";
			$sql .= "  `regex` varchar(128) NOT NULL, ";
			$sql .= "  `regex_index` int(11) NOT NULL DEFAULT 1, ";
			$sql .= "  `description` text DEFAULT NULL, ";
			$sql .= "  `type` enum('user','system') NOT NULL DEFAULT 'user', ";
			$sql .= "  PRIMARY KEY (`id`)) "; 
			$sql .= "  ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);

			//Insert default regex
			self::insertDefaultRegex();
		}else{
			//Delete all old system regex
			$wpdb->query( "DELETE FROM ".$wp_table." WHERE type='system'" );

			//Insert default regex
			self::insertDefaultRegex();
		}
	}

	public static function createPlanDatabase()
	{
		global $table_prefix, $wpdb;
		
		$tblname = 'wireless_butler_plan_database';
		$wp_table = $table_prefix . $tblname;

		#Check to see if the table exists already, if not, then create it
		if($wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table) 
		{
			$sql = "CREATE TABLE `". $wp_table . "` ( ";
			$sql .= "  `id` int(11) NOT NULL auto_increment, ";
			$sql .= "  `PlanId` int(11) NOT NULL, ";
			$sql .= "  `CarrierId` int(11) NOT NULL, ";
			$sql .= "  `PlanName` varchar(255) NOT NULL, ";
			$sql .= "  `GBAllowance` int(11) NOT NULL, ";
			$sql .= "  `MhsGbAllowance` int(11) NOT NULL, ";
			$sql .= "  `HostPlanCharge` double(11,6) NOT NULL, ";
			$sql .= "  `Line1AccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line2AccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line3AccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line4AccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line5AccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `TabletOrMHSLineAccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `WearableLineAccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `IsHostPlanDIscountable` enum('0','1') DEFAULT '0', ";
			$sql .= "  `LineAccessAutoPayCreditMaxLines` int(11) DEFAULT NULL, ";
			$sql .= "  `SpecialWorkerLineAccessCredit` int(11) DEFAULT NULL, ";
			$sql .= "  `IsTaxFree` enum('0','1') DEFAULT '0', ";
			$sql .= "  `Line1AccessAutoPayCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line2AccessAutoPayCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line3AccessAutoPayCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line4AccessAutoPayCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line1SeniorCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line2SeniorCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line3SeniorCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line4SeniorCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line1SpecialWorkerCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line2SpecialWorkerCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line3SpecialWorkerCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line4SpecialWorkerCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `HasOverage` enum('0','1') DEFAULT '0', ";
			$sql .= "  `GbOverageUnit` int(11) DEFAULT NULL, ";
			$sql .= "  `OverageAmount` double(11,6) DEFAULT NULL, ";
			$sql .= "  `CanChooseOverage` enum('0','1') DEFAULT '0', ";
			$sql .= "  `IsStacked` enum('0','1') DEFAULT '0', ";
			$sql .= "  `IsShared` enum('0','1') DEFAULT '0', ";
			$sql .= "  `Has5GAccess` enum('0','1') DEFAULT '0', ";
			$sql .= "  `5gAccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `MusicServicesValue` double(11,6) DEFAULT NULL, ";
			$sql .= "  `MovieTVServicesValue` double(11,6) DEFAULT NULL, ";
			$sql .= "  `ModifiedDate` date DEFAULT NULL, ";
			$sql .= "  `VerificationDate` date DEFAULT NULL, ";
			$sql .= "  `ExpirationDate` date DEFAULT NULL, ";
			$sql .= "  `MaxLineCount` int(11) DEFAULT NULL, ";
			$sql .= "  `SubsidizedFee` double(11,6) DEFAULT NULL, ";
			$sql .= "  `DeviceType` varchar(255) DEFAULT NULL, ";
			$sql .= "  `MinimumLineCount` int(11) DEFAULT NULL, ";
			$sql .= "  PRIMARY KEY (`id`)) "; 
			$sql .= "  ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}
	} 

	public static function createRecommendationDatabase()
	{
		global $table_prefix, $wpdb;

		$tblname = 'wireless_butler_recommendation';
		$wp_table = $table_prefix . $tblname;

		#Check to see if the table exists already, if not, then create it
		if($wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table) 
		{
			$sql = "CREATE TABLE `". $wp_table . "` ( ";
			$sql .= "  `id` int(11) NOT NULL auto_increment, ";
			$sql .= "  `customer_id` int(11) DEFAULT NULL, ";
			$sql .= "  `AccountNumber` varchar(255) DEFAULT NULL, ";
			$sql .= "  `UploadedPlanCosts` double(11,6) DEFAULT NULL, ";
			$sql .= "  `UploadedLineCount` int(11) DEFAULT NULL, ";
			$sql .= "  `isCarrier` enum('0','1') DEFAULT '0', ";
			$sql .= "  `isPlanAllowed` enum('0','1') DEFAULT '0', ";
			$sql .= "  `CheapeastPlanCosts` double(11,6) DEFAULT NULL, ";
			$sql .= "  `CostForThisPlan` double(11,6) DEFAULT NULL, ";
			$sql .= "  `isCheapestPlanAndSameCarrier` enum('0','1') DEFAULT '0', ";
			$sql .= "  `ThisPlanWithAutoPayCredits` int(11) DEFAULT NULL, ";
			$sql .= "  `ThisPlanWithSeniorCredits` int(11) DEFAULT NULL, ";
			$sql .= "  `ThisPlanWithAutoPayANDSeniorCredits` int(11) DEFAULT NULL, ";
			$sql .= "  `ThisPlanWithSpecialWorkerCredits` int(11) DEFAULT NULL, ";
			$sql .= "  `ThisPlanWithAutoPayANDSpecialWorkerCredits` int(11) DEFAULT NULL, ";
			$sql .= "  `SavingsForThisPlan` varchar(255) DEFAULT NULL, ";
			$sql .= "  `SavingsForThisPlanWithAutoPay` varchar(255) DEFAULT NULL, ";
			$sql .= "  `SavingsForThisPlanWithSpecialWorker` varchar(255) DEFAULT NULL, ";
			$sql .= "  `SavingsForThisPlanWithAutoPayAndSpecialWorker` varchar(255) DEFAULT NULL, ";
			$sql .= "  `PlanId` int(11) NOT NULL, ";
			$sql .= "  `CarrierId` int(11) NOT NULL, ";
			$sql .= "  `PlanName` varchar(255) NOT NULL, ";
			$sql .= "  `GBAllowance` int(11) NOT NULL, ";
			$sql .= "  `MhsGbAllowance` int(11) NOT NULL, ";
			$sql .= "  `HostPlanCharge` double(11,6) NOT NULL, ";
			$sql .= "  `Line1AccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line2AccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line3AccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line4AccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line5AccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `TabletOrMHSLineAccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `WearableLineAccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `IsHostPlanDIscountable` enum('0','1') DEFAULT '0', ";
			$sql .= "  `LineAccessAutoPayCreditMaxLines` int(11) DEFAULT NULL, ";
			$sql .= "  `SpecialWorkerLineAccessCredit` int(11) DEFAULT NULL, ";
			$sql .= "  `IsTaxFree` enum('0','1') DEFAULT '0', ";
			$sql .= "  `Line1AccessAutoPayCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line2AccessAutoPayCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line3AccessAutoPayCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line4AccessAutoPayCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line1SeniorCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line2SeniorCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line3SeniorCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line4SeniorCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line1SpecialWorkerCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line2SpecialWorkerCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line3SpecialWorkerCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `Line4SpecialWorkerCredit` double(11,6) DEFAULT NULL, ";
			$sql .= "  `HasOverage` enum('0','1') DEFAULT '0', ";
			$sql .= "  `GbOverageUnit` int(11) DEFAULT NULL, ";
			$sql .= "  `OverageAmount` double(11,6) DEFAULT NULL, ";
			$sql .= "  `CanChooseOverage` enum('0','1') DEFAULT '0', ";
			$sql .= "  `IsStacked` enum('0','1') DEFAULT '0', ";
			$sql .= "  `IsShared` enum('0','1') DEFAULT '0', ";
			$sql .= "  `Has5GAccess` enum('0','1') DEFAULT '0', ";
			$sql .= "  `5gAccessCharge` double(11,6) DEFAULT NULL, ";
			$sql .= "  `MusicServicesValue` double(11,6) DEFAULT NULL, ";
			$sql .= "  `MovieTVServicesValue` double(11,6) DEFAULT NULL, ";
			$sql .= "  `ModifiedDate` date DEFAULT NULL, ";
			$sql .= "  `VerificationDate` date DEFAULT NULL, ";
			$sql .= "  `ExpirationDate` date DEFAULT NULL, ";
			$sql .= "  `MaxLineCount` int(11) DEFAULT NULL, ";
			$sql .= "  `SubsidizedFee` double(11,6) DEFAULT NULL, ";
			$sql .= "  `DeviceType` varchar(255) DEFAULT NULL, ";
			$sql .= "  `MinimumLineCount` int(11) DEFAULT NULL, ";
			$sql .= "  `CreatedAt` datetime NOT NULL, ";
			$sql .= "  PRIMARY KEY (`id`)) "; 
			$sql .= "  ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}
	}

	public static function insertDefaultRegex()
	{
		global $table_prefix, $wpdb;

		$tblname = 'wireless_butler_regex';
		$wp_table = $table_prefix . $tblname;

		/**
		 * Verizon Regex
		 */
		$wpdb->insert($wp_table, array(
			'key' 			=> 'billingPeriod',
			'carrier_id' 	=> 1,
			'regex' 		=> '/Billing period(.+?) - (.+?)Account/s',
			'regex_index' 	=> '2',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'accountNumber',
			'carrier_id' 	=> 1,
			'regex' 		=> '/Account number(.+?)Invoice/s',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalBill',
			'carrier_id' 	=> 1,
			'regex' 		=> '/bill is \$(.*)/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalBill',
			'carrier_id' 	=> 1,
			'regex' 		=> '/bill is(\n.*)/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'latestMonthBill',
			'carrier_id' 	=> 1,
			'regex' 		=> '/Due (.*), (.+?):(.*)/',
			'regex_index' 	=> '3',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'pastDue',
			'carrier_id' 	=> 1,
			'regex' 		=> '/Unpaid balance \$(.+?)Account/s',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'usedData',
			'carrier_id' 	=> 1,
			'regex' 		=> '/(\d+(\.\d+)? GB) of (.*) used/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalPlanData',
			'carrier_id' 	=> 1,
			'regex' 		=> '/(\d+(\.\d+)? GB) of (.*) used/',
			'regex_index' 	=> '3',
			'type' 			=> 'system'
		));
		/**
		 * Total plan charge Regex
		 */
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalPlanCharges',	
			'carrier_id' 	=> 1,
			'regex' 		=> '/(.*)\(START_DATE - END_DATE\) \$(.*)/',
			'regex_index' 	=> '2',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalPlanCharges',	
			'carrier_id' 	=> 1,
			'regex' 		=> '/(.*)\(START_DATE - END_DATE\)(\n.*)(\n.*)\$(.*)/',
			'regex_index' 	=> '4',
			'type' 			=> 'system'
		));
		/**
		 * Line Count Regex
		 */
		$wpdb->insert($wp_table, array(
			'key' 			=> 'lineCount',	
			'carrier_id' 	=> 1,
			'regex' 		=> '/Account number([\S\s]*?)Billing period/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		/**
		 * Device Info regex
		 */
		$wpdb->insert($wp_table, array(
			'key' 			=> 'deviceBalance',
			'carrier_id' 	=> 1,
			'regex' 		=> '/Conditional/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'deviceOwned',
			'carrier_id' 	=> 1,
			'regex' 		=> '/Conditional/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));


		/**
		 * Sprint Regex
 		 */
		$wpdb->insert($wp_table, array(
			'key' 			=> 'billingPeriod',
			'carrier_id' 	=> 4,
			'regex' 		=> '/Bill Period:(.+?) - (.+?)We/s',
			'regex_index' 	=> '2',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'accountNumber',
			'carrier_id' 	=> 4,
			'regex' 		=> '/Account Number:(.+?)Bill/s',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalBill',
			'carrier_id' 	=> 4,
			'regex' 		=> '/Total Amount Due(\n.*)/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalBill',
			'carrier_id' 	=> 4,
			'regex' 		=> '/TOTALDUE(\n.*)(\n.*)/',
			'regex_index' 	=> '2',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'latestMonthBill',
			'carrier_id' 	=> 4,
			'regex' 		=> '/New Charges (.*)/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'pastDue',
			'carrier_id' 	=> 4,
			'regex' 		=> '/Balance Forward (.*)/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'usedData',
			'carrier_id' 	=> 4,
			'regex' 		=> '/MB\)(\n.*) (.+?) (.+?)/',
			'regex_index' 	=> '2',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'usedData',
			'carrier_id' 	=> 4,
			'regex' 		=> '/YOUUSED(\n.*)/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalPlanData',
			'carrier_id' 	=> 4,
			'regex' 		=> '/\((.+?),(\n.*)(.+?)/',
			'regex_index' 	=> '1',
			'type'	 		=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalPlanData',
			'carrier_id' 	=> 4,
			'regex' 		=> '/YOUUSED(\n.*)(\n.*)(\n.*)(\n.*)of (.*) high speed data/',
			'regex_index' 	=> '5',
			'type'	 		=> 'system'
		));
		/**
		 * Total plan charge Regex
		 */
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalPlanCharges',
			'carrier_id' 	=> 4,
			'regex' 		=> '/Unlimited Freedom MRC (.*)/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalPlanCharges',
			'carrier_id' 	=> 4,
			'regex' 		=> '/(.+?)Unlimited Freedom MRC (.*)/',
			'regex_index' 	=> '2',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalPlanCharges',
			'carrier_id' 	=> 4,
			'regex' 		=> '/Conditional/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		/**
		 * Device Info regex
		 */
		$wpdb->insert($wp_table, array(
			'key' 			=> 'deviceBalance',
			'carrier_id' 	=> 4,
			'regex' 		=> '/Conditional/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'deviceOwned',
			'carrier_id' 	=> 4,
			'regex' 		=> '/Conditional/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));



		/**
		 * AT&T Regex
 		 */
		  $wpdb->insert($wp_table, array(
			'key' 			=> 'billingPeriod',
			'carrier_id' 	=> 2,
			'regex' 		=> '/Issue Date: (.*)/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'accountNumber',
			'carrier_id' 	=> 2,
			'regex' 		=> '/Account number: (.*)/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalBill',
			'carrier_id' 	=> 2,
			'regex' 		=> '/Total due \$(.*)/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'latestMonthBill',
			'carrier_id' 	=> 2,
			'regex' 		=> '/New Charges (.*)/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'pastDue',
			'carrier_id' 	=> 2,
			'regex' 		=> '/Balance Forward (.*)/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'usedData',
			'carrier_id' 	=> 2,
			'regex' 		=> '/Total usage (.+?) /',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalPlanData',
			'carrier_id' 	=> 2,
			'regex' 		=> '/Included in plan (.+?) /',
			'regex_index' 	=> '1',
			'type'	 		=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'usedData',
			'carrier_id' 	=> 2,
			'regex' 		=> '/Conditional/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalPlanData',
			'carrier_id' 	=> 2,
			'regex' 		=> '/Conditional/',
			'regex_index' 	=> '1',
			'type'	 		=> 'system'
		));
		/**
		 * Total plan charge Regex (Conditionlly Added)
		 */
		$wpdb->insert($wp_table, array(
			'key' 			=> 'lineCount',
			'carrier_id' 	=> 2,
			'regex' 		=> '/fees Total([\s\S]*)Total (-?)\$/',
			'regex_index' 	=> '1',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'totalPlanCharges',
			'carrier_id' 	=> 2,
			'regex' 		=> '/Conditional/',
			'regex_index' 	=> '2',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'deviceBalance',
			'carrier_id' 	=> 2,
			'regex' 		=> '/Conditional/',
			'regex_index' 	=> '2',
			'type' 			=> 'system'
		));
		$wpdb->insert($wp_table, array(
			'key' 			=> 'deviceOwned',
			'carrier_id' 	=> 2,
			'regex' 		=> '/Conditional/',
			'regex_index' 	=> '2',
			'type' 			=> 'system'
		));
	}

	public static function insertDefaultCarrier()
	{
		global $table_prefix, $wpdb;

		$tblname = 'wireless_butler_carrier';
		$wp_table = $table_prefix . $tblname;

		$wpdb->insert($wp_table, array(
			'carrier_id' 	=> '1',
			'name' 			=> 'Verizon'
		));
		$wpdb->insert($wp_table, array(
			'carrier_id' 	=> '2',
			'name' 			=> 'AT&T'
		));
		$wpdb->insert($wp_table, array(
			'carrier_id' 	=> '4',
			'name' 			=> 'T-Mobile/Sprint'
		));

	}
}
