<?php
/*
Plugin Name: Menta y Calendula
Plugin URI:  https://github.org/menta-y-calendula
Description: Organic Food Preparation and Ordering
Version:     20170927
Author:      ???
Author URI:  https://developer.wordpress.org/
License:     GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: wporg???
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

function recipe_init() {
	 // create recipe taxonomy
	 register_taxonomy(
		'recipe',
		'post',
		array(
			'label'        => __( 'Recipe', 'menta-y-calendula' )
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