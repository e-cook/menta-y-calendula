<?php
/*
   Plugin Name: Menta y Calendula
   Plugin URI:  https://github.org/menta-y-calendula
   Description: Organic Food Preparation and Ordering
   Version:     20170927
   Author:      e-cook
   Author URI:  https://github.com/e-cook/menta-y-calendula
   License:     GPLv3
   License URI: https://www.gnu.org/licenses/gpl-3.0.html
   Text Domain: en
   Domain Path: /languages

   This file is part of Menta y Calendula.

   Menta y Calendula is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Menta y Calendula is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Menta y Calendula.  If not, see <http://www.gnu.org/licenses/>.
 */

defined( 'ABSPATH' ) or die();

global $myc_db_version;

global $myc_all_db_versions;
$myc_all_db_versions = array( '0.1' );

add_action( 'init', 'create_post_type' );
function create_post_type() { /* move this to another place so it's not called every time a new page is opened */
    register_post_type( 'myc_provider',
			array(
			    'labels' => array(
				'name' => __( 'Providers' ),
				'singular_name' => __( 'Provider' ),
				'add_new_item' => _x( 'Add new provider', 'myc_provider' ),
				'edit_item' => _x( 'Edit provider', 'myc_provider' ),
				'view_item' => _x( 'View provider', 'myc_provider' ),
			    ),
			    'public' => true,
			    'has_archive' => true,
			)
    );
    
}

load_plugin_textdomain( 'menta-y-calendula', false, basename( dirname( __FILE__ ) ) . '/languages' );

function myc_install_0_1() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $ingredient_table_name     = $wpdb->prefix . 'ingredient'; 
    $provider_table_name       = $wpdb->prefix . 'provider';
    $provided_by_table_name    = $wpdb->prefix . 'provided_by';
    $buy_table_name            = $wpdb->prefix . 'buy';
    $recipe_table_name         = $wpdb->prefix . 'recipe';
    
    $sql = array(
	"CREATE TABLE $ingredient_table_name (
id bigint(20) NOT NULL AUTO_INCREMENT,
name varchar(55),
comment text,
modified date,
last_price decimal(8,2),
last_price_update date,
best_price decimal(8,2),
best_price_update date,
base_unit varchar(20),
PRIMARY KEY  (id),
KEY name (name)
) $charset_collate;",

	"CREATE TABLE $provider_table_name (
id bigint(20) NOT NULL AUTO_INCREMENT,
name varchar(100),
modified date,
address text,
phone1 varchar(20),
phone2 varchar(20),
email1 text,
email2 text,
url text,
account text,
comment text,
PRIMARY KEY  (id),
KEY name (name)
) $charset_collate;",

	"CREATE TABLE $provided_by_table_name (
id bigint(20) NOT NULL AUTO_INCREMENT,
ingredient_id bigint(20) UNSIGNED NOT NULL,
provider_id bigint(20) UNSIGNED NOT NULL,
PRIMARY KEY  (id),
KEY ingredient_id (ingredient_id),
KEY provider_id (provider_id)
) $charset_collate;",
	
	"CREATE TABLE $buy_table_name (
id bigint(20) NOT NULL AUTO_INCREMENT,
date timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
ingredient_id bigint(20) UNSIGNED NOT NULL,
provider_id bigint(20) UNSIGNED NOT NULL,
quantity decimal(8,2),
total_price decimal(8,2),
unit_price decimal(10,4),
PRIMARY KEY  (id),
KEY date (date),
KEY ingredient_id (ingredient_id),
KEY unit_price (unit_price)
) $charset_collate;",


	"CREATE TABLE $recipe_table_name (
id bigint(20) NOT NULL AUTO_INCREMENT,
name varchar(55),
modified date,
production_price decimal(8,2),
last_price_update date,
difficulty int(11),
PRIMARY KEY  (id),
KEY name (name)
) $charset_collate;"
    );

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    
}

function myc_uninstall_0_1() {
    global $wpdb;

    $ingredient_table_name     = $wpdb->prefix . 'ingredient'; 
    $provider_table_name       = $wpdb->prefix . 'provider';
    $provided_by_table_name    = $wpdb->prefix . 'provided_by';
    $buy_table_name            = $wpdb->prefix . 'buy';
    $recipe_table_name         = $wpdb->prefix . 'recipe';
    $posts_table               = $wpdb->prefix . 'posts';
    $terms_table               = $wpdb->prefix . 'terms';
    $terms_r_table             = $wpdb->prefix . 'term_relationships';
    $term_tax_table            = $wpdb->prefix . 'term_taxonomy';

    $woo_tax_table             = $wpdb->prefix . 'woocommerce_attribute_taxonomies';
    
    $wpdb->query("DELETE FROM $posts_table WHERE id>9");
    foreach(array($ingredient_table_name, $provider_table_name, $provided_by_table_name, $buy_table_name, $recipe_table_name) as $t) {
	$wpdb->query("DROP TABLE IF EXISTS $t");
    }

    
}

function float_version_to_string($version) {
    return str_replace( '.', '_', strval( $version ));
}

