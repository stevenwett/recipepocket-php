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
	 * @param int $recipe_id       Recipe ID.
	 * @param bool   $only_active  Only include active users.
	 */
	public function get_recipe( $recipe_id, $only_active = true ) {
		global $wpdb;

		if ( empty( $recipe_id ) ) {
			return false;
		}

		$recipe = array();

		if ( $only_active ) {
			$recipe = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT * FROM recipepocket_recipes WHERE id = %d AND active = 1',
					(int) $recipe_id
				),
				OBJECT
			);
		} else {
			$recipe = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT * FROM recipepocket_recipes WHERE id = %d',
					(int) $recipe_id
				),
				OBJECT
			);
		}

		return $recipe;
	}

	/**
	 * Add recipe
	 *
	 * @param array $recipe_data Recipe data.
	 */
	public function create_recipe( $recipe_data ) {
		global $wpdb;

		$response_code    = 400;
		$response_message = '';
		$new_recipe       = array();

		try {
			if ( empty( $recipe_data ) ) {
				throw new \Exception( 'No recipe data.', 400 );
			}


			if ( empty( $recipe_data['user_id'] ) ) {
				throw new \Exception( 'No user_id.', 400 );
			}

			if ( empty( $recipe_data['name'] ) ) {
				throw new \Exception( 'No name.', 400 );
			}

			if ( empty( $recipe_data['preparation_steps'] ) ) {
				throw new \Exception( 'No preparation_steps.', 400 );
			}

			if ( empty( $recipe_data['ingredients'] ) ) {
				throw new \Exception( 'No ingredients.', 400 );
			}

			$source            = '';
			$author            = '';
			$preparation_steps = wp_json_encode( $recipe_data['preparation_steps'] );
			$ingredients       = wp_json_encode( $recipe_data['ingredients'] );

			if ( ! empty( $recipe_data['source'] ) ) {
				$source = wp_json_encode( $recipe_data['source'] );
			}

			if ( isset( $recipe_data['author'] ) ) {
				$author = sanitize_text_field( $recipe_data['author'] );
			}

			$recipe = array(
				'active'            => 1,
				'user_id'           => (int) $recipe_data['user_id'],
				'name'              => sanitize_text_field( $recipe_data['name'] ),
				'source'            => $source,
				'author'            => $author,
				'preparation_steps' => $preparation_steps,
				'ingredients'       => $ingredients,
				'created_gmt'       => current_time( 'mysql' ),
				'modified_gmt'      => current_time( 'mysql' ),
			);

			$insert_response = $wpdb->insert(
				'recipepocket_recipes',
				$recipe,
				array(
					'%d',
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
				)
			);

			if ( false == $insert_response || null == $insert_response ) {
				$response_message = 'Did not create recipe';
				throw new \Exception( 'Did not create recipe.', 400 );
			}

			$new_recipe = $this->get_recipe( (int) $wpdb->insert_id );

			if ( empty( $new_recipe ) ) {
				throw new \Exception( 'Error getting recipe after creating.', 400 );
			}

			$response_code    = 201;
			$response_message = 'New recipe created.';
		} catch ( \Exception $e ) {
			// TODO: Log error.
			$response_message = $e->getMessage();
		}

		return array(
			'code'    => $response_code,
			'message' => $response_message,
			'recipe'  => $new_recipe,
		);
	}

	/**
	 * Update recipe
	 *
	 * @param array $recipe_data Recipe data.
	 */
	public function update_recipe( $recipe_data ) {
		global $wpdb;

		$response_code    = 400;
		$response_message = '';
		$updated_recipe   = array();

		try {
			if ( empty( $recipe_data ) ) {
				throw new \Exception( 'No recipe data.', 400 );
			}

			if ( empty( $user_data['recipe_id'] ) ) {
				throw new \Exception( 'No recipe id.', 400 );
			}

			$recipe_id = (int) $recipe_data['recipe_id'];
			$recipe    = array(
				'modified_gmt' => current_time( 'mysql', true ),
			);

			$format = array(
				'%s',
			);

			if ( isset( $recipe_data['active'] ) ) {
				$recipe['active'] = (int) $recipe_data['active'];
				$format[]         = '%d';
			}

			if ( ! empty( $recipe_data['name'] ) ) {
				$recipe['name'] = sanitize_text_field( $recipe_data['name'] );
				$format[]       = '%s';
			}

			if ( ! empty( $recipe_data['source'] ) ) {
				$recipe['source'] = wp_json_encode( $recipe_data['source'] );
				$format[]         = '%s';
			}

			if ( ! empty( $recipe_data['author'] ) ) {
				$recipe['author'] = sanitize_text_field( $recipe_data['author'] );
				$format[]         = '%s';
			}

			if ( ! empty( $recipe_data['preparation_steps'] ) ) {
				$recipe['preparation_steps'] = wp_json_encode( $recipe_data['preparation_steps'] );
				$format[]                    = '%s';
			}

			if ( ! empty( $recipe_data['ingredients'] ) ) {
				$recipe['ingredients'] = wp_json_encode( $recipe_data['ingredients'] );
				$format[]              = '%s';
			}

			$update_response = $wpdb->update(
				'recipepocket_recipes',
				$user,
				array(
					'id' => $recipe_id,
				),
				$format,
				array(
					'%d',
				)
			);

			if ( null === $update_response ) {
				$response_code = 409;
				throw new \Exception( 'Error updating recipe.', 409 );
			}

			$updated_recipe = $this->get_recipe( $recipe_id );

			if ( empty( $updated_recipe ) ) {
				throw new \Exception( 'Error getting recipe after updating.', 400 );
			}

			$response_code    = 200;
			$response_message = 'Updated recipe.';
		} catch ( \Exception $e ) {
			// TODO: Log error.
			$response_message = $e->getMessage();
		}

		return array(
			'code'    => $response_code,
			'message' => $response_message,
			'recipe'  => $updated_recipe,
		);
	}

	/**
	 * Delete recipe
	 *
	 * @param array $recipe_id Recipe ID.
	 */
	public function delete_recipe( $recipe_data ) {
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
				'methods'             => 'POST',
				'callback'            => array( $this, 'endpoint_callback_create_recipe' ),
				'permission_callback' => array( $this, 'endpoint_permissions_authenticated_users' ),
			)
		);

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

		// The endpoint for getting a recipe.
		register_rest_route(
			'recipepocket/v1',
			'/recipe',
			array(
				'methods'             => 'PATCH',
				'callback'            => array( $this, 'endpoint_callback_update_recipe' ),
				'permission_callback' => array( $this, 'endpoint_permissions_authenticated_users' ),
			)
		);

		// The endpoint for getting a recipe.
		register_rest_route(
			'recipepocket/v1',
			'/recipe',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'endpoint_callback_update_recipe' ),
				'permission_callback' => array( $this, 'endpoint_permissions_authenticated_users' ),
			)
		);
	}

	/**
	 * Endpoint permissions for authenticated users
	 */
	public function endpoint_permissions_authenticated_users() {
		return $this->is_authorized;
	}

	/**
	 * Endpoint permissions for the public.
	 */
	public function endpoint_permissions_public() {
		return true;
	}

	/**
	 * REST API endpoint callback function for creating a recipe.
	 *
	 * @param \WP_REST_Request $request Request.
	 */
	public function endpoint_callback_create_recipe( \WP_REST_Request $request ) {
		$response_code    = 400;
		$response_message = '';

		$recipe = array();

		try {

			$create_response = $this->create_recipe( $request );

			$response_code    = $create_response['code'];
			$response_message = $create_response['message'];
			// $recipe           = $create_response['recipe'];

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
				// 'recipe'  => $recipe,
			),
			$response_code
		);

		return rest_ensure_response( $response );
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
			if ( empty( $request['recipe_id'] ) ) {
				throw new \Exception( 'No recipe_id.', 400 );
			}

			$recipe_id       = (int) $request['recipe_id'];
			$recipe_response = $this->get_recipe( $recipe_id );

			if ( empty( $recipe_response ) ) {
				$recipe_message = 'Error getting recipe.';
			}

			$response_code    = 200;
			$response_message = 'Successfully got recipe.';
			$recipe           = $recipe_response;

			if ( isset( $recipe_response->name ) ) {
				$response_message = sprintf( 'Successfully got recipe: %s', $recipe_response->name );
			}

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

	/**
	 * REST API endpoint callback function for updating a recipe.
	 *
	 * @param \WP_REST_Request $request Request.
	 */
	public function endpoint_callback_update_recipe( \WP_REST_Request $request ) {
		$response_code    = 400;
		$response_message = '';
		$recipe           = array();

		try {
			if ( empty( $request['recipe_id'] ) ) {
				throw new \Exception( 'recipe_id not in request.', 400 );
			}

			// Update recipe.
			$update_response = $this->update_recipe( $request );

			$response_code    = $update_response['code'];
			$response_message = $update_response['message'];
			// $recipe           = $update_response['recipe'];

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
				// 'recipe'  => $recipe,
			),
			$response_code
		);

		return rest_ensure_response( $response );
	}
}
