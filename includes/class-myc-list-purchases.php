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

    protected $_latest;
    
    public function __construct( $latest ) {
	parent::__construct();
	$this->_latest = $latest;
    }
    
    public function get_columns() {
	return array(
	    'purchase_date' => __( 'Date' ),
	    'provider'      => __( 'Provider' ),
	    'qty_unit'      => __( 'Quantity' ),
	    'price_paid'    => __( 'Paid' ),
	    'unit_price'    => __( 'Unit Price'),
	);
    }

    public function prepare_items() {
	$columns = $this->get_columns();
	$hidden = array();
	$sortable = array();
	$this->_column_headers = array($columns, $hidden, $sortable);
	$this->items = $this->_latest;
    }

    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'purchase_date':
            case 'provider':
            case 'qty_unit':
            case 'price_paid':
            case 'unit_price':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }

}