function myc_install() {
    myc_install_0_1();
    add_option( 'myc_db_version', '0.1', '', 'yes' );

    global $wpdb;
    if ('myc_'===$wpdb->prefix) {
	require_once( dirname( __FILE__ ). '/tests/populate_database.php' );

	populate_ingredients    ($wpdb, $wpdb->prefix . 'ingredient');
	populate_providers      ($wpdb, $wpdb->prefix . 'provider');
	populate_provided_by    ($wpdb, $wpdb->prefix . 'provided_by');
	populate_buy            ($wpdb, $wpdb->prefix . 'buy');
	populate_recipes        ($wpdb, $wpdb->prefix . 'recipe');
    }
    return 1;
}

register_activation_hook( __FILE__, 'myc_install' );

function myc_uninstall() {
    myc_uninstall_0_1();
}

register_deactivation_hook( __FILE__, 'myc_uninstall' );

//      fwrite(STDERR, print_r( "\nmyc_install calling " . $update_func . "\n" ));





/**
 * Register the custom product type after init
 */
function register_ingredient_product_type() {
    /**
     * This should be in its own separate file.
     */
    class WC_Product_Ingredient extends WC_Product {
	public function __construct( $product ) {
	    $this->product_type = 'ingredient';
	    parent::__construct( $product );
	}
    }
}
add_action( 'plugins_loaded', 'register_ingredient_product_type' );

/**
 * Add to product type drop down.
 */
function add_ingredient_product( $types ){
    // Key should be exactly the same as in the class
    $types[ 'ingredient' ] = __( 'Ingredient' );
    return $types;
}
add_filter( 'product_type_selector', 'add_ingredient_product' );

/**
 * Show pricing fields for ingredient product.
 */
function ingredient_custom_js() {
    if ( 'product' != get_post_type() ) :
    return;
    endif;
?><script type='text/javascript'>
   jQuery( document ).ready( function() {
       jQuery( '.options_group.pricing' ).addClass( 'show_if_ingredient' ).show();
   });
</script><?php
	 }

	 add_action( 'admin_footer', 'ingredient_custom_js' );

	 /**
	  * Add a custom product tab.
	  */
	 function custom_product_tabs( $tabs) {
	     $tabs['cooking'] = array(
		 'label'		=> __( 'Cooking', 'woocommerce' ),
		 'target'	=> 'cooking_options',
		 'class'		=> array( 'show_if_ingredient', 'show_if_recipe'  ),
	     );
	     return $tabs;
	 }

	 add_filter( 'woocommerce_product_data_tabs', 'custom_product_tabs' );

	 /**
	  * Contents of the cooking options product tab.
	  */
	 function cooking_options_product_tab_content() {
	     global $post;
	 ?><div id='cooking_options' class='panel woocommerce_options_panel'><?php
									     ?><div class='options_group'><?php
													  woocommerce_wp_checkbox( array(
													      'id' 		=> '_enable_cooking_option',
													      'label' 	=> __( 'Enable cooking option X', 'woocommerce' ),
													  ) );
													  woocommerce_wp_text_input( array(
													      'id'			=> '_text_input_y',
													      'label'			=> __( 'What is the value of Y', 'woocommerce' ),
													      'desc_tip'		=> 'true',
													      'description'	=> __( 'A handy description field', 'woocommerce' ),
													      'type' 			=> 'text',
													  ) );
													  ?></div>

	 </div><?php
	       }
	       add_action( 'woocommerce_product_data_panels', 'cooking_options_product_tab_content' );

	       /**
		* Save the custom fields.
		*/
	       function save_cooking_option_field( $post_id ) {
		   $cooking_option = isset( $_POST['_enable_cooking_option'] ) ? 'yes' : 'no';
		   update_post_meta( $post_id, '_enable_cooking_option', $cooking_option );
		   if ( isset( $_POST['_text_input_y'] ) ) :
				     update_post_meta( $post_id, '_text_input_y', sanitize_text_field( $_POST['_text_input_y'] ) );
		   endif;
	       }
	       add_action( 'woocommerce_process_product_meta_ingredient', 'save_cooking_option_field'  );
	       add_action( 'woocommerce_process_product_meta_recipe', 'save_cooking_option_field'  );

	       /**
		* Hide Attributes data panel.
		*/
	       /* function hide_attributes_data_panel( $tabs) {
		* 	$tabs['attribute']['class'][] = 'hide_if_ingredient hide_if_recipe';
		* 	return $tabs;
		* }
		* add_filter( 'woocommerce_product_data_tabs', 'hide_attributes_data_panel' );*/

	       function wh_ingredient_admin_custom_js() {

		   if ('product' != get_post_type()) :
		   return;
		   endif;
	       ?>
	     <script type='text/javascript'>
              jQuery(document).ready(function () {
		  // for Price tab
		  jQuery('.product_data_tabs .general_tab').addClass('show_if_ingredient').show();
		  jQuery('#general_product_data .pricing').addClass('show_if_ingredient').show();
		  // for Inventory tab
		  jQuery('.inventory_options').addClass('show_if_ingredient').show();
		  jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_ingredient').show();
		  /* jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_ingredient').show();
		   * jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_ingredient').show();*/
              });
	     </script>
<?php

}

add_action('admin_footer', 'wh_ingredient_admin_custom_js');

