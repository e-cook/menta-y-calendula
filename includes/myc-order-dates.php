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
    $next_monday    = date_i18n( 'l, d M Y', strtotime( 'next Monday' ) );
    $next_tuesday   = date_i18n( 'l, d M Y', strtotime( 'next Tuesday' ) );
    $next_wednesday = date_i18n( 'l, d M Y', strtotime( 'next Wednesday' ) );
?>
<div id="myc_delivery_date_checkout_field"><h3><?php echo __('How would you like to receive your order?', 'myc') ?></h3>
    <table class="woocommerce-orders-table shop_table delivery-times-table">
	<thead>
	    <tr>
		<th class="woocommerce-orders-table__header"><span class="nobr"><?php echo __( 'Delivery mode', 'myc' ) ?></span></th>
		<th class="woocommerce-orders-table__header"><span class="nobr"><?php echo __( 'Date', 'myc' ) ?></span></th>
		<th class="woocommerce-orders-table__header"><span class="nobr"><?php echo __( 'Time', 'myc' ) ?></span></th>
	    </tr>
	</thead>
	<tbody>
	    <tr class="woocommerce-orders-table__row">
		<th rowspan="5">
		    <?php echo __( 'Pick up', 'myc' ) ?>
		    <label for="pickup-ateneu" class="delivery-label">
			<input class="pickup-place" type="radio" name="pickup_place" value="pickup-ateneu" id="pickup-ateneu">
			<?php echo __( 'At ateneu', 'myc' ) ?>
		    </label>
		    <label for="pickup-obrador" class="delivery-label">
			<input class="pickup-place" type="radio" name="pickup_place" value="pickup-obrador" id="pickup-obrador">
			<?php echo __( 'At obrador', 'myc' ) ?>
		    </label>
		</th>
		<td>
		    <?php echo $next_monday; ?>
		</td>
		<td class="delivery-time">
		    <label for="pickup-monday-late" class="delivery-label delivery-pickup">
			20-22
			<input class="delivery-input delivery-pickup" type="radio" name="pickup_time" id="pickup-monday-late" value="pickup_monday_late">
		    </label>
		</td>
	    </tr>
	    <tr class="woocommerce-orders-table__row">
		<td rowspan="2" class="gray-background">
		    <?php echo $next_tuesday; ?>
		</td>
		<td class="delivery-time gray-background">
		    <label for="pickup-tuesday-early" class="delivery-label delivery-pickup">
			13-14
			<input class="delivery-input delivery-pickup" type="radio" name="pickup_time" id="pickup-tuesday-early" value="pickup_tuesday_early">
		    </label>
		</td>
	    </tr>
	    <tr class="woocommerce-orders-table__row">
		    <td class="delivery-time gray-background">
			<label for="pickup-tuesday-late" class="delivery-label delivery-pickup">
			    20-22
			    <input class="delivery-input delivery-pickup" type="radio" name="pickup_time" id="pickup-tuesday-late" value="pickup_tuesday_late">
			</label>
		    </td>
		</tr>
		
		<tr class="woocommerce-orders-table__row">
		    <td rowspan="2">
			<?php echo $next_wednesday; ?>
		    </td>
		    <td class="delivery-time">
			<label for="pickup-wednesday-early" class="delivery-label delivery-pickup">
			    13-14
			    <input class="delivery-input delivery-pickup" type="radio" name="pickup_time" id="pickup-wednesday-early" value="pickup_wednesday_early">
			</label>
		    </td>
		</tr>
		<tr class="woocommerce-orders-table__row">
		    <td class="delivery-time">
			<label for="pickup-wednesday-late" class="delivery-label delivery-pickup">
			    20-22
			    <input class="delivery-input delivery-pickup" type="radio" name="pickup_time" id="pickup-wednesday-late" value="pickup_wednesday_late">
			</label>
		    </td>
		</tr>

		<tr class="woocommerce-orders-table__row">
		    <th rowspan="5" class="delivery-receive gray-background">
			<?php echo __( 'Receive', 'myc' ) ?>
			<label for="receive-individual">
			    <input class="receive-mode" type="radio" name="pickup_place" value="receive-individual">
			    <?php echo __( 'individually', 'myc' ) ?> (4&euro;)
			</label>
			<label for="receive-group">
			    <input class="receive-mode" type="radio" name="pickup_place" value="receive-group">
			    <?php echo __( 'as group', 'myc' ) ?> (2&euro;)
			</label>
		    </th>
		    <td class="delivery-receive gray-background">
			<?php echo $next_monday; ?>
		    </td>
		    <td class="delivery-time delivery-receive gray-background">
			<label for="receive-monday-late" class="delivery-label delivery-receive">
			    20-22
			    <input class="delivery-input delivery-receive" type="radio" name="pickup_time" id="receive-monday-late" value="receive_monday_late">
			</label>
		    </td>
		</tr>
		<tr class="woocommerce-orders-table__row">
		    <td class="delivery-receive">
			<?php echo $next_tuesday; ?>
		    </td>
		    <td class="delivery-time delivery-receive">
			<label for="receive-tuesday-early" class="delivery-label delivery-receive">
			    14-15:30
			    <input class="delivery-input delivery-receive" type="radio" name="pickup_time" id="receive-tuesday-early" value="receive_tuesday_early">
			</label>
		    </td>
		</tr>
		<tr class="woocommerce-orders-table__row">
		    <td rowspan="2" class="delivery-receive gray-background">
			<?php echo $next_wednesday; ?>
		    </td>
		    <td class="delivery-time delivery-receive gray-background">
			<label for="receive-wednesday-early" class="delivery-label delivery-receive">
			    14-15:30
			    <input class="delivery-input delivery-receive" type="radio" name="pickup_time" id="receive-wednesday-early" value="receive_wednesday_early">
			</label>
		    </td>
		</tr>
		<tr class="woocommerce-orders-table__row">
		    <td class="delivery-time delivery-receive gray-background">
			<label for="receive-wednesday-late" class="delivery-label delivery-receive">
			    20-22
			    <input class="delivery-input delivery-receive" type="radio" name="pickup_time" id="receive-wednesday-late" value="receive_wednesday_late">
			</label>
		    </td>
		</tr>
	</tbody>
    </table>
