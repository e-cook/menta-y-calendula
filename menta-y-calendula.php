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

load_plugin_textdomain( 'menta-y-calendula', false, basename( dirname( __FILE__ ) ) . '/languages' );

function myc_install_0_1() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $purchase_table_name    = $wpdb->prefix . 'purchase'; 
    $stock_table_name       = $wpdb->prefix . 'stock';
    
    $sql = array(
	"CREATE TABLE $purchase_table_name (
id bigint(20) NOT NULL AUTO_INCREMENT,
phys_ingredient_id bigint(20),
provider_id bigint(20),
dt datetime,
qty decimal(8,2),
price_paid decimal(8,2),
base_unit varchar(20),
unit_price decimal(8,2),
PRIMARY KEY  (id),
KEY phys_ingredient_id (phys_ingredient_id),
KEY provider_id (provider_id),
KEY dt (dt)
) $charset_collate;",

	"CREATE TABLE $stock_table_name (
id bigint(20) NOT NULL AUTO_INCREMENT,
phys_ingredient_id bigint(20),
dt datetime,
qty_before decimal(8,2),
delta decimal(8,2),
qty_current decimal(8,2),
base_unit varchar(20),
PRIMARY KEY  (id),
KEY phys_ingredient_id (phys_ingredient_id),
KEY dt (dt)
) $charset_collate;",
    );

//    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//    dbDelta( $sql );

    
}

function myc_uninstall_0_1() {
    global $wpdb;


    $purchase_table_name    = $wpdb->prefix . 'purchase'; 
    $stock_table_name       = $wpdb->prefix . 'stock';
    $woo_tax_table             = $wpdb->prefix . 'woocommerce_attribute_taxonomies';
    
    $wpdb->query("DELETE FROM $posts_table WHERE id>9");
    /* foreach(array($purchase_table_name, $stock_table_name) as $t) {
       $wpdb->query("DROP TABLE IF EXISTS $t");
     * }
     */
    
}

function float_version_to_string($version) {
    return str_replace( '.', '_', strval( $version ));
}

function myc_install() {
    myc_install_0_1();
    add_option( 'myc_db_version', '0.1', '', 'yes' );

    /* global $wpdb;
     * if ('myc_'===$wpdb->prefix) {
       require_once( dirname( __FILE__ ). '/tests/populate_database.php' );

       populate_ingredients    ($wpdb, $wpdb->prefix . 'ingredient');
       populate_providers      ($wpdb, $wpdb->prefix . 'provider');
       populate_provided_by    ($wpdb, $wpdb->prefix . 'provided_by');
       populate_buy            ($wpdb, $wpdb->prefix . 'buy');
       populate_recipes        ($wpdb, $wpdb->prefix . 'recipe');
     * }*/
    return 1;
}

register_activation_hook( __FILE__, 'myc_install' );

function myc_uninstall() {
    myc_uninstall_0_1();
}

register_deactivation_hook( __FILE__, 'myc_uninstall' );

//      fwrite(STDERR, print_r( "\nmyc_install calling " . $update_func . "\n" ));


//require_once(dirname(__FILE__) . "/../woocommerce/woocommerce.php");
/*
   // Hook to the 'all' action
   //add_action( 'all', 'backtrace_filters_and_actions');
   function backtrace_filters_and_actions() {
   // The arguments are not truncated, so we get everything
   $arguments = func_get_args();
   $tag = array_shift( $arguments ); // Shift the tag

   // Get the hook type by backtracing
   $backtrace = debug_backtrace();
   $hook_type = $backtrace[3]['function'];

   error_log("$hook_type $tag");
   foreach ( $arguments as $argument ) {
   error_log("arg: " . var_export( $argument, true ) );
   }
   }

   //add_action( 'all', create_function( '', 'error_log( var_export( current_filter(), true ) );' ) );
 */

//require_once(dirname(__FILE__) . '/../woocommerce/woocommerce.php');

$id = dirname(__FILE__) . '/includes/';

require_once($id . 'class-myc-ingredient.php');
require_once($id . 'class-myc-recipe.php');
require_once($id . 'class-myc-provider.php');
require_once($id . 'class-myc-meal.php');

require_once($id . 'data-stores/class-myc-product-ingredient-data-store-cpt.php');
require_once($id . 'data-stores/class-myc-product-recipe-data-store-cpt.php');
require_once($id . 'data-stores/class-myc-product-provider-data-store-cpt.php');
require_once($id . 'data-stores/class-myc-product-meal-data-store-cpt.php');

require_once($id . 'class-myc-customize.php');

require_once($id . 'myc-ingredient.php');
