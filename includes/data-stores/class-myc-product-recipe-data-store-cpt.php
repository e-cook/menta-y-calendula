<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function create_recipe_store() {
    /**
     * WC Recipe Product Data Store: Stored in CPT.
     *
     * @version  0.1
     * @category Class
     * @author   MYC
     */
    class WC_Product_Recipe_Data_Store_CPT extends WC_Product_Data_Store_CPT implements WC_Object_Data_Store_Interface {

	/**
	 * Helper method that updates all the post meta for an recipe.
	 *
	 * @param WC_Product_Recipe
	 * @param bool Force update. Used during create.
	 * @since 3.0.0
	 */
	protected function update_post_meta( &$product, $force = false ) {
	    error_log("update_post_meta");
	    $meta_key_to_props = array(
		'_provided_by' => 'provided_by',
		'_purchases' => 'purchases',
		'_myc_stock' => 'myc_stock',
		'_base_unit' => 'base_unit',
	    );

	    $props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $product, $meta_key_to_props );

	    foreach ( $props_to_update as $meta_key => $prop ) {
		$value   = $product->{"get_$prop"}( 'edit' );
		$updated = update_post_meta( $product->get_id(), $meta_key, $value );
		if ( $updated ) {
		    $this->updated_props[] = $prop;
		}
	    }

	    parent::update_post_meta( $product, $force );
	}

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @since  3.0.0
	 * @param  WC_Product $product
	 */
	/*
	   protected function handle_updated_props( &$product ) {
	   if ( in_array( 'children', $this->updated_props ) ) {
	   $child_prices = array();
	   foreach ( $product->get_children( 'edit' ) as $child_id ) {
	   $child = wc_get_product( $child_id );
	   if ( $child ) {
	   $child_prices[] = $child->get_price();
	   }
	   }
	   $child_prices = array_filter( $child_prices );
	   delete_post_meta( $product->get_id(), '_price' );

	   if ( ! empty( $child_prices ) ) {
	   add_post_meta( $product->get_id(), '_price', min( $child_prices ) );
	   add_post_meta( $product->get_id(), '_price', max( $child_prices ) );
	   }
	   }
	   parent::handle_updated_props( $product );
	   }
	 */
	/**
	 * Sync grouped product prices with children.
	 *
	 * @since 3.0.0
	 * @param WC_Product|int $product
	 */
	/*
	   public function sync_price( &$product ) {
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
	   }
	 */
    }
}
add_action( 'plugins_loaded', 'create_recipe_store' );
