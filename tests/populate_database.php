<?php
/**
 * Some functions to populate the test and original database
 *
 * @package Menta_Y_Calendula
 */

function populate_products($wpdb, $posts_table) {
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
  }

function populate_providers($wpdb, $provided_by_table) {
  $wpdb->query(
"INSERT INTO $provided_by_table (`id`, `product_id`, `provider_id`)
VALUES
(1, 1, 4),
(2, 1, 5),
(3, 2, 6),
(4, 2, 7),
(5, 3, 5),
(6, 3, 7)");
}

function populate_buy($wpdb, $buy_table) {
	  $wpdb->query(
"INSERT INTO $buy_table (`date`, `product_id`, `provider_id`, `quantity`, `total_price`, `unit_price`)
VALUES
(NOW() - INTERVAL 1 WEEK, 1, 4, 4.0, 4.5, 1.125),
(NOW(),                   1, 4, 4.0, 5.0, 1.25),
(NOW() - INTERVAL 1 WEEK, 1, 5, 4.0, 6.0, 1.5),
(NOW(),                   1, 5, 4.0, 5.5, 1.375),
(NOW(),                   2, 6, 10.0, 30.0, 3.0),
(NOW() - INTERVAL 2 WEEK, 2, 7, 10.0, 40.0, 4.0),
(NOW(),                   3, 5, 0.5, 4.0, 8.0),
(NOW(),                   3, 7, 0.5, 5.0, 10.0)");
}