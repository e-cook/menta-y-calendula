<?php
/**
 * Some functions to populate the test and original database
 *
 * @package Menta_Y_Calendula
 */
function test_install() {
    $this->assertTrue(myc_install()==1);
}

function populate_products($wpdb, $posts_table) {
  $wpdb->query(
"INSERT INTO $posts_table (`id`, `post_author`, `post_content`, `post_title`, `post_status`, `comment_status`, `guid`, `post_type`)
VALUES
(10, 1, 'vermell i bo', 'Tomaquet', 'draft', 'open', 'http://localhost/wordpress/?post_type=product&#038;p=1', 'product'),
(11, 1, 'verd i bo', 'Oli d.oliva', 'draft', 'open', 'http://localhost/wordpress/?post_type=product&#038;p=2', 'product'),
(12, 1, 'blanc i bo','All', 'draft', 'open', 'http://localhost/wordpress/?post_type=product&#038;p=3', 'product'),
(13, 1, 'molts productes', 'Cal Valls', 'draft', 'open', 'http://localhost/wordpress/?post_type=product&#038;p=4', 'provider'),
(14, 1, 'moltes verdures', 'Aurora', 'draft', 'open', 'http://localhost/wordpress/?post_type=product&#038;p=5', 'provider'),
(15, 1, 'molt d.oli', 'Ull de Molins', 'draft', 'open', 'http://localhost/wordpress/?post_type=product&#038;p=6', 'provider'),
(16, 1, 'molts productes', 'Xarxa', 'draft', 'open', 'http://localhost/wordpress/?post_type=product&#038;p=7', 'provider')"
	       );
  }

function populate_providers($wpdb, $provided_by_table) {
  $wpdb->query(
"INSERT INTO $provided_by_table (`id`, `product_id`, `provider_id`)
VALUES
(1, 10, 4),
(2, 10, 5),
(3, 13, 6),
(4, 13, 7),
(5, 14, 5),
(6, 14, 7)");
}

function populate_buy($wpdb, $buy_table) {
	  $wpdb->query(
"INSERT INTO $buy_table (`date`, `product_id`, `provider_id`, `quantity`, `total_price`, `unit_price`)
VALUES
(NOW() - INTERVAL 1 WEEK, 10, 4, 4.0, 4.5, 1.125),
(NOW(),                   10, 4, 4.0, 5.0, 1.25),
(NOW() - INTERVAL 1 WEEK, 10, 5, 4.0, 6.0, 1.5),
(NOW(),                   10, 5, 4.0, 5.5, 1.375),
(NOW(),                   11, 6, 10.0, 30.0, 3.0),
(NOW() - INTERVAL 2 WEEK, 11, 7, 10.0, 40.0, 4.0),
(NOW(),                   14, 5, 0.5, 4.0, 8.0),
(NOW(),                   14, 7, 0.5, 5.0, 10.0)");
}
