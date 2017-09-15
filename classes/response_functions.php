<?php


use \Firebase\JWT\JWT;

	
	
	


/**********************************************************************************
					USER TOKEN CLASSES
**********************************************************************************/

function create_user_token( $user ) {
	
	$token_array = array(
		'jti'  => base64_encode(mcrypt_create_iv(32)),	// Json Token Id: an unique identifier for the token
		'iss'  => WEBSITE_NAME,					// Issuer
		'iat'  => time(),						// Issued at: time when the token was generated
		'nbf'  => time(),// + 3,				// Not to be used before 3 Seconds : To stop robots
		'exp'  => time() + 31104000,			// Expire After An Year
		'aud'  => WEBSITE_NAME . ' Mobile App',	// Audiance
		'sub'  => 'User Identification',	// Subject
		'userdata' => array(							// Data related to the signer user
			'user_login'			=> $user->data->user_login,		// 
			'user_email'			=> $user->data->user_email,		// 
			'display_name'			=> $user->data->display_name,	// 
			'user_nicename'			=> $user->data->user_nicename,	// 
			'user_url'				=> $user->data->user_url		// 
		)
	);
	
	return JWT::encode( $token_array , JWT_PRIVATE_KEY , 'RS256');
	
}


/**********************************************************************************
					/USER TOKEN CLASSES
**********************************************************************************/



	
	
	
	
	













?>