<?php
/*
   Plugin Name: Menta y Calendula
   Plugin URI:  https://github.org/menta-y-calendula
   Description: Organic Food Preparation and Ordering
   Version:     20170927
   Author:      e-cook
   Author URI:  https://github.com/e-cook/menta-y-calendula
   License:     GPLv3
   License URI: https://www.gnu.org/licenses/gpl-3.0.html
   Text Domain: en
   Domain Path: /languages

   This file is part of Menta y Calendula.

   Menta y Calendula is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Menta y Calendula is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Menta y Calendula.  If not, see <http://www.gnu.org/licenses/>.
 */

defined( 'ABSPATH' ) or die();

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    //require_once(dirname(__FILE__) . '/class-wp-list-table.php');
}

class MYC_Latest_Purchases extends WP_List_Table {

    function get_columns() {
	return array(
	    'provider_name'        => __( 'Provider' ),
	    'dt'                   => __( 'Date' ),
	    'qty_unit'             => __( 'Quantity' ),
	    'price_paid'           => __( 'Paid' ),
	    'unit_price'           => __( 'Unit Price'),
	);
    }

    function query() {
	global $wpdb, $post;
	$result = $wpdb->query("SELECT * FROM myc_purchase 
WHERE phys_ingredient_id = {$post->ID}
AND dt >= NOW() - INTERVAL 2 WEEK
ORDER BY dt DESC");
	return array(
	    'provider_name'        => $result['provider_id'],
	    'dt'                   => $result['dt'],
	    'qty_unit'             => $result['qty'] . ' ' . $result['base_unit'],
	    'unit_price'           => $result['unit_price']
	);
    }
    
    function prepare_items() {
	$columns = $this->get_columns();
	$hidden = array();
	$sortable = array();
	$this->_column_headers = array($columns, $hidden, $sortable);
	$this->items = $this->query();
    }
}
