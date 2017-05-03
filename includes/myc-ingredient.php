<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the custom product type after init
 */
/* function add_stores($stores) {
 *     error_log("add_stores");
 *     $stores['_acquisition'] = 'WC_Product_Ingredient_Data_Store_CPT';
 *     return $stores;
 * }
 * */
function register_ingredient_product_type() {
    require_once(dirname(__FILE__) . '/class-myc-ingredient.php');
//    require_once(dirname(__FILE__) . '/class-myc-ingredient-data-store.php');

//    add_filter( 'woocommerce_data_stores', 'add_stores' );

}
add_action( 'plugins_loaded', 'register_ingredient_product_type' );


function add_acquisition_fields() {
    error_log("add_acquisition_fields");
    global $woocommerce, $post;
    echo '<div class="options_group">';
    // Custom field Type
?>
    <p class="form-field _previous_prices">
	<label for="_previous_prices"><?php echo __( 'Previous prices', 'woocommerce' ); ?></label>
	<span class="wrap">
	    <?php $prices = get_post_meta( $post->ID, '_prices', false );
	    if ($prices) {
		echo '<table><tr><th>'
		   . __( 'Date' ) . '</th><th>' . __( 'Total price' ) . '</th><th>'
		   . __( 'Quantity' ) . '</th><th>' . __( 'Unit price' ) . '</th></tr>';
		foreach ( $prices as $dpqa ) {
		    echo "<tr><td>{$dpqa[0]}</td><td>{$dpqa[1]}</td><td>{$dpqa[2]}</td><td>{$dpqa[3]}</td></tr>";
		}
		echo '</table>';
	    }?>
	</span>
    </p>
    <p class="form-field _acquisition">
	<label for="_acquisition"><?php echo __( 'New acquisition', 'woocommerce' ); ?></label>
	<span class="wrap">
	    <?php
	    echo
	    '<input class="" type="text" name="_field_date" value="' . date('Y-m-d') . '" style="width: 100px;" />' .
	    '<input placeholder="' . __( 'Price', 'woocommerce' )     . '" type="text" name="_field_price" style="width: 80px;" />' .
	    '<input placeholder="' . __( 'Quantity', 'woocommerce' )  . '" type="text" name="_field_qty"   style="width: 80px;" />';
	    ?>
	</span>
	<span class="description"><?php __( 'New acquisition', 'woocommerce' ); ?></span>
    </p>
    <?php
    echo '</div>';
    }
    add_action( 'woocommerce_product_options_general_product_data', 'add_acquisition_fields' );

    function save_ingredient_fields( $post_id ) {
	add_post_meta( $post_id, '_prices', array( esc_attr( $_POST['_field_date'] ),
						   esc_attr( $_POST['_field_price'] ),
						   esc_attr( $_POST['_field_qty'] ),
						   esc_attr( (float) $_POST['_field_price'] / (float) $_POST['_field_qty']) ) );
    }
    add_action( 'woocommerce_process_product_meta', 'save_ingredient_fields' );
    
    /**
     * Add to product type drop down.
     */
    function add_ingredient_product( $types ){
	// Key should be exactly the same as in the class
	$types[ 'ingredient' ] = __( 'Ingredient' );
	return $types;
    }
    add_filter( 'product_type_selector', 'add_ingredient_product' );

    /**
     * Show pricing fields for ingredient product.
     */
    function ingredient_custom_js() {
	if ( 'product' != get_post_type() ) {
	    return;
	}
    ?><script type='text/javascript'>
       jQuery( document ).ready( function() {
	 //  jQuery( '.options_group.pricing' ).addClass( 'show_if_ingredient' ).show();
       });
    </script>
    <?php
    }

    //add_action( 'admin_footer', 'ingredient_custom_js' );

    /**
     * Add a custom product tab.
     */
    function custom_product_tabs( $tabs) {
	$tabs['cooking'] = array(
	    'label'		=> __( 'Cooking', 'woocommerce' ),
	    'target'	=> 'cooking_options',
	    'class'		=> array( 'show_if_ingredient', 'show_if_recipe'  ),
	);
	return $tabs;
    }

    add_filter( 'woocommerce_product_data_tabs', 'custom_product_tabs' );

    /**
     * Contents of the cooking options product tab.
     */
    function cooking_options_product_tab_content() {
	global $post;
    ?><div id='cooking_options' class='panel woocommerce_options_panel'><?php
									?><div class='options_group'><?php
												     woocommerce_wp_checkbox( array(
													 'id' 		=> '_enable_cooking_option',
													 'label' 	=> __( 'Enable cooking option X', 'woocommerce' ),
												     ) );
												     woocommerce_wp_text_input( array(
													 'id'			=> '_text_input_y',
													 'label'			=> __( 'What is the value of Y', 'woocommerce' ),
													 'desc_tip'		=> 'true',
													 'description'	=> __( 'A handy description field', 'woocommerce' ),
													 'type' 			=> 'text',
												     ) );
												     ?></div>

    </div><?php
	  }
	  add_action( 'woocommerce_product_data_panels', 'cooking_options_product_tab_content' );

	  /**
	   * Save the custom fields.
	   */
	  function save_cooking_option_field( $post_id ) {
	      $cooking_option = isset( $_POST['_enable_cooking_option'] ) ? 'yes' : 'no';
	      update_post_meta( $post_id, '_enable_cooking_option', $cooking_option );
	      if ( isset( $_POST['_text_input_y'] ) ) :
				update_post_meta( $post_id, '_text_input_y', sanitize_text_field( $_POST['_text_input_y'] ) );
	      endif;
	  }
	  add_action( 'woocommerce_process_product_meta_ingredient', 'save_cooking_option_field'  );
	  add_action( 'woocommerce_process_product_meta_recipe', 'save_cooking_option_field'  );

	  /**
	   * Hide Attributes data panel.
	   */
	  /* function hide_attributes_data_panel( $tabs) {
	   * 	$tabs['attribute']['class'][] = 'hide_if_ingredient hide_if_recipe';
	   * 	return $tabs;
	   * }
	   * add_filter( 'woocommerce_product_data_tabs', 'hide_attributes_data_panel' );*/

	  function wh_ingredient_admin_custom_js() {

	      if ('product' != get_post_type()) :
	      return;
	      endif;
	  ?>
	<script type='text/javascript'>
	 jQuery(document).ready(function () {
	     // for Price tab
	     jQuery('.product_data_tabs .general_tab').addClass('show_if_ingredient').show();
	     jQuery('#general_product_data .pricing').hide();
	     // for Inventory tab
	     jQuery('.inventory_options').addClass('show_if_ingredient').show();
	     jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_ingredient').show();
	     /* jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_ingredient').show();
	      * jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_ingredient').show();*/
	     jQuery('.shipping_options').addClass('hide_if_ingredient').show();
	     jQuery('.linked_product_options').addClass('hide_if_ingredient').show();
	 });
	</script>
<?php

}

add_action('admin_footer', 'wh_ingredient_admin_custom_js');
