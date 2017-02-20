<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Grouped Recipe Class.
 *
 * Grouped recipes are wrappers for other recipes.
 *
 * @class 		MYC_Recipe_Grouped
 * @version		0.1
 * @package		Myc/Classes/Recipes
 * @category	Class
 * @author 		e-cook
 */
class MYC_Recipe_Grouped extends MYC_Recipe {

	/** @public array Array of child recipes/posts/variations. */
	public $children;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @param mixed $recipe
	 */
	public function __construct( $recipe ) {
		$this->recipe_type = 'grouped';
		parent::__construct( $recipe );
	}

	/**
	 * Get the add to cart button text.
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_text() {
		return apply_filters( 'myc_recipe_add_to_cart_text', __( 'View recipes', 'myc' ), $this );
	}

	/**
	 * Return the recipes children posts.
	 *
	 * @access public
	 * @return array
	 */
	public function get_children() {
		if ( ! is_array( $this->children ) || empty( $this->children ) ) {
			$transient_name = 'myc_recipe_children_' . $this->id;
			$this->children = array_filter( array_map( 'absint', (array) get_transient( $transient_name ) ) );

			if ( empty( $this->children ) ) {

				$args = apply_filters( 'myc_grouped_children_args', array(
					'post_parent' 	=> $this->id,
					'post_type'		=> 'recipe',
					'orderby'		=> 'menu_order',
					'order'			=> 'ASC',
					'fields'		=> 'ids',
					'post_status'	=> 'publish',
					'numberposts'	=> -1,
				) );

				$this->children = get_posts( $args );

				set_transient( $transient_name, $this->children, DAY_IN_SECONDS * 30 );
			}
		}
		return (array) $this->children;
	}

	/**
	 * Returns whether or not the recipe has any child recipe.
	 *
	 * @access public
	 * @return bool
	 */
	public function has_child() {
		return sizeof( $this->get_children() ) ? true : false;
	}
}
