<?php
/**
 * Class SampleTest
 *
 * @package Menta_Y_Calendula
 */

require_once( dirname( dirname( __FILE__ ) ). '/menta-y-calendula.php' );

/**
 * Sample test case.
 */
class InstallTest extends WP_UnitTestCase {

	function test_install() {
	  $this->assertTrue( myc_install( array( 0, 0.1 ) ) == 0.1 );
	}

	function test_populate_price() {
	  global $wpdb;
	  $table_name = $wpdb->prefix . "price";

	  $wpdb->insert($table_name, 
			array(
			      'id' => 1,
			      'product_id' => 2,
			      'provider_id' => 3,
			      'last_update' => '2017-02-02 12:34:40',
			      'week_price' => 23.0,
			      'month_price' => 24.0
			      ));
	  $this->assertTrue( $wpdb->get_var( "SELECT week_price FROM $table_name WHERE id='1'" ) == 23.0 );
	}

	function test_populate_buy() {
	  global $wpdb;
	  $table_name = $wpdb->prefix . "buy";

	  $wpdb->insert($table_name, 
			array(
			      'id' => 1,
			      'time' => '2017-02-02 12:30:00',
			      'product_id' => 2,
			      'provider_id' => 3,
			      'quantity' => 4.0,
			      'buy_total_price' => 23.0
			      ));
	  $this->assertTrue( $wpdb->get_var( "SELECT buy_total_price FROM $table_name WHERE id='1'" ) == 23.0 );
	}

	function test_uninstall() {
	  global $wpdb;

	  $price_table_name = $wpdb->prefix . "price";
	  myc_uninstall();
	  $this->assertTrue( $wpdb->query( "SHOW TABLES LIKE '$price_table_name'" ) === 0 );
	}
}
