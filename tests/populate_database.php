<?php
/**
 * Some functions to populate the test and original database
 *
 * @package Menta_Y_Calendula
 */
function test_install() {
    $this->assertTrue(myc_install()==1);
}

function populate_product_types() {
    global $wpdb;
    $wpdb->query(
	"INSERT INTO myc_terms (`name`, `slug`) VALUES
('generic_ingredient', 'generic_ingredient'),
('physical_ingredient', 'physical_ingredient'),
('provider', 'provider'),
('recipe', 'recipe'),
('meal', 'meal')"
    );
}

