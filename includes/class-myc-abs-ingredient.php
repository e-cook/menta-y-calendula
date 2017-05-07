<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function create_abs_ingredient() {

    class WC_Product_Abs_Ingredient extends WC_Product {

	public function get_type() {
	    return 'abs_ingredient';
	}

	protected $extra_data = array(
	    'has_instance' => array(),
	);

	public function get_has_instance( $context = 'view' ) {
	    return $this->get_prop( 'has_instance', $context );
	}

	public function set_has_instance( $instance ) {
	    $this->set_prop( 'has_instance', array_filter( wp_parse_id_list( (array) $instance ) ) );
	}

	public function instances() {
	    $items = get_post_meta( $this->get_id(), '_has_instance', false );
	    if ( ! $items || is_wp_error( $items ) ) {
		return array();
	    }
	    $result = array();
	    foreach ($items as $i) {
		if ($i) {
		    $result[] = array(
			'phys_ingredient_id' => $i['phys_ingredient_id'],
		    );
		}
	    }
	    return $result;
	}

	public function total_inventory() {
	    $items = get_post_meta( $this->get_id(), '_has_instance', false );
	    if ( ! $items || is_wp_error( $items ) ) {
		error_log( "no instances found" );
		return array();
	    }
	    $result = array();
	    foreach ($items as $i) {
		if ($i) {
		    $result[] = array(
			'phys_ingredient_id' => $i['phys_ingredient_id'],
			'qty'                => $i['qty'],
		    );
		}
	    }
	    return $result;
	}

	public function total_purchases() {
	    $items = get_post_meta( $this->get_id(), '_has_instance', false );
	    if ( ! $items || is_wp_error( $items ) ) {
		error_log( "no instances found" );
		return array();
	    }
	    $result = array();
	    foreach ($items as $i) {
		if ($i) {
		    $result[] = array(
			'date'               => $i['date'],
			'phys_ingredient_id' => $i['phys_ingredient_id'],
			'unit_price'         => $i['unit_price'],
		    );
		}
	    }
	    return $result;
	}
    }
}
add_action( 'plugins_loaded', 'create_abs_ingredient' );

