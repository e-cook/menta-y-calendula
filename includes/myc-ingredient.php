<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once(dirname(__FILE__) . '/class-myc-list-purchases.php');

function render_latest_purchases( $id ) {
?><div id='purchases_options' class='panel woocommerce_options_panel'>
    <div class="options_group">
	<p class="form-field _latest_purchases">
	    <label for="_latest_purchases"><?php echo __( 'Latest Purchases' ); ?></label>
	    <span class="wrap">
		<?php $latest_purchases = new MYC_Latest_Purchases( $id );
		$latest_purchases->prepare_items();
		$latest_purchases->display();
		?>
	    </span>
	</p>
	<p class="form-field _latest_purchases">
	    <label for="_latest_purchases"><?php echo __( 'New purchase', 'woocommerce' ); ?></label>
	    <span class="wrap">
		<input placeholder="<?= __( 'Date' )?>"     type="text" name="_field_date"     style="width: 80px;" />
		<input placeholder="<?= __( 'Provider' )?>" type="text" name="_field_provider" style="width: 80px;" />
		<input placeholder="<?= __( 'Price' )?>   " type="text" name="_field_price"    style="width: 80px;" />
		<input placeholder="<?= __( 'Quantity' )?>" type="text" name="_field_qty"      style="width: 80px;" />
	    </span>
	    <span class="description"><?php __( 'New purchase' ); ?></span>
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
    error_log("saving $POST");
}
add_action( 'woocommerce_process_product_meta', 'save_ingredient_fields' );


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
