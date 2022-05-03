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
		$response_user    = array();

		try {
			if ( empty( $user_data ) ) {
				throw new \Exception( 'No user data.', 400 );
			}

			if ( empty( $user_data['email'] ) ) {
				throw new \Exception( 'No email in user data.', 400 );
			}

			$email = filter_var( $request['email'], FILTER_VALIDATE_EMAIL );

			if ( ! $email ) {
				$response_code = 406;
				throw new \Exception( 'Email not valid.', 406 );
			}

			$auth_controller = new \Stevenwett\WPFirebaseAuth\Auth( true, true );

			$firebase_user           = $auth_controller->create_firebase_user( $email );
			$potential_existing_user = false;
			$firebase_uid            = '';

			if ( $firebase_user && isset( $firebase_user->uid ) ) {
				$firebase_uid = $firebase_user->uid;
			}

			if ( '' === $firebase_uid ) {
				throw new \Exception( 'Could not get the firebase_uid to create the user.', 406 );
			}


			$user = array(
				'firebase_uid' => $firebase_uid,
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
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
				)
			);

			if ( 1 !== (int) $insert_response ) {
				throw new \Exception( 'Did not create a new database user.', 400 );
			}

			$response_user    = $this->get_user( $firebase_uid );
			$response_code    = 200;
			$response_message = 'New user created.';
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

			if ( empty( $user_data['firebase_uid'] ) ) {
				throw new \Exception( 'No firebase_uid in user data.', 400 );
			}

			$firebase_uid = $user_data['firebase_uid'];
			$user         = array(
				'gmt_modified' => current_time( 'mysql', true ),
			);

			$format = array(
				'%s',
			);

			if ( isset( $user_data['email'] ) ) {
				$email = filter_var( $request['email'], FILTER_VALIDATE_EMAIL );

				if ( ! $email ) {
					$response_code = 406;
					throw new \Exception( 'Email not valid.', 406 );
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
					'firebase_uid' => $firebase_uid,
				),
				$format,
				array(
					'%s',
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
}
