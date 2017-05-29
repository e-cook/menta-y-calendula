<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function order_dates( $only_enabled = false ) {
    $id = get_term_by( 'name', 'order_date', 'category' )->term_id;
    $raw_dates = get_term_meta( $id )[ 'order_date' ];
    sort( $raw_dates );
    $dates = array();
    $now = strtotime( 'now' );
    foreach( $raw_dates as $date ) {
	$dt = strtotime( $date );
	$ordering_threshold = ( false == $only_enabled )
			    ? PHP_INT_MAX
			    : strtotime( $date . ' - 3 days' );
	if ( $dt >= $now && $ordering_threshold >= $now ) {
	    $dates[] = $date;
	}
    }
    return $dates;
}

function formatted_order_dates() {
    $dates = array();
    foreach ( order_dates() as $date ) {
	$dates[] = array( 'order_date' => $date . ' (' . date( 'D', strtotime( $date ) ) . ')' );
    }
    return $dates;
}

function next_order_date() {
    $now = date( 'Y-m-d', strtotime( 'now' ) );
    foreach( order_dates( true ) as $date ) {
	if ( $date >= $now ) {
	    return $date;
	}	    
    }
    return '';
}


function manage_order_dates_page() {
    echo '<h2>' . __( 'Order dates', 'myc' ) . '</h2>';
?>
    <div class="options_group">
	<p class="form-field _order_dates">
	    <span class="wrap">
		<?php
		$table = new MYC_Order_Dates( formatted_order_dates() );
		$table->prepare_items();
		$table->display();
		?>
	    </span>
	</p>
	<p class="form-field _new_order_date">
	    <h4><?php echo __( 'Add Order Date' );?></h4>
	    <input type="text" class="datepicker order_date_picker" id="new_order_date" />
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
	 jQuery( '.order_date_picker' ).datepicker();
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

add_action( 'admin_footer', function() {?>
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
});


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

add_action( 'woocommerce_after_order_notes', function( $checkout ) {
    echo '<div class="myc_order_date_checkout_field"><h2>' . __('Date for Order') .'</h2>';
    //    error_log(var_export($checkout,1));
    session_start();
    woocommerce_form_field( 'order_date_checkout', array(
	'type'       => 'text',
	'label'      => __('Date for Order', 'myc'),
	'placeholder'=> _x('Date for Order', 'placeholder', 'myc'),
	'required'   => true,
	'class'      => array('form-row-wide'),
	'input_class'=> array('order_date_picker'),
	'clear'      => true,
    ), isset( $_SESSION[ 'order_date' ] )
			  ? $_SESSION[ 'order_date' ]
			  : '' );

    echo '</div>';
});

add_action( 'woocommerce_after_cart_contents', function () {
    echo '<div class="myc_order_date_checkout_field"><h3>' . __('Date for Order') .'</h3>';
    error_log( 'next_order_date: ' . next_order_date() );
    echo '<input type="text" class="datepicker order_date_picker" id="order_date" value="' . date( 'M d, Y', strtotime( next_order_date() ) ) . '"/>';
    echo '</div>';
});

function php_array_2_js( $arr ) {
    $jarr = '[';
    $ct = 0;
    foreach ( $arr as $a ) {
	if ( $ct > 0 ) {
	    $jarr .= ',';
	} else {
	    $ct = 1;
	}
	$jarr .= '"' . $a . '"';
    }
    return $jarr . ']';
}

add_action( 'wp_footer', function () {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 jQuery( '.order_date_picker' ).datepicker({
	     onSelect: function() {
		 jQuery.post( ajaxurl, {
		     'action': 'set_order_date_in_session',
		     'order_date': jQuery( this ).val(),
		     '_nonce': '<?php echo wp_create_nonce( 'set_order_date_in_session' ) ?>'
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
		 var enabled_dates = <?php echo php_array_2_js( order_dates( true ) ) ?>;
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

add_action( 'wp_ajax_set_order_date_in_session', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'set_order_date_in_session' ) ) {
	wp_die( "Don't mess with me!" );
    }
    session_start();
    $_SESSION[ 'order_date' ] = $_POST[ 'order_date' ];
    wp_die();
});

add_action( 'woocommerce_after_checkout_validation', function( $data, $errors ) {
    error_log("myc_after_checkout_validation");
    error_log("data " . var_export($data,1));
    error_log("errors " . var_export($errors,1));
    session_start();
    if ( ! isset( $_SESSION[ 'order_date' ] ) ) {
	$errors->add( 'validation', __( 'Please enter a valid date for your order', 'myc' ) );
    } elseif( strtotime( $_SESSION[ 'order_date' ] ) < strtotime( 'now' ) ) {
	$errors->add( 'validation', __( 'The date for your order must lie in the future', 'myc' ) );
    }
}, 10, 2 );

add_action( 'woocommerce_checkout_update_order_meta', function( $order_id, $posted ) {
    session_start();
    error_log("order_id: $order_id, posted: ". var_export($posted,1));
    $order = wc_get_order( $order_id );
    $order->update_meta_data( '_order_date', date( 'Y-m-d', strtotime( $_SESSION[ 'order_date' ] ) ) );
    $order->save();
}, 10, 2 );

