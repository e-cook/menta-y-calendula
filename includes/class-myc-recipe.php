<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Product_Recipe extends WC_Product {
    public function __construct( $product ) {
	$this->product_type = 'recipe';
	parent::__construct( $product );
    }
    protected $extra_data = array(
	'recipe_lines' => array()
    );

    public function get_type() {
	return 'recipe';
    }

    public function get_recipe_lines( $context = 'view' ) {
	return $this->get_prop( 'recipe_lines', $context );
    }

    public function set_recipe_lines( $recipe_lines ) {
	$this->set_prop( 'recipe_lines', $recipe_lines );
    }
}
