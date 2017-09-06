<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Redirect to login if not logged in and accessing shop or products
// this and the next function adapted from http://wordpress.org/plugins/redirect-to-login-if-not-logged-in/ by Daan Kortenbach
add_action( 'parse_request', function( $query ) {
    $req = $query->request;
    if ( is_user_logged_in() !== true && 
	 ( stripos( $req, 'shop' )        !== false ||
	   stripos( $req, 'cart' )        !== false ||
	   stripos( $req, 'checkout' )    !== false ||
	   stripos( $req, 'account' )     !== false ||
	   stripos( $req, 'product' )     !== false ||
	   stripos( $req, 'espai-soci' )  !== false ) ) {
	auth_redirect();
    }
}, 1 );

// Strips '?loggedout=true' from redirect url after login.
add_filter( 'login_url', function( $login_url ) {
    return str_replace( '%3Floggedout%3Dtrue', '', $login_url );
}, 1, 1 );

// post title customization
add_filter( 'the_title', function( $title ) {
    return str_replace( 'Private: ', 'Menú: ', str_replace( 'Privat: ', 'Menú: ', $title ) );
});


// add jquery-ui

function myc_add_jquery() {
    wp_enqueue_script( 'myc-script-1', plugins_url( 'assets/jquery-ui-1.12.1/jquery-ui.min.js',  dirname(__FILE__) ), array( 'jquery' ) );
    wp_register_style( 'myc-style-1',  plugins_url( 'assets/jquery-ui-1.12.1/jquery-ui.min.css', dirname(__FILE__) ) );

    wp_register_script( 'myc-helpers',  plugins_url( 'assets/js/myc-helpers.js',  dirname(__FILE__) ), array( 'jquery' ) );
    wp_enqueue_script( 'myc-helpers' );
    
    wp_enqueue_style(  'myc-style-1' );
    wp_register_style( 'myc-style-2',  plugins_url( 'assets/css/myc.css', dirname(__FILE__) ) );
    wp_enqueue_style(  'myc-style-2' );
    wp_enqueue_script( 'jquery-ui-datepicker' );
}
add_action( 'admin_enqueue_scripts', 'myc_add_jquery', 2000 ); // 2000 = late so that myc.css overrides the menu icon styles
add_action(    'wp_enqueue_scripts', 'myc_add_jquery', 2000 );

/**
 * add custom types
 */
add_filter( 'product_type_selector', function( $def ) {
    /* $def['meal'] = __( 'Meal', 'myc' );
     * $def['variable_meal'] = __( 'Variable Meal', 'myc' );
     * return $def;*/
    // Key should be exactly the same as in the class
    return array (
	'meal'          => __( 'Meal', 'myc' ),
	'variable_meal' => __( 'Variable Meal', 'myc' )
    );
    /*
       'phys_ingredient'  => __( 'Physical Ingredient', 'myc' ),
       'abs_ingredient'  => __( 'Abstract Ingredient', 'myc' ),
       'recipe'      => __( 'Recipe', 'myc' ),
       'provider'    => __( 'Provider', 'myc' ),
     */
    //	'meal'        => __( 'Meal', 'myc' ),
    //  );
});

add_filter( 'woocommerce_product_filters', function( $output ) {
    return '<select name="product_type" id="dropdown_product_type"><option value="meal" >Meals</option><option value="variable_meal">Variable Meals</option></select>';
});

add_filter( 'woocommerce_data_stores', function ( $stores ) {
    $stores[ 'product-phys_ingredient' ] = 'WC_Product_Phys_Ingredient_Data_Store_CPT';
    $stores[ 'product-meal' ] = 'WC_Product_Meal_Data_Store_CPT';
    $stores[ 'product-variable_meal' ] = 'WC_Product_Variable_Meal_Data_Store_CPT';
    return $stores;
});


add_action('admin_footer', function() {
    // modify behavior of attributes to permit meals to show variations
?>
    <script type='text/javascript'>
     jQuery( document.body ).on( 'woocommerce_added_attribute', function() {
	 if ('variable_meal' === jQuery( 'select#product-type' ).val() ) {
	     jQuery( '.enable_variation' ).addClass( 'show_if_variable_meal' ).show();
	 }
     });
    </script>
<?php 
});

