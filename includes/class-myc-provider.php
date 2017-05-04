<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function create_provider() {
    
    class WC_Product_Provider extends WC_Product {

	public function get_type() {
	    return 'provider';
	}

	protected $extra_data = array(
	    'provides' => array()
	);

	public function get_provides( $context = 'view' ) {
	    return $this->get_prop( 'provides', $context );
	}

	public function set_provides( $provides ) {
	    $this->set_prop( 'provides', array_filter( wp_parse_id_list( (array) $provides ) ) );
	}

    }
}
add_action( 'plugins_loaded', 'create_provider' );

