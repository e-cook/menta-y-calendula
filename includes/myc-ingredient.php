<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function render_latest_purchases() {
?>
    <div id='purchases_options' class='panel woocommerce_options_panel'>
	<div class="options_group">
	    <p class="form-field _latest_purchases">
		<label for="_latest_purchases"><?= __( 'Latest Purchases' )?></label>
		<span class="wrap">
		    <?php
		    global $post;
		    echo "latest";
//		    echo wc_get_product($post)->latest_purchases();
		    ?>
		</span>
	    </p>
	    <p class="form-field _new_purchase">
		<?php echo __( 'New purchase', 'woocommerce' ); 
		woocommerce_wp_text_input( array(
		   'id'		=> '_purchase_date',
		   'label'	        => __( 'Date' ),
		   'desc_tip'	=> 'true',
		   'description'	=> __( 'When did you buy this?' ),
		   'type' 	        => 'text',
		   ) );
		   
		   woocommerce_wp_text_input( array(
		   'id'		=> '_purchase_provider',
		   'label'	        => __( 'Provider' ),
		   'desc_tip'	=> 'true',
		   'description'	=> __( 'Who did you buy this from?' ),
		   'type' 	        => 'text',
		   ) );
		   
		   woocommerce_wp_text_input( array(
		   'id'		=> '_purchase_price',
		   'label'	        => __( 'Price' ),
		   'desc_tip'	=> 'true',
		   'description'	=> __( 'How much did you pay in total?' ),
		   'type' 	        => 'text',
		   ) );
		   
		   woocommerce_wp_text_input( array(
		   'id'		=> '_purchase_qty',
		   'label'	        => __( 'Quantity' ),
		   'desc_tip'	=> 'true',
		   'description'	=> __( 'How much did you buy?' ),
		   'type' 	        => 'text',
		   ) );
		?>
	    </p>
	</div>
    </div>
<?php
}
add_action( 'woocommerce_product_data_panels', 'render_latest_purchases' );


function save_ingredient_fields( $post_id ) {
    /*
       add_post_meta( $post_id, '_prices', array( esc_attr( $_POST['_field_date'] ),
       esc_attr( $_POST['_field_price'] ),
       esc_attr( $_POST['_field_qty'] ),
       esc_attr( (float) $_POST['_field_price'] / (float) $_POST['_field_qty']) ) );
     */
    error_log("save_ingredient_fields");
}
add_action( 'woocommerce_process_product_meta_ingredient', 'save_ingredient_fields' );


/**
 * Save the custom fields.

   function save_cooking_option_field( $post_id ) {
   $cooking_option = isset( $_POST['_enable_cooking_option'] ) ? 'yes' : 'no';
   update_post_meta( $post_id, '_enable_cooking_option', $cooking_option );
   if ( isset( $_POST['_text_input_y'] ) ) :
   update_post_meta( $post_id, '_text_input_y', sanitize_text_field( $_POST['_text_input_y'] ) );
   endif;
   }
   add_action( 'woocommerce_process_product_meta_ingredient', 'save_cooking_option_field'  );
   add_action( 'woocommerce_process_product_meta_recipe', 'save_cooking_option_field'  );
 */