// show variations for meals on add to cart page
add_action( 'woocommerce_meal_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
add_action( 'woocommerce_variable_meal_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
add_filter( 'woocommerce_add_to_cart_handler', function( $type, $product ) {
    return ( 'variable_meal' === $product->get_type() ) ? 'variable' : $type;
}, 10, 2);

// show continue shopping button on cart page
add_action( 'woocommerce_cart_coupon', function() {?>
</div>
<div class="coupon">
    <a class="button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"> <?php _e( 'Return to shop', 'woocommerce' ) ?> </a>
<?php });

// show tags on shop page
add_action( 'woocommerce_after_shop_loop_item', function() {
    global $product;
    echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">', '.</span>' );
}, 5);

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
add_action('admin_footer', function() {

    if ('product' != get_post_type()) {
	return;
    }
?>
<script type='text/javascript'>
 jQuery(document).ready(function () {
     jQuery('.product_data_tabs .general_tab').addClass( 'hide_if_provider show_if_meal show_if_variable_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
     jQuery('#general_product_data .pricing') .addClass( 'hide_if_provider show_if_meal show_if_variable_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
     jQuery('.inventory_options')             .addClass( 'hide_if_provider show_if_meal show_if_variable_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
     jQuery('.shipping_options')              .addClass( 'hide_if_provider show_if_meal show_if_variable_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
     jQuery('.linked_product_options')        .addClass( 'hide_if_provider show_if_meal show_if_variable_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
     jQuery('.variations_options')            .addClass( 'hide_if_provider hide_if_meal show_if_variable_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
     jQuery('.advanced_options')              .addClass( 'hide_if_provider show_if_meal show_if_variable_meal hide_if_phys_ingredient hide_if_abs_ingredient hide_if_recipe' );
     jQuery('.general_tab').show();
     jQuery('#general_product_data .pricing').show()
     
     jQuery('#post-query-submit').select(); // to select "meals"
 });
</script>
<?php
});

// Allow subscribers to see Private posts and pages

$subRole = get_role( 'subscriber' ); 
$subRole->add_cap( 'read_private_posts' );
$subRole->add_cap( 'read_private_pages' );

/**
 * Add a custom product tab.
 */
add_filter( 'woocommerce_product_data_tabs', function ( $tabs ) {
    $tabs['provides'] = array(
	'label'                => __( 'Provides What', 'myc' ),
	'target'               => 'provides_options',
	'class'                => array( 'show_if_provider', 'hide_if_meal', 'hide_if_variable_meal', 'hide_if_phys_ingredient', 'hide_if_abs_ingredient', 'hide_if_recipe' ),
    );

    $tabs['purchases'] = array(
	'label'                => __( 'Purchases', 'myc' ),
	'target'               => 'purchases_options',
	'class'                => array( 'hide_if_provider', 'hide_if_meal', 'hide_if_variable_meal', 'show_if_phys_ingredient', 'hide_if_abs_ingredient', 'hide_if_recipe' ),
    );

    $tabs['instances'] = array(
	'label'                => __( 'Instances', 'myc' ),
	'target'               => 'instances_options',
	'class'                => array( 'hide_if_provider', 'hide_if_meal', 'hide_if_variable_meal', 'hide_if_phys_ingredient', 'show_if_abs_ingredient', 'hide_if_recipe' ),
    );

    $tabs['total_purchases'] = array(
	'label'                => __( 'Price Evolution', 'myc' ),
	'target'               => 'total_purchases_options',
	'class'                => array( 'hide_if_provider', 'hide_if_meal', 'hide_if_variable_meal', 'hide_if_phys_ingredient', 'show_if_abs_ingredient', 'hide_if_recipe' ),
    );

    $tabs['total_inventory'] = array(
	'label'                => __( 'Total Inventory', 'myc' ),
	'target'               => 'total_inventory_options',
	'class'                => array( 'hide_if_provider', 'hide_if_meal', 'hide_if_variable_meal', 'hide_if_phys_ingredient', 'show_if_abs_ingredient', 'hide_if_recipe' ),
    );

    $tabs['composition'] = array(
	'label'                => __( 'Composition', 'myc' ),
	'target'               => 'composition_options',
	'class'                => array( 'hide_if_provider', 'hide_if_meal', 'hide_if_variable_meal', 'hide_if_phys_ingredient', 'hide_if_abs_ingredient', 'show_if_recipe' ),
    );

    $tabs['uses_recipe'] = array(
	'label'                => __( 'Recipes', 'myc' ),
	'target'               => 'uses_recipe_options',
	'class'                => array( 'hide_if_provider', 'show_if_meal', 'show_if_variable_meal', 'hide_if_phys_ingredient', 'hide_if_recipe' ),
    );

    $tabs[ 'variations' ][ 'class' ][] = 'show_if_variable_meal';

    return $tabs;
});

function hide_non_meals_query ( $query ) {
    if ( $query->is_admin ||
	 'post' == get_post_type() ||
	 is_post_type_archive( 'product' ) &&
	 ! $query->is_main_query() ) {
	return;
    }

    $only_meals = array(
	array(
	    'taxonomy' => 'product_type',
	    'field'    => 'slug',
	    'terms'    => '(meal, variable_meal)',
	    'operator' => 'IN'
	)
    );

    $query->tax_query->queries[] = $only_meals;
    $query->query_vars[ 'tax_query' ] = $query->tax_query->queries;
}
//add_action( 'pre_get_posts', 'hide_non_meals_query', 10, 2 );

// filter search results if not logged in
add_action( 'pre_get_posts', function( $query ) {
    global $wpdb;
    if ( $query->is_search && !is_user_logged_in() ) {
	$query->set( 'post_type', array( 'post', 'page' ) );
    }

    if ( ( $query->get( 'post_type' ) == 'product' && !is_admin() ) ) {
	$query->set( 'meta_query' , array( array( 'key'     => '_visible_to_date',
						  'value'   => date( 'Y-m-d', strtotime( 'now' ) ),
						  'compare' => '>=' ) ) );
    }

    $capabilities = get_user_meta( get_current_user_id(), "{$wpdb->prefix}capabilities", true );

    if ( isset( $capabilities[ __( 'user', 'myc' ) ] ) &&
	 $query->get( 'post_type' ) != 'nav_menu_item' ) {

	$query->tax_query->queries[] = array(
	    'taxonomy' => 'product_tag',
	    'field'    => 'slug',
	    'terms'    => 'per_usuaris',
	);
	$query->query_vars['tax_query'] = $query->tax_query->queries;
    }

    if ( isset( $capabilities[ __( 'coope', 'myc' ) ] )  &&
	 $query->get( 'post_type' ) != 'nav_menu_item' ) {

	$query->tax_query->queries[] = array(
	    'taxonomy' => 'product_tag',
	    'field'    => 'slug',
	    'terms'    => 'per_coopes',
	);
	$query->query_vars['tax_query'] = $query->tax_query->queries;
    }

});

// number of items per page
add_filter( 'loop_shop_per_page', function( $cols ) {
    return 50;
}, 20);

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


// add visibility columns in product_posts

function add_visiblity_column( $existing_columns ) {
    $offset = 3;
    return array_slice( $existing_columns, 0, $offset, true ) +
	   array('active'       => __( 'active', 'myc' ),
		 'visible_to'   => __( 'Visible until', 'myc' ) ) +
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
	       . '" />';
	    return;
	case 'visible_to' :
	    echo ( 'meal' == substr( $the_product->get_type(), -4 ) ) ? $the_product->get_catalog_visibility_to() : '';
	    return;
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
	     jQuery.post( ajaxurl, {
		 action : ( checked == 'checked' ) ? 'activate_meal' : 'deactivate_meal',
		 post_id: $.attr( 'id' ).substring( 8 ), // leave out '_active_'
		 _nonce : '<?php echo wp_create_nonce( 'active' ) ?>'
	     });

	     // color row title accordingly
	     var row_title = $.parent().prev().find( '.row-title' );
	     if ( checked === undefined ) {
		 row_title.css( 'color', '#cdcdc1' );
	     } else {
		 row_title.css( 'color', '' );
	     }
	 });

	 // on document ready, grey out row titles if meal is deactivated
	 jQuery( '#the-list' ).find( '.row-title' ).each( function() {
	     var active = jQuery( this ).parent().parent().next().children( '.active_button' ).attr('checked');
	     if ( active === undefined ) {
		 jQuery( this ).css( "color", '#cdcdc1' );
	     }
	 });
     });
    </script>
<?php
}
add_action( 'admin_footer', 'toggle_activity_js' );

add_action( 'wp_ajax_activate_meal', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'active' ) ) {
	wp_die( "Don't mess with me!" );
    }
    $prod = wc_get_product( $_POST[ 'post_id' ] );
    $prod->set_catalog_visibility( 'visible' );
    $prod->save();
});

add_action( 'wp_ajax_deactivate_meal', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'active' ) ) {
	wp_die( "Don't mess with me!" );
    }
    $prod = wc_get_product( $_POST[ 'post_id' ] );
    $prod->set_catalog_visibility( 'hidden' );
    $prod->save();
});


