<?php
/**
 * Recipepocket recipe controller.
 *
 * @package Recipepocket
 * @author  Steven Wett <stevenwett@gmail.com>
 * @version 0.0.1
 */

namespace Recipepocket;

/**
 * Recipe controller
 */
class Recipe_Controller {
	public function __construct( $init = false ) {
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	/**
	 * Get recipe
	 *
	 * @param int $recipe_id Recipe ID.
	 */
	public function get_recipe( $recipe_id ) {
	}

	/**
	 * Add recipe
	 *
	 * @param array $recipe_data Recipe data.
	 */
	public function add_recipe( $recipe_data ) {
	}

	/**
	 * Update recipe
	 *
	 * @param array $recipe_data Recipe data.
	 */
	public function update_recipe( $recipe_data ) {
	}

	/**
	 * Delete recipe
	 *
	 * @param array $recipe_id Recipe ID.
	 */
	public function delete_recipe( $recipe_data ) {
	}

	/**
	 * Set the recipe to deactive
	 *
	 * @param array $recipe_id Recipe ID.
	 */
	public function deactivate_recipe( $recipe_id ) {
	}

	/**
	 * The URL of the recipe you're trying to scrape.
	 *
	 * @param string $url Recipe URL.
	 */
	public function get_recipe_from_url( $url ) {
	}

	/**
	 * Registering endpoints using the WordPress REST API
	 */
	public function register_endpoints() {
		// The endpoint for getting a recipe.
		register_rest_route(
			'recipepocket/v1',
			'/recipe',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'endpoint_callback_get_recipe' ),
				'permission_callback' => array( $this, 'endpoint_permissions_public' ),
			)
		);
	}

	/**
	 * Endpoint permissions for the public.
	 */
	public function endpoint_permissions_public() {
		return true;
	}

	/**
	 * REST API endpoint callback function for getting a recipe.
	 *
	 * @param \WP_REST_Request $request Request.
	 */
	public function endpoint_callback_get_recipe( \WP_REST_Request $request ) {
		$response_code    = 400;
		$response_message = '';

		$recipe = array();

		try {
		} catch ( \Exception $e ) {
			// TODO: Log error.
		}

		if ( is_wp_error( $request ) ) {
			$response_code    = 400;
			$response_message = $request->get_error_message();
		}

		$response = new \WP_REST_Response(
			array(
				'message' => $response_message,
				'recipe'  => $recipe,
			),
			$response_code
		);

		return rest_ensure_response( $response );
	}
}
