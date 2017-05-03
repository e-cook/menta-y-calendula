<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

//require_once(dirname(__FILE__) . "/../../woocommerce/woocommerce.php");

class WC_Product_Provider extends WC_Product {
    public function __construct( $product ) {
	$this->product_type = 'provider';
	parent::__construct( $product );
    }

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
?>
