<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function create_recipe() {
    
    class WC_Product_Recipe extends WC_Product {

	public function get_type() {
	    return 'recipe';
	}

	protected $extra_data = array(
	    'recipe_lines' => array()
	);

	public function get_recipe_lines( $context = 'view' ) {
	    return $this->get_prop( 'recipe_lines', $context );
	}

	public function set_recipe_lines( $recipe_lines ) {
	    $this->set_prop( 'recipe_lines', $recipe_lines );
	}
    }
}
add_action( 'plugins_loaded', 'create_recipe' );
