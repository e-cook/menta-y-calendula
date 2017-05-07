<?php
if ( ! defined( 'ABSPATH' ) ) {
exit;
}

add_action( 'woocommerce_product_data_panels', 'render_recipe_lines' );
function render_recipe_lines() {
    global $post;
    $product = wc_get_product( $post );
    if ( 'recipe' != $product->get_type() ) {
	return;
    }
?>
<div id='composition_options' class='panel woocommerce_options_panel'>
    <div class="options_group">
	<p class="form-field _recipe_lines">
	    <span class="wrap">
		<h4 for="_recipe_lines"><?= __( 'Ingredients' )?></h4>
		<?php
		$table = new MYC_Recipe_Lines( $product->recipe_lines() );
		$table->prepare_items();
		$table->display();
		?>
	    </span>
	</p>
	<p class="form-field _new_ingredient">
	    <?php echo __( 'New ingredient' ); 
	    woocommerce_wp_text_input( array(
		'id'		=> '_recipe_ingredient',
		'label'	        => __( 'Ingredient' ),
		'desc_tip'	=> 'true',
		'description'	=> __( 'Which ingredient' ),
		'type' 	        => 'text',
	    ) );
	    
	    woocommerce_wp_text_input( array(
		'id'		=> '_recipe_qty',
		'label'	        => __( 'Quantity' ),
		'desc_tip'	=> 'true',
		'description'	=> __( 'How much?' ),
		'type' 	        => 'text',
	    ) );
	    ?>
	</p>
    </div>
</div>
<?php
}
