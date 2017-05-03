<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once(dirname(__FILE__) . "/../../woocommerce/woocommerce.php");

class WC_Product_Ingredient extends WC_Product {
    public function __construct( $product ) {
	$this->product_type = 'ingredient';
	parent::__construct( $product );
    }

    protected $extra_data = array(
	'providers' => array(),
	'prices' => array(),
	'best_recent_price' => array(),
	'base_unit' => array()
    );

    public function get_providers( $context = 'view' ) {
	return $this->get_prop( 'providers', $context );
    }

    public function set_providers( $providers ) {
	$this->set_prop( 'providers', array_filter( wp_parse_id_list( (array) $providers ) ) );
    }

    public function get_prices( $context = 'view' ) {
	return $this->get_prop( 'prices', $context );
    }

    public function set_prices( $prices ) {
	$this->set_prop( 'prices', $prices );
    }

    public function get_best_recent_price( $context = 'view' ) {
	return $this->get_prop( 'best_recent_price', $context );
    }

    public function set_best_recent_price( $best_recent_price ) {
	$this->set_prop( 'best_recent_price', $best_recent_price );
    }

    public function get_base_unit( $context = 'view' ) {
	return $this->get_prop( 'base_unit', $context );
    }

    public function set_base_unit( $base_unit ) {
	$this->set_prop( 'base_unit', $base_unit );
    }

}
?>