// visibility
add_action( 'woocommerce_product_options_pricing', function() {
    global $post;
    echo '<p class="form-field">';
    echo '<label for="visible_to_date">' . __('Visible to', 'myc') . '</label>';
    echo '<input type="text" class="datepicker visible_to_date_picker" id="visible_to_date" value="' . get_post_meta($post->ID, '_visible_to_date', true) . '"/>';
    echo '</p>';
});

add_action( 'admin_footer', function () {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 jQuery( '.visible_to_date_picker' ).datepicker({
	     onSelect: function() {
		 jQuery.post( ajaxurl, {
		     'post_id': woocommerce_admin_meta_boxes.post_id,
		     'action': 'myc_set_visible_to_date',
		     'to_date': jQuery( this ).datepicker("getDate"),
		     '_nonce': '<?php echo wp_create_nonce( 'myc_set_visible_to_date' ) ?>'
		 });
	     },
	     dateFormat: "yy-mm-dd"
	 });
     });
    </script>
<?php
});

add_action( 'wp_ajax_myc_set_visible_to_date', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'myc_set_visible_to_date' ) ) {
	wp_die( "Don't mess with me!" );
    }
    update_post_meta( wc_get_product( $_POST[ 'post_id' ] )->get_id(), 
		      '_visible_to_date' , 
		      date( 'Y-m-d', strtotime( $_POST[ 'to_date' ] ) ) );
});


