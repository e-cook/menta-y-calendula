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

global $myc_all_db_versions;
$myc_all_db_versions = array( '0', '0.1' );

function recipe_init() {
	 // create recipe taxonomy
	 register_taxonomy(
		'recipe',
		'post',
		array(
		      'label'        => __( 'Recipe', 'menta-y-calendula' ),
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

/*
function upgrade_not_found($problematic_version_number) {
    ?>
    <div class="error">
        <p><?php _e( "Could not find code to upgrade to version $problematic_version_number", 'myc-text-domain' ); ?></p>
    </div>
    <?php
}

function register_version_number($new_version_float) {
  global $myc_db_version;
  global $myc_all_db_versions;
  if( !in_array( $new_version_float, $myc_all_db_versions )) {
    add_action( 'admin_notices', upgrade_not_found( $new_version_float ));
  }
  $myc_db_version = strval($new_version_float);
  add_option( 'myc_db_version', $myc_db_version );
}
*/

function myc_install_0() {
}

function myc_uninstall_0() {
}

function myc_install_0_1() {
  global $wpdb;

  $charset_collate = $wpdb->get_charset_collate();

  $buy_table_name = $wpdb->prefix . 'buy';
  $price_table_name = $wpdb->prefix . 'price';
  $provided_by_table_name = $wpdb->prefix . 'provided_by';

  $sql = array(
"CREATE TABLE $buy_table_name (
id bigint(20) NOT NULL AUTO_INCREMENT,
time timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
product_id bigint(20) UNSIGNED NOT NULL,
provider_id bigint(20) UNSIGNED NOT NULL,
quantity decimal(8,2),
buy_total_price decimal(8,2),
PRIMARY KEY  (id),
KEY time (time),
KEY product_id (product_id)
  ) $charset_collate;",

"CREATE TABLE $price_table_name (
id bigint(20) NOT NULL AUTO_INCREMENT,
product_id bigint(20) UNSIGNED NOT NULL,
provider_id bigint(20) UNSIGNED NOT NULL,
last_update timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
best_price_week decimal(8,2),
best_price_month decimal(8,2),
PRIMARY KEY  (id),
KEY last_update (last_update),
KEY product_id (product_id)
) $charset_collate;",

"CREATE TABLE $provided_by_table_name (
id bigint(20) NOT NULL AUTO_INCREMENT,
product_id bigint(20) UNSIGNED NOT NULL,
provider_id bigint(20) UNSIGNED NOT NULL,
PRIMARY KEY  (id),
KEY product_id (product_id),
KEY provider_id (provider_id)
) $charset_collate;"
	       );

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}

function myc_uninstall_0_1() {
  global $wpdb;

  $buy_table_name = $wpdb->prefix . "buy";
  $price_table_name = $wpdb->prefix . "price";
  $provided_by_table_name = $wpdb->prefix . 'provided_by';

  $wpdb->query("DROP TABLE IF EXISTS $buy_table_name");  
  $wpdb->query("DROP TABLE IF EXISTS $price_table_name");  
  $wpdb->query("DROP TABLE IF EXISTS $provided_by_table_name");  
}

function float_version_to_string($version) {
  return str_replace( '.', '_', strval( $version ));
}

function myc_install( $myc_test_db_versions=array() ) {
  global $myc_db_version;

  $myc_db_version = get_option('myc_db_version');
  $myc_db_version_float = ( $myc_db_version == false ) ? 0.0 : strval($myc_db_version);
  $testing = ( sizeof( $myc_test_db_versions ) == 0 ) ? true : false;
  $test_versions = $testing ? $myc_all_db_versions : $myc_test_db_versions;

  foreach( $test_versions as $previous_version_float ) {
    if( $myc_db_version_float < $previous_version_float ) {
      call_user_func( 'myc_install_' . float_version_to_string( $previous_version_float ));
    }
  }
    
  $myc_db_version = strval( array_slice( $myc_test_db_versions, -1 )[0] );
  add_option( 'myc_db_version', $myc_db_version );  
  return $myc_db_version;
}

register_activation_hook( __FILE__, 'myc_install' );

function myc_uninstall() {
  global $myc_all_db_versions;

  foreach( array_reverse( $myc_all_db_versions ) as $version_float ) {
    call_user_func( 'myc_uninstall_' . float_version_to_string( $version_float ));
  }

}

//      fwrite(STDERR, print_r( "\nmyc_install calling " . $update_func . "\n" ));
