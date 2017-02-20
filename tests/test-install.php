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
	  $this->assertTrue( myc_install( array( 0, 0.1 ) ) == 0.1 );
	}

	function test_populate_products() {
	  global $wpdb;
	  $posts_table = $wpdb->prefix . 'posts';
	  populate_products($wpdb, $posts_table);
	  $this->assertTrue( $wpdb->get_var( "SELECT post_title FROM $posts_table WHERE id=6" ) == 'Ull de Molins' );
	}

	function test_populate_providers() {
	  global $wpdb;
	  $provided_by_table = $wpdb->prefix . 'provided_by';
	  populate_providers($wpdb, $provided_by_table);
	  $this->assertTrue( $wpdb->get_var( "SELECT provider_id FROM $provided_by_table WHERE id=6" ) == '7' );
	}

	function test_populate_buy() {
	  global $wpdb;
	  $table_name = $wpdb->prefix . "buy";
	  populate_buy($wpdb, $table_name);
	  $this->assertTrue( $wpdb->get_var( "SELECT total_price FROM $table_name WHERE id='1'" ) == 4.5 );
	}


	function test_uninstall() {
	  global $wpdb;

	  $price_table_name = $wpdb->prefix . "price";
	  myc_uninstall();
	  $this->assertTrue( $wpdb->query( "SHOW TABLES LIKE '$price_table_name'" ) === 0 );
	}
}
