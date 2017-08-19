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
	    <h4><?php echo __( 'Add Order Deadline', 'myc' );?></h4>
	    <input type="text" class="datepicker order_date_picker" id="new_order_deadline" />
	    <button class="button" id="save-date-button"><?php echo __( 'Save date', 'myc' )?></button>
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

add_action( 'woocommerce_checkout_before_customer_details', function() {
    echo '<div id="myc_delivery_date_checkout_field"><h3>' . __('Date for Delivery', 'myc') .'</h3>';
    woocommerce_form_field( 'delivery_date', array(
	'type'       => 'text',
	'class'      => array('myc-delivery-date-class form-row-wide'),
	'id'         => 'delivery-date-datepicker',
	'label'      => __('Date for Delivery', 'myc'),
	'placeholder'=> __('Select date for delivery', 'myc'),
	'required'   => true,
    ), '' );

    echo '</div>';
});

add_action( 'woocommerce_checkout_process', function() {
    if ( ! $_POST[ 'delivery_date' ] ) {
	wc_add_notice( __( 'Please enter a delivery date', 'myc' ), 'error' );
    }
});

add_action( 'woocommerce_checkout_update_order_meta', function( $order_id, $posted ) {
    if ( $_POST[ 'delivery_date' ] ) {
	update_post_meta( $order_id, '_shipping_delivery_date', date( 'Y-m-d', strtotime( $_POST[ 'delivery_date' ] ) ) );
    }
}, 10, 2 );

add_action( 'wp_footer', function () {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 jQuery( '#delivery-date-datepicker' ).datepicker({
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

