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

	public function provides_lines() {
	    $items = get_post_meta( $this->get_id(), '_provides', false );
	    if ( ! $items || is_wp_error( $items ) ) {
		error_log( "no provided items found" );
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
    }
}
add_action( 'plugins_loaded', 'create_provider' );

