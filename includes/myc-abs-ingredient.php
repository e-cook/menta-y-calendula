<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'woocommerce_product_data_panels', 'render_instances' );
function render_instances() {
    global $post;
    $product = wc_get_product( $post );
    if ( 'abs_ingredient' != $product->get_type() ) {
	return;
    }
?>
<div id='instances_options' class='panel woocommerce_options_panel'>
    <div class="options_group">
	<p class="form-field _instances">
	    <span class="wrap">
		<h4 for="_instances"><?= __( 'Instances' )?></h4>
		<?php
		$table = new MYC_Instances( $product->instances() );
		$table->prepare_items();
		$table->display();
		?>
	    </span>
	</p>
	<p class="form-field _new_instance">
	    <h4><?php echo __( 'New instance' )?></h4>
	    <form id="new-instance-select" class="category-select" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
	    <?php wp_dropdown_categories( 'show_count=1&hierarchical=1' ); ?>
	    </form>
	</p>
    </div>
</div>
<?php
}

add_action( 'woocommerce_product_data_panels', 'render_total_inventory' );
function render_total_inventory() {
    global $post;
    $product = wc_get_product( $post );
    if ( 'abs_ingredient' != $product->get_type() ) {
	return;
    }
?>
<div id='total_inventory_options' class='panel woocommerce_options_panel'>
    <div class="options_group">
	<p class="form-field _total_inventory">
	    <span class="wrap">
		<h4 for="_total_inventory"><?= __( 'Total Inventory' )?></h4>
		<?php

		$table = new MYC_Total_Inventory( $product->total_inventory() );
		$table->prepare_items();
		$table->display();

		?>
	    </span>
	</p>
    </div>
</div>
<?php
}

add_action( 'woocommerce_product_data_panels', 'render_total_purchases' );
function render_total_purchases() {
    global $post;
    $product = wc_get_product( $post );
    if ( 'abs_ingredient' != $product->get_type() ) {
	return;
    }
?>
<div id='total_purchases_options' class='panel woocommerce_options_panel'>
    <div class="options_group">
	<h4 for="_total_purchases"><?= __( 'Weekly Price Evolution per product' )?></h4>
	<p class="form-field _total_purchases">
	    <img src="http://localhost/wordpress/wp-content/uploads/2017/05/spark.png">
	</p>
	<p class="form-field _total_purchases">
	    <span class="wrap">
		<?php

		$table = new MYC_Total_Purchases( $product->total_purchases() );
		$table->prepare_items();
		$table->display();

		?>
	    </span>
	</p>
    </div>
</div>
<?php
}
