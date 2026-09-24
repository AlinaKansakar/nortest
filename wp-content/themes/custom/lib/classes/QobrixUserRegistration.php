<?php
/**
 * Class QobrixUserRegistration file.
 *
 * @package custom
 */

namespace QobrixClasses;

use QobrixClasses\Admin\QobrixAdminSettings;
use QobrixClasses\Admin\QobrixAdminSettingsGeneral;

/**
 * Class QobrixUserRegistration
 *
 * UserRegistration frontend implementation.
 */
class QobrixUserRegistration {

	/**
	 * User variable
	 *
	 * @var object $user User of instances
	 */
	private static $user;
	/**
	 * Data variable
	 *
	 * @var array $data form data of instances
	 */
	private static $data;
	/**
	 * Error variable
	 *
	 * @var object $errors errors of instances
	 */
	private static $errors;

	/**
	 * Initializes the user registration.
	 */
	public static function init() {
		static $wp_error;
		//phpcs:ignore
		self::$errors = isset( $wp_error ) ? $wp_error : ( $wp_error = new \WP_Error( null, null, null ) );
		add_role(
			'member',
			__( 'Member' ),
			[
				'read'       => true,
				'edit_posts' => false,
			]
		);


		add_action( 'init', [ get_called_class(), 'blockusers_init' ] );
		add_filter( 'show_admin_bar', [ get_called_class(), 'custom_admin_bar_function' ] );
		add_action( 'wp_ajax_user_login', [ get_called_class(), 'user_login' ] );
		add_action( 'wp_ajax_nopriv_user_login', [ get_called_class(), 'user_login' ] );

	}

	/**
	 * User registration function
	 *
	 * @param array $data  The contact form data.
	 *
	 * @return bool error status.
	 */
	public static function register_user( $data ) {
		$user_id   = null;
		$user_data = [
			'user_login'           => $data['email'],
			'user_email'           => $data['email'],
			'user_pass'            => $data['password'],
			'confirm_pass'         => $data['password'],
			'role'                 => 'customer',
			'show_admin_bar_front' => false,
		];
		$user_id   = wp_insert_user( $user_data );
		if ( ! is_wp_error( $user_id ) ) {
			update_user_meta( $user_id, 'first_name', $data['first-name'] );
			update_user_meta( $user_id, 'last_name', $data['last-name'] );
			wp_set_current_user( $user_id, $data['email'] );
			wp_set_auth_cookie( $user_id );
			return 0;
		} else {
			return 1;
		}
	}

	/**
	 * BLock member from backend
	 */
	public static function blockusers_init() {
		$wp_user = wp_get_current_user();
		if ( is_user_logged_in() ) {
			if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				if ( in_array( 'member', (array) $wp_user->roles ) ) {
					wp_redirect( home_url() );
					exit;
				}
			}
		}
	}

	/**
	 * Remove top bar for member
	 *
	 * @return bool error status.
	 */
	public static function custom_admin_bar_function() {
		$wp_user = wp_get_current_user();
		if ( is_user_logged_in() ) {
			if ( in_array( 'member', (array) $wp_user->roles ) ) {
				return false;
			} else {
				return true;
			}
		}

	}

	/**
	 * User Login function
	 */
	public static function user_login() {
		self::sanitize_input_fields( $_REQUEST['data'] );

		self::$user = get_user_by( 'email', self::$data['email'] );
		$errors = self::validation();
		if ( empty( $errors ) ) {

			$login = self::complete( self::$data['email'] );

			if ( $login ) {

				$redirect_url = get_site_url() . '/my-projects/';
				wp_send_json(
					[
						'status' => 'success',
						'redirectTo' => $redirect_url,
					]
				);
			} else {
				self::$errors->add( 'password-fieldc', __( 'Invalid email or password.' ) );
				$errors = self::$errors->get_error_codes();
				$error_with_code = [];
				foreach ( $errors as $error ) {
					$error_with_code[ $error ] = self::$errors->get_error_messages( $error );
				}

				wp_send_json(
					[
						'status' => 'error',
						'errors' => $error_with_code,
					]
				);
			}
		} else {

			$error_with_code = [];
			foreach ( $errors as $error ) {
				$error_with_code[ $error ] = self::$errors->get_error_messages( $error );
			}

			wp_send_json(
				[
					'status' => 'error',
					'errors' => $error_with_code,
				]
			);
		}
	}

	/**
	 * Sanitize input fields
	 *
	 * @param string $inputs input fields.
	 */
	private static function sanitize_input_fields( $inputs ) {
		parse_str( $inputs, $user_info );

		if ( ! wp_verify_nonce( $user_info['nonce'], 'user_login' ) ) {
			exit( 'Invalid request' );
		}

		self::$data = [
			'email' => sanitize_email( $user_info['email'] ),
			'password' => $user_info['password'],
		];
	}

	 /**
	  * Validation password
	  *
	  * @return array return validation result.
	  */
	private static function validation() {
		if ( ! self::$user ) {
			self::$errors->add( 'password-fieldc', esc_html( 'Invalid email or password.', 'qobrix-connect' ) );
		} elseif ( ! isset( self::$data['password'] ) || '' == self::$data['password'] ) {
			self::$errors->add( 'password-fieldc', esc_html( 'Invalid email or password.', 'qobrix-connect' ) );
		} elseif ( ! wp_check_password( self::$data['password'], self::$user->user_pass, self::$user->ID ) ) {
			self::$errors->add( 'password-fieldc', esc_html( 'Invalid username or password.', 'qobrix-connect' ) );
		}

		return self::$errors->get_error_codes();
	}

	/**
	 * Login functionality
	 *
	 * @return bool success or fail message.
	 */
	private static function complete() {
		if ( is_user_logged_in() ) {
			wp_logout();
		}

		$wp_user = get_user_by( 'email', self::$data['email'] );
		$user_id = $wp_user->ID;
		if ( ! is_wp_error( $user_id ) ) {
			wp_set_current_user( $user_id, self::$data['email'] );
			wp_set_auth_cookie( $user_id );
			return true;
		}

		return false;
	}
}
