<?php
/**
 * Recipepocket user controller
 *
 * @package Recipepocket
 * @author  Steven Wett <stevenwett@gmail.com>
 * @version 0.0.1
 */

namespace Recipepocket;

/**
 * User controller
 */
class User_Controller {
	/**
	 * Is User Signed In
	 *
	 * @var bool $is_authorized Is the user signed in?
	 */
	public $is_authorized = false;

	/**
	 * The current user record.
	 *
	 * @var mixed|object $user Current User Record.
	 */
	private $user = false;

	public function __construct( $is_authorized = false, $firebase_uid = false, $init = false ) {
		if ( true === $is_authorized ) {
			$this->is_authorized = $is_authorized;
		}

		if ( $firebase_uid ) {
			// Get user by firebase_id and partner_id.
			$this->user = $this->get_user( $firebase_uid );
		}

		if ( $init ) {
			add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
		}
	}

	public function current_user() {
		return $this->user;
	}

	/**
	 * Get user by firebase unique ID
	 *
	 * @param string $firebase_uid Firebase ID.
	 * @param bool   $only_active  Only include active users.
	 */
	public function get_user( $firebase_uid, $only_active = true ) {
		global $wpdb;

		if ( empty( $firebase_uid ) ) {
			return false;
		}

		$user = array();

		if ( $only_active ) {
			$user = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT * FROM recipepocket_users WHERE firebase_uid = %s AND active = 1',
					$firebase_uid
				),
				OBJECT
			);
		} else {
			$user = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT * FROM recipepocket_users WHERE firebase_uid = %s',
					$firebase_uid
				),
				OBJECT
			);
		}

		return $user;
	}

	/**
	 * Get user by database user ID
	 *
	 * @param int $user_id User ID.
	 * @param bool   $only_active  Only include active users.
	 */
	public function get_user_by_id( $user_id ) {
		global $wpdb;

		if ( empty( $user_id ) ) {
			return false;
		}

		$user = array();

		if ( $only_active ) {
			$user = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT * FROM recipepocket_users WHERE user_id = %s AND active = 1',
					$user_id
				)
			);
		} else {
			$user = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT * FROM recipepocket_users WHERE user_id = %s',
					$user_id
				)
			);
		}

		return $user;
	}

	/**
	 * Create user
	 *
	 * @param array $user_data User data.
	 * @throws \Exception error.
	 */
	public function create_user( $user_data ) {
		global $wpdb;

		$response_code    = 400;
		$response_message = '';

		try {
			if ( empty( $user_data ) ) {
				throw new \Exception( 'No user data.', 400 );
			}

			if ( empty( $user_data['email'] ) ) {
				throw new \Exception( 'No email in user data.', 400 );
			}

			$email = filter_var( $user_data['email'], FILTER_VALIDATE_EMAIL );

			if ( ! $email ) {
				$response_code = 406;
				throw new \Exception( 'Email not valid.', 406 );
			}

			$auth_controller = new \Stevenwett\WPFirebaseAuth\Auth();

			$firebase_user_response  = $auth_controller->create_firebase_user( $email );
			$firebase_user           = array();
			$firebase_uid            = '';

			if ( ! empty( $firebase_user_response['user'] ) ) {
				$firebase_user = $firebase_user_response['user'];
			}

			if ( isset( $firebase_user_response['code'] ) ) {
				$response_code = $firebase_user_response['code'];
			}

			if ( $firebase_user && isset( $firebase_user->uid ) ) {
				$firebase_uid = $firebase_user->uid;
			}

			if ( '' === $firebase_uid ) {
				throw new \Exception( 'Could not get the firebase_uid to create the user.', 406 );
			}

			$user = array(
				'firebase_uid' => $firebase_uid,
				'active'       => 1,
				'email'        => $email,
				'first_name'   => '',
				'last_name'    => '',
				'gmt_created'  => current_time( 'mysql', true ),
				'gmt_modified' => current_time( 'mysql', true ),
			);

			if ( isset( $user_data['first_name'] ) ) {
				$user['first_name'] = sanitize_text_field( $user_data['first_name'] );
			}

			if ( isset( $user_data['last_name'] ) ) {
				$user['last_name'] = sanitize_text_field( $user_data['last_name'] );
			}

			$insert_response = $wpdb->insert(
				'recipepocket_users',
				$user,
				array(
					'%s',
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
				)
			);


			if ( false === $insert_response ) {
				$response_code = 409;
				throw new \Exception( 'Could not add a new user. User may already exist.', 409 );
			}

			$response_code    = 200;
			$response_message = 'New user created.';
		} catch ( \Exception $e ) {
			// TODO: Log error.
			$response_message = $e->getMessage();
		}

		return array(
			'code'    => $response_code,
			'message' => $response_message,
		);
	}

	/**
	 * Update user
	 *
	 * @param array $user_data User data.
	 */
	public function update_user( $user_data ) {
		global $wpdb;

		$response_code    = 400;
		$response_message = '';
		$response_user    = array();

		try {
			if ( empty( $user_data ) ) {
				throw new \Exception( 'No user data.', 400 );
			}

			// var_dump($user_data);
			// die();
			if ( empty( $user_data['user_id'] ) ) {
				throw new \Exception( 'No user id.', 400 );
			}

			$user_id      = (int) $user_data['user_id'];
			$firebase_uid = $user_data['firebase_uid'];
			$user         = array(
				'gmt_modified' => current_time( 'mysql', true ),
			);

			$format = array(
				'%s',
			);

			if ( ! empty( $user_data['firebase_uid'] ) ) {
				$user['firebase_uid'] = sanitize_text_field( $user_data['firebase_uid'] );
				$format[]          = '%s';
			}

			if ( ! empty( $user_data['email'] ) ) {
				$email = filter_var( $user_data['email'], FILTER_VALIDATE_EMAIL );

				if ( ! $email ) {
					$response_code = 406;
					throw new \Exception( 'Email not valid.', 406 );
				}

				// TODO: Update firebase email.
				if ( empty( $user_data['firebase_uid'] ) ) {
					throw new \Exception( 'Need firebase_uid in order to change email.', 400 );
				}

				$auth_controller       = new \Stevenwett\WPFirebaseAuth\Auth();
				$update_firebase_email = $auth_controller->update_email( $email );

				die();

				if ( false === $update_firebase_email ) {
					throw new \Exception( 'Unable to update Firebase email.', 400 );
				}

				// Update firebase_uid from Firebase response.
				if ( isset( $user['firebase_uid'] ) && ! empty( $update_firebase_email->uid ) ) {
					$user['firebase_uid'] = $update_firebase_email->uid;
				}

				$user['email'] = $email;
				$format[]      = '%s';
			}

			if ( isset( $user_data['first_name'] ) ) {
				$user['first_name'] = sanitize_text_field( $user_data['first_name'] );
				$format[]           = '%s';
			}

			if ( isset( $user_data['last_name'] ) ) {
				$user['last_name'] = sanitize_text_field( $user_data['last_name'] );
				$format[]          = '%s';
			}

			$update_response = $wpdb->update(
				'recipepocket_users',
				$user,
				array(
					'id' => $user_id,
				),
				$format,
				array(
					'%d',
				)
			);

			if ( null === $update_response ) {
				$response_code = 409;
				throw new \Exception( 'Error updating user.', 409 );
			}

			$response_user    = $this->get_user( $firebase_uid );
			$response_message = 'Updated user.';

		} catch ( \Exception $e ) {
			// TODO: Log error.
			$response_message = $e->getMessage();
		}

		return array(
			'code'    => $response_code,
			'message' => $response_message,
			'user'    => $response_user,
		);
	}

	/**
	 * Delete user
	 *
	 * @param array $user_id User ID.
	 */
	public function delete_user( $user_data ) {
		global $wpdb;

		$response_code    = 400;
		$response_message = '';

		try {
			if ( empty( $user_data ) ) {
				throw new \Exception( 'No user data.', 400 );
			}

			if ( empty( $user_data['id'] ) ) {
				throw new \Exception( 'No id in user data.', 400 );
			}

			if ( empty( $user_data['firebase_uid'] ) ) {
				throw new \Exception( 'No firebase_uid in user data.', 400 );
			}

			$user_id      = (int) $user_data['id'];
			$firebase_uid = $user_data['firebase_uid'];

			$delete_response = $wpdb->delete(
				'recipepocket_users',
				array(
					'id'           => $user_id,
					'firebase_uid' => $firebase_uid,
				),
				array(
					'%d',
					'%s',
				)
			);

			if ( ! $delete_response ) {
				throw new \Exception( sprintf( 'Cannot delete user %d. Error with delete database query.', $user_id ), 400 );
			}

			$response_code    = 200;
			$response_message = 'User deleted';
		} catch ( \Exception $e ) {
			// TODO: Log error.
			$response_message = $e->getMessage();
		}

		return array(
			'code'    => $response_code,
			'message' => $response_message,
		);
	}

	/**
	 * Set the recipe to deactive
	 *
	 * @param array $user_id User ID.
	 */
	public function deactivate_user( $user_id ) {
	}

	/**
	 * Registering endpoints using the WordPress REST API
	 */
	public function register_endpoints() {
		// The endpoint for creating a user.
		register_rest_route(
			'recipepocket/v1',
			'/user',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'endpoint_callback_create_user' ),
				'permission_callback' => array( $this, 'endpoint_permissions_public' ),
			)
		);

		// The endpoint for updating a user.
		register_rest_route(
			'recipepocket/v1',
			'/user',
			array(
				'methods'             => 'PATCH',
				'callback'            => array( $this, 'endpoint_callback_update_user' ),
				'permission_callback' => array( $this, 'endpoint_permissions_public' ),
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
	 * REST API endpoing callback function for logging in a user
	 *
	 * @param \WP_REST_Request $request request object.
	 */
	public function endpoint_callback_create_user( \WP_REST_Request $request ) {
		$response_code    = 400;
		$response_message = '';

		try {
			if ( empty( $request['email'] ) ) {
				throw new \Exception( 'Email not in request.', 400 );
			}

			$user_data = array(
				'email'      => $request['email'],
				'first_name' => '',
				'last_name'  => '',
			);

			if ( isset( $request['first_name'] ) ) {
				$user_data['first_name'] = sanitize_text_field( $request['first_name'] );
			}

			if ( isset( $request['last_name'] ) ) {
				$user_data['last_name'] = sanitize_text_field($request['last_name'] );
			}

			// Create user.
			$create_response = $this->create_user( $user_data );

			$response_code    = $create_response['code'];
			$response_message = $create_response['message'];

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
			),
			$response_code
		);

		return rest_ensure_response( $response );
	}

	/**
	 * REST API endpoing callback function for logging in a user
	 *
	 * @param \WP_REST_Request $request request object.
	 */
	public function endpoint_callback_read_user( \WP_REST_Request $request ) {
	}

	/**
	 * REST API endpoing callback function for logging in a user
	 *
	 * @param \WP_REST_Request $request request object.
	 */
	public function endpoint_callback_update_user( \WP_REST_Request $request ) {
		$response_code    = 400;
		$response_message = '';
		$user             = array();

		try {
			if ( empty( $request['user_id'] ) ) {
				throw new \Exception( 'User ID not in request.', 400 );
			}

			// $user_id = (int) $request['user_id'];

			// Update user.
			$update_response = $this->update_user( $request );

			$response_code    = $update_response['code'];
			$response_message = $update_response['message'];
			$user             = $update_response['user'];

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
				'user'    => $user,
			),
			$response_code
		);

		return rest_ensure_response( $response );
	}
}