// think about visibility date and user type when displaying related products
add_filter( 'woocommerce_product_related_posts_query', function( $query ) {
    global $wpdb;
    $query[ 'where' ] .= " AND p.ID IN ( SELECT post_id FROM {$wpdb->prefix}postmeta "
		       . 'WHERE meta_key = "_visible_to_date" AND meta_value >= "' . date( 'Y-m-d', strtotime( 'now' ) ) . '" )';
    
    $capabilities = get_user_meta( get_current_user_id(), "{$wpdb->prefix}capabilities", true );
    if ( isset( $capabilities[ __( 'user', 'myc' ) ] ) ||
	 isset( $capabilities[ __( 'coope', 'myc' ) ] )) {
	$slug = isset( $capabilities[ __( 'user', 'myc' ) ] ) ? 'per_individuals' : 'per_coopes';
	$term_id = $wpdb->get_var( "SELECT term_id FROM {$wpdb->prefix}terms WHERE slug='{$slug}' LIMIT 1" );

	$products_of_tag = $wpdb->get_col( "SELECT tr.object_id FROM {$wpdb->prefix}term_relationships tr INNER JOIN {$wpdb->prefix}term_taxonomy t USING (term_taxonomy_id) WHERE t.taxonomy = 'product_tag' AND t.term_id = '{$term_id}'" );

	$query[ 'where' ] .= " AND p.ID IN (" . implode( ',', $products_of_tag ) . ')';
    }

    return $query;
});

// date format
function myc_custom_delivery_date_format( $post ) {
    return get_post_time( __( 'd/m/Y', 'woocommerce' ), $post );
}
add_filter( 'woocommerce_admin_delivery_date_format' , 'myc_custom_delivery_date_format' );

// show delivery date in admin screen for order
add_filter( 'woocommerce_admin_shipping_fields', function( $fields ) {
    $fields[ 'delivery_date' ] = array(
	'label' => __( 'Delivery date', 'myc' ),
	'show'  => true,
    );
    return $fields;
});


