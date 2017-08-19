<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( dirname(__FILE__) . '/myc-order-date-functions.php' );


function what_to_cook_page() {
    echo '<h2>' . __( 'For the orders with deadline on', 'myc' ) . '</h2>';
?>
    <div class="options_group">
	<label for="what-to-cook" class="screen-reader-text"><?php _e( 'Select deadline for order' ); ?></label>
	<select name="d" id="what-to-cook">
	    <?php
	    foreach( order_deadlines_for_processing() as $date ) {
		printf( "<option value='%s'>%s</option>\n", $date, prettify_date( $date ) );
	    }
	    ?>
	</select>
    </div>
    <h2><?php echo __( 'You need to cook', 'myc' ); ?>: &nbsp;&nbsp;<a id="download-pdf-button" href="<?php echo wp_upload_dir()[ 'url' ] . '/cook.pdf' ?>">(<?php echo __( 'Download pdf', 'myc' )?>)</a>
    </h2>
    <div id="table-wrapper"></div>
<?php
}

add_action( 'admin_footer', function() {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 // handle date selector
	 jQuery( '#download-pdf-button' ).hide();
	 function wtc() {
	     var cook_date = jQuery( '#what-to-cook' ).val();
	     jQuery.post( ajaxurl, {
		 'action' : 'show_what_to_cook',
		 'date'   : cook_date,
		 '_nonce' : '<?php echo wp_create_nonce( 'what_to_cook' ) ?>'
	     },
			  function( response ) {
			      jQuery( '#table-wrapper' ).html(response);
			      jQuery( '#download-pdf-button' ).show();
			  });
	 }
	 jQuery( '#what-to-cook' ).change( wtc() );
     });
    </script>
<?php
});

function process_order_item( $oi_id, $meal_name, $delivery_date, $customer, &$meals_of_category, &$table) {
    $product_id = wc_get_order_item_meta( $oi_id, '_product_id' );
    $meal_category = '';
    foreach( wc_get_product_terms( $product_id, 'product_cat' ) as $term ) {
	$meal_category = $term->name;
    }
    if ( $meal_category == '' ) {
	$meal_category = __( 'other', 'myc' );
    }
    
    $qty = (int) wc_get_order_item_meta( $oi_id, '_qty' );

    if ( ! isset( $meals_of_category[ $meal_category ] ) ) {
	$meals_of_category[ $meal_category ] = array();
    }
    $meals_of_category[ $meal_category ][] = $meal_name;

    if ( ! isset( $table[ $delivery_date ] ) ) {
	$table[ $delivery_date ] = array();
    }
    if ( ! isset( $table[ $delivery_date ][ $meal_name ] ) ) {
	$table[ $delivery_date ][ $meal_name ] = array();
    }
    if ( isset( $table[ $delivery_date ][ $meal_name ][ $customer ] ) ) {
	$qty += (int) $table[ $delivery_date ][ $meal_name ][ $customer ];
    }
    $table[ $delivery_date ][ $meal_name ][ $customer ] = $qty;
}    

function cook_table_data( $date ) {
    /*
       Will make a table

       | delivery date | date 1 | date 2 |       | date 3 |
       |---------------+--------+--------+-------+--------|
       | customer      | cust1  | cust1  | cust2 | cust2  |
       |---------------+--------+--------+-------+--------|
       | product 1     | qty    | qty    | qty   | qty    |
       | product 2     | qty    | qty    | qty   | qty    |

       which is indexed $table[$date][$cust][$product]

     */
    $table = array();
    $meals_of_category = array();
    $delivery_on = array();
    
    global $wpdb;
    $prefix = $wpdb->prefix;
    foreach( $wpdb->get_results( 'SELECT post_id '
			       . "FROM {$prefix}postmeta "
			       . 'WHERE meta_key="_shipping_delivery_date" '
			       . 'AND meta_value BETWEEN "' . $date . '" AND "' . last_delivery_date( $date ) . '"', ARRAY_N ) as $result_order ) {
	$order_id = $result_order[0];
	$customer = implode( ' ' , array(
	    get_post_meta( $order_id, '_billing_first_name', true ),
	    get_post_meta( $order_id, '_billing_last_name', true ),
	) );

	$delivery_date = get_post_meta( $order_id, '_delivery_date', true );
	if ( ! isset( $delivery_on[ $delivery_date ] ) ) {
	    $delivery_on[ $delivery_date ] = array();
	}
	$delivery_on[ $delivery_date ][ $customer ] = 0;

	foreach( $wpdb->get_results( 'select order_item_id, order_item_name '
				   . "from {$prefix}woocommerce_order_items "
				   . "where order_id={$order_id} and order_item_type='line_item'", ARRAY_N ) as $result_order_item ) {
	    process_order_item( $result_order_item[0], $result_order_item[1], $delivery_date, $customer, $meals_of_category, $table );
	}
    }
    foreach ( array_keys( $meals_of_category ) as $cat ) {
	$meals = array_unique( $meals_of_category[ $cat ] );
	ksort( $meals );
	$meals_of_category[ $cat ] = $meals;
    }
    ksort( $meals_of_category );
    ksort( $delivery_on );
    $delivery_on_sorted = array();
    foreach ( $delivery_on as $date => $c ) {
	$customers = array_keys( $c );
	ksort( $customers );
	$delivery_on_sorted[ $date ] = $customers;
    }
    return array( 'meals_of_category' => $meals_of_category,
		  'table'             => $table,
		  'delivery_on'       => $delivery_on_sorted,
    );
}

