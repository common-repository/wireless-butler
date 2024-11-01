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

class Wireless_Butler_Admin_Entries extends WP_List_Table {

	/**
	 * Prepare table list items
	 */
	function prepare_items() {
		$columns      = $this->get_columns();
		$hidden       = array();
		$sortable     = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
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

		$keys = [
			'manual' 	=> 'Manual',
			'auto' 		=> 'Auto',
		];
		foreach($keys as $index=>$value) {
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
		$tblname = $wpdb->prefix . 'wireless_butler_customer';
		$carrierTblName = $wpdb->prefix . 'wireless_butler_carrier';

		$key = ( isset($_REQUEST['key']) ? sanitize_text_field($_REQUEST['key']) : '');

		$sql = 'SELECT e.*, c.name as carrier, c.carrier_id ';
		$sql .= ' FROM ' . $tblname . ' as e';
		$sql .= ' LEFT JOIN ' . $carrierTblName . ' as c ON c.carrier_id=e.carrier';
		if($key == 'manual') {
			$sql .= " WHERE e.`manual` = '1'";
		}else if($key == 'auto') {
			$sql .= " WHERE e.`manual` = '0'";
		}
 		$sql .= ' ORDER BY e.id DESC';
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
 		return $result;
	}

	/**
	 * Regex WP list Column name
	 */
	public function get_columns() {
		$columns = array(
			'firstName'       	=> 'First Name',
			'lastName'         	=> 'Last Name',
			'email'   		    => 'Email',
			'phoneNumber'       => 'Phone',
			'carrier'           => 'Carrier',
			'manual'           	=> 'Manual',
			'wirelessBillURL'   => 'Bill',
			'recommendation'   	=> 'Recommendation',
			'CreatedAt'   		=> 'Created At',
		);

		return $columns;
	}

	/**
	 * WP list column values
	 */
	function column_default( $item, $column_name ) {
		switch( $column_name ) {
		case 'firstName':
			return $item[ $column_name ];
			break;
		case 'lastName':
			return $item[ $column_name ];
			break;
		case 'email':
			return $item[ $column_name ];
			break;
		case 'phoneNumber':
			return $item[ $column_name ];
			break;
		case 'carrier':
			return $item[ $column_name ];
			break;
		case 'CreatedAt':
			return date('d M Y', strtotime($item[ $column_name ]));
			break;
		case 'manual':
		case 'wirelessBillURL':
		case 'recommendation':
		default:
		  return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Wireless Bill column HTML return
	 */
	function column_wirelessBillURL( $item ) {
		return ($item['manual'] == '0')? 
		sprintf(
            '<a href="%1$s">View Bill</a>',
            $item['wirelessBillURL']
        ): '';
	}

	/**
	 * Recommendation column HTML return
	 */
	function column_recommendation( $item ) {
		return sprintf(
            '<a href="%1$s">Recommendation Plans</a>',
            admin_url('admin.php?page=wireless_butler_customer_recommendation_plan&id='.$item['id'])
        );
	}
	
	/**
	 * Manual column HTML return
	 */
	function column_manual( $item ) {
        return ($item['manual'] == '1')? 'Yes': 'No';
    }

	/**
	 * No Item 
	 */
	public function no_items() {
		_e( 'No item avaliable.', 'wireless_butler' );
	}
}