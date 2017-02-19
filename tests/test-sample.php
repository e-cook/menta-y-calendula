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

	function test_uninstall() {
	  global $wpdb;

	  $price_table_name = $wpdb->prefix . "price";
	  myc_uninstall();
	  $this->assertTrue( $wpdb->query( "SHOW TABLES LIKE '$price_table_name'" ) === 0 );
	}
}
