<?php

/**
 * The admin-regex-list functionality of the plugin.
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/admin
 */

/**
 * The admin-regex-list functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-regex-list stylesheet and JavaScript.
 *
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/admin
 * @author     Jai Awasthi <jay.awasthi@gmail.com>
 */

class Wireless_Butler_Admin_Regex_list extends WP_List_Table {

	/**
	 * Field Keys
	 *
	 * @keys array
	 */
	public $keys = [
		'billingPeriod' 	=> 'Biling Period',
		'accountNumber' 	=> 'Account Number',
		'totalBill' 		=> 'Total Bill',
		'latestMonthBill' 	=> 'Latest Month Bill',
		'pastDue' 			=> 'Past Due',
		'totalPlanCharges' 	=> 'Total Plan Cost',
		'usedData' 			=> 'Used Data',
		'totalPlanData' 	=> 'Total Data',
		'lineCount' 		=> 'Line Count',
		'deviceBalance' 	=> 'Device Balance',
		'deviceOwned' 		=> 'Device Owned',
	];

	/**
	 * Prepare table list items
	 */
	function prepare_items() {
		$columns      = $this->get_columns();
		$hidden       = array();
		$sortable     = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();
		$items        = $this->table_data();

		$per_page     = 10;
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
	 * Regex list table filter
	 */
	protected function get_views() { 
		$views = array();
		$current = ( !empty($_REQUEST['key']) ? sanitize_text_field($_REQUEST['key']) : 'all');

		//All link
		$class = ($current == 'all' ? ' class="current"' :'');
		$all_url = remove_query_arg('key');
		$views['all'] = "<a href='{$all_url }' {$class} >All</a>";

		foreach($this->keys as $index=>$value) {
			$linkUrl = add_query_arg('key', $index);
			$class = ($current == $index ? ' class="current"' :'');
			$views[$index] = "<a href='{$linkUrl}' {$class} >" . $value . "</a>";
		}

		return $views;
	}

	/**
	 * DB query to list regex list
	 */
	function table_data() {
		global $wpdb;
		$carrierTblName = $wpdb->prefix . 'wireless_butler_carrier';
		$regexTblName = $wpdb->prefix . 'wireless_butler_regex';

		$key = ( isset($_REQUEST['key']) ? sanitize_text_field($_REQUEST['key']) : '');

		$sql = 'SELECT r.*, c.name as carrier ';
		$sql .= ' FROM ' . $regexTblName . ' as r';
		$sql .= ' LEFT JOIN ' . $carrierTblName . ' as c ON c.carrier_id=r.carrier_id';
		if($key != '') {
			$sql .= " WHERE r.`key` = '" . $key . "'";
		}
 		$sql .= ' ORDER BY r.id DESC';
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
 		return $result;
	}

	/**
	 * Regex WP list Column name
	 */
	public function get_columns() {
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'title'       	=> 'Title',
			'carrier'    	=> 'Carrier',
			'regex'   		=> 'Regex',
			'regex_index'   => 'Regex Index',
			'description'   => 'Description'
		);

		return $columns;
	}

	/**
	 * WP list column values
	 */
	function column_default( $item, $column_name ) {
		switch( $column_name ) {
		case 'title':
		case 'carrier':
			return $item[ $column_name ];
			break;
		case 'regex':
			return $item[ $column_name ];
			break;
		case 'regex_index':
			return $item[ $column_name ];
			break;
		case 'description':
			return $item[ $column_name ];
			break;
		default:
		  return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * List Checkbox
	 */
	function column_cb($item) {
        return ($item['type'] != 'system')? sprintf(
            '<input type="checkbox" name="rows[]" value="%s" />', $item['id']
        ): '<input type="checkbox" disabled="disabled"/>';
    }

	/**
	 * Title column HTML return
	 */
	function column_title( $item ) {
		$actions = array();
		if($item['type'] != 'system') {
			$onclick = 'if (confirm(\'Please confirm to delete.\')) {return true;} return false;';
			$actions = array(
				'edit'      => sprintf(
					'<a href="?page=%s&action=%s&id=%d">Edit</a>',
					'wireless_butler_regex_list',
					'edit',
					$item['id']
				),
				'delete'    => sprintf(
					'<a href="?page=%s&action=%s&id=%d" onclick="' . $onclick . '">Delete</a>',
					'wireless_butler_regex_list',
					'delete',
					$item['id']
				),
			);
		}

        return sprintf(
            '%1$s <span style="color:silver ; display : none;">(id:%2$s)</span>%3$s',
            $this->keys[$item['key']],
            $item['id'],
            $this->row_actions($actions)
        );
    }

	/**
	 * Options for bulk options
	 */
	function get_bulk_actions() {
		$actions = array(
		    'delete'    => 'Delete'
		);
		return $actions;
	}

	/**
	 * Bulk action logic
	 */
	function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {
			if ( count( $_POST['rows'] ) > 0 ) {
				foreach ( $_POST['rows'] as $id ) {
					global $wpdb;
					$tblname = $wpdb->prefix . 'wireless_butler_regex';
					$sql = "DELETE FROM " . $tblname . " WHERE id =%d ";
					$wpdb->query( $wpdb->prepare( $sql, $id ) );
				}
			}

			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		}
	}

	/**
	 * No regex 
	 */
	public function no_items() {
		_e( 'No regex avaliable.', 'wireless_butler' );
	}

	/**
	 * Delete regex from DB
	 */
	function delete_regex() {
		$redirect = admin_url('admin.php?page=wireless_butler_regex_list');

		global $wpdb;
		$tblname = $wpdb->prefix . 'wireless_butler_regex';

		$id = intval( sanitize_text_field($_GET['id']) );

		if( ! empty( $id ) ) {
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM ".$tblname." WHERE id=%d",
					$id
				)
			);
			$redirect = admin_url( 'admin.php?page=wireless_butler_regex_list&deleted=true' );
		}
		wp_redirect($redirect);
	}
}