// Menu
require_once( ABSPATH . 'wp-includes/pluggable.php' );
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once( dirname(__FILE__) . '/myc-order-dates.php' );
require_once( dirname(__FILE__) . '/myc-what-to-cook.php' );

add_action( 'admin_menu', function() {
    if ( current_user_can( 'manage_woocommerce' ) ) {
	add_submenu_page( 'woocommerce', __( 'Order deadline', 'myc' ), __( 'Order deadlines', 'myc' ), 'manage_woocommerce', 'manage_order_deadlines', 'manage_order_deadlines_page' );
	add_submenu_page( 'woocommerce', __( 'What to cook', 'myc' ), __( 'What to cook', 'myc' ), 'manage_woocommerce', 'what_to_cook', 'what_to_cook_page' );
    }
    if ( ! get_term_by( 'slug', 'shipping_delivery_date', 'category' ) ) {
	wp_insert_term( 'shipping_delivery_date', 'category', 'shipping_delivery_date' );
    }
    if ( ! get_term_by( 'slug', 'order_deadline', 'category' ) ) {
	wp_insert_term( 'order_deadline', 'category', 'order_deadline' );
    }
    global $menu;
    $menu['55.5'][0] = __( 'Orders', 'myc' );
}, 11 );

add_filter( 'get_pages', function( $items, $menu ) {
    $filtered = array();
    foreach ( $items as $item ) {
	if ( is_user_logged_in() === true ||
	     ( stripos( $item->post_name, 'cart' )       !== false &&
	       stripos( $item->post_name, 'checkout' )   !== false &&
	       stripos( $item->post_name, 'account' )    !== false &&
	       stripos( $item->post_name, 'shop' )       !== false &&
	       stripos( $item->post_name, 'product' )    !== false &&
	       stripos( $item->post_name, 'espai-soci' ) !== false ) ) {
	    $filtered[] = $item;
	}
    }
    return $filtered;
}, 20, 2);

add_filter( 'woocommerce_account_menu_items', function( $items ) {
    unset( $items[ 'downloads' ] );
    $items[ 'orders' ] = __( 'Orders and Comments', 'myc' );
    return $items;
});


// product reviews
function props_of ( $data ) {
    // make an array ( product_id => ( property_name => value ) )
    $props_of = array();
    foreach( $data as $input ) {
	list( $property_name, $product_id ) = explode( '-', $input[ 'name' ] );
	if ( ! isset( $props_of[ $product_id ] ) ) {
	    $props_of[ $product_id ] = array();
	}
	$props_of[ $product_id ][ $property_name ] = $input[ 'value' ];
    }
    return $props_of;
}

function recent_product_comment_id( $user_id, $product_id ) {
    global $wpdb;
    return $wpdb->get_var( $wpdb->prepare("
	SELECT comment_ID FROM $wpdb->comments
	WHERE comment_post_ID = %d
	AND user_id = %d
        AND comment_date >= '%s'
	AND comment_approved = '1'
        AND comment_parent = 0
    ", $product_id, $user_id, date( 'Y-m-d', strtotime( '1 week ago' ) ) ) );
}

add_action( 'wp_ajax_myc_read_order_comments', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'myc_read_order_comments' ) ) {
	wp_die( "Don't mess with me!" );
    }
    global $current_user;

    $props_of = array();
    foreach( $_POST[ 'product_ids' ] as $product_id ) {
	$comment_id = recent_product_comment_id( $current_user->ID, $product_id );
	$props_of[ $product_id ] = array(
	    'comment_content'   => get_comment( $comment_id )->comment_content,
	    'star'              => get_comment_meta( $comment_id, 'rating', true ),
	    'repeat'            => get_comment_meta( $comment_id, 'would_repeat', true ),
	    'variation'         => get_comment_meta( $comment_id, 'suggested_variation', true ),
	);
    }
    wp_die( json_encode( $props_of ) );
});

