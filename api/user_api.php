<?php






/* Creating a new API End Point : User Registration */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v1', 
		'/register', 
		array(
			'methods' => 'POST',
			'callback' => 'register_new_mobile_app_user',
		) 
	);
} );
function register_new_mobile_app_user( $request ) {
	
	
	
	$username	= $request->get_param("username");
	$email		= $request->get_param("email");
	$password	= $request->get_param("password");
	
	if( $username == '' ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->message	= 'Must provide username.';
		return $response;
	}
	if( $email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->message	= 'Must provide a valid email.';
		return $response;
	}
	if( $password == '' ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->message	= 'Must provide a valid password.';
		return $response;
	}
	
	$user_id = wp_create_user( $username , $password , $email );
	
	if( ! is_wp_error( $user_id ) ) {
		
		/* Updating The User Role as "NONE" */ /* Will be updated to "mobile app user" once email has been verified. */
		wp_update_user( array (
			'ID'					=> $user_id, 
			'role'					=> 'mobile_app_subscriber'
		));
		
		/* Creating The User Activation Key */
		$user_activation_key = get_mobile_app_user_password_reset_key( 
			get_user_by( 'ID' , $user_id ) 
		);
		
		/* Set User as unverified */
		update_user_meta( 
			$user_id, 
			'mobile_app_subscriber_verified', 
			0
		);
		
		/* Getting Mobile User Verification Page Slug */
		$dd_mobile_app_important_pages = json_decode( get_option( 'dd_mobile_app_important_pages' ) );
		if( 
				! $dd_mobile_app_important_pages
			||	$dd_mobile_app_important_pages->mobile_app_user_verification_page == ''
		) {
			$mobile_app_user_verification_page_slug = 'mobile_app_user_verification_page';
		} else {
			$mobile_app_user_verification_page = get_post( $dd_mobile_app_important_pages->mobile_app_user_verification_page );
			$mobile_app_user_verification_page_slug = $mobile_app_user_verification_page->post_name;
		}
		
		/* Emailing the activation URL to the User */
		$to			= $email;
		$subject	= WEBSITE_NAME . ' Email Verification';
		$message	= '
			Welcome to '. WEBSITE_NAME .', '. WEBSITE_TAGLINE .'!
			Please, click on the link below to verify your email and complete the registration process:-
			' . WEBSITE_SITEURL . '/' . $mobile_app_user_verification_page_slug . '/'
			. '?username=' 				. $username
			. '&user_activation_key='	. $user_activation_key
			;
		wp_mail(
			$to,
			$subject,
			$message
		);
		
		/* Creating a Response */
		$response = new stdClass();
		$response->type		= 'Success';
		$response->message	= 'New user created.';
		$response->email	= $email; // To display which email address the verification email has been sent to
		
		/* Sending Response */
		//return get_userdata( $user_id );
		//return check_password_reset_key( $user_activation_key , $username );
		return $response;

	} else {
		
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->message	= $user_id->get_error_message();
		
		return $response;
		
	}
	
}











/* Creating a new API End Point : User Login */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v1', 
		'/login', 
		array(
			'methods' => 'POST',
			'callback' => 'login_mobile_app_user',
		) 
	);
} );
function login_mobile_app_user( $request ) {
	
	
	
	$username_email	= $request->get_param("username_email");
	$password		= $request->get_param("password");
	
	
	
	if( $username_email == '' ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Missing Username/Email';
		$response->message	= 'Must provide a valid Username/Email.';
		return $response;
	}
	if( $password == '' ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Missing Password';
		$response->message	= 'Must provide a valid password.';
		return $response;
	}
	
	/* Authenticate user */
	$user = wp_authenticate($username_email, $password);
	
	/** If the authentication fails return a error*/
	if (is_wp_error($user)) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Wrong Credentials';
		$response->message	= 'Wrong login credentials. Please try again!';
		return $response;
	}
	
	
	/* If user email not verified, Return Error. */
	if( 
			in_array( 'mobile_app_subscriber', (array) $user->roles ) 
		&&	get_user_meta( $user->ID , 'mobile_app_subscriber_verified' )[0] == 0
	) {
		$response				= new stdClass();
		$response->type			= 'Failure';
		$response->code			= 'mobile_app_subscriber_not_verified';
		$response->message		= "WARNING: User not verified. Please, check your email for a user verification link. If you don't find the email in your inbox, please check your spam folder. You can also reset password by tapping on 'Reset Password' button.";
		return $response;
	}
	
	
	/* Create a user token */
	$token = create_user_token( $user );
	
	
	
	/* Creating Userdata To Be Stored */
	$userdata = new stdClass();
	$userdata->token			= $token;
	$userdata->user_login		= $user->data->user_login;
	$userdata->user_email		= $user->data->user_email;
	$userdata->display_name		= $user->data->display_name;
	$userdata->user_nicename	= $user->data->user_nicename;
	$userdata->user_url 		= $user->data->user_url;
	$userdata->logged_in 		= true;
	
	
	$response = new stdClass();
	$response->type		= 'Success';
	$response->message	= 'Login Successful. Token Created.';
	$response->userdata	= $userdata; // User Specific Details
	
	return $response;
	
	
}












