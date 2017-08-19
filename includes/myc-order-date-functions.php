<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function canonicalize_date( $datestr ) {
    return date( 'Y-m-d', strtotime( $datestr ) );
}

function prettify_date( $datestr ) {
    return wc_format_datetime( new WC_DateTime( $datestr ), 'D, j \d\e F \d\e Y' );
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

function order_deadlines_for_processing() {
    $dates = array();
    $term_id = get_term_by( 'slug', 'order_deadline', 'category' )->term_id;
    $dates = array();
    $now  = date( 'Y-m-d', strtotime( 'now' ) );
    $next = date( 'Y-m-d', strtotime( 'now + 2 week' ) );
    foreach( get_term_meta( $term_id, '', false )[ 'order_deadline' ] as $date ) {
	if ( $date >= $now && $date <= $next ) {
	    $dates[] = $date;
	}
    }
    $dates = array_unique( $dates );
    sort( $dates );
    return $dates;
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

function last_delivery_date( $datestr ) {
    $next_monday = date( 'Y-m-d', strtotime( $datestr . ' next Monday' ) );
    return date( 'Y-m-d', strtotime( $next_monday . " + 3 days" ) );
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


