<?php
/**
 * Some functions to populate the test and original database
 *
 * @package Menta_Y_Calendula
 */
function test_install() {
    $this->assertTrue(myc_install()==1);
}

function populate_posts($wpdb) {
    $wpdb->query(
	"INSERT INTO myc_posts (`post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`)
VALUES
(1, NOW(), NOW() - INTERVAL 1 HOUR, 'vermell', 'Tomàquet')"
	);
}

function populate_ingredients($wpdb, $table) {
    $wpdb->query(
	"INSERT INTO $table (`id`, `name`, `comment`, `modified`, `last_price`, `last_price_update`, `best_price`, `best_price_update`, `base_unit`)
VALUES
(1, 'Tomàquet', 'vermell', NOW(), 0.98, NOW(), .7, NOW() - INTERVAL 1 WEEK, 'kg'),
(2, 'Oli d.Oliva', 'verd i bo', NOW(), 12.3, NOW(), 10, NOW() - INTERVAL 1 WEEK, 'l'),
(3, 'All', 'blanc i bo', NOW(), .3, NOW(), .2, NOW() - INTERVAL 2 WEEK, 'kg')"
    );
}

function populate_providers($wpdb, $table) {
    $wpdb->query(
	"INSERT INTO $table (`id`, `name`, `modified`, `address`, `phone1`, `phone2`, `email1`, `account`, `comment`)
VALUES
(1, 'Cal Valls', NOW(), 'donde Cal Valls', '0123', '2345', 'cal@valls', 'banco cal valls', 'molts productes'),
(2, 'Aurora', NOW(), 'donde Aurora', '0123', '2345', 'auro@ra', 'banc aurora', 'sale el sol'),
(3, 'Ull de Molins', NOW(), 'donde Ull de molins', '0123', '2345', 'ull@molins', 'banc ull', 'molt d.oli'),
(4, 'Xarxa', NOW(), 'per tot arreu', '0123', '2345', 'xar@xa', 'banc xarxa', 'molts productes')"
    );
}

function populate_provided_by($wpdb, $table) {
    $wpdb->query(
	"INSERT INTO $table (`id`, `ingredient_id`, `provider_id`)
VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 4),
(4, 2, 3),
(5, 3, 1),
(6, 3, 2)");
}

function populate_buy($wpdb, $buy_table) {
    $wpdb->query(
	"INSERT INTO $buy_table (`date`, `ingredient_id`, `provider_id`, `quantity`, `total_price`, `unit_price`)
VALUES
(NOW() - INTERVAL 1 WEEK, 1, 4, 4.0, 4.5, 1.125),
(NOW(),                   1, 4, 4.0, 5.0, 1.25),
(NOW() - INTERVAL 1 WEEK, 1, 2, 4.0, 6.0, 1.5),
(NOW(),                   1, 2, 4.0, 5.5, 1.375),
(NOW(),                   2, 3, 10.0, 30.0, 3.0),
(NOW() - INTERVAL 2 WEEK, 2, 3, 10.0, 40.0, 4.0),
(NOW(),                   3, 2, 0.5, 4.0, 8.0),
(NOW(),                   3, 4, 0.5, 5.0, 10.0)");
}

function populate_recipes($wpdb, $table) {
    $wpdb->query(
	"INSERT INTO $table (`id`, `name`, `modified`, `production_price`, `last_price_update`, `difficulty`)
VALUES
(1, 'Sofrito', NOW() - INTERVAL 1 WEEK, 5.2, NOW() - INTERVAL 1 HOUR, 1), 
(2, 'All i verdura', NOW() - INTERVAL 1 WEEK, 3.2, NOW() - INTERVAL 30 MINUTE, 1)");
}

