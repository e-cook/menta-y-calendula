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
    register_post_type( 'myc_ingredient',
			array(
			    'labels' => array(
				'name' => __( 'Ingredients' ),
				'singular_name' => __( 'Ingredient' ),
				'add_new_item' => _x( 'Add new ingredient', 'myc_ingredient' ),
				'edit_item' => _x( 'Edit ingredient', 'myc_ingredient' ),
				'view_item' => _x( 'View ingredient', 'myc_ingredient' ),
			    ),
			    'public' => true,
			    'has_archive' => true,
			)
    );

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
    
    register_post_type( 'myc_recipe',
			array(
			    'labels' => array(
				'name' => __( 'Recipes' ),
				'singular_name' => __( 'Recipe' ),
				'add_new_item' => _x( 'Add new recipe', 'myc_recipe' ),
				'edit_item' => _x( 'Edit recipe', 'myc_recipe' ),
				'view_item' => _x( 'View recipe', 'myc_recipe' ),
			    ),
			    'public' => true,
			    'has_archive' => true,
			)
    );

    register_taxonomy(
	'type',
	'myc_recipe',
	array(
	    'label'        => __( 'Type', 'taxonomy' ),
	    'rewrite'      => array( 'slug' => 'type' ),
	    /* 'capabilities' => array( 'assign_terms' => 'edit_recipes',
	       'edit_terms'   => 'edit_recipes',
	       'manage_terms' => 'edit_recipes',
	       'delete_terms' => 'edit_recipes',
	       )*/
	)
    );
    wp_insert_term( __( 'Soup' ), 'type' ); 
    wp_insert_term( __( 'Salad' ), 'type' ); 
    wp_insert_term( __( 'Side' ), 'type' ); 
    wp_insert_term( __( 'Dessert' ), 'type' ); 

    
    register_taxonomy(
	'spiciness',
	'myc_recipe',
	array(
	    'label'        => __( 'Spiciness', 'taxonomy' ),
	    'rewrite'      => array( 'slug' => 'spiciness' ),
	    /* 'capabilities' => array( 'assign_terms' => 'edit_recipes',
	       'edit_terms'   => 'edit_recipes',
	       'manage_terms' => 'edit_recipes',
	       'delete_terms' => 'edit_recipes',
	       )*/
	)
    );
    wp_insert_term( __( 'bland' ), 'spiciness' ); 
    wp_insert_term( __( 'spicy' ), 'spiciness' ); 
    wp_insert_term( __( 'hot' ), 'spiciness' ); 
    wp_insert_term( __( 'extreme' ), 'spiciness' ); 

    register_taxonomy(
	'difficulty',
	'myc_recipe',
	array(
	    'label'        => __( 'Difficulty', 'taxonomy' ),
	    'rewrite'      => array( 'slug' => 'difficulty' ),
	    /* 'capabilities' => array( 'assign_terms' => 'edit_recipes',
	       'edit_terms'   => 'edit_recipes',
	       'manage_terms' => 'edit_recipes',
	       'delete_terms' => 'edit_recipes',
	       )*/
	)
    );
    wp_insert_term( __( '1' ), 'difficulty' ); 
    wp_insert_term( __( '2' ), 'difficulty' ); 
    wp_insert_term( __( '3' ), 'difficulty' ); 
    wp_insert_term( __( '4' ), 'difficulty' ); 
    wp_insert_term( __( '5' ), 'difficulty' ); 
}

load_plugin_textdomain( 'menta-y-calendula', false, basename( dirname( __FILE__ ) ) . '/languages' );

function myc_install_0_1() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $ingredient_table_name     = $wpdb->prefix . 'ingredient'; 
    $ingredient_tag_table_name = $wpdb->prefix . 'ingredient_tag'; 

    $provider_table_name       = $wpdb->prefix . 'provider';
    $provider_tag_table_name   = $wpdb->prefix . 'provider_tag'; 

    $provided_by_table_name    = $wpdb->prefix . 'provided_by';
    $buy_table_name            = $wpdb->prefix . 'buy';

    $recipe_table_name         = $wpdb->prefix . 'recipe';
    $recipe_tag_table_name     = $wpdb->prefix . 'recipe_tag'; 
    
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
