<?php
/**
 * Class QobrixForgetPassword file.
 *
 * @package custom
 */

namespace QobrixClasses;

use QobrixClasses\Admin\QobrixAdminSettings;
use QobrixClasses\Admin\QobrixAdminSettingsGeneral;

/**
 * Class QobrixForgetPassword
 *
 * ForgetPassword  implementation.
 */
class QobrixForgetPassword {

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

		add_action( 'wp_ajax_forget_password', [ get_called_class(), 'forget_password' ] );
		add_action( 'wp_ajax_nopriv_forget_password', [ get_called_class(), 'forget_password' ] );
	}

	/**
	 * Forget password function
	 */
	public static function forget_password() {
		self::sanitize_input_fields( $_REQUEST['data'] );
		self::$user = get_user_by( 'email', self::$data['email'] );
		$errors     = self::validation();

		if ( empty( $errors ) ) {
			self::$user = self::$user->data;
			$success = self::complete();
			if ( $success ) {
				wp_send_json(
					[
						'status' => 'success',
					]
				);
			} else {
				wp_send_json(
					[
						'status' => 'error',
						'errors' => [ 'forget-password-email' => 'Something went wrong when sending email.' ],
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
		if ( ! wp_verify_nonce( $user_info['nonce'], 'forget_password' ) ) {
			exit( 'Invalid request' );
		}

		self::$data = [
			'email' => sanitize_email( $user_info['email'] ),
		];
	}

	/**
	 * Validate input fields
	 *
	 * @return array errors.
	 */
	private static function validation() {
		if ( ! self::$user ) {
			self::$errors->add( 'forget-password-email', __( 'Invalid Email Address.' ) );
		}

		return self::$errors->get_error_codes();
	}

	/**
	 * Generate new password and set it
	 *
	 * @return bool status.
	 */
	private static function complete() {
		$new_password = self::generate_password( 20 );
		wp_set_password( $new_password, self::$user->ID );
		$args = [ 'new_password' => $new_password ];
			$subject           = 'Reset password';
			$message           = qobrix_get_template_html(
				'forget-password-email-template',
				[
					'atts' => $args,
				]
			);

			$headers           = [ 'Content-Type: text/html; charset=UTF-8' ];
		return wp_mail( self::$data['email'], $subject, $message, $headers );
	}

	/**
	 * Generate password function
	 *
	 * @param int $length length of password.
	 *
	 * @return string password.
	 */
	private static function generate_password( $length = 20 ) {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' .
			'0123456789`-=~!@#$%^&*()_+,.<>?;:[]{}|';

		$str = '';
		$max = strlen( $chars ) - 1;

		for ( $i = 0; $i < $length; $i++ ) {
			$str .= $chars[ random_int( 0, $max ) ];
		}

		return $str;
	}

}