</div>
<?php 
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

add_action( 'wp_footer', function() {
    $pickup_place = get_user_meta( get_current_user_id(), 'pickup_place' );
    if ( 0 == sizeof($pickup_place) ) {
	$pickup_place = 'pickup-obrador';
    }

    $pickup_time  = get_user_meta( get_current_user_id(), 'pickup_time' );
    if ( 0 == sizeof($pickup_time) ) {
	$pickup_time = 'pickup-monday-late';
    }

    $pickup_or_receive = substr( $pickup_place, 0, strpos( $pickup_place, '-' ) );
    $other_choice = ( 'pickup' == $pickup_or_receive ) ? 'receive' : 'pickup';
?>
<script type="text/javascript">
 jQuery(document).ready(function() {
     jQuery( '#<?php echo $pickup_place ?>' ).prop("checked", true);
     jQuery( '#<?php echo $pickup_time ?>' ).prop("checked", true);
     jQuery( '.delivery-<?php echo $other_choice ?>' ).attr('disabled', 'true');
     function is_one_checked( class_name ) {
	 if ( jQuery( 'input.' + class_name + '[type=radio]:checked' ).length ) {
	     return true;
	 }
	 return false;
     }
     jQuery( '.pickup-place' ).click( function() {
	 jQuery( '.delivery-pickup' ).removeAttr( 'disabled' );
	 jQuery( '.delivery-receive' ).attr( 'disabled', 'true' ).prop("checked", false);
	 if ( ! is_one_checked( 'delivery-pickup' ) ) {
	     jQuery( '#pickup-monday-late' ).prop( 'checked', true );
	 }
     });
     jQuery( '.receive-mode' ).click( function() {
	 jQuery( '.delivery-receive' ).removeAttr( 'disabled' );
	 jQuery( '.delivery-pickup' ).attr( 'disabled', 'true' ).prop("checked", false);	 
	 if ( ! is_one_checked( 'delivery-receive' ) ) {
	     jQuery( '#receive-monday-late' ).prop( 'checked', true );
	 }
     });
 });
</script>
<?php
});
