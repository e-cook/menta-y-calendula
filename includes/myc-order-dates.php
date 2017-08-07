<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( dirname(__FILE__) . '/myc-order-date-functions.php' );


function manage_order_deadlines_page() {
    echo '<h2>' . __( 'Order deadlines', 'myc' ) . '</h2>';
?>
    <div class="options_group">
	<p class="form-field _order_deadlines">
	    <span class="wrap">
		<?php
		$table = new MYC_Order_Deadlines( formatted_order_deadlines_for_ordering() );
		$table->prepare_items();
		$table->display();
		?>
	    </span>
	</p>
	<p class="form-field _new_order_deadline">
	    <h4><?php echo __( 'Add Order Deadline' );?></h4>
	    <input type="text" class="datepicker order_date_picker" id="new_order_deadline" />
	    <button class="button" id="save-date-button"><?php echo __( 'Save date' )?></button>
	</p>
    </div>
<?php
}

add_action( 'admin_footer', function() {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 // handle click on save date button
	 jQuery( '.order_date_picker' ).datepicker();
	 jQuery( '#save-date-button' ).click( function() {
	     jQuery.post( ajaxurl, {
		 'action' : 'save_new_order_deadline',
		 'date'   : jQuery( '#new_order_deadline' ).val(),
		 '_nonce' : '<?php echo wp_create_nonce( 'order_deadline' ) ?>'
	     }, function( response ) {
		 jQuery( '.datepicker' ).val('');
		 window.location.reload();
	     });
	 });
     });
    </script>
<?php
});

add_action( 'wp_ajax_save_new_order_deadline', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'order_deadline' ) ) {
	wp_die( "Don't mess with me!" );
    }
    $new_date = date('Y-m-d', strtotime( $_POST[ 'date' ] ) );
    $term_id = get_term_by( 'slug', 'order_deadline', 'category' )->term_id;
    if ( ! in_array( $new_date, get_term_meta( $term_id )[ 'order_deadline' ] ) ) {
	add_term_meta( $term_id, 'order_deadline', $new_date );
    }
});

// delete order deadline

add_action( 'admin_footer', function() {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 jQuery( '#the-list' ).find( '.delete_order_deadline' ).click( function() {
	     jQuery.post( ajaxurl, {
		 'action': 'delete_order_deadline',
		 'date'  : jQuery( this ).attr( 'date' ),
		 '_nonce' : '<?php echo wp_create_nonce( 'delete_order_deadline' ) ?>'
	     }, function( response ) {
		 jQuery( '.datepicker' ).val('');
		 window.location.reload();
	     });
	 });
     });
    </script>
<?php
});


add_action( 'wp_ajax_delete_order_deadline', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'delete_order_deadline' ) ) {
	wp_die( "Don't mess with me!" );
    }
    $date = $_POST['date'];
    $term_id = get_term_by( 'slug', 'order_deadline', 'category' )->term_id;
    delete_term_meta( $term_id, 'order_deadline', $date );
});


// add dates to order
// http://www.portmanteaudesigns.com/blog/2015/02/04/woocommerce-custom-checkout-fields-email-backend/

add_action( 'woocommerce_before_checkout_form', function( $checkout ) {
    echo '<div class="myc_delivery_date_checkout_field"><h2>' . __('Date for Delivery') .'</h2>';
    //    error_log(var_export($checkout,1));
    session_start();
    woocommerce_form_field( 'delivery_date_checkout', array(
	'type'       => 'text',
	'label'      => __('Date for Delivery', 'myc'),
	'placeholder'=> _x('Date for Delivery', 'placeholder', 'myc'),
	'required'   => true,
	'class'      => array('form-row-wide'),
	'input_class'=> array('order_date_picker'),
	'clear'      => true,
    ), isset( $_SESSION[ 'delivery_date' ] )
			  ? $_SESSION[ 'delivery_date' ]
			  : '' );

    echo '</div>';
});

add_action( 'woocommerce_after_cart_contents', function () {
    echo '<div class="myc_delivery_date_checkout_field"><h3>' . __('Date for Delivery') .'</h3>';
    echo '<input type="text" class="datepicker order_date_picker" id="delivery_date" value="' . date( 'M d, Y', strtotime( next_order_deadline_for_ordering() ) ) . '"/>';
    echo '</div>';
});

add_action( 'wp_footer', function () {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 jQuery( '.order_date_picker' ).datepicker({
	     onSelect: function() {
		 jQuery.post( ajaxurl, {
		     'action': 'set_delivery_date_in_session',
		     'delivery_date': jQuery( this ).val(),
		     '_nonce': '<?php echo wp_create_nonce( 'set_delivery_date_in_session' ) ?>'
		 });
	     },
	     beforeShowDay: function( date ) {
		 var d = date.getDate();
		 if (d<10) {
		     d = '0' + d;
		 }
		 var m = date.getMonth() + 1;
		 if (m<10) {
		     m = '0' + m;
		 }
		 var y = date.getFullYear();
		 var enabled_dates = <?php echo php_array_2_js( valid_delivery_dates() ) ?>;
		 var current_date = y + '-' + m + '-' + d;
		 if ( jQuery.inArray( current_date, enabled_dates ) != -1 ) {
		     return [true];
		 } else {
		     return [false];
		 }
	     }
	 });
     });
    </script>
<?php
});

add_action( 'wp_ajax_set_delivery_date_in_session', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'set_delivery_date_in_session' ) ) {
	wp_die( "Don't mess with me!" );
    }
    session_start();
    $_SESSION[ 'delivery_date' ] = $_POST[ 'delivery_date' ];
    wp_die();
});

add_action( 'woocommerce_after_checkout_validation', function( $data, $errors ) {
    session_start();
    if ( ! isset( $_SESSION[ 'delivery_date' ] ) ) {
	$errors->add( 'validation', __( 'Please enter a valid delivery date for your order', 'myc' ) );
    } elseif( strtotime( $_SESSION[ 'delivery_date' ] ) < strtotime( 'now' ) ) {
	$errors->add( 'validation', __( 'The delivery date for your order must lie in the future', 'myc' ) );
    }
}, 10, 2 );

add_action( 'woocommerce_checkout_update_order_meta', function( $order_id, $posted ) {
    session_start();
    $order = wc_get_order( $order_id );
    $order->update_meta_data( '_delivery_date', date( 'Y-m-d', strtotime( $_SESSION[ 'delivery_date' ] ) ) );
    $order->save();
}, 10, 2 );