/* Creating a new API End Point : User Login */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v1', 
		'/validate', 
		array(
			'methods' => 'POST',
			'callback' => 'validate_mobile_app_user',
		) 
	);
} );
function validate_mobile_app_user( $request ) {
	
	
	/************************** TOKEN VERIFICATION **********************************/
	/* Check if token exists */
	$token	= $request->get_param("token");
	if( is_null( $token ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->message	= 'Token Not Sent';
		return $response;
	}
	/* Get User From Token */
	$user = get_user_object_from_token( $token );
	/* If Token Not Valid */
	if( !$user ) {
		
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->message	= 'Invalid Token';
		return $response;
		
	}
	/************************** /TOKEN VERIFICATION *********************************/
	
	
	$response = new stdClass();
	$response->type		= 'Success';
	$response->message	= 'Valid Token. User Information Retreived.';
	$response->userdata	= $user; // User Specific Details
	return $response;
	
}















/* Creating a new API End Point : Password Reset */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v1', 
		'/reset_password', 
		array(
			'methods' => 'POST',
			'callback' => 'send_mobile_app_user_password_reset_link',
		) 
	);
} );
function send_mobile_app_user_password_reset_link( $request ) {
	
	
	
	$username_email	= $request->get_param("username_email");
		
	$user = '';
	if ( filter_var( $username_email , FILTER_VALIDATE_EMAIL ) ) {
		// invalid emailaddress
		$user = get_user_by( 'email' , $username_email );
		$username_email_type = 'email';
	} else {
		$user = get_user_by( 'login' , $username_email );
		$username_email_type = 'username';
	}
	
	if ( ! $user ) {
		
		//return $send_password_reset_link;
		
		$response = new stdClass();
		$response->code		= 'Failure';
		$response->message	= 'No such account with '. $username_email_type .' "'. $username_email .'" exists in '. WEBSITE_NAME .'. Please, check you '. $username_email_type .' and try again!';
		return $response;
		
	}
	
	$send_password_reset_link = retrieve_password_email( sanitize_text_field( $username_email ) );
	
	if ( $send_password_reset_link ) {
		
		//return $send_password_reset_link;
		
		$response = new stdClass();
		$response->code		= 'Success';
		$response->message	= 'A new password reset link has been sent to your email address "'. $user->user_email .'". Please, check your email visit the link in your email to reset your password.';
		return $response;
		
	} else {
		
		//return "ERROR";
			
		$response = new stdClass();
		$response->code		= 'Failure';
		$response->message	= 'A password reset link could not be created to be sent to your email. Please, check you Username/Email and try again!';
		return $response;
		
	}
	
	
	
}





/* Code To Verify User as soon as the new password is saved (using a hook) */
add_action( 'password_reset', 'mobile_app_subscriber_verify_on_password_reset', 10, 2 );
function mobile_app_subscriber_verify_on_password_reset( $user, $new_pass ) {
	// Do something before password reset.
	
	if( 
			in_array( 'mobile_app_subscriber', (array) $user->roles ) 
		&&	get_user_meta( $user->ID , 'mobile_app_subscriber_verified' )[0] == 0
	) {
		// Set "mobile_app_user_verified" to true if already false
		update_user_meta( 
			$user->ID, 
			'mobile_app_subscriber_verified', 
			1, // New value
			0 // Old value : to be checked before update
		);
	}
}



















?>