<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function remove_products_from_dropdown( $types ){
    // Key should be exactly the same as in the class
    return array('ingredient' => __( 'Ingredient' ),
		 'recipe' => __('Recipe' ) );
}
add_filter( 'product_type_selector', 'remove_products_from_dropdown' );
