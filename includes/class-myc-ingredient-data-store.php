<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Product_Ingredient_Data_Store_CPT extends WC_Product_Grouped_Data_Store_CPT implements WC_Object_Data_Store_Interface {

    /**
     * Helper method that updates all the post meta for an ingredient.
     *
     * @param WC_Product
     * @param bool Force update. Used during create.
     * @since 3.0.0
     */
    protected function add_post_meta( &$product, $force = false ) {
	error_log("add_post_meta");
	$meta_key_to_props = array(
	    '_providers' => 'providers',
	    '_prices' => 'prices',
	    '_best_recent_price' => 'best_recent_price',
	    '_base_unit' >= 'base_unit'
	);

	$props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $product, $meta_key_to_props );
	error_log("props_to_update: " . var_dump($props_to_update));
	foreach ( $props_to_update as $meta_key => $prop ) {
	    $value   = $product->{"get_$prop"}( 'edit' );
	    $updated = update_post_meta( $product->get_id(), $meta_key, $value );
	    if ( $updated ) {
		$this->updated_props[] = $prop;
	    }
	}

	parent::add_post_meta( $product, $force );
    }

    /**
     * Handle updated meta props after updating meta data.
     *
     * @since  3.0.0
     * @param  WC_Product $product
     */
    protected function handle_updated_props( &$product ) {
	if ( in_array( 'prices', $this->updated_props ) ) {
	    $last_price = array_slice($product->get_prices(), -1)[0];
	    $best_recent_price = $product->get_best_recent_price() ? $product->get_best_recent_price() : array('1900-1-1', 100000);
	    if ($last_price[1] < $best_recent_price[1]) {
		$product->set_best_recent_price($last_price);
		delete_post_meta( $product->get_id(), '_best_recent_price' );
		add_post_meta( $product->get_id(), '_best_recent_price', $last_price );
	    }
	}
	parent::handle_updated_props( $product );
    }

    /**
     * Sync grouped product prices with children.
     *
     * @since 3.0.0
     * @param WC_Product|int $product
     */
    /* public function sync_price( &$product ) {
       global $wpdb;

       $children_ids = get_posts( array(
       'post_parent' => $product->get_id(),
       'post_type'   => 'product',
       'fields'      => 'ids',
       ) );
       $prices = $children_ids ? array_unique( $wpdb->get_col( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_price' AND post_id IN ( " . implode( ',', array_map( 'absint', $children_ids ) ) . " )" ) ) : array();

       delete_post_meta( $product->get_id(), '_price' );
       delete_transient( 'wc_var_prices_' . $product->get_id() );

       if ( $prices ) {
       sort( $prices );
       // To allow sorting and filtering by multiple values, we have no choice but to store child prices in this manner.
       foreach ( $prices as $price ) {
       add_post_meta( $product->get_id(), '_price', $price, false );
       }
       }
     * }*/
}
