<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( dirname(__FILE__) . '/myc-order-date-functions.php' );


function what_to_cook_page() {
    echo '<h2>' . __( 'For the orders received on', 'myc' ) . '</h2>';
?>
    <div class="options_group">
	<label for="what-to-cook" class="screen-reader-text"><?php _e( 'Select date for order' ); ?></label>
	<select name="d" id="what-to-cook">
	    <?php
	    foreach( order_dates( 'for_processing' ) as $date ) {
		printf( "<option value='%s'>%s</option>\n", $date, prettify_date( $date ) );
	    }
	    ?>
	</select>
    </div>
    <?php
    echo '<h2>' . __( 'You need to cook', 'myc' ) . '</h2>';
    echo '<div id="table-wrapper"></div>';
    }

    add_action( 'admin_footer', function() {?>
	<script type="text/javascript">
	 jQuery(document).ready(function() {
	     // handle date selector
	     jQuery( '#what-to-cook' ).change( function() {
		 jQuery.post( ajaxurl, {
		     'action' : 'show_what_to_cook',
		     'date'   : jQuery( '#what-to-cook' ).val(),
		     '_nonce' : '<?php echo wp_create_nonce( 'what_to_cook' ) ?>'
		 },
			      function( response ) {
				  jQuery( '#table-wrapper' ).html(response);
			      });
	     });
	 });
	</script>
    <?php
    });

    add_action( 'wp_ajax_show_what_to_cook', function() {
	if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'what_to_cook' ) ) {
	    wp_die( "Don't mess with me!" );
	}
	/*
	   Will make a table
     	   xxxxx| customer | cust | cust
	   -----+-------------------------
	   meal |  qty     |  qty |
	   meal |  qty     |  qty |
	 */
	$table = array();
	$customers = array();
	$meals = array();
	
	global $wpdb;
	$prefix = $wpdb->prefix;
	foreach( $wpdb->get_results( 'select post_id '
				   . "from {$prefix}postmeta "
				   . 'where meta_key="_order_date" '
				   . 'and meta_value="' . $_POST[ 'date' ] . '"', ARRAY_N ) as $result_order ) {
	    $order_id = $result_order[0];
	    $customer = implode( '|' , array(
		get_post_meta( $order_id, '_billing_last_name', true ),
		get_post_meta( $order_id, '_billing_first_name', true ),
		get_post_meta( $order_id, '_customer_user',  true ),
	    ) );
	    $customers[ $customer ] = 0;
	    foreach( $wpdb->get_results( 'select order_item_id, order_item_name '
				       . "from {$prefix}woocommerce_order_items "
				       . 'where order_id=' . $order_id, ARRAY_N ) as $result_order_item ) {
		$oi_id = $result_order_item[0];
		$oi_name = $result_order_item[1];
		$qty = (int) wc_get_order_item_meta( $oi_id, '_qty' );
		$var = wc_get_order_item_meta( $oi_id, '_variation_id' );
		$meal = implode( '|', array( $oi_name, $var ) );
		$meals[ $meal ] = 0;
		if ( ! isset( $table[ $meal ] ) ) {
		    $table[ $meal ] = array();
		}
		if ( isset( $table[ $meal ][ $customer ] ) ) {
		    $qty += (int) $table[ $meal ][ $customer ];
		}
		$table[ $meal ][ $customer ] = $qty;		
	    }
	}
	ksort( $customers );
	ksort( $meals );
	echo "<table>\n<tr><th/>";
	foreach ( $customers as $c => $v ) {
	    echo "<th>$c</th>";
	}
	echo "</tr>\n";
	foreach ( $meals as $m => $v ) {
	    echo "<tr><td>$m</td>";
	    foreach ( $customers as $c => $v ) {
		if ( isset( $table[ $m ][ $c ] ) ) {
		    echo '<td>' . $table[ $m ][ $c ] . '</td>';
		}
	    }
	    echo "</tr>\n";
	}
	echo '</table>';
	wp_die();
    });

    /*
       post_meta:
       62 _billing_last_name cust2
       62 _billing_first_name cust1
       62 _date_for_order
     */
