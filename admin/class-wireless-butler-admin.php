<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/admin
 * @author     Jai Awasthi <jay.awasthi@gmail.com>
 */
class Wireless_Butler_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wireless_Butler_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wireless_Butler_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wireless-butler-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wireless_Butler_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wireless_Butler_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wireless-butler-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Plugin menu for admin
	 */
	function add_admin_menu(){
		add_menu_page('Read Bills', 'Wireless Butler', 'manage_options', 'wireless_butler_regex_list', array( $this, 'regex_list' ));
		//Regex list page 
		add_submenu_page( 'wireless_butler_regex_list', 'Wireless Butler', 'Read Bills', 'manage_options', 'wireless_butler_regex_list', array( $this, 'regex_list' ));
		
		//Regex add page
		add_submenu_page( null, 'Wireless Butler', 'Wireless Butler', 'manage_options', 'wireless_butler_add_regex', array( $this, 'add_regex' ));
		
		//Entries listing page
		add_submenu_page( 'wireless_butler_regex_list', 'Entries', 'Entries', 'manage_options', 'wireless_butler_entries', array( $this, 'form_entries' ));
		add_submenu_page( null, 'Entries CSV', 'Entries CSV', 'manage_options', 'wireless_butler_entries_csv', array( $this, 'download_entries_csv' ));
		add_submenu_page( null, 'Customer Recommendation Plan', 'Customer Recommendation Plan', 'manage_options', 'wireless_butler_customer_recommendation_plan', array( $this, 'customer_recommendation_plan' ));
		add_submenu_page( null, 'Customer Recommendation Plan', 'Customer Recommendation Plan', 'manage_options', 'wireless_butler_recommendation_download', array( $this, 'download_customer_recommendation_plan' ));
		
		
		//Email notification page
		add_submenu_page( 'wireless_butler_regex_list', 'Notification', 'Notification', 'manage_options', 'wireless_butler_notification', array( $this, 'email_notification' ));

		//Form options page
		add_submenu_page( 'wireless_butler_regex_list', 'Labels', 'Labels', 'manage_options', 'wireless_butler_form_1_option', array( $this, 'form_1_option' ));

		//Plan DB page
		add_submenu_page( 'wireless_butler_regex_list', 'Plan DB', 'Plan DB', 'manage_options', 'wireless_butler_plan_db', array( $this, 'plan_db' ));
		add_submenu_page( null, 'Plan DB CSV', 'Plan DB CSV', 'manage_options', 'wireless_butler_plan_db_csv', array( $this, 'download_plan_db_csv' ));

		add_action( 'admin_init', array($this, 'register_wireless_butler_plugin_settings') );
	}
	
	/**
	 * Form Option page
	 */
	function form_1_option(){
		include_once plugin_dir_path( __FILE__ ) . 'partials/wireless-butler-form-1-option.php';
	}

	/**
	 * Email Notification page
	 */
	function email_notification(){
		include_once plugin_dir_path( __FILE__ ) . 'partials/wireless-butler-email-notification.php';
	}

	/**
	 * Plugin Setting
	 */
	function register_wireless_butler_plugin_settings() {
		$page = isset( $_GET['page'] )? sanitize_text_field( $_GET['page'] ) : '';
		if ( $page == 'wireless_butler_add_regex' || $page == 'wireless_butler_regex_list' || $page == 'wireless_butler_entries_csv' || $page == 'wireless_butler_plan_db_csv' || $page == 'wireless_butler_plan_db' || $page == 'wireless_butler_customer_recommendation_plan' || $page == 'wireless_butler_recommendation_download') {
			ob_start();
		}

		//register plugin options
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_email_to_user_subject', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_email_to_user_content', 'sanitize_textarea_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_notification_email', 'sanitize_email');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_notification_template', 'sanitize_textarea_field');

		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_1_greeting', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_1_heading', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_1_label', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_1_account_holder', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_1_smartphone_heading', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_1_button_text', 'sanitize_text_field');

		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_2_heading', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_2_chepest_plan_text', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_2_total_bill', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_2_latest_month_bill', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_2_past_due', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_2_total_plan_charges', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_2_gb_of_data_used', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_2_gb_in_your_plan', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_2_reach_out_text', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_2_device_balance', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_2_device_owned', 'sanitize_text_field');
		register_setting( 'wireless_butler_plugin_options', 'wireless_butler_form_1_step_2_button_text', 'sanitize_text_field');
	}

	/**
	 * Regex WP list Page
	 */
	function regex_list() {
		$listTable = new Wireless_Butler_Admin_Regex_list();
		$action = isset( $_GET['action'] )? sanitize_text_field($_GET['action']) : '';
		if ( $action == 'edit' ) {
			// Regex edit page
			$this->edit_regex();
		} elseif ( $action == 'delete' ) {
			// Delete Regex
			$listTable->delete_regex();
		} else {
			$listTable->prepare_items();
			?>
			<?php if ( isset( $_GET['updated'] ) ) { ?>
				<div id="message" class="updated is-dismissible">
					<p>Wireless Butler regex updated successfully.</p>
				</div>
			<?php } elseif ( isset( $_GET['saved'] ) ) { ?>
				<div id="message" class="updated is-dismissible">
					<p>Wireless Butler regex added successfully.</p>
				</div>
			<?php } elseif ( isset( $_GET['deleted'] ) ) { ?>
				<div id="message" class="updated is-dismissible">
					<p>Wireless Butler regex deleted successfully.</p>
				</div>
			<?php } ?>
			<div class="wrap">
				<h1>
					<?php _e( 'Wireless Butler Regex List', 'wireless-butler' ); ?>
					<a class="add-new-h2" href="<?php echo esc_url(admin_url( 'admin.php?page=wireless_butler_add_regex' )); ?>">Add New</a>
				</h1>
				<?php $listTable->views(); ?>
				<form action="" method="post">
				<?php $listTable->display(); ?>
				</form>
			</div>
			<?php
		}
	}

	/**
	 * Add Regex page
	 */
	function add_regex() {
		global $wpdb;
		if ( isset( $_POST['addregex'] ) && check_admin_referer( 'wireless_butler_add_regex' ) ) {
			$tblname  = $wpdb->prefix . 'wireless_butler_regex';

			$key = sanitize_text_field( $_POST['key'] );
			$carrierId = sanitize_text_field( $_POST['carrier_id'] );
			$regex = stripslashes(sanitize_text_field($_POST['regex']) );
			$regex_index = sanitize_text_field( $_POST['regex_index'] );
			$description = sanitize_textarea_field( $_POST['description'] );

			if( ! empty( $key ) && ! empty( $carrierId ) && ! empty( $regex ) ) {
				$data = array(
					'key'   		=> $key,
					'carrier_id'    => $carrierId,
					'regex'        	=> $regex,
					'regex_index'  	=> $regex_index,
					'description'  	=> $description,
				);
				$wpdb->insert( $tblname, $data );
			}

			wp_redirect( admin_url( 'admin.php?page=wireless_butler_regex_list&saved=true' ) );
		}
		$carrierTblName = $wpdb->prefix . 'wireless_butler_carrier';

		//carrier list
		$sql = 'SELECT * ';
		$sql .= ' FROM ' . $carrierTblName;
		$sql .= ' ORDER BY id ASC';
		$carrierList = $wpdb->get_results( $sql, 'ARRAY_A' );

		$listTable = new Wireless_Butler_Admin_Regex_list();
		?>
		<div class="wrap wireless_butler_add_regex">
			<h1><?php _e( 'Add New Regex', 'wireless-butler' ); ?></h1>

			<form method="post" action="" id="wireless_butler_add_regex">

				<?php wp_nonce_field( 'wireless_butler_add_regex'); ?>

				<table class="form-table" role="presentation">
				<tbody>
					<tr class="form-field form-required">
						<th scope="row">
							<label for="user_login">Field</label>
						</th>
						<td>
							<select name="key" id="fieldKey">
								<?php
								foreach($listTable->keys as $index=>$value) {
									echo '<option value="'.esc_attr($index).'">'.esc_attr($value).'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row">
							<label for="user_login">Carrier</label>
						</th>
						<td>
							<select name="carrier_id" id="carrier">
								<?php
								foreach($carrierList as $carrier) {
									echo '<option value="'.esc_attr($carrier['carrier_id']).'">'.esc_attr($carrier['name']).'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row">
							<label for="regex">Regex</label>
						</th>
						<td><input name="regex" type="text" id="regex" value=""></td>
					</tr>
					<tr class="form-field">
						<th scope="row"><label for="regex_index">Regex Index</label></th>
						<td><input name="regex_index" type="text" id="regex_index" value=""></td>
					</tr>
					<tr class="form-field">
						<th scope="row"><label for="description">Description</label></th>
						<td>
							<textarea name="description" id="description" rows="5" cols="30" spellcheck="false"></textarea>
						</td>
					</tr>
	
					</tbody>
				</table>

				<p class="submit"><input type="submit" name="addregex" id="addregexsub" class="button button-primary" value="Add Regex"></p>

			</form>
		</div>
		<?php
	}

	/**
	 * Edit Regex page
	 */
	function edit_regex() {
		global $wpdb;

		$tblname  = $wpdb->prefix . 'wireless_butler_regex';
		$id     = intval( sanitize_text_field($_GET['id']) );
		$row = $wpdb->get_row("SELECT * FROM ".$tblname." WHERE id = ".$id);
		
		if ( isset( $_POST['editregex'] ) && check_admin_referer( 'wireless_butler_edit_regex' ) ) {
	
			$key = sanitize_text_field( $_POST['key'] );
			$carrierId = sanitize_text_field( $_POST['carrier_id'] );
			$regex = stripslashes( sanitize_text_field($_POST['regex']) );
			$regex_index = sanitize_text_field( $_POST['regex_index'] );
			$description = sanitize_textarea_field( $_POST['description'] );

			if( ! empty( $key ) && ! empty( $carrierId ) && ! empty( $regex ) ) {
				$data = array(
					'key'   		=> $key,
					'carrier_id'    => $carrierId,
					'regex'        	=> $regex,
					'regex_index'  	=> $regex_index,
					'description'  	=> $description,
				);
				$wpdb->update( $tblname, $data, ['id' => $id] );
			}
			wp_redirect( admin_url( 'admin.php?page=wireless_butler_regex_list&updated=true' ) );
		}

		$carrierTblName = $wpdb->prefix . 'wireless_butler_carrier';

		//carrier list
		$sql = 'SELECT * ';
		$sql .= ' FROM ' . $carrierTblName;
		$sql .= ' ORDER BY id ASC';
		$carrierList = $wpdb->get_results( $sql, 'ARRAY_A' );

		$listTable = new Wireless_Butler_Admin_Regex_list();
		?>
		<div class="wrap wireless_butler_add_regex">
		<h1>
			<?php _e( 'Edit Regex', 'wireless-butler' ); ?>
			<a class="add-new-h2" href="<?php echo esc_url(admin_url( 'admin.php?page=wireless_butler_add_regex' )) ; ?>">Add New</a>
		</h1>
	
			<form method="post" action="" id="wireless_butler_add_regex">
				<?php wp_nonce_field( 'wireless_butler_edit_regex' ); ?>
	
				<table class="form-table" role="presentation">
				<tbody>
					<tr class="form-field form-required">
						<th scope="row">
							<label for="user_login">Field</label>
						</th>
						<td>
							<select name="key" id="fieldKey">
								<?php
								foreach($listTable->keys as $index=>$value) {
									$selected = ($index == $row->key)? 'selected="selected"' : '';
									echo '<option value="'.esc_attr($index).'" '.$selected.'>'.esc_attr($value).'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row">
							<label for="user_login">Carrier</label>
						</th>
						<td>
							<select name="carrier_id" id="carrier">
								<?php
								foreach($carrierList as $carrier) {
									$selected = ($row->carrier_id == $carrier['carrier_id'])? 'selected="selected"' : '';
									echo '<option value="'.esc_attr($carrier['carrier_id']).'" '.$selected.'>'.esc_attr($carrier['name']).'</option>';
								}
								?>			
							</select>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row">
							<label for="regex">Regex</label>
						</th>
						<td><input name="regex" type="text" id="regex" value="<?php echo esc_attr($row->regex); ?>"></td>
					</tr>
					<tr class="form-field">
						<th scope="row"><label for="regex_index">Regex Index</label></th>
						<td><input name="regex_index" type="text" id="regex_index" value="<?php echo esc_attr($row->regex_index); ?>"></td>
					</tr>
					<tr class="form-field">
						<th scope="row"><label for="description">Description</label></th>
						<td>
							<textarea name="description" id="description" rows="5" cols="30" spellcheck="false"><?php echo esc_html($row->description); ?></textarea>
						</td>
					</tr>
	
					</tbody>
				</table>

				<p class="submit"><input type="submit" name="editregex" id="editregexsub" class="button button-primary" value="Update Regex"></p>
			</form>
		<?php
	}

	/**
	 * Form Entries list Page
	 */
	function form_entries() {
		$listTable = new Wireless_Butler_Admin_Entries();
		$listTable->prepare_items();
		?>
		<div class="wrap">
			<h1>
				<?php _e( 'Wireless Butler Entries', 'wireless-butler' ); ?>
				<a class="add-new-h2" href="<?php echo esc_url(admin_url( 'admin.php?page=wireless_butler_entries_csv' )); ?>">Download CSV</a>
			</h1>
			<?php $listTable->views(); ?>
			<form action="" method="post">
			<?php $listTable->display(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Customer recommendation plan list Page
	 */
	function customer_recommendation_plan() {
		$customerID = isset($_GET['id'])? sanitize_text_field($_GET['id']):'';
		if(!$customerID) {
			wp_redirect( admin_url('admin.php?page=wireless_butler_entries') );
			exit;
		}

		global $table_prefix, $wpdb;
		$wp_table = $table_prefix . 'wireless_butler_customer';
		$customer = $wpdb->get_row("SELECT * FROM ".$wp_table." WHERE id = ".$customerID);
		if(!$customer) {
			wp_redirect( admin_url('admin.php?page=wireless_butler_entries') );
			exit;
		}

		$listTable = new Wireless_Butler_Admin_Customer_Recommendation_Plan();
		$listTable->prepare_items();
		?>
		<div class="wrap">
			<h1>
				<?php _e( 'Recommendation Plan', 'wireless-butler' ); ?>
				<a class="add-new-h2" href="<?php echo esc_url(admin_url( 'admin.php?page=wireless_butler_recommendation_download&id='.$customerID )); ?>">Download CSV</a>
			</h1>
			<span>Customer Name: <?php echo esc_attr($customer->firstName).' '.esc_attr($customer->lastName); ?></span>
			<form action="" method="post">
			<?php $listTable->display(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Recommendation Plan CSV download
	 */
	function download_customer_recommendation_plan() {
		$customerID = isset($_GET['id'])? sanitize_text_field($_GET['id']):'';
		if(!$customerID) {
			wp_redirect( admin_url('admin.php?page=wireless_butler_entries') );
			exit;
		}

		global $table_prefix, $wpdb;
		$wp_table = $table_prefix . 'wireless_butler_customer';
		$customer = $wpdb->get_row("SELECT * FROM ".$wp_table." WHERE id = ".$customerID);
		if(!$customer) {
			wp_redirect( admin_url('admin.php?page=wireless_butler_entries') );
			exit;
		}

		global $wpdb;
		$tblName = $wpdb->prefix . 'wireless_butler_recommendation';

		$sql = 'SELECT * ';
		$sql .= ' FROM ' . $tblName;
		$sql .= ' WHERE customer_id=' . $customerID;
 		$sql .= ' ORDER BY id ASC';
		$results = $wpdb->get_results( $sql );
		
		// file creation
		$wp_filename = "wireless_butler_recommendation_plan_".$customerID."_".date("d-m-y").".csv";
		
		// Clean object
		ob_end_clean ();
		
		// Open file
		$wp_file = fopen($wp_filename,"w");
		
		//CSV header column
		$columns = array(
			'AccountNumber'       	                                => 'AccountNumber',
			'UploadedPlanCosts'       	                            => 'UploadedPlanCosts',
			'UploadedLineCount'       	                            => 'UploadedLineCount',
			'isCarrier'       	                                    => 'isCarrier',
			'isPlanAllowed'       	                                => 'isPlanAllowed',
			'CheapeastPlanCosts'       	                            => 'CheapeastPlanCosts',
			'CostForThisPlan'       	                            => 'CostForThisPlan',
			'isCheapestPlanAndSameCarrier'       	                => 'isCheapestPlanAndSameCarrier',
			'ThisPlanWithAutoPayCredits'       	                    => 'ThisPlanWithAutoPayCredits',
			'ThisPlanWithSeniorCredits'       	                    => 'ThisPlanWithSeniorCredits',
			'ThisPlanWithAutoPayANDSeniorCredits'       	        => 'ThisPlanWithAutoPayANDSeniorCredits',
			'ThisPlanWithSpecialWorkerCredits'       	            => 'ThisPlanWithSpecialWorkerCredits',
            'ThisPlanWithAutoPayANDSpecialWorkerCredits'       	    => 'ThisPlanWithAutoPayANDSpecialWorkerCredits',
            'SavingsForThisPlan'       	                            => 'SavingsForThisPlan',
			'SavingsForThisPlanWithAutoPay'       	                => 'SavingsForThisPlanWithAutoPay',
			'SavingsForThisPlanWithSpecialWorker'       	        => 'SavingsForThisPlanWithSpecialWorker',
            'SavingsForThisPlanWithAutoPayAndSpecialWorker'       	=> 'SavingsForThisPlanWithAutoPayAndSpecialWorker',
            'PlanId'       	                                        => 'PlanId',
			'CarrierId'       	                                    => 'CarrierId',
			'PlanName'       	                                    => 'PlanName',
			'GBAllowance'       	                                => 'GBAllowance',
			'MhsGbAllowance'       	                                => 'MhsGbAllowance',
			'HostPlanCharge'       	                                => 'HostPlanCharge',
			'Line1AccessCharge'       	                            => 'Line1AccessCharge',
			'Line2AccessCharge'       	                            => 'Line2AccessCharge',
			'Line3AccessCharge'       	                            => 'Line3AccessCharge',
			'Line4AccessCharge'       	                            => 'Line4AccessCharge',
			'Line5AccessCharge'       	                            => 'Line5AccessCharge',
			'TabletOrMHSLineAccessCharge'       	                => 'TabletOrMHSLineAccessCharge',
			'WearableLineAccessCharge'       	                    => 'WearableLineAccessCharge',
			'IsHostPlanDIscountable'       	                        => 'IsHostPlanDIscountable',
			'LineAccessAutoPayCreditMaxLines'       	            => 'LineAccessAutoPayCreditMaxLines',
			'SpecialWorkerLineAccessCredit'       	                => 'SpecialWorkerLineAccessCredit',
			'IsTaxFree'       	                                    => 'IsTaxFree',
			'Line1AccessAutoPayCredit'       	                    => 'Line1AccessAutoPayCredit',
			'Line2AccessAutoPayCredit'       	                    => 'Line2AccessAutoPayCredit',
			'Line3AccessAutoPayCredit'       	                    => 'Line3AccessAutoPayCredit',
			'Line4AccessAutoPayCredit'       	                    => 'Line4AccessAutoPayCredit',
			'Line1SeniorCredit'       	                            => 'Line1SeniorCredit',
			'Line2SeniorCredit'       	                            => 'Line2SeniorCredit',
			'Line3SeniorCredit'       	                            => 'Line3SeniorCredit',
			'Line4SeniorCredit'       	                            => 'Line4SeniorCredit',
			'Line1SpecialWorkerCredit'       	                    => 'Line1SpecialWorkerCredit',
			'Line2SpecialWorkerCredit'       	                    => 'Line2SpecialWorkerCredit',
			'Line3SpecialWorkerCredit'       	                    => 'Line3SpecialWorkerCredit',
			'Line4SpecialWorkerCredit'       	                    => 'Line4SpecialWorkerCredit',
			'HasOverage'       	                                    => 'HasOverage',
			'GbOverageUnit'       	                                => 'GbOverageUnit',
			'OverageAmount'       	                                => 'OverageAmount',
			'CanChooseOverage'       	                            => 'CanChooseOverage',
			'IsStacked'       	                                    => 'IsStacked',
			'IsShared'       	                                    => 'IsShared',
			'Has5GAccess'       	                                => 'Has5GAccess',
			'5gAccessCharge'       	                                => '5gAccessCharge',
			'MusicServicesValue'       	                            => 'MusicServicesValue',
			'MovieTVServicesValue'       	                        => 'MovieTVServicesValue',
			'ModifiedDate'       	                                => 'ModifiedDate',
			'VerificationDate'       	                            => 'VerificationDate',
			'ExpirationDate'       	                                => 'ExpirationDate',
			'MaxLineCount'                    						=> 'MaxLineCount',
            'SubsidizedFee'                    						=> 'SubsidizedFee',
            'DeviceType'                      						=> 'DeviceType',
            'MinimumLineCount'                  					=> 'MinimumLineCount',
            'CreatedAt'                  							=> 'CreatedAt'
		);
		fputcsv($wp_file, $columns);

		// loop for insert data into CSV file
		foreach ($results as $result)
		{
			$wp_array = array(
				'AccountNumber'       	                                => $result->AccountNumber,
				'UploadedPlanCosts'       	                            => floatval($result->UploadedPlanCosts),
				'UploadedLineCount'       	                            => $result->UploadedLineCount,
				'isCarrier'       	                                    => $result->isCarrier,
				'isPlanAllowed'       	                                => $result->isPlanAllowed,
				'CheapeastPlanCosts'       	                            => floatval($result->CheapeastPlanCosts),
				'CostForThisPlan'       	                            => floatval($result->CostForThisPlan),
				'isCheapestPlanAndSameCarrier'       	                => $result->isCheapestPlanAndSameCarrier,
				'ThisPlanWithAutoPayCredits'       	                    => $result->ThisPlanWithAutoPayCredits,
				'ThisPlanWithSeniorCredits'       	                    => $result->ThisPlanWithSeniorCredits,
				'ThisPlanWithAutoPayANDSeniorCredits'       	        => $result->ThisPlanWithAutoPayANDSeniorCredits,
				'ThisPlanWithSpecialWorkerCredits'       	            => $result->ThisPlanWithSpecialWorkerCredits,
				'ThisPlanWithAutoPayANDSpecialWorkerCredits'       	    => $result->ThisPlanWithAutoPayANDSpecialWorkerCredits,
				'SavingsForThisPlan'       	                            => $result->SavingsForThisPlan,
				'SavingsForThisPlanWithAutoPay'       	                => $result->SavingsForThisPlanWithAutoPay,
				'SavingsForThisPlanWithSpecialWorker'       	        => $result->SavingsForThisPlanWithSpecialWorker,
				'SavingsForThisPlanWithAutoPayAndSpecialWorker'       	=> $result->SavingsForThisPlanWithAutoPayAndSpecialWorker,
				'PlanId'   												=> $result->PlanId,
				'CarrierId'   											=> $result->CarrierId,
				'PlanName' 												=> $result->PlanName,
				'GBAllowance' 											=> $result->GBAllowance,
				'MhsGbAllowance' 										=> $result->MhsGbAllowance,
				'HostPlanCharge' 										=> floatval($result->HostPlanCharge),
				'Line1AccessCharge' 									=> floatval($result->Line1AccessCharge),
				'Line2AccessCharge' 									=> floatval($result->Line2AccessCharge),
				'Line3AccessCharge' 									=> floatval($result->Line3AccessCharge),
				'Line4AccessCharge' 									=> floatval($result->Line4AccessCharge),
				'Line5AccessCharge' 									=> floatval($result->Line5AccessCharge),
				'TabletOrMHSLineAccessCharge' 							=> floatval($result->TabletOrMHSLineAccessCharge),
				'WearableLineAccessCharge' 								=> floatval($result->WearableLineAccessCharge),
				'IsHostPlanDIscountable' 								=> $result->IsHostPlanDIscountable,
				'LineAccessAutoPayCreditMaxLines' 						=> $result->LineAccessAutoPayCreditMaxLines,
				'SpecialWorkerLineAccessCredit' 						=> $result->SpecialWorkerLineAccessCredit,
				'IsTaxFree' 											=> $result->IsTaxFree,
				'Line1AccessAutoPayCredit' 								=> floatval($result->Line1AccessAutoPayCredit),
				'Line2AccessAutoPayCredit' 								=> floatval($result->Line2AccessAutoPayCredit),
				'Line3AccessAutoPayCredit' 								=> floatval($result->Line3AccessAutoPayCredit),
				'Line4AccessAutoPayCredit' 								=> floatval($result->Line4AccessAutoPayCredit),
				'Line1SeniorCredit' 									=> floatval($result->Line1SeniorCredit),
				'Line2SeniorCredit' 									=> floatval($result->Line2SeniorCredit),
				'Line3SeniorCredit' 									=> floatval($result->Line3SeniorCredit),
				'Line4SeniorCredit' 									=> floatval($result->Line4SeniorCredit),
				'Line1SpecialWorkerCredit' 								=> floatval($result->Line1SpecialWorkerCredit),
				'Line2SpecialWorkerCredit' 								=> floatval($result->Line2SpecialWorkerCredit),
				'Line3SpecialWorkerCredit' 								=> floatval($result->Line3SpecialWorkerCredit),
				'Line4SpecialWorkerCredit' 								=> floatval($result->Line4SpecialWorkerCredit),
				'HasOverage' 											=> $result->HasOverage,
				'GbOverageUnit' 										=> $result->GbOverageUnit,
				'OverageAmount' 										=> floatval($result->OverageAmount),
				'CanChooseOverage' 										=> $result->CanChooseOverage,
				'IsStacked' 											=> $result->IsStacked,
				'IsShared' 												=> $result->IsShared,
				'Has5GAccess' 											=> $result->Has5GAccess,
				'5gAccessCharge' 										=> floatval($result->{'5gAccessCharge'}),
				'MusicServicesValue' 									=> floatval($result->MusicServicesValue),
				'MovieTVServicesValue' 									=> floatval($result->MovieTVServicesValue),
				'ModifiedDate' 											=> ($result->ModifiedDate != NULL)? date('m/d/y', strtotime($result->ModifiedDate)):NULL,
				'VerificationDate' 										=> ($result->VerificationDate != NULL)? date('m/d/y', strtotime($result->VerificationDate)): NULL,
				'ExpirationDate' 										=> ($result->ExpirationDate != NULL)? date('m/d/y', strtotime($result->ExpirationDate)): NULL,
				'MaxLineCount'                    						=> $result->MaxLineCount,
				'SubsidizedFee'                    						=> floatval($result->SubsidizedFee),
				'DeviceType'                      						=> $result->DeviceType,
				'MinimumLineCount'                  					=> $result->MinimumLineCount
			);
			fputcsv($wp_file, $wp_array);
		}
		
		// Close file
		fclose($wp_file);
		
		// download csv file
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=".$wp_filename);
		header("Content-Type: application/csv;");
		readfile($wp_filename);
		exit;
	}

	/**
	 * Form Entries CSV download
	 */
	function download_entries_csv() {
		global $wpdb;
		$customerTblName = $wpdb->prefix . 'wireless_butler_customer';
		$carrierTblName = $wpdb->prefix . 'wireless_butler_carrier';

		$sql = 'SELECT e.*, c.name as carrier, c.carrier_id as carrier_id ';
		$sql .= ' FROM ' . $customerTblName . ' as e';
		$sql .= ' LEFT JOIN ' . $carrierTblName . ' as c ON c.carrier_id=e.carrier';
 		$sql .= ' ORDER BY c.id DESC';
		$results = $wpdb->get_results( $sql );
		
		// file creation
		$wp_filename = "wireless_butler_entries_".date("d-m-y").".csv";
		
		// Clean object
		ob_end_clean ();
		
		// Open file
		$wp_file = fopen($wp_filename,"w");
		
		//CSV Sheet header row
		$wp_array = array(
			"firstName"		=>	'firstName',
			"lastName"		=>	'lastName',
			"email"			=>	'email',
			"phoneNumber"	=>	'phoneNumber',
			"carrier"		=>	'carrier',
			"manual"		=>	'manual',
			"bill"			=>	'wirelessBillURL',
		);
		fputcsv($wp_file, $wp_array);

		// loop for insert data into CSV file
		foreach ($results as $result)
		{
			$wp_array = array(
				"firstName"		=>	$result->firstName,
				"lastName"		=>	$result->lastName,
				"email"			=>	$result->email,
				"phoneNumber"	=>	$result->phoneNumber,
				"carrier"		=>	$result->carrier,
				"manual"		=>	$result->manual,
				"bill"			=>	$result->wirelessBillURL,
			);
			fputcsv($wp_file, $wp_array);
		}
		
		// Close file
		fclose($wp_file);
		
		// download csv file
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=".$wp_filename);
		header("Content-Type: application/csv;");
		readfile($wp_filename);
		exit;
	}

	/**
	 * Plan DB list Page
	 */
	function plan_db() {
		$err = '';
		if(isset($_POST['upload_plan_db'])) {
			if(isset($_FILES['file']) && $_FILES['file']['name'] != '') {
				//check file mime type
				$allowedMimetypeArr = array('csv' => 'text/csv');
				$fileTypeCheck = wp_check_filetype($_FILES['file']['name'], $allowedMimetypeArr);
				if(!$fileTypeCheck['type']) {
					$err = 'Please upload csv file only';
				}

				if(!$err) {
					$upload_overrides = array( 'test_form' => false );
					$movefile = wp_handle_upload( $_FILES['file'], $upload_overrides );
					if ( $movefile && ! isset( $movefile['error'] ) ) {
						global $wpdb;
						$tblname = $wpdb->prefix . 'wireless_butler_plan_database';
	
						//delete old plan DB entries
						$sql = "DELETE FROM " . $tblname ;
						$wpdb->query( $sql );
	
						// Open file in read mode
						$csvFile = fopen($movefile['file'], 'r');
	
						fgetcsv($csvFile); // Skipping header row
	
						// Read file
						while(($csvData = fgetcsv($csvFile)) !== FALSE){
							$csvData = array_map("utf8_encode", $csvData);
							
							//insert into plan DB
							if(isset($csvData['0']) && $csvData['0'] != '') {
								$data = array(
									'PlanId'   							=> $csvData[0],
									'CarrierId'   						=> $csvData[1],
									'PlanName' 							=> $csvData[2],
									'GBAllowance' 						=> $csvData[3],
									'MhsGbAllowance' 					=> $csvData[4],
									'HostPlanCharge' 					=> $csvData[5],
									'Line1AccessCharge' 				=> $csvData[6],
									'Line2AccessCharge' 				=> $csvData[7],
									'Line3AccessCharge' 				=> $csvData[8],
									'Line4AccessCharge' 				=> $csvData[9],
									'Line5AccessCharge' 				=> $csvData[10],
									'TabletOrMHSLineAccessCharge' 		=> $csvData[11],
									'WearableLineAccessCharge' 			=> $csvData[12],
									'IsHostPlanDIscountable' 			=> $csvData[13],
									'LineAccessAutoPayCreditMaxLines' 	=> $csvData[14],
									'SpecialWorkerLineAccessCredit' 	=> $csvData[15],
									'IsTaxFree' 						=> $csvData[16],
									'Line1AccessAutoPayCredit' 			=> $csvData[17],
									'Line2AccessAutoPayCredit' 			=> $csvData[18],
									'Line3AccessAutoPayCredit' 			=> $csvData[19],
									'Line4AccessAutoPayCredit' 			=> $csvData[20],
									'Line1SeniorCredit' 				=> $csvData[21],
									'Line2SeniorCredit' 				=> $csvData[22],
									'Line3SeniorCredit' 				=> $csvData[23],
									'Line4SeniorCredit' 				=> $csvData[24],
									'Line1SpecialWorkerCredit' 			=> $csvData[25],
									'Line2SpecialWorkerCredit' 			=> $csvData[26],
									'Line3SpecialWorkerCredit' 			=> $csvData[27],
									'Line4SpecialWorkerCredit' 			=> $csvData[28],
									'HasOverage' 						=> $csvData[29],
									'GbOverageUnit' 					=> $csvData[30],
									'OverageAmount' 					=> $csvData[31],
									'CanChooseOverage' 					=> $csvData[32],
									'IsStacked' 						=> $csvData[33],
									'IsShared' 							=> $csvData[34],
									'Has5GAccess' 						=> $csvData[35],
									'5gAccessCharge' 					=> $csvData[36],
									'MusicServicesValue' 				=> $csvData[37],
									'MovieTVServicesValue' 				=> $csvData[38],
									'ModifiedDate' 						=> ($csvData[39] != NULL && $csvData[39] != 'NULL')? date('Y-m-d',strtotime($csvData[39])): NULL,
									'VerificationDate' 					=> ($csvData[40] != NULL && $csvData[40] != 'NULL')? date('Y-m-d',strtotime($csvData[40])): NULL,
									'ExpirationDate' 					=> ($csvData[41] != NULL && $csvData[41] != 'NULL')? date('Y-m-d',strtotime($csvData[41])): NULL,
									'MaxLineCount' 						=> $csvData[42],
									'SubsidizedFee' 					=> $csvData[43],
									'DeviceType' 						=> $csvData[44],
									'MinimumLineCount' 					=> $csvData[45],
								);
								$wpdb->insert( $tblname, $data );
							}
						}
					}else{
						$err = $movefile['error'];
					}
				}
			}
		}

		$listTable = new Wireless_Butler_Admin_Plan_DB();
		$listTable->prepare_items();
		?>
		<div class="wrap">
			<h1>
				<?php _e( 'Wireless Butler Plan DB', 'wireless-butler' ); ?>
				<a class="add-new-h2" href="<?php echo esc_url(admin_url( 'admin.php?page=wireless_butler_plan_db_csv' )); ?>">Download CSV</a>
			</h1>
			<form method='post' action='' name='myform' enctype='multipart/form-data'>
				<table>
					<tr>
						<td>Add file: <input type='file' name='file'> <input type='submit' name='upload_plan_db' value='Submit'></td>
					</tr>
				</table>
				<?php if($err != '') { ?>
					<div style="color:red;"><?php echo esc_attr($err); ?></div>
				<?php } ?>
			</form>
			<form action="" method="post">
			<?php $listTable->display(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Plan DB CSV download
	 */
	function download_plan_db_csv() {
		global $wpdb;
		$planTblName = $wpdb->prefix . 'wireless_butler_plan_database';

		$sql = 'SELECT * ';
		$sql .= ' FROM ' . $planTblName;
 		$sql .= ' ORDER BY id ASC';
		$results = $wpdb->get_results( $sql );
		
		// file creation
		$wp_filename = "wireless_butler_plan_db_".date("d-m-y").".csv";
		
		// Clean object
		ob_end_clean ();
		
		// Open file
		$wp_file = fopen($wp_filename,"w");

		$columns = array(
            'PlanId'       	                    => 'PlanId',
            'CarrierId'                         => 'CarrierId',
            'PlanName'                          => 'PlanName',
            'GBAllowance'                       => 'GBAllowance',
            'MhsGbAllowance'                    => 'MhsGbAllowance',
            'HostPlanCharge'                    => 'HostPlanCharge',
            'Line1AccessCharge'                 => 'Line1AccessCharge',
            'Line2AccessCharge'                 => 'Line2AccessCharge',
            'Line3AccessCharge'                 => 'Line3AccessCharge',
            'Line4AccessCharge'                 => 'Line4AccessCharge',
            'Line5AccessCharge'                 => 'Line5AccessCharge',
            'TabletOrMHSLineAccessCharge'       => 'TabletOrMHSLineAccessCharge',
            'WearableLineAccessCharge'          => 'WearableLineAccessCharge',
            'IsHostPlanDIscountable'            => 'IsHostPlanDIscountable',
            'LineAccessAutoPayCreditMaxLines'   => 'LineAccessAutoPayCreditMaxLines',
            'SpecialWorkerLineAccessCredit'     => 'SpecialWorkerLineAccessCredit',
			'IsTaxFree'                         => 'IsTaxFree',
            'Line1AccessAutoPayCredit'          => 'Line1AccessAutoPayCredit',
            'Line2AccessAutoPayCredit'          => 'Line2AccessAutoPayCredit',
            'Line3AccessAutoPayCredit'          => 'Line3AccessAutoPayCredit',
			'Line4AccessAutoPayCredit'          => 'Line4AccessAutoPayCredit',
			'Line1SeniorCredit'          		=> 'Line1SeniorCredit',
			'Line2SeniorCredit'          		=> 'Line2SeniorCredit',
			'Line3SeniorCredit'          		=> 'Line3SeniorCredit',
			'Line4SeniorCredit'          		=> 'Line4SeniorCredit',
			'Line1SpecialWorkerCredit'          => 'Line1SpecialWorkerCredit',
			'Line2SpecialWorkerCredit'          => 'Line2SpecialWorkerCredit',
			'Line3SpecialWorkerCredit'          => 'Line3SpecialWorkerCredit',
			'Line4SpecialWorkerCredit'          => 'Line4SpecialWorkerCredit',
            'HasOverage'                        => 'HasOverage',
            'GbOverageUnit'                     => 'GbOverageUnit',
            'OverageAmount'                     => 'OverageAmount',
            'CanChooseOverage'                  => 'CanChooseOverage',
            'IsStacked'                         => 'IsStacked',
            'IsShared'                          => 'IsShared',
            'Has5GAccess'                       => 'Has5GAccess',
            '5gAccessCharge'                    => '5gAccessCharge',
            'MusicServicesValue'                => 'MusicServicesValue',
            'MovieTVServicesValue'              => 'MovieTVServicesValue',
            'ModifiedDate'                      => 'ModifiedDate',
            'VerificationDate'                  => 'VerificationDate',
            'ExpirationDate'                    => 'ExpirationDate',
            'MaxLineCount'                    	=> 'MaxLineCount',
            'SubsidizedFee'                    	=> 'SubsidizedFee',
            'DeviceType'                      	=> 'DeviceType',
            'MinimumLineCount'                  => 'MinimumLineCount'
		);
		fputcsv($wp_file, $columns);
		
		// loop for insert data into CSV file
		foreach ($results as $result)
		{
			$wp_array = array(
				'PlanId'   							=> $result->PlanId,
				'CarrierId'   						=> $result->CarrierId,
				'PlanName' 							=> $result->PlanName,
				'GBAllowance' 						=> $result->GBAllowance,
				'MhsGbAllowance' 					=> $result->MhsGbAllowance,
				'HostPlanCharge' 					=> floatval($result->HostPlanCharge),
				'Line1AccessCharge' 				=> floatval($result->Line1AccessCharge),
				'Line2AccessCharge' 				=> floatval($result->Line2AccessCharge),
				'Line3AccessCharge' 				=> floatval($result->Line3AccessCharge),
				'Line4AccessCharge' 				=> floatval($result->Line4AccessCharge),
				'Line5AccessCharge' 				=> floatval($result->Line5AccessCharge),
				'TabletOrMHSLineAccessCharge' 		=> floatval($result->TabletOrMHSLineAccessCharge),
				'WearableLineAccessCharge' 			=> floatval($result->WearableLineAccessCharge),
				'IsHostPlanDIscountable' 			=> $result->IsHostPlanDIscountable,
				'LineAccessAutoPayCreditMaxLines' 	=> $result->LineAccessAutoPayCreditMaxLines,
				'SpecialWorkerLineAccessCredit' 	=> $result->SpecialWorkerLineAccessCredit,
				'IsTaxFree' 						=> $result->IsTaxFree,
				'Line1AccessAutoPayCredit' 			=> floatval($result->Line1AccessAutoPayCredit),
				'Line2AccessAutoPayCredit' 			=> floatval($result->Line2AccessAutoPayCredit),
				'Line3AccessAutoPayCredit' 			=> floatval($result->Line3AccessAutoPayCredit),
				'Line4AccessAutoPayCredit' 			=> floatval($result->Line4AccessAutoPayCredit),
				'Line1SeniorCredit' 				=> floatval($result->Line1SeniorCredit),
				'Line2SeniorCredit' 				=> floatval($result->Line2SeniorCredit),
				'Line3SeniorCredit' 				=> floatval($result->Line3SeniorCredit),
				'Line4SeniorCredit' 				=> floatval($result->Line4SeniorCredit),
				'Line1SpecialWorkerCredit' 			=> floatval($result->Line1SpecialWorkerCredit),
				'Line2SpecialWorkerCredit' 			=> floatval($result->Line2SpecialWorkerCredit),
				'Line3SpecialWorkerCredit' 			=> floatval($result->Line3SpecialWorkerCredit),
				'Line4SpecialWorkerCredit' 			=> floatval($result->Line4SpecialWorkerCredit),
				'HasOverage' 						=> $result->HasOverage,
				'GbOverageUnit' 					=> $result->GbOverageUnit,
				'OverageAmount' 					=> floatval($result->OverageAmount),
				'CanChooseOverage' 					=> $result->CanChooseOverage,
				'IsStacked' 						=> $result->IsStacked,
				'IsShared' 							=> $result->IsShared,
				'Has5GAccess' 						=> $result->Has5GAccess,
				'5gAccessCharge' 					=> floatval($result->{'5gAccessCharge'}),
				'MusicServicesValue' 				=> floatval($result->MusicServicesValue),
				'MovieTVServicesValue' 				=> floatval($result->MovieTVServicesValue),
				'ModifiedDate' 						=> ($result->ModifiedDate != NULL)? date('m/d/y', strtotime($result->ModifiedDate)):NULL,
				'VerificationDate' 					=> ($result->VerificationDate != NULL)? date('m/d/y', strtotime($result->VerificationDate)): NULL,
				'ExpirationDate' 					=> ($result->ExpirationDate != NULL)? date('m/d/y', strtotime($result->ExpirationDate)): NULL,
				'MaxLineCount' 						=> $result->MaxLineCount,
				'SubsidizedFee' 					=> $result->SubsidizedFee,
				'DeviceType' 						=> $result->DeviceType,
				'MinimumLineCount' 					=> $result->MinimumLineCount,
			);
			fputcsv($wp_file, $wp_array);
		}
		
		// Close file
		fclose($wp_file);
		
		// download csv file
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=".$wp_filename);
		header("Content-Type: application/csv;");
		readfile($wp_filename);
		exit;
	}
}
