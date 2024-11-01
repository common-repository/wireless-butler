<?php

/**
 * The admin-plan-db functionality of the plugin.
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/admin
 */

/**
 * The admin-plan-db functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-plan-db stylesheet and JavaScript.
 *
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/admin
 * @author     Jai Awasthi <jay.awasthi@gmail.com>
 */

class Wireless_Butler_Admin_Plan_DB extends WP_List_Table {

	/**
	 * Prepare table list items
	 */
	function prepare_items() {
		$columns      = $this->get_columns();
		$hidden       = array();
		$sortable     = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$items        = $this->table_data();

		$per_page     = 50;
		$current_page = $this->get_pagenum();
		$total_items  = count($items);

		// only necessary because we have sample data
		$data = array_slice( $items, (( $current_page - 1 ) * $per_page ), $per_page );

		$this->set_pagination_args( array(
		  'total_items' => $total_items,                  //WE have to calculate the total number of items
		  'per_page'    => $per_page                     //WE have to determine how many items to show on a page
		) );
		$this->items  = $data;
	}

	/**
	 * DB query to list regex list
	 */
	function table_data() {
		global $wpdb;
		$tblname = $wpdb->prefix . 'wireless_butler_plan_database';

		$sql = 'SELECT * ';
		$sql .= ' FROM ' . $tblname;
 		$sql .= ' ORDER BY id ASC';
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
 		return $result;
	}

	/**
	 * Regex WP list Column name
	 */
	public function get_columns() {
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
            'MaxLineCount'                      => 'MaxLineCount',
            'SubsidizedFee'                     => 'SubsidizedFee',
            'DeviceType'                      	=> 'DeviceType',
            'MinimumLineCount'                  => 'MinimumLineCount'
		);

		return $columns;
	}

	/**
	 * WP list column values
	 */
	function column_default( $item, $column_name ) {
		//FloatColumns
		$floatColumns = ['HostPlanCharge', 'Line1AccessCharge', 'Line2AccessCharge', 'Line3AccessCharge', 'Line4AccessCharge', 'Line5AccessCharge', 'TabletOrMHSLineAccessCharge', 'WearableLineAccessCharge', 'Line1AccessAutoPayCredit', 'Line2AccessAutoPayCredit', 'Line3AccessAutoPayCredit', 'Line4AccessAutoPayCredit', 'Line1SeniorCredit', 'Line2SeniorCredit', 'Line3SeniorCredit', 'Line4SeniorCredit', 'Line1SpecialWorkerCredit', 'Line2SpecialWorkerCredit', 'Line3SpecialWorkerCredit', 'Line4SpecialWorkerCredit', 'OverageAmount', '5gAccessCharge', 'MusicServicesValue', 'MovieTVServicesValue', 'SubsidizedFee'];
		$dateColumns = ['ModifiedDate', 'VerificationDate', 'ExpirationDate'];

		if(in_array($column_name, $floatColumns)) {
			return floatval($item[$column_name]);
		}else if(in_array($column_name, $dateColumns)) {
			return ($item[$column_name] != NULL)? date('m/d/y', strtotime($item[$column_name])):"";
		}else{
			return $item[ $column_name ];
		}
	}

	/**
	 * No Item 
	 */
	public function no_items() {
		_e( 'No item avaliable.', 'wireless_butler' );
	}
}