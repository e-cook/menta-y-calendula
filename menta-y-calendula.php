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

function myc_install_0_1() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $buy_table_name = $wpdb->prefix . 'buy';
    $provided_by_table_name = $wpdb->prefix . 'provided_by';

    $sql = array(
	"CREATE TABLE $buy_table_name (
id bigint(20) NOT NULL AUTO_INCREMENT,
date timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
product_id bigint(20) UNSIGNED NOT NULL,
provider_id bigint(20) UNSIGNED NOT NULL,
quantity decimal(8,2),
total_price decimal(8,2),
unit_price decimal(10,4),
PRIMARY KEY  (id),
KEY date (date),
KEY product_id (product_id),
KEY unit_price (unit_price)
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
    $provided_by_table_name = $wpdb->prefix . 'provided_by';

    $wpdb->query("DROP TABLE IF EXISTS $buy_table_name");  
    $wpdb->query("DROP TABLE IF EXISTS $provided_by_table_name");  
}

function float_version_to_string($version) {
    return str_replace( '.', '_', strval( $version ));
}

function myc_install( $myc_test_db_versions=array() ) {
    myc_install_0_1();
    add_option( 'myc_db_version', '0.1', '', 'yes' );  
}

register_activation_hook( __FILE__, 'myc_install' );

function myc_uninstall() {
    myc_uninstall_0_1();
}

register_activation_hook( __FILE__, 'myc_uninstall' );

//      fwrite(STDERR, print_r( "\nmyc_install calling " . $update_func . "\n" ));
