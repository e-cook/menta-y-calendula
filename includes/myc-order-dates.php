<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function order_dates() {
    $id = get_term_by( 'name', 'order_date', 'category' )->term_id;
    $raw_dates = get_term_meta( $id )[ 'order_date' ];
    sort( $raw_dates );
    $dates = array();
    foreach( $raw_dates as $date ) {
	$dates[] = array( 'order_date' => $date . ' (' . date('D', strtotime( $date ) ) . ')' );
    }
    return $dates;
}

function manage_order_dates_page() {
    echo '<h2>' . __( 'Order dates', 'myc' ) . '</h2>';
?>
    <div class="options_group">
	<p class="form-field _order_dates">
	    <span class="wrap">
		<?php
		$table = new MYC_Order_Dates( order_dates() );
		$table->prepare_items();
		$table->display();
		?>
	    </span>
	</p>
	<p class="form-field _new_order_date">
	    <h4><?php echo __( 'Add Order Date' );?></h4>
	    <input type="text" class="datepicker" id="new_order_date" />
	    <button class="button" id="save-date-button"><?php echo __( 'Save date' )?></button>

	</p>
    </div>
<?php
}

function save_date_js() {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 // Validates that the input string is a valid date formatted as "mm/dd/yyyy"
	 function validateDate(dateString)
	 {
	     // First check for the pattern
	     if (!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString))
		 return false;

	     // Parse the date parts to integers
	     var parts = dateString.split("/");
	     var day = parseInt(parts[1], 10);
	     var month = parseInt(parts[0], 10);
	     var year = parseInt(parts[2], 10);

	     // Check the ranges of month and year
	     if (year < 1000 || year > 3000 || month == 0 || month > 12)
		 return false;

	     var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

	     // Adjust for leap years
	     if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
		 monthLength[1] = 29;

	     // Check the range of the day
	     if (day < 0 || day > monthLength[month - 1])
		 return false;
	     return parts[2] + "-" + parts[0] + "-" + parts[1];
	 };
	 
	 // handle click on save date button
	 jQuery( '#new_order_date' ).datepicker();
	 jQuery( '#save-date-button' ).click( function() {
	     jQuery.post( ajaxurl, {
		 'action' : 'save_new_order_date',
		 'date'   : jQuery( '#new_order_date' ).val(),
		 '_nonce' : '<?php echo wp_create_nonce( 'order_date' ) ?>'
	     }, function( response ) {
		 jQuery( '.datepicker' ).val('');
		 window.location.reload();
	     });
	 });
     });
    </script>
<?php
}
add_action( 'admin_footer', 'save_date_js' );

function myc_save_new_order_date() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'order_date' ) ) {
	wp_die( "Don't mess with me!" );
    }
    $new_date = date('Y-m-d', strtotime( $_POST[ 'date' ] ) );
    $term_id = get_term_by( 'slug', 'order_date', 'category' )->term_id;
    if ( ! in_array( $new_date, get_term_meta( $term_id )[ 'order_date' ] ) ) {
	add_term_meta( $term_id, 'order_date', $new_date );
    }
}
add_action( 'wp_ajax_save_new_order_date', 'myc_save_new_order_date' );

// delete order date

function delete_order_date_js() {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 jQuery( '#the-list' ).find( '.delete_order_date' ).click( function() {
	     jQuery.post( ajaxurl, {
		 'action': 'delete_order_date',
		 'date'  : jQuery( this ).attr( 'date' ),
		 '_nonce' : '<?php echo wp_create_nonce( 'delete_order_date' ) ?>'
	     }, function( response ) {
		 jQuery( '.datepicker' ).val('');
		 window.location.reload();
	     });
	 });
     });
    </script>
<?php
}
add_action( 'admin_footer', 'delete_order_date_js' );


function myc_delete_order_date() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'delete_order_date' ) ) {
	wp_die( "Don't mess with me!" );
    }
    $date = $_POST['date'];
    $term_id = get_term_by( 'slug', 'order_date', 'category' )->term_id;
    delete_term_meta( $term_id, 'order_date', $date );
}
add_action( 'wp_ajax_delete_order_date', 'myc_delete_order_date' );


// add dates to order
// http://www.portmanteaudesigns.com/blog/2015/02/04/woocommerce-custom-checkout-fields-email-backend/

