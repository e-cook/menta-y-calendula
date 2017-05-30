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
    <h2><?php echo __( 'You need to cook', 'myc' ); ?></h2>
    <div id="table-wrapper"></div>
    <br/>
    <button class="button" id="download-pdf-button"><?php echo __( 'Download pdf' )?></button>
<?php
}

add_action( 'admin_footer', function() {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 // handle date selector
	 jQuery( '#download-pdf-button' ).hide();
	 jQuery( '#what-to-cook' ).change( function() {
	     jQuery.post( ajaxurl, {
		 'action' : 'show_what_to_cook',
		 'date'   : jQuery( '#what-to-cook' ).val(),
		 '_nonce' : '<?php echo wp_create_nonce( 'what_to_cook' ) ?>'
	     },
			  function( response ) {
			      jQuery( '#table-wrapper' ).html(response);
			      jQuery( '#download-pdf-button' ).show();
			  });
	 });
	 jQuery( '#download-pdf-button' ).click( function() {
	     jQuery.post( ajaxurl, {
		 'action' : 'show_what_to_cook_pdf',
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

function cook_table_data( $date ) {
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
			       . 'and meta_value="' . $date . '"', ARRAY_N ) as $result_order ) {
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
    return array( 'customers' => $customers,
		  'meals' => $meals,
		  'table' => $table );
}

function cook_table_html( $customers, $meals, $table ) {
    $out = '<table cellspacing="0" cellpadding="1" border="1">'."\n".'<tr><th></th>';
    foreach ( $customers as $c => $v ) {
	$out .= "<th>$c</th>";
    }
    $out .= "</tr>\n";
    foreach ( $meals as $m => $v ) {
	$out .= "<tr><td>$m</td>";
	foreach ( $customers as $c => $v ) {
	    $out .= '<td>' .
		    ( isset( $table[ $m ][ $c ] )
		    ? $table[ $m ][ $c ]
		    : '&nbsp;' )
		  . '</td>';
	}
	$out .= "</tr>\n";
    }
    $out .= '</table>';
    return $out;
}    

add_action( 'wp_ajax_show_what_to_cook', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'what_to_cook' ) ) {
	wp_die( "Don't mess with me!" );
    }
    $cd = cook_table_data( $_POST[ 'date' ] );
    echo cook_table_html( $cd[ 'customers' ], $cd[ 'meals' ], $cd[ 'table' ] );
    wp_die();
});

add_action( 'wp_ajax_show_what_to_cook_pdf', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'what_to_cook' ) ) {
	wp_die( "Don't mess with me!" );
    }

    $date = $_POST[ 'date' ];
    
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

    // set some language-dependent strings (optional)
    /* if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
       require_once(dirname(__FILE__).'/lang/eng.php');
       $pdf->setLanguageArray($l);
     * }
     */
    // ---------------------------------------------------------

    // set font
    $pdf->SetFont('helvetica', 'B', 20);

    $cd   = cook_table_data( $date );
    $html = cook_table_html( $cd[ 'customers' ], $cd[ 'meals' ], $cd[ 'table' ] );
    
    // add a page
    $pdf->AddPage();
    $pdf->Write(0, 'What to cook on ' . $date, '', 0, 'L', true, 0, false, false, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->writeHTML($html, true, false, false, false, '');
    $pdf->Output("/tmp/cook-for-$date.pdf", 'F');
    wp_die();
});
