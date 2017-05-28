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

class NoNonce_Table extends WP_List_Table {
    protected function display_tablenav( $which ) {
	/*
	   if ( 'top' === $which ) {
	   wp_nonce_field( 'bulk-' . $this->_args['plural'] );
	   }
	 */
?>
    <div class="tablenav <?php echo esc_attr( $which ); ?>">

	<?php if ( $this->has_items() ): ?>
	    <div class="alignleft actions bulkactions">
		<?php $this->bulk_actions( $which ); ?>
	    </div>
	<?php endif;
	$this->extra_tablenav( $which );
	$this->pagination( $which );
	?>

	<br class="clear" />
    </div>
<?php
}

}

class MYC_Latest_Purchases extends NoNonce_Table {

    protected $_latest;
    
    public function __construct( $latest ) {
	parent::__construct();
	$this->_latest = $latest;
    }
    
    public function get_columns() {
	return array(
	    'purchase_date' => __( 'Date' ),
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
            case 'qty_unit':
            case 'price_paid':
            case 'unit_price':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }

}


class MYC_Recipe_Lines extends NoNonce_Table {

    protected $_lines;
    
    public function __construct( $lines ) {
	parent::__construct();
	$this->_lines = $lines;
    }
    
    public function get_columns() {
	return array(
	    'ingredient' => __( 'Ingredient' ),
	    'qty_unit'   => __( 'Quantity' ),
	);
    }

    public function prepare_items() {
	$columns = $this->get_columns();
	$hidden = array();
	$sortable = array();
	$this->_column_headers = array($columns, $hidden, $sortable);
	$this->items = $this->_lines;
    }

    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'ingredient':
            case 'qty_unit':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }

}


class MYC_Uses_Recipe_Lines extends NoNonce_Table {

    protected $_lines;
    
    public function __construct( $lines ) {
	parent::__construct();
	$this->_lines = $lines;
    }
    
    public function get_columns() {
	return array(
	    'recipe' => __( 'Recipe' ),
	);
    }

    public function prepare_items() {
	$columns = $this->get_columns();
	$hidden = array();
	$sortable = array();
	$this->_column_headers = array($columns, $hidden, $sortable);
	$this->items = $this->_lines;
    }

    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'recipe':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }

}


class MYC_Instances extends NoNonce_Table {

    protected $_lines;
    
    public function __construct( $lines ) {
	parent::__construct( array( 'plural' => 'instances',
				    'singular' => 'instance' ) );
	$this->_lines = $lines;
    }
    
    public function get_columns() {
	return array(
	    'phys_ingredient_id' => __( 'Ingredient' ),
	);
    }

    public function prepare_items() {
	$columns = $this->get_columns();
	$hidden = array();
	$sortable = array();
	$this->_column_headers = array($columns, $hidden, $sortable);
	$this->items = $this->_lines;
    }

    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'phys_ingredient_id':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }

}

class MYC_Total_Inventory extends NoNonce_Table {

    protected $_lines;
    
    public function __construct( $lines ) {
	parent::__construct();
	$this->_lines = $lines;
    }
    
    public function get_columns() {
	return array(
	    'phys_ingredient_id' => __( 'Ingredient' ),
	    'qty'                => __( 'Quantity' ),
	);
    }

    public function prepare_items() {
	$columns = $this->get_columns();
	$hidden = array();
	$sortable = array();
	$this->_column_headers = array($columns, $hidden, $sortable);
	$this->items = $this->_lines;
    }

    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'phys_ingredient_id':
	    case 'qty':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }

}

class MYC_Total_Purchases extends NoNonce_Table {

    protected $_lines;
    
    public function __construct( $lines ) {
	parent::__construct();
	$this->_lines = $lines;
    }
    
    public function get_columns() {
	return array(
	    'date'               => __( 'Date' ),
	    'phys_ingredient_id' => __( 'Ingredient' ),
	    'unit_price'         => __( 'Unit Price' ),
	);
    }

    public function prepare_items() {
	$columns = $this->get_columns();
	$hidden = array();
	$sortable = array();
	$this->_column_headers = array($columns, $hidden, $sortable);
	$this->items = $this->_lines;
    }

    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
	    case 'date':
            case 'phys_ingredient_id':
	    case 'unit_price':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }

}

class MYC_Provides_Lines extends NoNonce_Table {

    protected $_lines;
    
    public function __construct( $lines ) {
	parent::__construct();
	$this->_lines = $lines;
    }
    
    public function get_columns() {
	return array(
	    'ingredient' => __( 'Ingredient' ),
	);
    }

    public function prepare_items() {
	$columns = $this->get_columns();
	$hidden = array();
	$sortable = array();
	$this->_column_headers = array($columns, $hidden, $sortable);
	$this->items = $this->_lines;
    }

    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'ingredient':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }

}


class MYC_Order_Dates extends NoNonce_Table {

    protected $_dates;
    
    public function __construct( $dates ) {
	parent::__construct();
	$this->_dates = $dates;
    }
    
    public function get_columns() {
	return array(
	    'order_date' => __( 'Order Date', 'myc' ),
	);
    }

    public function prepare_items() {
	$columns = $this->get_columns();
	$hidden = array();
	$sortable = array();
	$this->_column_headers = array($columns, $hidden, $sortable);
	$this->items = $this->_dates;
    }

    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'order_date':
		$action = array( 'delete' => '<button class="delete_order_date button" date="' . substr( $item[$column_name], 0, 10 ) 
					 . '">' . __( 'Delete' ) . '</button>' );
                return sprintf('%1$s %2$s', $item[ $column_name ], $this->row_actions( $action ) );
            default:
                return print_r( $item, true ) ;
        }
    }
}
