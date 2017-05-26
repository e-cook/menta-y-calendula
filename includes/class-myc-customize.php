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

/*
   // Bulk action change visibility

   // Adds a new item into the Bulk Actions dropdown.
   function register_myc_bulk_actions( $bulk_actions ) {
   $bulk_actions['toggle_visibility'] = __( 'Toggle visibility', 'myc' );
   return $bulk_actions;
   }
   add_filter( 'bulk_actions-edit-post', 'register_myc_bulk_actions' );

   // Handles the bulk action.
   function myc_bulk_action_handler( $redirect_to, $action, $post_ids ) {
   if ( $action !== 'toggle_visibility' ) {
   return $redirect_to;
   }

   foreach ( $post_ids as $post_id ) {
   wp_update_post( array(
   'ID'          => $post_id,
   'post_status' => 'draft',
   ) );
   }

   $redirect_to = add_query_arg( 'bulk_draft_posts', count( $post_ids ), $redirect_to );

   return $redirect_to;
   }
   add_filter( 'handle_bulk_actions-edit-post', 'myc_bulk_action_handler', 10, 3 );

   // Shows a notice in the admin once the bulk action is completed.
   function myc_bulk_action_admin_notice() {
   if ( ! empty( $_REQUEST['bulk_toggle_visibility'] ) ) {
   $toggle_count = intval( $_REQUEST['bulk_toggle_visibility'] );

   printf(
   '<div id="message" class="updated fade">' .
   _n( 'Toggled visibility for %s meal.', 'Toggled visibility for %s posts.', $toggle_count, 'domain' )
   . '</div>',
   $toggle_count
   );
   }
   }
   add_action( 'admin_notices', 'myc_bulk_action_admin_notice' );
 */


// add visibility column in product_posts

function add_visiblity_column( $existing_columns ) {
    $offset = 3;
    return array_slice( $existing_columns, 0, $offset, true ) +
	   array('active' => __('active')) +
	   array_slice( $existing_columns, $offset, NULL, true);
}
add_action( 'manage_product_posts_columns', 'add_visiblity_column', 12 );

function render_visibility_column( $column ) {
    global $post, $the_product;
    if ( empty( $the_product ) || $the_product->get_id() != $post->ID ) {
	$the_product = wc_get_product( $post );
    }

    // Only continue if we have a product.
    if ( empty( $the_product ) ) {
	return;
    }

    switch ( $column ) {
	case 'active' :
	    $id = '_active_' . $the_product->get_id();
	    $myc_active_nonce = wp_create_nonce( 'active' );
	    $currently_visible = $the_product->get_catalog_visibility();
	    echo '<input type="checkbox" name="' . $id . '" id="' . $id . '" '
	       . 'class="active_button" '
	       . checked( $currently_visible, 'visible', false )
	       . ' link="' . $link . '" />';
    }
}
add_action( 'manage_product_posts_custom_column', 'render_visibility_column' );

function toggle_activity_js() {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {

	 // handle click on activation button
	 jQuery( '#the-list' ).find( '.active_button' ).click( function() {
	     var $ = jQuery( this );
	     var checked = $.attr( 'checked' );
	     
	     // post query to persist change in the database
	     var data = {
		 'action' : ( checked == 'checked' ) ? 'activate_meal' : 'deactivate_meal',
		 'post_id': $.attr( 'id' ).substring( 8 ), // leave out '_active_'
		 '_nonce' : '<?php echo wp_create_nonce( 'active' ) ?>';
	     };
	     jQuery.post( ajaxurl, data );

	     // color row title accordingly
	     var row_title = $.parent().prev().find( '.row-title' );
	     if ( checked === undefined ) {
		 row_title.css( 'color', '#d3d3d3' );
	     } else {
		 row_title.css( 'color', '' );
	     }
	 });

	 // on document ready, grey out row titles if meal is deactivated
	 jQuery( '#the-list' ).find( '.row-title' ).each( function() {
	     var active = jQuery( this ).parent().parent().next().children( '.active_button' ).attr('checked');
	     if ( active === undefined ) {
		 jQuery( this ).css( "color", "#d3d3d3" );
	     }
	 });
     });
    </script>
<?php
}
add_action( 'admin_footer', 'toggle_activity_js' );

function myc_ajax_activate_meal() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'active' ) ) {
	wp_die( "Don't mess with me!" );
    }
    $prod = wc_get_product( $_POST[ 'post_id' ] );
    $prod->set_catalog_visibility( 'visible' );
    $prod->save();
}
add_action( 'wp_ajax_activate_meal' , 'myc_ajax_activate_meal' );

function myc_ajax_deactivate_meal() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'active' ) ) {
	wp_die( "Don't mess with me!" );
    }
    $prod = wc_get_product( $_POST[ 'post_id' ] );
    $prod->set_catalog_visibility( 'hidden' );
    $prod->save();
}
add_action( 'wp_ajax_deactivate_meal' , 'myc_ajax_deactivate_meal' );
