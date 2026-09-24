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
		<input type="password" id="loginPassword" class="form__control" placeholder="Password"
			name="password" />
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
