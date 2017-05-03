<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function populate_dropdown( $types ) {
    // Key should be exactly the same as in the class
    return array(
	'provider'            => __( 'Provider' ),
	'meal'                => __( 'Meal' ),
	'physical_ingredient' => __( 'Physical Ingredient' ),
	'recipe'              => __( 'Recipe' )
    );
}
add_filter( 'product_type_selector', 'populate_dropdown' );

function set_editor_height( $settings ) {
    $settings['editor_height'] = 50;
    return $settings;
}
add_filter( 'wp_editor_settings', 'set_editor_height' );

function myc_admin_custom_js() {

    if ('product' != get_post_type()) {
	return;
    }
?>
<script type='text/javascript'>
 jQuery(document).ready(function () {
     jQuery('.product_data_tabs .general_tab').addClass('hide_if_provider show_if_meal hide_if_ingredient hide_if_recipe');
     jQuery('#general_product_data .pricing') .addClass('hide_if_provider show_if_meal hide_if_ingredient hide_if_recipe');
     jQuery('.inventory_options')             .addClass('hide_if_provider show_if_meal hide_if_ingredient hide_if_recipe');
     jQuery('.shipping_options')              .addClass('hide_if_provider show_if_meal hide_if_ingredient hide_if_recipe');
     jQuery('.linked_product')                .addClass('show_if_provider hide_if_meal show_if_ingredient show_if_recipe');
 });
</script>
<?php

}

add_action('admin_footer', 'myc_admin_custom_js');
