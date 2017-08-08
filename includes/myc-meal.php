<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'woocommerce_product_data_panels', 'render_uses_recipe_lines' );
function render_uses_recipe_lines() {
    global $post;
    $product = wc_get_product( $post );
    if ( 'meal' != $product->get_type() ) {
	return;
    }
?>
<div id='uses_recipe_options' class='panel woocommerce_options_panel'>
    <div class="options_group">
	<p class="form-field _uses_recipe_lines">
	    <span class="wrap">
		<h4 for="_uses_recipe_lines"><?= __( 'Recipes', 'myc' )?></h4>
		<?php
		$table = new MYC_Uses_Recipe_Lines( $product->uses_recipe_lines() );
		$table->prepare_items();
		$table->display();
		?>
	    </span>
	</p>
	<p class="form-field _new_recipe">
	    <h4><?php echo __( 'Add Recipe', 'myc' );?></h4>
	    <form id="recipe-select" class="category-select" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
		<?php wp_dropdown_categories( 'show_count=1&hierarchical=1' ); ?>
	    </form>
	</p>
    </div>
</div>
<?php
}
