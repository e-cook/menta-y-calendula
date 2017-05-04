<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once(dirname(__FILE__) . '/class-myc-list-purchases.php');

function create_ingredient() {

    class WC_Product_Ingredient extends WC_Product {

	public function get_type() {
	    return 'ingredient';
	}

	protected $extra_data = array(
	    'provided_by' => array(),
	    'was_purchased' => array(),
	    'changed_stock' => array(),
	    'base_unit' => array()
	);

	public function get_provided_by( $context = 'view' ) {
	    return $this->get_prop( 'provided_by', $context );
	}

	public function set_provided_by( $provided_by ) {
	    $this->set_prop( 'provided_by', array_filter( wp_parse_id_list( (array) $provided_by ) ) );
	}

	public function get_was_purchased( $context = 'view' ) {
	    return $this->get_prop( 'was_purchased', $context );
	}

	public function set_was_purchased( $was_purchased ) {
	    $this->set_prop( 'was_purchased', $was_purchased );
	}

	public function get_changed_stock( $context = 'view' ) {
	    return $this->get_prop( 'changed_stock', $context );
	}

	public function set_changed_stock( $changed_stock ) {
	    $this->set_prop( 'changed_stock', $changed_stock );
	}

	public function get_base_unit( $context = 'view' ) {
	    return $this->get_prop( 'base_unit', $context );
	}

	public function set_base_unit( $base_unit ) {
	    $this->set_prop( 'base_unit', $base_unit );
	}

	public function latest_purchases() {
	    $purchases = get_post_meta( $this->get_id(), '_was_purchased', false );
	    $base_unit = get_post_meta( $this->get_id(), '_base_unit', true);
	    $latest = array();
	    foreach ($purchases as $p) {
		if ($p) {
		    $unit_price = (float) $p['price'] / (float) $p['qty'];
		    $latest[] = array(
			'purchase_date' => $p['date'],
			'provider'      => $p['provider'],
			'qty_unit'      => $p['qty'] . $base_unit,
			'price_paid'    => $p['price'],
			'unit_price'    => round( 100 * $unit_price ) / 100
		    );
		}
	    }
	    return $latest;
	}
    }
}
add_action( 'plugins_loaded', 'create_ingredient' );

