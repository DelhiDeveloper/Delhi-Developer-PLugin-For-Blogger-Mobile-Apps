<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Sends Reset Pasword Email for Rest API */
/* Customized Version of Wordpress retrieve_password() function  */




function dd_retrieve_password_email( $user_login ) {
	
	$errors = new WP_Error();

	if ( ! $user_login ) {
		$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or email address.'));
	} elseif ( strpos( $user_login, '@' ) ) {
		$user_data = get_user_by( 'email', trim( wp_unslash( $user_login ) ) );
		if ( empty( $user_data ) )
			$errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
	} else {
		$login = trim( $user_login );
		$user_data = get_user_by('login', $login);
	}
	
	do_action( 'lostpassword_post', $errors );

	if ( $errors->get_error_code() )
		return $errors;

	if ( !$user_data ) {
		$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or email.'));
		return $errors;
	}
	
	// Redefining user_login ensures we return the right case in the email.
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	
	$key = dd_get_mobile_app_user_password_reset_key( $user_data );

	if ( is_wp_error( $key ) ) {
		return $key;
	}
	
	$password_reset_url = DD_WEBSITE_URL . "/wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login);
	$password_reset_link = '<a href="' . $password_reset_url . '">' . $password_reset_url . '</a>';
	
	$message = __('Someone has requested a password reset for the following account:') . "\r\n\r\n";
	$message .= network_home_url( '/' ) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
	$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
	$message .= $password_reset_url . "\r\n";

	if ( is_multisite() ) {
		$blogname = get_network()->site_name;
	} else {
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	}
	
	$title = sprintf( __('[%s] Password Reset'), $blogname );
	
	$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );
	
	$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
	
	if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) )
		return false;
	//wp_die( __('The email could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.') );

	return $password_reset_url;
	
}

/*

if (
	retrieve_password(
		sanitize_text_field( $user_login )
	)
) {
    echo "SUCCESS";
} else {
    echo "ERROR";
}
*/


















?>