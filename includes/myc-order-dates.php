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
	 jQuery( '#save-date-button' ).click( function() {
	     var new_date = validateDate( jQuery( '.datepicker' ).val() );
	     if ( new_date == false ) {
		 alert( "<?php echo __('invalid date') ?>" );
		 return;
	     }
	     jQuery.post( ajaxurl, {
		 'action' : 'save_new_order_date',
		 'date'   : new_date,
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
    $new_date = $_POST['date'];
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
