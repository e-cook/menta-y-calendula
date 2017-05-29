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

function order_dates( $enable_for = '' ) {
    $id = get_term_by( 'name', 'order_date', 'category' )->term_id;
    $raw_dates = get_term_meta( $id )[ 'order_date' ];
    sort( $raw_dates );
    $dates = array();
    $now = strtotime( 'now' );
    foreach( $raw_dates as $date ) {
	$dt = strtotime( $date );
	switch ( $enable_for ) {
	    case 'for_ordering': $ordering_threshold = strtotime( $date . ' - 3 days'); break;
	    case 'for_processing': $ordering_threshold = $now; break;
	    default: $ordering_threshold = PHP_INT_MAX;
	}
	if ( $dt >= $now && $ordering_threshold >= $now ) {
	    $dates[] = $date;
	}
    }
    return $dates;
}

function formatted_order_dates() {
    $dates = array();
    foreach ( order_dates() as $date ) {
	$dates[] = array( 'order_date' => prettify_date( $date ) );
    }
    return $dates;
}

function next_order_date() {
    $now = canonicalize_date( 'now' );
    foreach( order_dates( 'for_ordering' ) as $date ) {
	if ( $date >= $now ) {
	    return $date;
	}	    
    }
    return '';
}


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


