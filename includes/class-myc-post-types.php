<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @class     MYC_Post_types
 * @version   0.1
 * @package   MentaYCalendula/Classes/Products
 * @category  Class
 * @author    e-cook
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MYC_Post_types Class.
 */
class MYC_Post_types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
		add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
	}

	/**
	 * Register core taxonomies.
	 */
	public static function register_taxonomies() {
		if ( taxonomy_exists( 'recipe_type' ) ) {
			return;
		}

		do_action( 'myc_register_taxonomy' );

		$permalinks = get_option( 'myc_permalinks' );

		register_taxonomy( 'recipe_type',
			apply_filters( 'myc_taxonomy_objects_recipe_type', array( 'recipe' ) ),
			apply_filters( 'myc_taxonomy_args_recipe_type', array(
				'hierarchical'      => false,
				'show_ui'           => false,
				'show_in_nav_menus' => false,
				'query_var'         => is_admin(),
				'rewrite'           => false,
				'public'            => false
			) )
		);

		register_taxonomy( 'recipe_cat',
			apply_filters( 'myc_taxonomy_objects_recipe_cat', array( 'recipe' ) ),
			apply_filters( 'myc_taxonomy_args_recipe_cat', array(
				'hierarchical'          => true,
				'update_count_callback' => '_myc_term_recount',
				'label'                 => __( 'Recipe Categories', 'myc' ),
				'labels' => array(
						'name'              => __( 'Recipe Categories', 'myc' ),
						'singular_name'     => __( 'Recipe Category', 'myc' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'myc' ),
						'search_items'      => __( 'Search Recipe Categories', 'myc' ),
						'all_items'         => __( 'All Recipe Categories', 'myc' ),
						'parent_item'       => __( 'Parent Recipe Category', 'myc' ),
						'parent_item_colon' => __( 'Parent Recipe Category:', 'myc' ),
						'edit_item'         => __( 'Edit Recipe Category', 'myc' ),
						'update_item'       => __( 'Update Recipe Category', 'myc' ),
						'add_new_item'      => __( 'Add New Recipe Category', 'myc' ),
						'new_item_name'     => __( 'New Recipe Category Name', 'myc' ),
						'not_found'         => __( 'No Recipe Category found', 'myc' ),
					),
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_recipe_terms',
					'edit_terms'   => 'edit_recipe_terms',
					'delete_terms' => 'delete_recipe_terms',
					'assign_terms' => 'assign_recipe_terms',
				),
				'rewrite'               => array(
					'slug'         => empty( $permalinks['category_base'] ) ? _x( 'recipe-category', 'slug', 'myc' ) : $permalinks['category_base'],
					'with_front'   => false,
					'hierarchical' => true,
				),
			) )
		);

		register_taxonomy( 'recipe_tag',
			apply_filters( 'myc_taxonomy_objects_recipe_tag', array( 'recipe' ) ),
			apply_filters( 'myc_taxonomy_args_recipe_tag', array(
				'hierarchical'          => false,
				'update_count_callback' => '_myc_term_recount',
				'label'                 => __( 'Recipe Tags', 'myc' ),
				'labels'                => array(
						'name'                       => __( 'Recipe Tags', 'myc' ),
						'singular_name'              => __( 'Recipe Tag', 'myc' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'myc' ),
						'search_items'               => __( 'Search Recipe Tags', 'myc' ),
						'all_items'                  => __( 'All Recipe Tags', 'myc' ),
						'edit_item'                  => __( 'Edit Recipe Tag', 'myc' ),
						'update_item'                => __( 'Update Recipe Tag', 'myc' ),
						'add_new_item'               => __( 'Add New Recipe Tag', 'myc' ),
						'new_item_name'              => __( 'New Recipe Tag Name', 'myc' ),
						'popular_items'              => __( 'Popular Recipe Tags', 'myc' ),
						'separate_items_with_commas' => __( 'Separate Recipe Tags with commas', 'myc'  ),
						'add_or_remove_items'        => __( 'Add or remove Recipe Tags', 'myc' ),
						'choose_from_most_used'      => __( 'Choose from the most used Recipe tags', 'myc' ),
						'not_found'                  => __( 'No Recipe Tags found', 'myc' ),
					),
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_recipe_terms',
					'edit_terms'   => 'edit_recipe_terms',
					'delete_terms' => 'delete_recipe_terms',
					'assign_terms' => 'assign_recipe_terms',
				),
				'rewrite'               => array(
					'slug'       => empty( $permalinks['tag_base'] ) ? _x( 'recipe-tag', 'slug', 'myc' ) : $permalinks['tag_base'],
					'with_front' => false
				),
			) )
		);

		register_taxonomy( 'recipe_shipping_class',
			apply_filters( 'myc_taxonomy_objects_recipe_shipping_class', array( 'recipe', 'recipe_variation' ) ),
			apply_filters( 'myc_taxonomy_args_recipe_shipping_class', array(
				'hierarchical'          => false,
				'update_count_callback' => '_update_post_term_count',
				'label'                 => __( 'Shipping Classes', 'myc' ),
				'labels' => array(
						'name'              => __( 'Shipping Classes', 'myc' ),
						'singular_name'     => __( 'Shipping Class', 'myc' ),
						'menu_name'         => _x( 'Shipping Classes', 'Admin menu name', 'myc' ),
						'search_items'      => __( 'Search Shipping Classes', 'myc' ),
						'all_items'         => __( 'All Shipping Classes', 'myc' ),
						'parent_item'       => __( 'Parent Shipping Class', 'myc' ),
						'parent_item_colon' => __( 'Parent Shipping Class:', 'myc' ),
						'edit_item'         => __( 'Edit Shipping Class', 'myc' ),
						'update_item'       => __( 'Update Shipping Class', 'myc' ),
						'add_new_item'      => __( 'Add New Shipping Class', 'myc' ),
						'new_item_name'     => __( 'New Shipping Class Name', 'myc' )
					),
				'show_ui'               => false,
				'show_in_quick_edit'    => false,
				'show_in_nav_menus'     => false,
				'query_var'             => is_admin(),
				'capabilities'          => array(
					'manage_terms' => 'manage_recipe_terms',
					'edit_terms'   => 'edit_recipe_terms',
					'delete_terms' => 'delete_recipe_terms',
					'assign_terms' => 'assign_recipe_terms',
				),
				'rewrite'               => false,
			) )
		);

		global $myc_recipe_attributes;

		$myc_recipe_attributes = array();

		if ( $attribute_taxonomies = myc_get_attribute_taxonomies() ) {
			foreach ( $attribute_taxonomies as $tax ) {
				if ( $name = myc_attribute_taxonomy_name( $tax->attribute_name ) ) {
					$tax->attribute_public          = absint( isset( $tax->attribute_public ) ? $tax->attribute_public : 1 );
					$label                          = ! empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;
					$myc_recipe_attributes[ $name ] = $tax;
					$taxonomy_data                  = array(
						'hierarchical'          => true,
						'update_count_callback' => '_update_post_term_count',
						'labels'                => array(
								'name'              => $label,
								'singular_name'     => $label,
								'search_items'      => sprintf( __( 'Search %s', 'myc' ), $label ),
								'all_items'         => sprintf( __( 'All %s', 'myc' ), $label ),
								'parent_item'       => sprintf( __( 'Parent %s', 'myc' ), $label ),
								'parent_item_colon' => sprintf( __( 'Parent %s:', 'myc' ), $label ),
								'edit_item'         => sprintf( __( 'Edit %s', 'myc' ), $label ),
								'update_item'       => sprintf( __( 'Update %s', 'myc' ), $label ),
								'add_new_item'      => sprintf( __( 'Add New %s', 'myc' ), $label ),
								'new_item_name'     => sprintf( __( 'New %s', 'myc' ), $label ),
								'not_found'         => sprintf( __( 'No &quot;%s&quot; found', 'myc' ), $label ),
							),
						'show_ui'            => true,
						'show_in_quick_edit' => false,
						'show_in_menu'       => false,
						'show_in_nav_menus'  => false,
						'meta_box_cb'        => false,
						'query_var'          => 1 === $tax->attribute_public,
						'rewrite'            => false,
						'sort'               => false,
						'public'             => 1 === $tax->attribute_public,
						'show_in_nav_menus'  => 1 === $tax->attribute_public && apply_filters( 'myc_attribute_show_in_nav_menus', false, $name ),
						'capabilities'       => array(
							'manage_terms' => 'manage_recipe_terms',
							'edit_terms'   => 'edit_recipe_terms',
							'delete_terms' => 'delete_recipe_terms',
							'assign_terms' => 'assign_recipe_terms',
						)
					);

					if ( 1 === $tax->attribute_public ) {
						$taxonomy_data['rewrite'] = array(
							'slug'         => empty( $permalinks['attribute_base'] ) ? '' : trailingslashit( $permalinks['attribute_base'] ) . sanitize_title( $tax->attribute_name ),
							'with_front'   => false,
							'hierarchical' => true
						);
					}

					register_taxonomy( $name, apply_filters( "myc_taxonomy_objects_{$name}", array( 'recipe' ) ), apply_filters( "myc_taxonomy_args_{$name}", $taxonomy_data ) );
				}
			}
		}

		do_action( 'myc_after_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( post_type_exists('recipe') ) {
			return;
		}

		do_action( 'myc_register_post_type' );

		$permalinks        = get_option( 'myc_permalinks' );
		$recipe_permalink = empty( $permalinks['recipe_base'] ) ? _x( 'recipe', 'slug', 'myc' ) : $permalinks['recipe_base'];

		register_post_type( 'recipe',
			apply_filters( 'myc_register_post_type_recipe',
				array(
					'labels'              => array(
							'name'                  => __( 'Recipes', 'myc' ),
							'singular_name'         => __( 'Recipe', 'myc' ),
							'menu_name'             => _x( 'Recipes', 'Admin menu name', 'myc' ),
							'add_new'               => __( 'Add Recipe', 'myc' ),
							'add_new_item'          => __( 'Add New Recipe', 'myc' ),
							'edit'                  => __( 'Edit', 'myc' ),
							'edit_item'             => __( 'Edit Recipe', 'myc' ),
							'new_item'              => __( 'New Recipe', 'myc' ),
							'view'                  => __( 'View Recipe', 'myc' ),
							'view_item'             => __( 'View Recipe', 'myc' ),
							'search_items'          => __( 'Search Recipes', 'myc' ),
							'not_found'             => __( 'No Recipes found', 'myc' ),
							'not_found_in_trash'    => __( 'No Recipes found in trash', 'myc' ),
							'parent'                => __( 'Parent Recipe', 'myc' ),
							'featured_image'        => __( 'Recipe Image', 'myc' ),
							'set_featured_image'    => __( 'Set recipe image', 'myc' ),
							'remove_featured_image' => __( 'Remove recipe image', 'myc' ),
							'use_featured_image'    => __( 'Use as recipe image', 'myc' ),
							'insert_into_item'      => __( 'Insert into recipe', 'myc' ),
							'uploaded_to_this_item' => __( 'Uploaded to this recipe', 'myc' ),
							'filter_items_list'     => __( 'Filter recipes', 'myc' ),
							'items_list_navigation' => __( 'Recipes navigation', 'myc' ),
							'items_list'            => __( 'Recipes list', 'myc' ),
						),
					'description'         => __( 'This is where you can add new recipes to your store.', 'myc' ),
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'recipe',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
					'rewrite'             => $recipe_permalink ? array( 'slug' => untrailingslashit( $recipe_permalink ), 'with_front' => false, 'feeds' => true ) : false,
					'query_var'           => true,
					'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
					'has_archive'         => ( $recipes_page_id = myc_get_page_id( 'recipes' ) ) && get_post( $recipes_page_id ) ? get_page_uri( $recipes_page_id ) : 'recipes',
					'show_in_nav_menus'   => true
				)
			)
		);


		register_post_type( 'recipe_variation',
			apply_filters( 'myc_register_post_type_recipe_variation',
				array(
					'label'        => __( 'Variations', 'myc' ),
					'public'       => false,
					'hierarchical' => false,
					'supports'     => false,
					'capability_type' => 'recipe'
				)
			)
		);
	/**
	 * Add Product Support to Jetpack Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'recipe' );
		}
	}

	/**
	 * Added product for Jetpack related posts.
	 *
	 * @param  array $post_types
	 * @return array
	 */
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'recipe';

		return $post_types;
	}
}

MYC_Post_types::init();
