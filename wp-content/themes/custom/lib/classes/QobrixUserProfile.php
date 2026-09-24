<?php
/**
 * Class QobrixUserProfile file.
 *
 * @package custom
 */

namespace QobrixClasses;

use QobrixClasses\Admin\QobrixAdminSettings;
use QobrixClasses\Admin\QobrixAdminSettingsGeneral;

/**
 * Class QobrixUserProfile
 *
 * UserRegistration frontend implementation.
 */
class QobrixUserProfile {

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
	 * Initializes the user profile.
	 */
	public static function init() {
		static $wp_error;
		//phpcs:ignore
		self::$errors = isset( $wp_error ) ? $wp_error : ( $wp_error = new \WP_Error( null, null, null ) );

		add_action( 'wp_ajax_user_profile', [ get_called_class(), 'user_profile' ] );
		add_action( 'wp_ajax_nopriv_user_profile', [ get_called_class(), 'user_profile' ] );

	}

	/**
	 * User profile update function
	 *
	 * @param array $data  The contact form data.
	 *
	 * @return bool error status.
	 */
	public static function user_profile() {
		self::sanitize_input_fields( $_REQUEST );
		self::$user = wp_get_current_user();
		$errors = self::validation();

		if ( empty( $errors ) ) {
			// Update user data
            $user_data = array(
                'ID' => self::$user->ID,
                'first_name' => self::$data['first_name'],
                'last_name' => self::$data['last_name'],
            );

            wp_update_user($user_data);

            // Update password if provided
            if (!empty(self::$data['password']) && !empty(self::$data['confirm_password'])) {
                wp_set_password(self::$data['password'], self::$user->ID);
            }

			// Handle profile picture upload
			if (!empty(self::$data['profile_picture']['tmp_name'])) {
				require_once ABSPATH . 'wp-admin/includes/image.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';
		
				$attachment_id = media_handle_upload('profile_picture', 0);
		
				if (!is_wp_error($attachment_id)) {
					// Update user meta with attachment ID
					update_user_meta(self::$user->ID, 'profile_picture', $attachment_id);
				}
			}

			$redirect_url = get_site_url() . '/my-profile/';
			wp_send_json(
				[
					'status' => 'success',
					'redirectTo' => $redirect_url,
				]
			);
		}

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

	/**
	 * Sanitize input fields
	 *
	 * @param string $inputs input fields.
	 */
	private static function sanitize_input_fields( $inputs ) {
		if ( ! wp_verify_nonce( $inputs['nonce'], 'user_profile' ) ) {
			exit( 'Invalid request' );
		}

		self::$data = [
			'first_name' => sanitize_text_field( $inputs['first_name'] ),
			'last_name' => sanitize_text_field( $inputs['last_name'] ),
			'profile_picture' => $_FILES['profile_picture'],
			'password' => sanitize_text_field( $inputs['password'] ),
			'confirm_password' => sanitize_text_field( $inputs['confirm_password'] ),
		];
	}

	 /**
	  * Validation
	  *
	  * @return array return validation result.
	  */
	private static function validation() {
		if (empty(self::$data['first_name']) || empty(self::$data['last_name'])) {
			self::$errors->add( 'full_name', esc_html( 'First and last name are required.', 'qobrix-connect' ) );
		}

		if (!empty(self::$data['password']) && self::$data['password'] !== self::$data['confirm_password']) {
			self::$errors->add( 'password_field', esc_html( 'Passwords do not match, handle accordingly.', 'qobrix-connect' ) );
		}

		return self::$errors->get_error_codes();
	}
}
