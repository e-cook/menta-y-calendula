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
	    'purchases' => array(),
	    'myc_stock' => array(),
	    'base_unit' => array()
	);

	public function get_provided_by( $context = 'view' ) {
	    return $this->get_prop( 'provided_by', $context );
	}

	public function set_provided_by( $provided_by ) {
	    $this->set_prop( 'provided_by', array_filter( wp_parse_id_list( (array) $provided_by ) ) );
	}

	public function get_purchases( $context = 'view' ) {
	    return $this->get_prop( 'purchases', $context );
	}

	public function set_purchases( $purchases ) {
	    $this->set_prop( 'purchases', $purchases );
	}

	public function get_myc_stock( $context = 'view' ) {
	    return $this->get_prop( 'myc_stock', $context );
	}

	public function set_myc_stock( $myc_stock ) {
	    $this->set_prop( 'myc_stock', $myc_stock );
	}

	public function get_base_unit( $context = 'view' ) {
	    return $this->get_prop( 'base_unit', $context );
	}

	public function set_base_unit( $base_unit ) {
	    $this->set_prop( 'base_unit', $base_unit );
	}

	public function latest_purchases() {
	    $latest_purchases = new MYC_Latest_Purchases();
	    $latest_purchases->prepare_items();
	    return $latest_purchases->display();
	}
	
    }
}
add_action( 'plugins_loaded', 'create_ingredient' );

