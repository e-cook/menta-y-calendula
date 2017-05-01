<?php
/**
 * Class InstallTest
 *
 * @package Menta_Y_Calendula
 */

require_once( dirname( dirname( __FILE__ ) ). '/menta-y-calendula.php' );
require_once( dirname( __FILE__ ). '/populate_database.php' );


class InstallTest extends WP_UnitTestCase {

    function test_install() {
	$this->assertTrue( myc_install() === 1 );
    }

    function test_populate_ingredients() {
	global $wpdb;
	$table = $wpdb->prefix . 'ingredient';
	populate_ingredients($wpdb, $table);
	$this->assertTrue( $wpdb->get_var( "SELECT name FROM $table WHERE id=3" ) == 'All' );
    }

    function test_populate_ingredient_tags() {
	global $wpdb;
	$table = $wpdb->prefix . 'ingredient_tag';
	populate_ingredient_tags($wpdb, $table);
	$this->assertTrue( $wpdb->get_var( "SELECT for_id FROM $table WHERE id=3" ) == '3' );
    }

    function test_populate_providers() {
	global $wpdb;
	$table = $wpdb->prefix . 'provider';
	populate_providers($wpdb, $table);
	$this->assertTrue( $wpdb->get_var( "SELECT name FROM $table WHERE id=4" ) == 'Xarxa' );
    }

    function test_populate_provider_tags() {
	global $wpdb;
	$table = $wpdb->prefix . 'provider_tag';
	populate_provider_tags($wpdb, $table);
	$this->assertTrue( $wpdb->get_var( "SELECT for_id FROM $table WHERE id=3" ) == '2' );
    }

    function test_populate_provided_by() {
	global $wpdb;
	$table = $wpdb->prefix . 'provided_by';
	populate_provided_by($wpdb, $table);
	$this->assertTrue( $wpdb->get_var( "SELECT provider_id FROM $table WHERE id=3" ) == '4' );
    }

    function test_populate_buy() {
	global $wpdb;
	$table_name = $wpdb->prefix . "buy";
	populate_buy($wpdb, $table_name);
	$this->assertTrue( $wpdb->get_var( "SELECT total_price FROM $table_name WHERE id='1'" ) == 4.5 );
    }

    function test_populate_recipe() {
	global $wpdb;
	$table_name = $wpdb->prefix . "recipe";
	populate_recipes($wpdb, $table_name);
	$this->assertTrue( $wpdb->get_var( "SELECT difficulty FROM $table_name WHERE id='2'" ) == '1' );
    }

    function test_populate_recipe_tag() {
	global $wpdb;
	$table = $wpdb->prefix . 'recipe_tag';
	populate_recipe_tags($wpdb, $table);
	$this->assertTrue( $wpdb->get_var( "SELECT for_id FROM $table WHERE id=4" ) == '2' );
    }
    

    function test_uninstall() {
	global $wpdb;

	$table = $wpdb->prefix . "buy";
	myc_uninstall();
	$this->assertTrue( $wpdb->query( "SHOW TABLES LIKE '$table'" ) === 0 );
    }
}
