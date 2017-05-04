<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function create_meal() {

    class WC_Product_Meal extends WC_Product {

	public function get_type() {
	    return 'meal';
	}

	protected $extra_data = array(
	    'uses_recipe' => array(),
	    'uses_phys_ingredient' => array()
	);

	public function get_uses_recipe( $context = 'view' ) {
	    return $this->get_prop( 'uses_recipe', $context );
	}

	public function set_uses_recipe( $uses_recipe ) {
	    $this->set_prop( 'uses_recipe', array_filter( wp_parse_id_list( (array) $uses_recipe ) ) );
	}

	public function get_uses_phys_ingredient( $context = 'view' ) {
	    return $this->get_prop( 'uses_phys_ingredient', $context );
	}

	public function set_uses_phys_ingredient( $uses_phys_ingredient ) {
	    $this->set_prop( 'uses_phys_ingredient', array_filter( wp_parse_id_list( (array) $uses_phys_ingredient ) ) );
	}

    }
}
add_action( 'plugins_loaded', 'create_meal' );

