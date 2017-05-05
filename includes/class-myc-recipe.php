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
	    'has_line' => array()
	);

	public function get_has_line( $context = 'view' ) {
	    return $this->get_prop( 'has_line', $context );
	}

	public function set_has_line( $line ) {
	    $this->set_prop( 'has_line', $line );
	}

	public function recipe_lines() {
	    $recipe_lines = get_post_meta( $this->get_id(), '_has_line', false );
	    if ( ! $recipe_lines || is_wp_error( $recipe_lines ) ) {
		error_log( "no recipe lines found" );
		return array();
	    }
	    $lines = array();
	    foreach ($recipe_lines as $l) {
		if ($l) {
		    $base_unit = get_post_meta( $l['ingredient_id'], '_base_unit', true);
		    $lines[] = array(
			'ingredient' => $l['ingredient_id'],
			'qty_unit'   => $l['qty'] . $base_unit,
		    );
		}
	    }
	    return $lines;
	}
    }
}
add_action( 'plugins_loaded', 'create_recipe' );
