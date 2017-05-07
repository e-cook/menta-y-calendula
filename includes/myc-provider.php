<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'woocommerce_product_data_panels', 'render_provides' );
function render_provides() {
    global $post;
    $product = wc_get_product( $post );
    if ( 'provider' != $product->get_type() ) {
	return;
    }
?>
<div id='provides_options' class='panel woocommerce_options_panel'>
    <div class="options_group">
	<p class="form-field _provides">
	    <span class="wrap">
		<h4 for="_provides"><?= __( 'Physical Ingredients' )?></h4>
		<?php
		$table = new MYC_Provides_Lines( $product->provides_lines() );
		$table->prepare_items();
		$table->display();
		?>
	    </span>
	</p>
	<p class="form-field _provides_phys_ingredients">
	    <h4><?php echo __( 'Add Ingredient' );?></h4>
	    <form id="provides-ingredient-select" class="category-select" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
		<?php wp_dropdown_categories( 'show_count=1&hierarchical=1' ); ?>
	    </form>
	</p>
    </div>
</div>
<?php
}
