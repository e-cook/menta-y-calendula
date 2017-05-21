<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function create_meal() {

    class WC_Product_Meal extends WC_Product_Simple {

	public function get_type() {
	    return 'meal';
	}

	protected $extra_data = array(
	    'uses_recipe' => array(),
	);

	public function get_uses_recipe( $context = 'view' ) {
	    return $this->get_prop( 'uses_recipe', $context );
	}

	public function set_uses_recipe( $uses_recipe ) {
	    $this->set_prop( 'uses_recipe', array_filter( wp_parse_id_list( (array) $uses_recipe ) ) );
	}

	public function uses_recipe_lines() {
	    $items = get_post_meta( $this->get_id(), '_uses_recipe', false );
	    if ( ! $items || is_wp_error( $items ) ) {
		error_log( "no items found" );
		return array();
	    }
	    $result = array();
	    foreach ($items as $i) {
		if ($i) {
		    $result[] = array(
			'recipe_id' => $i['recipe_id'],
		    );
		}
	    }
	    return $result;
	}
    }
}
add_action( 'plugins_loaded', 'create_meal' );

