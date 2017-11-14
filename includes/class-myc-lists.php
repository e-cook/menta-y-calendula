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
    public function __construct() {
	if ( ! is_admin() ) {
	    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
	    require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
	    require_once( ABSPATH . 'wp-admin/includes/template.php' );
	    $GLOBALS['hook_suffix'] = '';
	}
	parent::__construct();
    }

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
	    'purchase_date' => __( 'Date', 'myc' ),
	    'qty_unit'      => __( 'Quantity', 'myc' ),
	    'price_paid'    => __( 'Paid', 'myc' ),
	    'unit_price'    => __( 'Unit Price', 'myc'),
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
	    'ingredient' => __( 'Ingredient', 'myc' ),
	    'qty_unit'   => __( 'Quantity', 'myc' ),
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
	    'recipe' => __( 'Recipe', 'myc' ),
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
	    'phys_ingredient_id' => __( 'Ingredient', 'myc' ),
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
	    'phys_ingredient_id' => __( 'Ingredient', 'myc' ),
	    'qty'                => __( 'Quantity', 'myc' ),
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
	    'date'               => __( 'Date', 'myc' ),
	    'phys_ingredient_id' => __( 'Ingredient', 'myc' ),
	    'unit_price'         => __( 'Unit Price', 'myc' ),
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
	    'ingredient' => __( 'Ingredient', 'myc' ),
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


class MYC_Order_Deadlines extends NoNonce_Table {

    protected $_dates;
    
    public function __construct( $dates ) {
	parent::__construct();
	$this->_dates = $dates;
    }
    
    public function get_columns() {
	return array(
	    'order_deadline' => __( 'Order Deadline', 'myc' ),
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
            case 'order_deadline':
		$action = array( 'delete' => '<button class="delete_order_deadline button" date="' . substr( $item[$column_name], 0, 10 ) 
					 . '">' . __( 'Delete', 'myc' ) . '</button>' );
                return sprintf('%1$s %2$s', $item[ $column_name ], $this->row_actions( $action ) );
            default:
                return print_r( $item, true ) ;
        }
    }
}

class MYC_Cash_Transactions extends NoNonce_Table {

    protected $_lines;
    
    public function __construct( $lines ) {
	parent::__construct();
	$this->_lines = $lines;
    }
    
    public function get_columns() {
	return array(
	    'id' => __( 'Id', 'myc' ),
	    'date'   => __( 'Date', 'myc' ),
	    'balance_before' => __( 'Balance before', 'myc' ),
	    'money_qty' => __( 'Euros', 'myc' ),
	    'balance_after' => __( 'Balance after', 'myc' ),
	    'transaction_type' => __( 'Transaction Type', 'myc' ), // 10 hrs web maintenance @ 22 euros/hr | Order #222 (with link)
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
            case 'id':
            case 'date':
	    case 'transaction_type':
		return $item[ $column_name ];

	    case 'balance_before':
	    case 'money_qty':
	    case 'balance_after':
		return number_format((float)$item[ $column_name ], 2, '.', '');

            default:
		return print_r( $item, true ) ;
        }
    }
    
}