add_action( 'wp_ajax_myc_write_order_comments', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'myc_write_order_comments' ) ) {
	wp_die( "Don't mess with me!" );
    }

    global $current_user;

    foreach( props_of( $_POST[ 'data' ] ) as $product_id => $props ) {
	$comment_id = recent_product_comment_id( $current_user->ID, $product_id );

	if ( null === $comment_id ) {
	    $comment_id = wp_insert_comment( array(
		'comment_post_ID'      => $product_id,
		'comment_author'       => $current_user->user_login,
		'comment_author_email' => $current_user->user_email,
		'comment_author_url'   => $current_user->user_url,
		'comment_content'      => $props[ 'comment_content' ],
		'comment_agent'        => 'Menta i Calendula',
		'comment_type'         => 'order_note',
		'comment_parent'       => 0,
		'comment_approved'     => 1,
		'user_id'              => $current_user->ID,
	    ) );
	} else {
	    wp_update_comment( array(
		'comment_ID' => $comment_id,
		'comment_content' => $props[ 'comment_content' ],
	    ) );
	}

	if ( isset( $props[ 'star' ] ) ) {
	    update_comment_meta( $comment_id, 'rating', $props[ 'star' ] );
	}

	if ( isset( $props[ 'repeat' ] ) ) {
	    update_comment_meta( $comment_id, 'would_repeat', 1 );
	} else {
	    update_comment_meta( $comment_id, 'would_repeat', 0 );
	}

	if ( isset( $props[ 'variation' ] ) ) {
	    update_comment_meta( $comment_id, 'suggested_variation', $props[ 'variation' ] );
	}
    }
});

// email

add_filter( 'woocommerce_email_classes', function ( $email_classes ) {
    require_once( 'class-myc-email.php' );
    return array( 
	'MYC_Users_Order_Now_Email'      => new MYC_Users_Order_Now_Email(), 
	'MYC_Coopes_Order_Now_Email'      => new MYC_Coopes_Order_Now_Email(), 
	'MYC_Users_Order_Reminder_Email' => new MYC_Users_Order_Reminder_Email(),
	'MYC_Coopes_Order_Reminder_Email' => new MYC_Coopes_Order_Reminder_Email(),
    ) + $email_classes;
});

add_action( 'admin_footer', function () {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 <?php 
	 foreach ( array( 'now', 'reminder' ) as $when ) {
	     foreach ( array( 'user', 'coope' ) as $entity ) {?>
	 jQuery( '#woocommerce_myc_<?php echo $entity;?>s_order_<?php echo $when; ?>_email_send_now' ).removeClass( 'input-text regular-input disabled' ).click( function() {
	     jQuery.post( ajaxurl, {
		 action: 'myc_trigger_<?php echo $entity;?>s_order_<?php echo $when; ?>_email',
		 _nonce: '<?php echo wp_create_nonce( 'myc_trigger_' . $entity . 's_order_' . $when . '_email') ?>'
	     }, function ( response ) {
		 if ( '1' == response ) {
		     alert( "<?php _e( 'The ' . $entity . 's have been successfully notified', 'myc' ); ?>" );
		 } else {
		     alert( "<?php _e( 'There was a problem notifying the ' . $entity .'s', 'myc' ) ?>" + response );
		 }
	     });
	 });
		 <?php 
		 foreach ( array( 'subject', 'heading', 'email_intro' ) as $what ) {?>
	 jQuery( '#woocommerce_myc_<?php echo $entity; ?>s_order_<?php echo $when; ?>_email_<?php echo $what; ?>' ).change( function() {
	     jQuery( '#woocommerce_myc_<?php echo $entity; ?>s_order_<?php echo $when; ?>_email_send_now' ).addClass( 'disabled' );
	 });
		 <?php } } }?>
     });
    </script>
<?php
});

$dummy_translations = array(
    __( 'The users have been successfully notified', 'myc' ),
    __( 'The coopes have been successfully notified', 'myc' ),
);

foreach ( array( 'now', 'reminder' ) as $when ) {
    foreach ( array( 'user', 'coope' ) as $entity ) {
	add_action ( 'wp_ajax_myc_trigger_' . $entity . 's_order_' . $when . '_email', function() use ( $when, $entity ) {
	    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'myc_trigger_' . $entity . 's_order_' . $when . '_email' ) ) {
		wp_die( "Don't mess with me!" );
	    }
	    require_once( 'class-myc-email.php' );
	    $the_class = 'MYC_' . ucwords( $entity ) . 's_Order_' . ucwords( $when ) . '_Email';
	    $e = new $the_class;
	    wp_die( $e->trigger() );
	});

    }
}
