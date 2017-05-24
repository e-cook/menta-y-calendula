<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * add custom types
 */
function myc_add_custom_product_types( $types ) {
    // Key should be exactly the same as in the class
    return array (
	'phys_ingredient'  => __( 'Physical Ingredient' ),
	'abs_ingredient'  => __( 'Abstract Ingredient' ),
	'recipe'      => __( 'Recipe' ),
	'provider'    => __( 'Provider' ),
	'meal'        => __( 'Meal' ),
    );
}
add_filter( 'product_type_selector', 'myc_add_custom_product_types' );

function myc_add_data_stores( $stores ) {
    $stores[ 'product-phys-ingredient' ] = 'WC_Product_Phys_Ingredient_Data_Store_CPT';
    return $stores;
}
add_filter( 'woocommerce_data_stores', 'myc_add_data_stores' );

/**
 * Set editor height
 */
function set_editor_height( $settings ) {
    $settings['editor_height'] = 50;
    return $settings;
}
add_filter( 'wp_editor_settings', 'set_editor_height' );


/**
 * Adjust visible tabs for different post types
 */
function myc_admin_custom_js() {

    if ('product' != get_post_type()) {
	return;
    }
?>
<script type='text/javascript'>
 jQuery(document).ready(function () {
     jQuery('.product_data_tabs .general_tab').addClass( 'hide_if_provider show_if_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
     jQuery('#general_product_data .pricing') .addClass( 'hide_if_provider show_if_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
     jQuery('.inventory_options')             .addClass( 'hide_if_provider show_if_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
     jQuery('.shipping_options')              .addClass( 'hide_if_provider show_if_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
     jQuery('.linked_product_options')        .addClass( 'hide_if_provider show_if_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
     jQuery('.advanced_options')              .addClass( 'hide_if_provider hide_if_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
 });
</script>
<?php
}
add_action('admin_footer', 'myc_admin_custom_js');

/**
 * Add a custom product tab.
 */
function custom_product_tabs( $tabs ) {
    $tabs['provides'] = array(
	'label'                => __( 'Provides What' ),
	'target'               => 'provides_options',
	'class'                => array( 'show_if_provider', 'hide_if_meal', 'hide_if_phys_ingredient', 'hide_if_abs_ingredient', 'hide_if_recipe' ),
    );

    $tabs['purchases'] = array(
	'label'                => __( 'Purchases' ),
	'target'               => 'purchases_options',
	'class'                => array( 'hide_if_provider', 'hide_if_meal', 'show_if_phys_ingredient', 'hide_if_abs_ingredient', 'hide_if_recipe' ),
    );

    $tabs['instances'] = array(
	'label'                => __( 'Instances' ),
	'target'               => 'instances_options',
	'class'                => array( 'hide_if_provider', 'hide_if_meal', 'hide_if_phys_ingredient', 'show_if_abs_ingredient', 'hide_if_recipe' ),
    );

    $tabs['total_purchases'] = array(
	'label'                => __( 'Price Evolution' ),
	'target'               => 'total_purchases_options',
	'class'                => array( 'hide_if_provider', 'hide_if_meal', 'hide_if_phys_ingredient', 'show_if_abs_ingredient', 'hide_if_recipe' ),
    );

    $tabs['total_inventory'] = array(
	'label'                => __( 'Total Inventory' ),
	'target'               => 'total_inventory_options',
	'class'                => array( 'hide_if_provider', 'hide_if_meal', 'hide_if_phys_ingredient', 'show_if_abs_ingredient', 'hide_if_recipe' ),
    );

    $tabs['composition'] = array(
	'label'                => __( 'Composition' ),
	'target'               => 'composition_options',
	'class'                => array( 'hide_if_provider', 'hide_if_meal', 'hide_if_phys_ingredient', 'hide_if_abs_ingredient', 'show_if_recipe' ),
    );

    $tabs['uses_recipe'] = array(
	'label'                => __( 'Recipes' ),
	'target'               => 'uses_recipe_options',
	'class'                => array( 'hide_if_provider', 'show_if_meal', 'hide_if_phys_ingredient', 'hide_if_recipe' ),
    );
    return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'custom_product_tabs' );

function hide_non_meals_query ( $query ) {

    if ( $query->is_admin ||
	 is_post_type_archive( 'product' ) &&
	 ! $query->is_main_query() ) {
	return;
    }

    $only_meals = array(
	array(
	    'taxonomy' => 'product_type',
	    'field'    => 'slug',
	    'terms'    => '(meal)',
	    'operator' => 'IN'
	)
    );

    $query->tax_query->queries[] = $only_meals;
    $query->query_vars[ 'tax_query' ] = $query->tax_query->queries;
}
add_action( 'pre_get_posts', 'hide_non_meals_query', 10, 2 );
