<?php
if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

/**
* Register the custom product type after init
*/
function register_recipe_product_type() {

    class WC_Product_Recipe extends WC_Product_Grouped {
	public function __construct( $product ) {
	    $this->product_type = 'recipe';
	    parent::__construct( $product );
	}
    }
}
add_action( 'plugins_loaded', 'register_recipe_product_type' );

/**
 * Add to product type drop down.
 */
function add_recipe_product( $types ){
    // Key should be exactly the same as in the class
    $types[ 'recipe' ] = __( 'Recipe' );
    return $types;
}
add_filter( 'product_type_selector', 'add_recipe_product' );

/**
 * Show pricing fields for recipe product.
 */
function recipe_custom_js() {
    if ( 'product' != get_post_type() ) :
    return;
    endif;
?><script type='text/javascript'>
   jQuery( document ).ready( function() {
       jQuery( '.options_group.pricing' ).addClass( 'show_if_recipe' ).show();
   });
</script><?php
	 }

add_action( 'admin_footer', 'recipe_custom_js' );

	 
function wh_recipe_admin_custom_js() {

	 if ('product' != get_post_type()) :
	 return;
	 endif;
	 ?>
    <script type='text/javascript'>
     jQuery(document).ready(function () {
	 // for Price tab
	 jQuery('.product_data_tabs .general_tab').addClass('show_if_recipe').show();
	 jQuery('#general_product_data .pricing').addClass('show_if_recipe').show();
	 // for Inventory tab
	 jQuery('.inventory_options').addClass('show_if_recipe').show();
	 jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_recipe').show();
	 /* jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_recipe').show();
	  * jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_recipe').show();*/
     });
    </script>
<?php

}

add_action('admin_footer', 'wh_recipe_admin_custom_js');
