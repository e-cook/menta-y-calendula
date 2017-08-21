<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function create_meal() {

    class WC_Product_Meal extends WC_Product_Simple {

	public function get_type() {
	    return 'meal';
	}

	function is_purchasable() {
	    return true;
	}

	/*
	function is_visible() {
	    $vtd = get_post_meta( $this->get_id(), '_visible_to_date' );
	    error_log($this->get_id() . "; " . var_export($vtd,1));
	    return 'visible' === $this->get_catalog_visibility() &&
		   isset($vtd[0]) &&
		   $vtd[0] > date( 'Y-m-d', strtotime( 'now' ) );
	}
	 */
	
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

	public function get_catalog_visibility_to() {
	    $pm = get_post_meta( $this->get_id(), '_visible_to', false );
	    return ( isset( $pm[0] ) && sizeof( $pm[0] ) > 0 ? $pm[0] : '' );
	}

	public function set_catalog_visibility_to( $to_date ) {
	    update_post_meta( $this->get_id(), '_visible_to', $to_date );
	}
    }

    class WC_Product_Variable_Meal extends WC_Product_Variable {

	public function get_type() {
	    return 'variable_meal';
	}

	public function is_type( $type ) {
	    if ( 'variable' == $type || ( is_array( $type ) && in_array( 'variable', $type ) ) ) {
		return true;
	    } else {
		return parent::is_type( $type );
	    }
	}
	
	function is_purchasable() {
	    return true;
	}
/*
	function is_visible() {
	    $vtd = get_post_meta( $this->get_id(), '_visible_to_date' );
	    error_log($this->get_id() . "; " . var_export($vtd,1));
	    return 'visible' === $this->get_catalog_visibility() &&
		   isset($vtd[0]) &&
		   $vtd[0] > date( 'Y-m-d', strtotime( 'now' ) );
	}
*/	
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

	public function get_catalog_visibility_to() {
	    $pm = get_post_meta( $this->get_id(), '_visible_to_date', false );
	    return ( isset( $pm[0] ) && sizeof( $pm[0] ) > 0 ? $pm[0] : '' );
	}

	public function set_catalog_visibility_to( $to_date ) {
	    update_post_meta( $this->get_id(), '_visible_to_date', $to_date );
	}
    }

}
add_action( 'plugins_loaded', 'create_meal' );

