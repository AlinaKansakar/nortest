<?php
/**
 * Login template
 *
 * @package qobrix
 */

if ( ! is_user_logged_in() ) {
	$forget_password_url = get_home_url() . '/' . $atts['forget_password_slug'] . '/';
	?>
<form class="qobrix_login__form qobrix_form" action="#" id="qobrix_login_form" method="post" autocomplete="off">
	<?php $qobrix_user_login = wp_create_nonce( 'user_login' ); ?>
	<input type="hidden" name="nonce" value="<?php echo esc_attr( $qobrix_user_login, 'nortest' ); ?>">
	<div class="form__element">
		<label for="loginEmail" class="form__label"><?php echo esc_html( 'Email Address', 'nortest' ); ?></label>
		<input type="email" id="loginEmail" class="form__control" placeholder="Email address"
			name="email" />
	</div>
	<div class="form__element">
		<label for="loginPassword" class="form__label"><?php echo esc_html( 'Password', 'nortest' ); ?></label>
		<div class="form__password">
			<input type="password" id="loginPassword" class="form__control" placeholder="Password"
				name="password" />
			<button type="button" class="form__password-toggle" aria-controls="loginPassword" aria-label="<?php echo esc_attr__( 'Show password', 'nortest' ); ?>" aria-pressed="false">
				<svg class="form__password-icon form__password-icon--show" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
					<circle cx="12" cy="12" r="3"></circle>
				</svg>
				<svg class="form__password-icon form__password-icon--hide" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path>
					<path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"></path>
					<path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
					<line x1="1" y1="1" x2="23" y2="23"></line>
				</svg>
			</button>
		</div>
	</div>
	<button class="btn qobrix_btn btn__fill btn__large form__btn"><span><?php echo esc_html( 'Login', 'nortest' ); ?></span></button>
	<div class="alert alert-danger" role="alert" id="login-error-msg" style="display:none;">
	</div>
	<div class="alert alert-success" role="alert" id="login-success-msg" style="display:none;">
	</div>
</form>
<div id="pleaseWaitDialog" class="qobrix-please-wait" style="display: none;">
	<div>
		<div class="lds-ring">
			<div class="loader"></div>
		</div>
	   <?php echo esc_html( 'Please wait' ); ?>
	</div>
</div>
	<?php
} else {
	?>
	<h3 class="logged-in-wrapper" >
	   <?php
		$qobrix_current_user = wp_get_current_user();
		$message = 'You are logged in as ' . $qobrix_current_user->user_email;
		echo esc_html( $message );
		?>
	</h3>
	<?php
}
