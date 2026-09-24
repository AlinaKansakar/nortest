<?php
/**
 * Profile template
 *
 * @package qobrix
 */
$current_user = wp_get_current_user();
$profile_picture = get_field('field_65b3726822c05', 'user_' . $current_user->ID);
?>
<form id="qobrix_profile_update_form" class="qobrix_login__form qobrix_form" method="post" autocomplete="off" enctype="multipart/form-data">
	<?php $qobrix_user_profile = wp_create_nonce('user_profile'); ?>
	<div class="form__element">
		<figure>
			<?php if($profile_picture): ?>
				<img src="<?php echo $profile_picture['sizes']['thumbnail']; ?>" alt="">
			<?php else: ?>
				<img src="<?php echo get_stylesheet_directory_uri() . '/includes/images/noimage.jpg'; ?>" alt="">
			<?php endif; ?>
		</figure>
		<div class="profile_picture_wrapper">
			<label for="profile_picture" class="form__label profile_picture_label"><?php echo esc_html('Upload Image', 'nortest'); ?></label>
			<div id="profile_picture_preview" class="profile_picture_preview"></div>
			<input type="file" id="profile_picture" name="profile_picture"  style="display: none;"/>
		</div>
	</div>
	<input type="hidden" name="nonce" value="<?php echo esc_attr($qobrix_user_profile, 'nortest'); ?>">
	<div class="form__element">
		<label for="first_name" class="form__label"><?php echo esc_html('First Name', 'nortest'); ?></label>
		<input type="text" id="first_name" class="form__control" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" placeholder="First Name" />
	</div>
	<div class="form__element">
		<label for="last_name" class="form__label"><?php echo esc_html('Last Name', 'nortest'); ?></label>
		<input type="text" id="last_name" class="form__control" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" placeholder="Last Name" />
	</div>
	<div class="form__element">
		<label for="user_email" class="form__label"><?php echo esc_html('Email Address', 'nortest'); ?></label>
		<input type="email" id="user_email" class="form__control" name="user_email" value="<?php echo esc_attr($current_user->user_email); ?>" placeholder="Email address" disabled />
	</div>
	<div class="form__element">
		<label for="user_company" class="form__label"><?php echo esc_html('Company Name', 'nortest'); ?></label>
		<input type="text" id="user_company" class="form__control" name="user_company" value="<?php echo esc_attr($current_user->company); ?>" disabled />
	</div>
	<div class="form__element">
		<label for="user_password" class="form__label"><?php echo esc_html('Password', 'nortest'); ?></label>
		<input type="password" id="user_password" class="form__control" name="password" placeholder="Password" />
	</div>
	<div class="form__element">
		<label for="user_confirm_password" class="form__label"><?php echo esc_html('Confirm Password', 'nortest'); ?></label>
		<input type="password" id="user_confirm_password" class="form__control" name="confirm_password" placeholder="Confirm Password" />
	</div>
	<button type="submit" class="btn qobrix_btn btn__fill btn__large form__btn">
		<span><?php echo esc_html('Update', 'nortest'); ?></span>
	</button>
	<div id="profile-error-msg" class="alert alert-danger" role="alert" style="display: none;">
	</div>
	<div id="profile-success-msg" class="alert alert-success" role="alert" style="display: none;">
	</div>
</form>
<div id="pleaseWaitDialog" class="qobrix-please-wait" style="display: none;">
	<div>
		<div class="lds-ring">
			<div class="loader"></div>
		</div>
	   <?php echo esc_html('Please wait'); ?>
	</div>
</div>
