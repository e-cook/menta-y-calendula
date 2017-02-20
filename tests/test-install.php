<?php
/**
 * Class InstallTest
 *
 * @package Menta_Y_Calendula
 */

require_once( dirname( dirname( __FILE__ ) ). '/menta-y-calendula.php' );

class InstallTest extends WP_UnitTestCase {

	function test_install() {
	  $this->assertTrue( myc_install( array( 0, 0.1 ) ) == 0.1 );
	}

	function test_populate_products() {
	  global $wpdb;
	  $posts_table = $wpdb->prefix . 'posts';
	  $wpdb->query(
"INSERT INTO $posts_table (`id`, `post_author`, `post_content`, `post_title`, `post_status`, `comment_status`, `guid`, `post_type`)
VALUES
(1, 1, 'vermell i bo', 'Tomaquet', 'draft', 'open', 'http://localhost/wp/?post_type=product&#038;p=1', 'product'),
(2, 1, 'verd i bo', 'Oli d.oliva', 'draft', 'open', 'http://localhost/wp/?post_type=product&#038;p=2', 'product'),
(3, 1, 'blanc i bo','All', 'draft', 'open', 'http://localhost/wp/?post_type=product&#038;p=3', 'product'),
(4, 1, 'molts productes', 'Cal Valls', 'draft', 'open', 'http://localhost/wp/?post_type=product&#038;p=4', 'provider'),
(5, 1, 'moltes verdures', 'Aurora', 'draft', 'open', 'http://localhost/wp/?post_type=product&#038;p=5', 'provider'),
(6, 1, 'molt d.oli', 'Ull de Molins', 'draft', 'open', 'http://localhost/wp/?post_type=product&#038;p=6', 'provider'),
(7, 1, 'molts productes', 'Xarxa', 'draft', 'open', 'http://localhost/wp/?post_type=product&#038;p=7', 'provider')"
		       );
	  $this->assertTrue( $wpdb->get_var( "SELECT post_title FROM $posts_table WHERE id=6" ) == 'Ull de Molins' );

	  $provided_by_table = $wpdb->prefix . 'provided_by';
	  $wpdb->query(
"INSERT INTO $provided_by_table (`id`, `product_id`, `provider_id`)
VALUES
(1, 1, 4),
(2, 1, 5),
(3, 2, 6),
(4, 2, 7),
(5, 3, 5),
(6, 3, 7)");
	  $this->assertTrue( $wpdb->get_var( "SELECT provider_id FROM $provided_by_table WHERE id=6" ) == '7' );
	}

	function test_populate_buy() {
	  global $wpdb;
	  $table_name = $wpdb->prefix . "buy";
	  $wpdb->query(
"INSERT INTO $table_name (`timestamp`, `product_id`, `provider_id`, `quantity`, `buy_total_price`)
VALUES
(NOW() - INTERVAL 1 WEEK, 1, 4, 4.0, 4.5),
(NOW(),                   1, 4, 4.0, 5.0),
(NOW() - INTERVAL 1 WEEK, 1, 5, 4.0, 6.0),
(NOW(),                   1, 5, 4.0, 5.5),
(NOW(),                   2, 6, 10.0, 30.0),
(NOW() - INTERVAL 2 WEEK, 2, 7, 10.0, 40.0),
(NOW(),                   3, 5, 0.5, 4.0),
(NOW(),                   3, 7, 0.5, 5.0)");
	  $this->assertTrue( $wpdb->get_var( "SELECT buy_total_price FROM $table_name WHERE id='1'" ) == 4.5 );
	}

	function test_populate_price() {
	  global $wpdb;
	  $table_name = $wpdb->prefix . 'price';
	  $wpdb->query(
"INSERT INTO $table_name (`id`, `product_id`, `provider_id`, `week_price`, `month_price`)
VALUES
(1, 2, 3, 			array(
			      'id' => 1,
			      'product_id' => 2,
			      'provider_id' => 3,
			      'week_price' => 23.0,
			      'month_price' => 24.0
			      ));
	  $this->assertTrue( $wpdb->get_var( "SELECT week_price FROM $table_name WHERE id='1'" ) == 23.0 );
	}

	function test_uninstall() {
	  global $wpdb;

	  $price_table_name = $wpdb->prefix . "price";
	  myc_uninstall();
	  $this->assertTrue( $wpdb->query( "SHOW TABLES LIKE '$price_table_name'" ) === 0 );
	}
}