function cook_table_category_html( $cat, $meals, $delivery_on, $table, &$out ) {
    $background_color = '#ffe3bc';
    $background_color_categories = '#e9c4f5';
    
    $out .= '<tr style="background-color:' . $background_color_categories . ';"><td colspan="100" align="center"><i>' . $cat . "</i></td></tr>\n";
    
    $mod2 = 0;
    foreach ( $meals as $m ) {
	if ( 0 == $mod2 ) {
	    $mod2 = 1;
	    $out .= "<tr><td><strong>$m</strong></td>";
	} else {
	    $mod2 = 0;
	    $out .= '<tr style="background-color:' . $background_color . ';"><td><strong>' . $m . '</strong></td>';
	}
	$total = 0;
	foreach ( $delivery_on as $date => $customers ) {
	    foreach ( $customers as $c ) {
		if( isset( $table[ $date ][ $m ][ $c ] ) ) { 
		    $out .= '<td align="center">' . $table[ $date ][ $m ][ $c ] . '</td>';
		    $total += $table[ $date ][ $m ][ $c ];
		} else {
		    $out .= '<td>&nbsp;</td>';
		}
	    }
	}
	$out .= "<td align=\"center\"><strong>$total</strong></td></tr>\n";
    }
}

function cook_table_html( $meals_of_category, $table, $delivery_on ) {
    $background_color = '#ffe3bc';

    $out = '<table class="what-to-cook-table">'."\n".'<tr><th><strong>' . __( 'Delivery date', 'myc' ) . '</strong></th>';
    foreach ( $delivery_on as $date => $customers ) {
	$out .= '<th colspan="' . sizeof( $customers ) . '" align="center"><strong>' . prettify_date_noyear( $date ) .'</strong></th>'; 
    }
    $out .= '<th align="center"><strong>' . __( 'Total', 'myc' ) . "</strong></th></tr>\n";

    $out .= '<tr style="background-color:' . $background_color . ';"><td></td>'; 
    foreach ( $delivery_on as $date => $customers ) {
	foreach ( $customers as $c ) {
	    $out .= "<td><div class='myc-customer-wrap'><div class='myc-customer' align=\"center\"><strong>$c</strong></div></div></td>";
	}
    }
    $out .= "<td>&nbsp;</td></tr>\n";

    foreach( $meals_of_category as $cat => $meals ) {
	cook_table_category_html( $cat, $meals, $delivery_on, $table, $out );
    }
    $out .= '</table>';
    return $out;
}

function create_pdf( $date, $html ) {
    require_once( dirname(__FILE__) . '/../assets/php/tcpdf/tcpdf_config.php' );
    require_once( dirname(__FILE__) . '/../assets/php/tcpdf/tcpdf.php' );

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Menta y Calendula');
    $pdf->SetTitle("What to cook on $date");

    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // ---------------------------------------------------------

    // set font
    $pdf->SetFont('helvetica', 'B', 20);

    $cd   = cook_table_data( $date );
    $html = '<style>' . file_get_contents( dirname(__FILE__) . '/../assets/css/myc.css' ) . '</style>'
	  . cook_table_html( $cd[ 'meals_of_category' ], $cd[ 'table' ], $cd[ 'delivery_on' ] );
    
    // add a page
    $pdf->AddPage();
    $pdf->Write(0, 'What to cook on ' . $date, '', 0, 'L', true, 0, false, false, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->writeHTML($html, true, false, false, false, '');
    $pdf->Output( wp_upload_dir()[ 'path' ] . "/cook.pdf", 'F');
}

add_action( 'wp_ajax_show_what_to_cook', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'what_to_cook' ) ) {
	wp_die( "Don't mess with me!" );
    }
    if ( isset( $_POST[ 'date' ] ) ) {
	$date = $_POST[ 'date' ];
	$cd = cook_table_data( $date );
	$html = cook_table_html( $cd[ 'meals_of_category' ], $cd[ 'table' ], $cd[ 'delivery_on' ] );
	create_pdf( $date, $html );
	echo $html;
	wp_die();
    }
});
