<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/public
 * @author     Jai Awasthi <jay.awasthi@gmail.com>
 */
class Wireless_Butler_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wireless-butler-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-dropzone', plugin_dir_url( __FILE__ ) . 'css/dropzone.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wireless-butler-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'-dropzone', plugin_dir_url( __FILE__ ) . 'js/dropzone.min.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, "wirelessButlerObj", array( 'ajaxurl' => admin_url( 'admin-post.php' ) ));

	}
	
	/**
	 * Plugin initlization function
	 */
	public function plugin_init( ) {
		add_shortcode( 'wireless_butler_form_1', array($this, 'wireless_butler_form_1_func') );
		
		add_action( 'admin_post_nopriv_wireless_butler_form_1_step_1', array($this, 'handle_form_1_step_1_submit') );
		add_action( 'admin_post_wireless_butler_form_1_step_1', array($this, 'handle_form_1_step_1_submit') );
		
		add_action( 'admin_post_nopriv_wireless_butler_form_1_step_2', array($this, 'handle_form_1_step_2_submit') );
		add_action( 'admin_post_wireless_butler_form_1_step_2', array($this, 'handle_form_1_step_2_submit') );
		
		add_action( 'upgrader_process_complete', array($this, 'plugin_upgrader_process_complete'), 10, 2 );
	}

	function plugin_upgrader_process_complete( $upgrader_object, $options ) {
		$pluginUpdated = false;

		if ( isset( $options['plugins'] ) && is_array( $options['plugins'] ) ) {
			foreach ( $options['plugins'] as $index => $plugin ) {
				if ( 'wireless-butler/wireless-butler.php' === $plugin ) {
					$pluginUpdated = true;
					break;
				}
			}
		}

		if ( ! $pluginUpdated ) {
			return;
		}

		// Do something when plugin has been updated.
		$data = array(
			'action' 			=> 'plugin_activation_sync',
			'sourceDomain' 		=> get_site_url(),
			'wirelessButler' 	=> 'updated',
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
	 * Frontend form shortcode function
	 */
	function wireless_butler_form_1_func( $atts ) {
		ob_start();

		global $wpdb;
		$carrierTblName = $wpdb->prefix . 'wireless_butler_carrier';

		//carrier list
		$sql = 'SELECT * ';
		$sql .= ' FROM ' . $carrierTblName;
 		$sql .= ' ORDER BY id ASC';
		$carrierList = $wpdb->get_results( $sql, 'ARRAY_A' );
		
		include_once plugin_dir_path( __FILE__ ) . 'partials/wireless-butler-form-1.php';

		return ob_get_clean();
	}

	/**
	 * Handle form submit
	 */
	function handle_form_1_step_1_submit() {

		global $table_prefix, $wpdb;

		$data = array(
			'fname' 					=> sanitize_text_field($_POST['fname']),
			'lname' 					=> sanitize_text_field($_POST['lname']),
			'email' 					=> sanitize_email($_POST['email']),
			'phone' 					=> sanitize_text_field($_POST['phone']),
			'auto_pay_with_carrier' 	=> sanitize_text_field($_POST['auto_pay_with_carrier']),
			'in_military' 				=> sanitize_text_field($_POST['in_military']),
			'over_55_year_of_age' 		=> sanitize_text_field($_POST['over_55_year_of_age']),
			'basic_phone' 				=> sanitize_text_field($_POST['basic_phone']),
			'tablet' 					=> sanitize_text_field($_POST['tablet']),
			'mobile_hotspot' 			=> sanitize_text_field($_POST['mobile_hotspot']),
			'wearable' 					=> sanitize_text_field($_POST['wearable']),
			'manual' 					=> sanitize_text_field($_POST['manual']),
			'carrier_id' 				=> sanitize_text_field($_POST['carrier_id']),
		);
		$returnData = array();

		$billFileURL 	= '';
		$manual 		= $data['manual'];
		if($manual == '0') {
			if ( isset( $_FILES['bill'] ) && $_FILES['bill']['name'] != '' ) {
				$filename = sanitize_file_name($_FILES['bill']['name']);

				//check file mime type
				$allowedMimetypeArr = array('pdf' => 'application/pdf');
				$fileTypeCheck = wp_check_filetype($filename, $allowedMimetypeArr);
				if(!$fileTypeCheck['type']) {
					wp_send_json_error(array('error' => ['bill' => 'Please upload pdf file only']));
				}

				//check file size
				// Allowed file size -> 5MB
				$allowed_file_size = 5242880;
				if ( filesize($_FILES['bill']['tmp_name']) > $allowed_file_size ) {
					wp_send_json_error(array('error' => ['bill' => 'File is too large. Max. upload file size is 5MB']));
				}

				$upload_overrides = array( 'test_form' => false, 'unique_filename_callback' => 'wp_unique_filename' );
				$movefile = wp_handle_upload( $_FILES['bill'], $upload_overrides );
				if ( $movefile && ! isset( $movefile['error'] ) ) {
					
					$billFileURL = $movefile['url'];
		
					$fromPage = null;
					$toPage = null;
		
					if(strtolower($data['carrier_id']) == 4) {
						$fromPage = 1;
						$toPage = 4;
					}else{
						$fromPage = 1;
						$toPage = 8;
					}
		
					$fields = [
						'pdfURL' => $billFileURL,
						'filename' => $filename,
						'fromPage' => $fromPage,
						'toPage' => $toPage,
					];
					$result = wp_remote_post('https://wirelessbutlerserver.com/pdf2txt/', 
						array(
							'method' 		=> 'POST',
							'timeout'     	=> 45,
							'httpversion' 	=> '1.0',
							'sslverify' 	=> false,
							'body' 			=> $fields
						)
					);
					if ( is_wp_error( $result ) ) {
						wp_send_json_error(array('error' => ['bill' => 'Some error in file reading. Try again later.']));
					} else {
						$text = $result['body'];
			
						$regexTblName = 'wireless_butler_regex';
						$regexTblName = $table_prefix . $regexTblName;
			
						$totalPlanCharges = 0;
						$result = $wpdb->get_results("SELECT * FROM ".$regexTblName." WHERE carrier_id = '".strtolower($data['carrier_id'])."'");
						foreach($result as $row) {
							if($row->key == 'totalPlanCharges' && strtolower($data['carrier_id']) == 1) {
			
								preg_match('/Billing period(.+?) - (.+?)Account/s', $text, $billing);
								$billingEndDate = isset($billing[2])? trim($billing[2]): '';
								$startDate = date('M j', strtotime($billingEndDate.' +1 day'));
								$d = date('t', strtotime($startDate));
								$endDate = date('M j', strtotime($billingEndDate.' +'.$d.' day'));
								$regex = $row->regex;
								$regex = str_replace('START_DATE', $startDate, $regex);
								$regex = str_replace('END_DATE', $endDate, $regex);
			
								preg_match_all($regex, $text, $regExResult);
								if(count($regExResult[$row->regex_index]) > 0) {
									foreach($regExResult[0] as $index=>$regRow) {
										if(strpos(strtolower($regRow), 'protect') === false) {
											$totalPlanCharges += $regExResult[$row->regex_index][$index];
										}
									}
								}
							} else if($row->key == 'lineCount' && strtolower($data['carrier_id']) == 1) {
								$lineCount = 1;
								preg_match($row->regex, $text, $regExResult);
								if(isset($regExResult[$row->regex_index]) && $regExResult[$row->regex_index] != '' && $regExResult[$row->regex_index] != NULL){
									$textExtracted = $regExResult[$row->regex_index];
									preg_match_all('/(\d+)-(\d+)-(\d+)/', $textExtracted, $lines);
									$lineCount = count($lines[0]);
								}
								$returnData[$row->key][] = $lineCount;
							} else if($row->key == 'totalPlanCharges' && strtolower($data['carrier_id']) == 4) {
								if($row->regex == '/Conditional/' ) {
									preg_match_all('/AutoPay Discount(\n.*)\$(.*)/', $text, $regExResult);
									if(isset($regExResult[2])) {
										foreach($regExResult[2] as $rowReg) {
											$totalPlanCharges += $rowReg;
										}
									}
								}else{
									preg_match($row->regex, $text, $regExResult);
									if(isset($regExResult[$row->regex_index]) && $regExResult[$row->regex_index] != '' && $regExResult[$row->regex_index] != NULL) {
										$totalPlanCharges += str_replace('$', '', $regExResult[$row->regex_index]);
									}
								}
							} else if(($row->key == 'lineCount' || $row->key == 'totalPlanCharges') && strtolower($data['carrier_id']) == 2) {
								preg_match($row->regex, $text, $regExResult);
								if(isset($regExResult[1])) {
									preg_match_all('/[0-9.]{12}/', $regExResult[1], $line); //8
									$lineCount = count($line);
									$returnData['lineCount'][] = $lineCount;

									preg_match_all('/Activity(\n.*)since(\n.*)last bill/', $text, $line); //8
									$hasActivityColumn = isset($line[0])? count($line[0]): 0;

									preg_match_all('/(\n.*)/', $regExResult[1], $eachLine); //8

									$planCost = 0;
									$lineAccessCost = 0;

									if(isset($eachLine[0])) {
										if($hasActivityColumn > 0) {
											foreach($eachLine[0] as $row) {
												preg_match_all('/(-?)\$(.+?) |-/', $row, $rowAmount); //8
												if(isset($rowAmount[2][1])) {
													preg_match('/Group/', $row, $groupText);
													if(isset($groupText[1])) {
														$planCost = $rowAmount[2][1];
													}else{
														$lineAccessCost += $rowAmount[2][1];
													}
												}
											}
										}else{
											foreach($eachLine[0] as $row) {
												preg_match('/(-?)\$(.+?) /', $row, $rowAmount); //8
												if(isset($rowAmount[2])) {
													preg_match('/Group/', $row, $groupText);
													if(isset($groupText[1])) {
														$planCost = $rowAmount[2];
													}else{
														$lineAccessCost += $rowAmount[2];
													}
												}
											}
										}
									}

									$totalPlanCost = $lineAccessCost + $planCost;
									$returnData['lineAccessCost'][] = $lineAccessCost;
									$returnData['planCost'][] = $planCost;
									$totalPlanCharges = $totalPlanCost;
								}
							} else if(($row->key == 'usedData' || $row->key == 'totalPlanData') && $row->regex == '/Conditional/' && strtolower($data['carrier_id']) == 2) {
								$value = 0;
								preg_match('/Usage summary([\s\S]*)myusage/', $text, $regExResult);
								if(isset($regExResult[0])) {
									preg_match_all('/(\n.*)/', $regExResult[0], $eachLine);
									if(isset($eachLine[0])) {
										foreach($eachLine[0] as $regRow) {
											preg_match('/(\d.*)GB (\(\w*\))/', $regRow, $rowAmount); //8
											if($row->key == 'usedData') {
												if(isset($rowAmount[1])) {
													$value += $rowAmount[1];
													$value = number_format((float)$value, 2, '.', '');
												}
											}else{
												if(isset($rowAmount[2])) {
													if($rowAmount[2] == '(unlimited)') {
														$value = '9999';
													}else{
														$value += $rowAmount[2];
														$value = number_format((float)$value, 2, '.', '');
													}
												}
											}
										}
									}
								}
								$returnData[$row->key][] = $value;
							} else if($row->key == 'deviceBalance' && strtolower($data['carrier_id']) == 2) {
								$deviceBalance = 0;
								preg_match_all('/Balance remaining \$(.*)/', $text, $regExResult);
								if(isset($regExResult[1])) {
									foreach($regExResult[1] as $regRow) {
										$deviceBalance += str_replace(",", "", $regRow);
									}
								}
								preg_match('/Balance remaining(.*)(\n.*)(\n.*)\$(.*)/', $text, $deviceInfo);
								if(isset($deviceInfo[4])) {
									$deviceBalance += str_replace(",", "", $deviceInfo[4]);
								}
								//Add promo balance
								preg_match('/Trade In Promo (.+?) of (.+?) (.*)\$(\d+\.\d+)/', $text, $promo);
								
								if(isset($promo[1]) && isset($promo[2]) && isset($promo[4])) {
									$deviceBalance += ($promo[2] - $promo[1]) * $promo[4];
								}
								$returnData[$row->key][] = $deviceBalance;
							} else if($row->key == 'deviceOwned' && strtolower($data['carrier_id']) == 2) {
								$deviceOwned = '';
								preg_match_all('/\n(.*)\n(.*)Established on/', $text, $regExResult);
								if(isset($regExResult[1])) {
									foreach($regExResult[1] as $regRow) {
										$explodeData = explode(' ', $regRow);
										if(count($explodeData) == 1) {
											//get multiple rows
											$deviceName = '';
											preg_match('/\n(.*)\n(.*)\n(.*)\nEstablished on/', $text, $deviceInfo);
											if(isset($deviceInfo[1])) {
												$deviceName .= ' '.$deviceInfo[1];
											}
											if(isset($deviceInfo[2])) {
												$deviceName .= ' '.$deviceInfo[2];
											}
											$deviceOwned .= ', '.trim($deviceName);
										}else{
											array_pop($explodeData);
											$deviceOwned .= ', '.implode(' ', $explodeData);
										}
									}
								}
								$returnData[$row->key][] = trim($deviceOwned, ',');
							} else if($row->key == 'deviceBalance' && strtolower($data['carrier_id']) == 1) {
								$deviceBalance = 0;
								preg_match_all('/\$(.*) remaining after/', $text, $regExResult);
								if(isset($regExResult[1])) {
									foreach($regExResult[1] as $regRow) {
										$deviceBalance += str_replace(",", "", $regRow);
									}
								}
								$returnData[$row->key][] = $deviceBalance;
							} else if($row->key == 'deviceOwned' && strtolower($data['carrier_id']) == 1) {
								$deviceOwned = array();
								preg_match('/Account Charges([\s\S]*)Your bill this month/', $text, $regExResult);
								if(isset($regExResult[1])) {
									preg_match_all('/(\d+)-(\d+)-(\d+)(\n.*)(\n.*)(\n.*)Save/', $regExResult[1], $lines);
									if(isset($lines[5])) {
										foreach($lines[5] as $regRow) {
											$deviceOwned[] = preg_replace('~[\r\n]+~', '', $regRow);
										}
									}

									preg_match_all('/(\d+)-(\d+)-(\d+)(\n.*)(\n.*)(\n.*)Monthly/', $regExResult[1], $lines);
									if(isset($lines[4])) {
										foreach($lines[4] as $index => $regRow) {
											if(strpos($regRow, '$') === false) {
												$deviceOwnerLine = trim($regRow, '\n');
												if(isset($lines[5])) {
													$deviceOwnerLine .= ' ';
													$deviceOwnerLine .= preg_replace('~[\r\n]+~', '', $lines[5][$index]);
												}
												$deviceOwned[] = $deviceOwnerLine;
											}else{
												$deviceOwned[] = preg_replace('~[\r\n]+~', '', $lines[5][$index]);
											}
										}
									}

									preg_match_all('/(\d+)-(\d+)-(\d+)(\n.*)(\n.*)(\n.*)(\n.*)Monthly/', $regExResult[1], $lines);
									if(isset($lines[4])) {
										foreach($lines[4] as $index => $regRow) {
											if(strpos($regRow, '$') === false) {
												$deviceOwnerLine = trim($regRow, '\n');
												if(isset($lines[5])) {
													$deviceOwnerLine .= ' ';
													$deviceOwnerLine .= preg_replace('~[\r\n]+~', '', $lines[5][$index]);
												}
												$deviceOwned[] = $deviceOwnerLine;
											}else{
												$deviceOwned[] = preg_replace('~[\r\n]+~', '', $lines[5][$index]);
											}
										}
									}
								}
								$returnData[$row->key][] = implode($deviceOwned, ',');
							} else if($row->key == 'deviceBalance' && strtolower($data['carrier_id']) == 4) {
								$deviceBalance = 0;
								preg_match_all('/(.*)(\n.*)ID: (.*)(\n.*)/', $text, $regExResult);
								if(isset($regExResult[3])) {
									foreach($regExResult[3] as $index => $rowReg) {
										preg_match('/(\d+) of (\d+)/', $rowReg, $rowRegResult);
										$amount = str_replace('$', '', str_replace('-', '', $regExResult[4][$index]));
										$deviceBalance += ($rowRegResult[2] - $rowRegResult[1]) * $amount;
									}
								}
								$returnData[$row->key][] = number_format((float)$deviceBalance, 2, '.', '');
							} else if($row->key == 'deviceOwned' && strtolower($data['carrier_id']) == 4) {
								preg_match_all('/(.*)(\n.*)ID: (.*)(\n.*)/', $text, $regExResult);
								$deviceOwnedArr = array();
								if(isset($regExResult[1])) {
									foreach($regExResult[1] as $index => $rowReg) {
										preg_match('/\((\d+)\) (\d+)-(\d+) (.*)/', $rowReg, $rowRegResult);
										if(isset($rowRegResult['4'])) {
											$deviceOwned = str_replace('T129 2019', '', $rowRegResult[4]);
											$deviceOwned = str_replace('Launch Trade and Save', '', $deviceOwned);
											$deviceOwnedArr[] = trim($deviceOwned);
										}else{
											$deviceOwnedArr[] = $rowReg;
										}
									}
								}
								$returnData[$row->key][] = implode($deviceOwnedArr, ',');
							} else {
								preg_match($row->regex, $text, $regExResult);
								if(isset($regExResult[$row->regex_index]) && $regExResult[$row->regex_index] != '' && $regExResult[$row->regex_index] != NULL){
									$value = trim($regExResult[$row->regex_index]);
									$value = str_replace('$', '', $value);
									$value = str_replace('GB', '', $value);
									$value = trim($value);
									if($row->key == 'totalPlanData' && strtolower($value) == 'unlimited'){
										$value = '9999';
									}
									$returnData[$row->key][] = $value;
								}
							}
						}	
					}
				}else{
					wp_send_json_error(array('error' => ['bill' => 'Some error in file upload. Try again later.']));
				}
			}else{
				wp_send_json_error(array('error' => ['bill' => 'Please upload pdf file only']));
			}
		}

		$tblname = 'wireless_butler_customer';
		$wp_table = $table_prefix . $tblname;

		$wpdb->insert($wp_table, array(
			'firstName' 					=> $data['fname'],
			'lastName' 						=> $data['lname'],
			'email' 						=> $data['email'],
			'phoneNumber' 					=> $data['phone']?? '',
			'carrier' 						=> $data['carrier_id'],
			'manual' 						=> $manual,
			'wirelessBillURL' 				=> $billFileURL,
			'billDate' 						=> isset($returnData['billingPeriod'])? date('Y-m-d', strtotime($returnData['billingPeriod'][0])):NULL,
			'accountNumber' 				=> isset($returnData['accountNumber'])? implode(',', $returnData['accountNumber']):NULL,
			'extractedTotalBills' 			=> isset($returnData['totalBill'])? implode(',', $returnData['totalBill']):NULL,
			'extractedLatestMonthBills' 	=> isset($returnData['latestMonthBill'])? implode(',', $returnData['latestMonthBill']):NULL,
			'extractedPastDues' 			=> isset($returnData['pastDue'])? implode(',', $returnData['pastDue']):NULL,
			'extractedTotalPlanCosts' 		=> $totalPlanCharges,
			'extractedTotalGbUsage' 		=> isset($returnData['usedData'])? implode(',', $returnData['usedData']):NULL,
			'extractedGbAllowance' 			=> isset($returnData['totalPlanData'])? implode(',', $returnData['totalPlanData']):NULL,
			'lineCount'						=> isset($returnData['lineCount'][0])? $returnData['lineCount'][0] : 1,
			'autoPayWithCarrier' 			=> $data['auto_pay_with_carrier'],
			'inMilitary' 					=> $data['in_military'],
			'over55YearOfAge' 				=> $data['over_55_year_of_age'],
			'basicPhoneCount' 				=> $data['basic_phone'],
			'tabletCount' 					=> $data['tablet'],
			'mhsCount' 						=> $data['mobile_hotspot'],
			'connectedDeviceCount' 			=> $data['wearable'],
			'CreatedAt' 					=> date('Y-m-d H:i:s'),
			'extractedDeviceBalance' 		=> isset($returnData['deviceBalance'][0])? $returnData['deviceBalance'][0] : 0,
			'extractedDeviceOwned' 			=> isset($returnData['deviceOwned'][0])? $returnData['deviceOwned'][0] : 0,
		));

		$billNumber 					= $wpdb->insert_id;
		$returnData['id'] 				= $billNumber;
		$returnData['totalPlanCharges'] = $totalPlanCharges;

		//update billNumber
		$wpdb->update($wp_table, array(
			'billNumber' 			=> $billNumber,
		), array('id' => $billNumber));

		$customerRow = $wpdb->get_row("SELECT * FROM ".$wp_table." WHERE id = ".$billNumber, 'ARRAY_A');
		$customerRow['action'] = 'wireless_butler_sync_customer';
		$customerRow['sourceDomain'] = get_site_url();
		$result = wp_remote_post('https://wirelessbutlerserver.com/wp/wp-admin/admin-post.php', 
			array(
				'method' 		=> 'POST',
				'timeout'     	=> 45,
				'httpversion' 	=> '1.0',
				'sslverify' 	=> false,
				'body' 			=> $customerRow
			)
		);
		
		wp_send_json_success($returnData);
	}

	/**
	 * Handle form submit
	 */
	function handle_form_1_step_2_submit() {

		global $table_prefix, $wpdb;

		$data = array(
			'totalBill' 		=> sanitize_text_field($_POST['totalBill']),
			'latestMonthBill' 	=> sanitize_text_field($_POST['latestMonthBill']),
			'pastDue' 			=> sanitize_text_field($_POST['pastDue']),
			'usedData' 			=> sanitize_text_field($_POST['usedData']),
			'totalPlanData'		=> sanitize_text_field($_POST['totalPlanData']),
			'totalPlanCharges' 	=> sanitize_text_field($_POST['totalPlanCharges']),
			'rowID' 			=> sanitize_text_field($_POST['rowID']),
			'deviceBalance' 	=> sanitize_text_field($_POST['deviceBalance']),
			'deviceOwned' 		=> sanitize_text_field($_POST['deviceOwned']),
		);
		$returnData = array();

		$customerTblName 		= $table_prefix . 'wireless_butler_customer';
		$carrierTblName 		= $table_prefix . 'wireless_butler_carrier';
		$planTblName 			= $table_prefix . 'wireless_butler_plan_database';
		$recommendationTblName 	= $table_prefix . 'wireless_butler_recommendation';

		$wpdb->update($customerTblName, array(
			'totalBill' 		=> $data['totalBill'],
			'latestMonthBill' 	=> $data['latestMonthBill'],
			'pastDue' 			=> $data['pastDue'],
			'totalGbUsage' 		=> $data['usedData'],
			'gBAllowance' 		=> $data['totalPlanData'],
			'totalPlanCost' 	=> $data['totalPlanCharges'],
			'deviceBalance' 	=> $data['deviceBalance'],
			'deviceOwned' 		=> $data['deviceOwned'],
		), array('id' => $data['rowID']));
		
		$customerRow = $wpdb->get_row("SELECT * FROM ".$customerTblName." WHERE id = ".$data['rowID'], 'ARRAY_A');
		$customerRow['action'] = 'wireless_butler_sync_customer';
		$customerRow['sourceDomain'] = get_site_url();
		$result = wp_remote_post('https://wirelessbutlerserver.com/wp/wp-admin/admin-post.php', 
			array(
				'method' 		=> 'POST',
				'timeout'     	=> 45,
				'httpversion' 	=> '1.0',
				'sslverify' 	=> false,
				'body' 			=> $customerRow
			)
		);

		$row = $wpdb->get_row("SELECT e.*, c.name as carrier, c.carrier_id as carrier_id FROM ".$customerTblName." as e LEFT JOIN ".$carrierTblName." as c ON c.carrier_id=e.carrier WHERE e.id = ".$data['rowID']);

		//RecommendedSavings
		$RecommendedSavings = $row->RecommendedSavings;

		//delete old recommendation from DB
		$sql = "DELETE FROM " . $recommendationTblName ." WHERE customer_id=".$row->id ;
		$wpdb->query( $sql );

		//add enrtries in recommendation table
		$planRows = $wpdb->get_results("SELECT * FROM ".$planTblName);
		if(count($planRows) > 0) {
			foreach($planRows as $plan)
			{
				$isCarrier = $this->isCarrier($plan, $row);
				$isPlanAllowed = $this->isPlanAllowed($plan, $row);
				$CostForThisPlan = $this->CostForThisPlan($plan, $row);
				$ThisPlanWithAutoPayCredits = $this->ThisPlanWithAutoPayCredits($plan, $row, $CostForThisPlan);
				$ThisPlanWithSpecialWorkerCredits = $this->ThisPlanWithSpecialWorkerCredits($plan, $row, $CostForThisPlan);
				$ThisPlanWithAutoPayANDSpecialWorkerCredits = $this->ThisPlanWithAutoPayANDSpecialWorkerCredits($plan, $row, $CostForThisPlan);
				$ThisPlanWithSeniorCredits = $this->ThisPlanWithSeniorCredits($plan, $row, $CostForThisPlan);
				$ThisPlanWithAutoPayANDSeniorCredits = $this->ThisPlanWithAutoPayANDSeniorCredits($plan, $row, $CostForThisPlan);
				$isCheapestPlanAndSameCarrier = '0';
				$CheapeastPlanCosts = $this->CheapeastPlanCosts($plan, $row, $ThisPlanWithAutoPayCredits, $ThisPlanWithSpecialWorkerCredits, $ThisPlanWithAutoPayANDSpecialWorkerCredits, $ThisPlanWithSeniorCredits, $ThisPlanWithAutoPayANDSeniorCredits, $CostForThisPlan);
	
				$wpdb->insert($recommendationTblName, array(
					'customer_id' 											=> $row->id,
					'AccountNumber' 										=> $row->accountNumber,
					'UploadedPlanCosts' 									=> $row->totalPlanCost,
					'UploadedLineCount' 									=> $row->lineCount,
					'isCarrier' 											=> $isCarrier,
					'isPlanAllowed' 										=> $isPlanAllowed,
					'CheapeastPlanCosts' 									=> $CheapeastPlanCosts,
					'CostForThisPlan' 										=> $CostForThisPlan,
					'isCheapestPlanAndSameCarrier' 							=> $isCheapestPlanAndSameCarrier,
					'ThisPlanWithAutoPayCredits' 							=> $ThisPlanWithAutoPayCredits,
					'ThisPlanWithSeniorCredits' 							=> $ThisPlanWithSeniorCredits,
					'ThisPlanWithAutoPayANDSeniorCredits' 					=> $ThisPlanWithAutoPayANDSeniorCredits,
					'ThisPlanWithSpecialWorkerCredits' 						=> $ThisPlanWithSpecialWorkerCredits,
					'ThisPlanWithAutoPayANDSpecialWorkerCredits' 			=> $ThisPlanWithAutoPayANDSpecialWorkerCredits,
					'SavingsForThisPlan' 									=> $this->SavingsForThisPlan($row, $CostForThisPlan),
					'SavingsForThisPlanWithAutoPay' 						=> $this->SavingsForThisPlanWithAutoPay($row, $ThisPlanWithAutoPayCredits),
					'SavingsForThisPlanWithSpecialWorker' 					=> $this->SavingsForThisPlanWithSpecialWorker($row, $ThisPlanWithSpecialWorkerCredits),
					'SavingsForThisPlanWithAutoPayAndSpecialWorker' 		=> $this->SavingsForThisPlanWithAutoPayAndSpecialWorker($row, $ThisPlanWithAutoPayANDSpecialWorkerCredits),
					'PlanId' 												=> $plan->PlanId,
					'CarrierId' 											=> $plan->CarrierId,
					'PlanName' 												=> $plan->PlanName,
					'GBAllowance' 											=> $plan->GBAllowance,
					'MhsGbAllowance' 										=> isset($plan->MhsGbAllowance)? $plan->MhsGbAllowance : 0,
					'HostPlanCharge' 										=> $plan->HostPlanCharge,
					'Line1AccessCharge' 									=> $plan->Line1AccessCharge,
					'Line2AccessCharge' 									=> $plan->Line2AccessCharge,
					'Line3AccessCharge' 									=> $plan->Line3AccessCharge,
					'Line4AccessCharge' 									=> $plan->Line4AccessCharge,
					'Line5AccessCharge' 									=> $plan->Line5AccessCharge,
					'TabletOrMHSLineAccessCharge' 							=> $plan->TabletOrMHSLineAccessCharge,
					'WearableLineAccessCharge' 								=> $plan->WearableLineAccessCharge,
					'IsHostPlanDIscountable' 								=> $plan->IsHostPlanDIscountable,
					'LineAccessAutoPayCreditMaxLines' 						=> $plan->LineAccessAutoPayCreditMaxLines,
					'SpecialWorkerLineAccessCredit' 						=> $plan->SpecialWorkerLineAccessCredit,
					'IsTaxFree' 											=> $plan->IsTaxFree,
					'Line1AccessAutoPayCredit' 								=> $plan->Line1AccessAutoPayCredit,
					'Line2AccessAutoPayCredit' 								=> $plan->Line2AccessAutoPayCredit,
					'Line3AccessAutoPayCredit' 								=> $plan->Line3AccessAutoPayCredit,
					'Line4AccessAutoPayCredit' 								=> $plan->Line4AccessAutoPayCredit,
					'HasOverage' 											=> $plan->HasOverage,
					'GbOverageUnit' 										=> $plan->GbOverageUnit,
					'OverageAmount' 										=> $plan->OverageAmount,
					'CanChooseOverage' 										=> $plan->CanChooseOverage,
					'IsStacked' 											=> $plan->IsStacked,
					'IsShared' 												=> $plan->IsShared,
					'Has5GAccess' 											=> $plan->Has5GAccess,
					'5gAccessCharge' 										=> $plan->{'5gAccessCharge'},
					'MusicServicesValue' 									=> $plan->MusicServicesValue,
					'MovieTVServicesValue' 									=> $plan->MovieTVServicesValue,
					'ModifiedDate' 											=> $plan->ModifiedDate,
					'VerificationDate' 										=> $plan->VerificationDate,
					'ExpirationDate' 										=> $plan->ExpirationDate,
					'MaxLineCount' 											=> $plan->MaxLineCount,
					'SubsidizedFee' 										=> $plan->SubsidizedFee,
					'DeviceType' 											=> $plan->DeviceType,
					'MinimumLineCount' 										=> $plan->MinimumLineCount,
					'CreatedAt' 											=> date('Y-m-d H:i:s'),
				));
			}

			//calculation for isCheapestPlanAndSameCarrier
			$recommendations = $wpdb->get_results("SELECT * FROM ".$recommendationTblName." WHERE customer_id=".$row->id);
			foreach($recommendations as $recommendation) {
				$isCheapestPlanAndSameCarrier = $this->isCheapestPlanAndSameCarrier($recommendation);
				$wpdb->update($recommendationTblName, array(
					'isCheapestPlanAndSameCarrier' 		=> $isCheapestPlanAndSameCarrier,
				), array('id' => $recommendation->id));
			}

			//update customer row for recommendation
			$recommendation = $wpdb->get_row("SELECT * FROM ".$recommendationTblName." WHERE customer_id=".$row->id." AND isCheapestPlanAndSameCarrier='1'");
			if($recommendation) {
				$RecommendedSavings = ($row->totalPlanCost - $recommendation->CheapeastPlanCosts);
				$wpdb->update($customerTblName, array(
					'RecommendedPlanName' 		=> $recommendation->PlanName,
					'RecommendedPlanCost' 		=> $recommendation->CheapeastPlanCosts,
					'RecommendedGbAllowance' 	=> $recommendation->GBAllowance,
					'RecommendedSavings' 		=> $RecommendedSavings,
				), array('id' => $row->id));
			}
		}

		$suggestionText = esc_attr(get_option('wireless_butler_form_1_step_2_chepest_plan_text'));
		if($RecommendedSavings > 0 && $RecommendedSavings != NULL && $RecommendedSavings != '') {
			$suggestionText = str_replace('[MONTHLY_SAVING]', $RecommendedSavings, esc_attr(get_option('wireless_butler_form_1_step_2_heading')));
		}

		//send email notification
		$notificationEmail = esc_attr(get_option('wireless_butler_notification_email'));
		if($notificationEmail != NULL && $notificationEmail != '') {
			$emailContent = esc_html(get_option("wireless_butler_form_notification_template"));
			$emailContent = str_replace('[FIRST_NAME]', $row->firstName, $emailContent);
			$emailContent = str_replace('[LAST_NAME]', $row->lastName, $emailContent);
			$emailContent = str_replace('[EMAIL]', $row->email, $emailContent);
			$emailContent = str_replace('[PHONE]', $row->phoneNumber, $emailContent);
			$emailContent = str_replace('[CARRIER]', $row->carrier, $emailContent);
			$emailContent = str_replace('[WIRELESS_BILL]', $row->wirelessBillURL, $emailContent);
			$emailContent = str_replace('[TOTAL_BILL]', $data['totalBill'], $emailContent);
			$emailContent = str_replace('[LATEST_MONTH_BILL]', $data['latestMonthBill'], $emailContent);
			$emailContent = str_replace('[PAST_DUE]', $data['pastDue'], $emailContent);
			$emailContent = str_replace('[TOTAL_PLAN_CHARGES]', $data['totalPlanCharges'], $emailContent);
			$emailContent = str_replace('[USED_DATA]', $data['usedData'], $emailContent);
			$emailContent = str_replace('[TOTAL_PLAN_DATA]', $data['totalPlanData'], $emailContent);
			$emailContent = str_replace('[SAVINGS_AMOUNT]', $RecommendedSavings, $emailContent);
			$emailContent = str_replace('[DEVICE_BALANCE]', $data['deviceBalance'], $emailContent);
			$emailContent = str_replace('[DEVICE_OWNED]', $data['deviceOwned'], $emailContent);
			$emailContent = str_replace('[RECOMMENDATION_URL]', admin_url('admin.php?page=wireless_butler_customer_recommendation_plan&id='.$row->id), $emailContent);
			wp_mail($notificationEmail, 'Wireless Butler Email Notification', $emailContent);
		}

		//send email to user
		$emailToUserSubject = esc_attr(get_option('wireless_butler_email_to_user_subject'));
		$emailToUserContent = esc_html(get_option('wireless_butler_email_to_user_content'));
		$content = str_replace('[FIRST_NAME]', $row->firstName, $emailToUserContent);

		//Name that will be used with email to send info to customer
		$adminName = get_option('wireless_butler_admin_name');

		//Email that will be used to send info to customer
		$adminEmail = get_option('wireless_butler_admin_mail');

		$headers = array('From: '.$adminName.' <'.$adminEmail.'>');
		wp_mail($row->email, $emailToUserSubject, $content, $headers);

		//check powered by is enabled for domain or not
		$domainKey = get_bloginfo('url');
		$domainKey = str_replace('https://', '', $domainKey);
		$domainKey = str_replace('http://', '', $domainKey);
		$domainKey = str_replace('www.', '', $domainKey);

		$remoteGet = wp_remote_get('https://wirelessbutlerserver.com/wp/wp-json/poweredBy/wb?domain='.$domainKey);
		if ( is_array( $remoteGet ) && ! is_wp_error( $remoteGet ) ) {
			$body   = $remoteGet['body']; // use the content
			$body 	= json_decode($body);
			if($body->status == 'success') {
				if($body->poweredBy == '1') {
					$suggestionText .= '<span class="powered_text">Powered by Validas</span>';
				}
			}
		}

		$returnData['suggestionText'] = $suggestionText;
		wp_send_json($returnData);
	}

	function isCarrier($plan, $row)
	{
		/**
		 * =IF(M2=$'Customer Database'.C$3,1)
		 */
		return ($plan->CarrierId == $row->carrier_id)? '1':'0';
	}

	function isPlanAllowed($plan, $row)
	{
		/**
		 * =IF($'Customer Database'.K$3-AU2<0,0,
		 * IF($'Customer Database'.U$3>0,1,
		 * IF($'UI Pre Upload'.D$9>0,1,
		 * IF($'UI Pre Upload'.E$9>0,1,
		 * IF($'UI Pre Upload'.E$9>0,1,
		 * IF(AK2+AL2>0,1))))))
		 */
		$result = '0';
		if($row->lineCount - $plan->MinimumLineCount < 0){
			$result = '0';
		}else{
			if($row->basicPhoneCount > 0){
				$result = '1';
			}else{
				// IF(AK2+AL2>0,1)
				if($plan->IsStacked + $plan->IsShared > 0) {
					$result = '1';
				}else{
					$result = '0';
				}
			}
		}
		return $result;
	}

	function CheapeastPlanCosts($plan, $row, $ThisPlanWithAutoPayCredits, $ThisPlanWithSpecialWorkerCredits, $ThisPlanWithAutoPayANDSpecialWorkerCredits, $ThisPlanWithSeniorCredits, $ThisPlanWithAutoPayANDSeniorCredits, $CostForThisPlan)
	{
		/**
		 * =IF(AND($'UI Pre Upload'.C$5=1,$'UI Pre Upload'.C$6=0,$'UI Pre Upload'.C$7=0),I2,IF(AND($'UI Pre Upload'.C$5=0,$'UI Pre Upload'.C$6=1),this plan with special worker credits,IF(AND($'UI Pre Upload'.C$5=1,$'UI Pre Upload'.C$6=1),this plan with auto pay and special worker credits,IF(AND($'UI Pre Upload'.C$5=0,$'UI Pre Upload'.C$7=1),J2,IF(AND($'UI Pre Upload'.C$5=1,$'UI Pre Upload'.C$7=1),K2,G2)))))
		 * 
		 */
		$result = NULL;
		if($row->autoPayWithCarrier == '1' && $row->inMilitary == '0' && $row->over55YearOfAge == '0'){
			$result = $ThisPlanWithAutoPayCredits;
		}else{
			if($row->autoPayWithCarrier == '0' && $row->inMilitary == '1') {
				$result = $ThisPlanWithSpecialWorkerCredits;
			}else{
				if($row->autoPayWithCarrier == '1' && $row->inMilitary == '1') {
					$result = $ThisPlanWithAutoPayANDSpecialWorkerCredits;
				}else{
					if($row->autoPayWithCarrier == '0' && $row->over55YearOfAge == '1') {
						$result = $ThisPlanWithSeniorCredits;
					}else{
						if($row->autoPayWithCarrier == '1' && $row->over55YearOfAge == '1') {
							$result = $ThisPlanWithAutoPayANDSeniorCredits;
						}else{
							$result = $CostForThisPlan;
						}
					}
				}
			}
		}
		return $result;
	}

	function CostForThisPlan($plan, $row)
	{
		/**
		 * =IF(AL2=1,hostplancharge+(R2*$'Customer Database'.K$3)+((IF($'Customer Database'.I$3>gballowance,($'Customer Database'.I$3-gballowance)/AH2,0)*AI2)),IF(AK2=1,IF($'Customer Database'.K$3=1,R2,IF($'Customer Database'.K$3=2,R2+S2,IF($'Customer Database'.K$3=3,R2+S2+T2,IF($'Customer Database'.K$3=4,R2+S2+T2+U2,IF($'Customer Database'.K$3=5,R2+S2+T2+U2+V2))))),(hostplancharge*$'Customer Database'.K$3)+(IF($'Customer Database'.I$3>gballowance,ROUNDUP(($'Customer Database'.I$3-gballowance)/AH2,0),0)*AI2)))
		 */
		$result = NULL;
		if($plan->IsShared == '1') {
			$gbAllowanceCalculated = 0;
			if($row->totalGbUsage > $plan->GBAllowance) {
				$gbAllowanceCalculated = ($row->totalGbUsage - $plan->GBAllowance)/$plan->GbOverageUnit;
			}
			$result = $plan->HostPlanCharge + ($plan->Line1AccessCharge * $row->lineCount) + ($gbAllowanceCalculated * $plan->OverageAmount);
		}else{
			if($plan->IsStacked == '1') {
				if($row->lineCount == 1) {
					$result = $plan->Line1AccessCharge;
				}else{
					if($row->lineCount == 2) {
						$result = $plan->Line1AccessCharge + $plan->Line2AccessCharge;
					}else{
						if($row->lineCount == 3) {
							$result = $plan->Line1AccessCharge + $plan->Line2AccessCharge + $plan->Line3AccessCharge;
						}else{
							if($row->lineCount == 4) {
								$result = $plan->Line1AccessCharge + $plan->Line2AccessCharge + $plan->Line3AccessCharge + $plan->Line4AccessCharge;
							}else{
								if($row->lineCount == 5) {	
									$result = $plan->Line1AccessCharge + $plan->Line2AccessCharge + $plan->Line3AccessCharge + $plan->Line4AccessCharge + $plan->Line5AccessCharge;
								}else{
									
								}
							}
						}
					}
				}
			}else{
				$gbAllowanceCalculated = 0;
				if($row->totalGbUsage - $plan->GBAllowance) {
					$gbAllowanceCalculated = round($row->totalGbUsage - $plan->GBAllowance);
				}
				$result = ($plan->HostPlanCharge * $row->lineCount) + ($gbAllowanceCalculated * $plan->OverageAmount);
			}
		}
		return $result;
	}

	function isCheapestPlanAndSameCarrier($recommendationRow)
	{
		/**
		 * =IF(D2=TRUE,G2=MINIFS(G:G,O:O,O2,E:E,TRUE),FALSE)
		 */
		$result = '0';
		if($recommendationRow->isCarrier == '1' && $recommendationRow->isPlanAllowed == '1') {
			global $table_prefix, $wpdb;
			$recommendationTblName 	= $table_prefix . 'wireless_butler_recommendation';

			$minif = $wpdb->get_row("SELECT * FROM ".$recommendationTblName." WHERE customer_id=".$recommendationRow->customer_id." AND CarrierId=".$recommendationRow->CarrierId." AND isPlanAllowed='1' AND CheapeastPlanCosts > 0  ORDER BY CheapeastPlanCosts ASC LIMIT 0,1");
			if(floatval($recommendationRow->CheapeastPlanCosts) == floatval($minif->CheapeastPlanCosts)) {
				$result = '1';
			}
		}
		return $result;
	}

	function ThisPlanWithAutoPayCredits($plan, $row, $CostForThisPlan)
	{
		/**
		 * =G2-(IF(AC2="null", 0, IF($'Customer Database'.K$3=1,AC2,IF($'Customer Database'.K$3=2,AC2+AD2,IF($'Customer Database'.K$3=3,AC2+AD2+AE2,IF($'Customer Database'.K$3=4,AC2+AD2+AE2+AF2))))))
		 */
		$calculation = 0;
		if($plan->Line1AccessAutoPayCredit == 'null' || $plan->Line1AccessAutoPayCredit == NULL) {
			$calculation = 0;
		}else{
			if($row->lineCount == 1)
			{
				$calculation = $plan->Line1AccessAutoPayCredit;
			}else{
				if($row->lineCount == 2)
				{
					$calculation = $plan->Line1AccessAutoPayCredit + $plan->Line2AccessAutoPayCredit;
				}else{
					if($row->lineCount == 3)
					{
						$calculation = $plan->Line1AccessAutoPayCredit + $plan->Line2AccessAutoPayCredit + $plan->Line3AccessAutoPayCredit;
					}else{
						if($row->lineCount == 4)
						{
							$calculation = $plan->Line1AccessAutoPayCredit + $plan->Line2AccessAutoPayCredit + $plan->Line3AccessAutoPayCredit + $plan->Line4AccessAutoPayCredit;
						}
					}
				}
			}
		}
		$result = $CostForThisPlan - $calculation;
		return $result;
	}

	function ThisPlanWithSeniorCredits($plan, $row, $CostForThisPlan)
	{
		/**
		 * =G2-($'Customer Database'.K$3*AI2)
		 */
		$result = $CostForThisPlan - ($row->lineCount * $plan->Line1SeniorCredit);
		return $result;
	}

	function ThisPlanWithAutoPayANDSeniorCredits($plan, $row, $CostForThisPlan)
	{
		/**
		 * =G2-(IF(AE2="null", 0, IF($'Customer Database'.K$3=1,AE2,IF($'Customer Database'.K$3=2,AE2+AF2,IF($'Customer Database'.K$3=3,AE2+AF2+AG2,IF($'Customer Database'.K$3=4,AE2+AF2+AG2+AH2))))))-($'Customer Database'.K$3*AC2)
		 */
		$calculation = 0;
		if($plan->Line1AccessAutoPayCredit != NULL) {
			if($row->lineCount == 1) {
				$calculation = $plan->Line1AccessAutoPayCredit;
			}else{
				if($row->lineCount == 2) {
					$calculation = $plan->Line1AccessAutoPayCredit + $plan->Line2AccessAutoPayCredit;
				}else{
					if($row->lineCount == 3) {
						$calculation = $plan->Line1AccessAutoPayCredit + $plan->Line2AccessAutoPayCredit + $plan->Line3AccessAutoPayCredit;
					}else{
						if($row->lineCount == 4) {
							$calculation = $plan->Line1AccessAutoPayCredit + $plan->Line2AccessAutoPayCredit + $plan->Line3AccessAutoPayCredit + $plan->Line4AccessAutoPayCredit;
						}
					}
				}
			}
		}
		$result = $CostForThisPlan - $calculation - ($row->lineCount * $plan->SpecialWorkerLineAccessCredit);
		return $result;
	}

	function ThisPlanWithSpecialWorkerCredits($plan, $row, $CostForThisPlan)
	{
		/**
		 * =G2-($'Customer Database'.K$3*AA2)
		 */
		$result = $CostForThisPlan - ($row->lineCount * $plan->SpecialWorkerLineAccessCredit);
		return $result;
	}

	function ThisPlanWithAutoPayANDSpecialWorkerCredits($plan, $row, $CostForThisPlan)
	{
		/**
		 * =G2-(IF(AC2="null", 0, IF($'Customer Database'.K$3=1,AC2,IF($'Customer Database'.K$3=2,AC2+AD2,IF($'Customer Database'.K$3=3,AC2+AD2+AE2,IF($'Customer Database'.K$3=4,AC2+AD2+AE2+AF2))))))-($'Customer Database'.K$3*AA2)
		 */
		$calculation = 0;
		if($plan->Line1AccessAutoPayCredit == NULL || $plan->Line1AccessAutoPayCredit == 'null') {
			$calculation = 0;
		}else{
			if($row->lineCount == 1) {
				$calculation = $plan->Line1AccessAutoPayCredit;
			}else{
				if($row->lineCount == 2) {
					$calculation = $plan->Line1AccessAutoPayCredit + $plan->Line2AccessAutoPayCredit;
				}else{
					if($row->lineCount == 3) {
						$calculation = $plan->Line1AccessAutoPayCredit + $plan->Line2AccessAutoPayCredit + $plan->Line3AccessAutoPayCredit;
					}else{
						if($row->lineCount == 4) {
							$calculation = $plan->Line1AccessAutoPayCredit + $plan->Line2AccessAutoPayCredit + $plan->Line3AccessAutoPayCredit + $plan->Line4AccessAutoPayCredit;
						}
					}
				}
			}
		}
		$result = $CostForThisPlan - $calculation - ($row->lineCount * $plan->SpecialWorkerLineAccessCredit);
		return $result;
	}

	function SavingsForThisPlan($row, $CostForThisPlan)
	{
		/**
		 * =B2-G2
		 */
		return $row->totalPlanCost - $CostForThisPlan;
	}

	function SavingsForThisPlanWithAutoPay($row, $ThisPlanWithAutoPayCredits)
	{
		/**
		 * =B2-I2`
		 */
		return $row->totalPlanCoast - $ThisPlanWithAutoPayCredits;
	}

	function SavingsForThisPlanWithSpecialWorker($row, $ThisPlanWithSpecialWorkerCredits)
	{
		/**
		 * =B2-J2
		 */
		return $row->totalPlanCost - $ThisPlanWithSpecialWorkerCredits;
	}

	function SavingsForThisPlanWithAutoPayAndSpecialWorker($row, $ThisPlanWithAutoPayANDSpecialWorkerCredits)
	{
		/**
		 * =B2-K2
		 */
		return $row->totalPlanCost - $ThisPlanWithAutoPayANDSpecialWorkerCredits;
	}
}
