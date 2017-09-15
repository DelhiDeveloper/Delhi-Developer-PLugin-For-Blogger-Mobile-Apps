<?php

/* get_mobile_app_user_password_reset_key() Function */

/* Had to make this custom function because the default get_password_reset_key() is not compatible with Brothersoft Recaptcha Plugin */

	
function get_mobile_app_user_password_reset_key( $user ) {
	
	global $wpdb;
	
	do_action( 'retreive_password', $user->user_login );

	
	$key = wp_generate_password( 20, false );
	
	do_action( 'retrieve_password_key', $user->user_login, $key );

	// Now insert the key, hashed, into the DB.
	if ( empty( $wp_hasher ) ) {
		require_once ABSPATH . WPINC . '/class-phpass.php';
		$wp_hasher = new PasswordHash( 8, true );
	}
	$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
	$key_saved = $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );
	if ( false === $key_saved ) {
		return new WP_Error( 'no_password_key_update', __( 'Could not save password reset key to database.' ) );
	}

	return $key;
	
}



?>