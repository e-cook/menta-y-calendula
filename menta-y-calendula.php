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
$myc_db_version = '0.1';

global $myc_previous_db_versions;
$myc_previous_db_versions = array( '0.0' );

function recipe_init() {
	 // create recipe taxonomy
	 register_taxonomy(
		'recipe',
		'post',
		array(
			'label'        => __( 'Recipe', 'menta-y-calendula' )
			'rewrite'      => array( 'slug' => 'recipe' ),
			'capabilities' => array( 'assign_terms' => 'edit_recipes',
				       	  	 'edit_terms'   => 'edit_recipes',
						 'manage_terms' => 'edit_recipes',
						 'delete_terms' => 'edit_recipes',
						 )
		)
	);
}

add_action( 'init', 'recipe_init' );

load_plugin_textdomain( 'menta-y-calendula', false, basename( dirname( __FILE__ ) ) . '/languages' );

function upgrade_admin_notice($problematic_version_number) {
    ?>
    <div class="error">
        <p><?php _e( "Tried to upgrade to version $problematic_version_number out of sequence", 'myc-text-domain' ); ?></p>
    </div>
    <?php
}

function register_version_number($new_version_float) {
  global $myc_db_version;
  global $myc_previous_db_versions;
  if( in_array( $new_version_float, $myc_previous_db_versions )) {
    add_action( 'admin_notices', upgrade_admin_notice( $new_version_float ));
  }
  $myc_previous_db_versions[] = $new_version_float;
  $myc_db_version = strval($new_version_float);
  add_option( 'myc_db_version', $myc_db_version );
}

function myc_install_0_1() {
  global $wpdb;

  $charset_collate = $wpdb->get_charset_collate();

  $buy_table_name = $wpdb->prefix . "buy";

  $buy_sql = "CREATE TABLE $buy_table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    product_id bigint(20) UNSIGNED NOT NULL,
    provider_id bigint(20) UNSIGNED NOT NULL,
    quantity decimal(8,2),
    buy_total_price decimal(8,2),
    PRIMARY KEY  (id),
    KEY (time),
    KEY (product_id)
  ) $charset_collate;";

  $price_table_name = $wpdb->prefix . "price";

  $price_sql = "CREATE TABLE $price_table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    product_id bigint(20) UNSIGNED NOT NULL,
    provider_id bigint(20) UNSIGNED NOT NULL,
    last_update datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    week_price decimal(8,2),
    month_price decimal(8,2),
    PRIMARY KEY  (id),
    KEY last_update (last_update),
    KEY product_id (product_id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $buy_sql );
  dbDelta( $price_sql );

  $myc_db_version = 0.1;

  
}

function myc_update_db_check() {
  global $myc_db_version;
  
}

register_activation_hook( __FILE__, 'myc_install' );
