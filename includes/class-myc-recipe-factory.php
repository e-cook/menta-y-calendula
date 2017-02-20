<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Recipe Factory Class
 *
 * The Myc recipe factory creating the right recipe object.
 *
 * @class 		MYC_Recipe_Factory
 * @version		2.3.0
 * @package		Myc/Classes
 * @category	Class
 * @author 		WooThemes
 */
class MYC_Recipe_Factory {

	/**
	 * Get a recipe.
	 *
	 * @param bool $the_recipe (default: false)
	 * @param array $args (default: array())
	 * @return MYC_Recipe|bool false if the recipe cannot be loaded
	 */
	public function get_recipe( $the_recipe = false, $args = array() ) {
		try {
			$the_recipe = $this->get_recipe_object( $the_recipe );

			if ( ! $the_recipe ) {
				throw new Exception( 'Recipe object does not exist', 422 );
			}

			$classname = $this->get_recipe_class( $the_recipe, $args );

			if ( ! $classname ) {
				throw new Exception( 'Missing classname', 422 );
			}

			if ( ! class_exists( $classname ) ) {
				$classname = 'MYC_Recipe_Simple';
			}

			return new $classname( $the_recipe, $args );

		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Create a MYC coding standards compliant class name e.g. MYC_Recipe_Type_Class instead of MYC_Recipe_type-class.
	 * @param  string $recipe_type
	 * @return string|false
	 */
	private function get_classname_from_recipe_type( $recipe_type ) {
		return $recipe_type ? 'MYC_Recipe_' . implode( '_', array_map( 'ucfirst', explode( '-', $recipe_type ) ) ) : false;
	}

	/**
	 * Get the recipe class name.
	 * @param  WP_Post $the_recipe
	 * @param  array $args (default: array())
	 * @return string
	 */
	private function get_recipe_class( $the_recipe, $args = array() ) {
		$recipe_id = absint( $the_recipe->ID );
		$post_type  = $the_recipe->post_type;

		if ( 'recipe' === $post_type ) {
			if ( isset( $args['recipe_type'] ) ) {
				$recipe_type = $args['recipe_type'];
			} else {
				$terms        = get_the_terms( $the_recipe, 'recipe_type' );
				$recipe_type = ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
			}
		} elseif( 'recipe_variation' === $post_type ) {
			$recipe_type = 'variation';
		} else {
			$recipe_type = false;
		}

		$classname = $this->get_classname_from_recipe_type( $recipe_type );

		// Filter classname so that the class can be overridden if extended.
		return apply_filters( 'myc_recipe_class', $classname, $recipe_type, $post_type, $recipe_id );
	}

	/**
	 * Get the recipe object.
	 * @param  mixed $the_recipe
	 * @uses   WP_Post
	 * @return WP_Post|bool false on failure
	 */
	private function get_recipe_object( $the_recipe ) {
		if ( false === $the_recipe ) {
			$the_recipe = $GLOBALS['post'];
		} elseif ( is_numeric( $the_recipe ) ) {
			$the_recipe = get_post( $the_recipe );
		} elseif ( $the_recipe instanceof MYC_Recipe ) {
			$the_recipe = get_post( $the_recipe->id );
		} elseif ( ! ( $the_recipe instanceof WP_Post ) ) {
			$the_recipe = false;
		}

		return apply_filters( 'myc_recipe_object', $the_recipe );
	}
}