add_action( 'woocommerce_after_order_notes', 'myc_order_date_checkout_field_after_order_notes' );
function myc_order_date_checkout_field_after_order_notes( $checkout ) {
    echo '<div class="myc_order_date_checkout_field"><h2>' . __('Date for Order') .'</h2>';

    woocommerce_form_field( 'order_date', array(
	'type'       => 'text',
	'label'      => __('Date for Order', 'myc'),
	'placeholder'=> _x('Date for Order', 'placeholder', 'myc'),
	'required'   => true,
	//	'class'      => array('form-row-wide'),
	'clear'      => true,
    ), $checkout->get_value( 'order_date' ) );

    echo '</div>';
}

add_action( 'woocommerce_after_cart_contents', 'myc_order_date_checkout_field_after_cart_contents' );
function myc_order_date_checkout_field_after_cart_contents() {
    echo '<div class="myc_order_date_checkout_field"><h3>' . __('Date for Order') .'</h3>';
    echo '<input type="text" class="datepicker" id="order_date" value="' . date( 'M d, Y', strtotime( 'next Monday' ) ) . '"/>';
    echo '</div>';
}

function order_date_cart_js() {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 jQuery( '#order_date' ).datepicker({
	     onSelect: function() {
		 sessionStorage.setItem( 'order_date', jQuery( this ).val() );
	     }
	 });
     });
    </script>
<?php
}
add_action( 'wp_footer', 'order_date_cart_js' );

/*
   add_action('woocommerce_checkout_update_order_meta', 'myc_order_date_update_order_meta' );
   function myc_order_date_update_order_meta( $order_id ) {
   if ( ! empty( $_POST['order_date'] ) ) {
   update_post_meta( $order_id, 'order_date', $_POST['order_date'] );
   }
   }
 */

// https://wisdmlabs.com/blog/add-custom-data-woocommerce-order/

add_filter('woocommerce_add_cart_item_data', 'myc_add_order_date_to_cart', 1, 2);
function myc_add_order_date_to_cart($cart_item_data, $product_id)
{
    if( ! isset( $_SESSION[ 'order_date' ] ) ) {
	return $cart_item_data;
    }

    session_start();    
    $new_value = array( 'order_date' => $_SESSION[ 'order_date' ] );
    unset($_SESSION['order_date']);

    return empty( $cart_item_data )
	 ? $new_value
	 : array_merge( $cart_item_data, $new_value );
}

add_filter('woocommerce_get_cart_item_from_session', 'myc_get_cart_items_from_session', 1, 3 );
function myc_get_cart_items_from_session($item, $values, $key)
{
    if( array_key_exists( 'order_date', $values ) ) {
	$item[ 'order_date' ] = $values[ 'order_date' ];
    }       
    return $item;
}

add_filter('woocommerce_checkout_cart_item_quantity','myc_add_order_date_from_session_into_cart', 1, 3);  
add_filter('woocommerce_cart_item_price',            'myc_add_order_date_from_session_into_cart', 1, 3);
function myc_add_order_date_from_session_into_cart( $product_name, $values, $cart_item_key )
{

    if( ! isset( $values[ 'order_date' ] ) ||
	empty( $values['order_date'] ) ) {
	return $product_name;
    }
    $return_string = $product_name . "</a><dl class='variation'>";
    $return_string .= "<table class='myc_options_table' id='" . $values['product_id'] . "'>";
    $return_string .= "<tr><td>" . $values['order_date'] . "</td></tr>";
    $return_string .= "</table></dl>"; 
    return $return_string;
}


add_action('woocommerce_add_order_item_meta','myc_add_order_date_to_order_item_meta', 1, 2);
function myc_add_order_date_to_order_item_meta( $item_id, $values )
{
    $order_date = $values['order_date'];
    if( ! empty( $order_date ) ) {
	wc_add_order_item_meta($item_id, 'order_date', $order_date);  
    }
}

add_action('woocommerce_before_cart_item_quantity_zero', 'myc_remove_order_data_from_cart', 1, 1);
function myc_remove_order_data_from_cart( $cart_item_key )
{
    global $woocommerce;
    $cart = $woocommerce->cart->get_cart();
    // For each item in cart, if item is upsell of deleted product, delete it
    foreach( $cart as $key => $values) {
	if ( $values['order_date'] == $cart_item_key )
	    unset( $woocommerce->cart->cart_contents[ $key ] );
    }
}

