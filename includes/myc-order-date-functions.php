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

function order_deadlines( $enable_for = '' ) {
    $id = get_term_by( 'name', 'order_deadline', 'category' )->term_id;
    $raw_dates = get_term_meta( $id )[ 'order_deadline' ];
    sort( $raw_dates );
    $dates = array();
    $now = strtotime( 'now' );
    foreach( $raw_dates as $date ) {
	$dt = strtotime( $date );
	switch ( $enable_for ) {
	    case 'for_ordering': $deadline_threshold = $now; break;
	    case 'for_processing': $deadline_threshold = strtotime( $date . ' + 1 week'); break;
	    default: $deadline_threshold = PHP_INT_MAX;
	}
	if ( $deadline_threshold >= $now ) {
	    $dates[] = $date;
	}
    }
    return $dates;
}

function formatted_order_deadlines() {
    $dates = array();
    foreach ( order_deadlines() as $date ) {
	$dates[] = array( 'order_deadline' => prettify_date( $date ) );
    }
    return $dates;
}

function next_order_deadline() {
    $now = canonicalize_date( 'now' );
    foreach( order_deadlines( 'for_ordering' ) as $date ) {
	if ( $date >= $now ) {
	    return $date;
	}	    
    }
    return '';
}

function valid_delivery_dates() {
    $vod = array();
    foreach ( order_deadlines( 'for_ordering' ) as $deadline_date ) {
	$next_monday = date( 'Y-m-d', strtotime( $deadline_date . ' next Monday' ) );
	$vod[] = $next_monday;
	foreach ( array( 1, 2, 3 ) as $i ) {
	    $vod[] = date( 'Y-m-d', strtotime( $next_monday . " + $i days" ) );
	}
    }
    return $vod;
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


