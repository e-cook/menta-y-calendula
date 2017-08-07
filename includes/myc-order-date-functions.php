<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function canonicalize_date( $datestr ) {
    return date( 'Y-m-d', strtotime( $datestr ) );
}

function prettify_date( $datestr ) {
    return $datestr . ' (' . date( 'D', strtotime( $datestr ) ) . ')';
}

function prettify_date_noyear( $datestr ) {
    return date( 'D d M', strtotime( $datestr ) );
}

function order_deadlines_for_ordering() {
    $id = get_term_by( 'name', 'order_deadline', 'category' )->term_id;
    $raw_dates = get_term_meta( $id )[ 'order_deadline' ];
    sort( $raw_dates );
    $dates = array();
    $now = date( 'Y-m-d', strtotime( 'now' ) );
    foreach( $raw_dates as $date ) {
	if ( $date >= $now ) {
	    $dates[] = $date;
	}
    }
    return $dates;
}

function order_dates_for_processing() {
    $dates = array();
    /*
       foreach( get_posts( array( 'post_type'      => 'shop_order',
       'post_status'    => 'wc_processing',
       'posts_per_page' => -1,
       'meta_query'     => array(
       array( 'key'       => '_delivery_date',
       'value'     => array( date( 'Y-m-d', strtotime( 'now' ) ),
       date( 'Y-m-d', strtotime( 'now + 2 week' ) ) ),
       'type'      => 'date',
       'compare'   => 'between',
       'inclusive' => true ) ) ) ) as $post ) {
       $dates[] = substr( $post->post_date, 0, 10 );
       }
     */
    $term_id = get_term_by( 'slug', 'order_deadline', 'category' )->term_id;
    $dates = array();
    $now  = date( 'Y-m-d', strtotime( 'now' ) );
    $next = date( 'Y-m-d', strtotime( 'now + 2 week' ) );
    foreach( get_term_meta( $term_id, '', false ) as $date ) {
	error_log("checking date " . var_export($date,1));
	if ( $date[0] >= $now && $date[0] <= $next ) {
	    $dates[] = $date[0];
	}
    }
    return array_unique( $dates );
}

function formatted_order_deadlines_for_ordering() {
    $dates = array();
    foreach ( order_deadlines_for_ordering() as $date ) {
	$dates[] = array( 'order_deadline' => prettify_date( $date ) );
    }
    return $dates;
}

function next_order_deadline_for_ordering() {
    $now = canonicalize_date( 'now' );
    foreach( order_deadlines_for_ordering() as $date ) {
	if ( $date >= $now ) {
	    return $date;
	}	    
    }
    return '';
}

function valid_delivery_dates() {
    $vdd = array();
    foreach ( order_deadlines_for_ordering() as $deadline_date ) {
	$next_monday = date( 'Y-m-d', strtotime( $deadline_date . ' next Monday' ) );
	foreach ( array( 0, 1, 2, 3 ) as $i ) {
	    $vdd[] = date( 'Y-m-d', strtotime( $next_monday . " + $i days" ) );
	}
    }
    return $vdd;
}

function php_array_2_js( $arr ) {
    $js_arr = '[';
    $ct = 0;
    foreach ( $arr as $a ) {
	if ( $ct > 0 ) {
	    $js_arr .= ',';
	} else {
	    $ct = 1;
	}
	$js_arr .= '"' . $a . '"';
    }
    return $js_arr . ']';
}


