<?php

/**
 * Template Name: Delhi Developer Mobile App User Verification Page
**/



// Testing User Role
//print_r( get_user_by( 'login' , 'jasmeet') );exit;


/* Registering User for a "Mobile APP User Role" */
$user_found = true;
if(
		!empty( $_GET )
	&&	isset( $_GET['username'] )
	&&	isset( $_GET['user_activation_key'] )
) {
	
	$username = $_GET['username'];
	$user_activation_key = $_GET['user_activation_key'];
	
	/* Getting The User */
	$user = check_password_reset_key( $user_activation_key , $username );
	//print_r( $user );
	
	if( ! is_wp_error( $user ) ) {
		
		/* Updating User Type */
		/*
		wp_update_user( array (
			'ID'					=> $user->ID, 
			'role'					=> 'mobile_app_subscriber'
		));
		*/
		
		/* Set "mobile_app_user_verified" to true if already false */
		update_user_meta( 
			$user->ID, 
			'mobile_app_subscriber_verified', 
			1, // New value
			0 // Old value : to be checked before update
		);
		
		/* Resetting User Activation Key */
		$user_activation_key = get_mobile_app_user_password_reset_key( $user );

	} else {
		
		$user_found = false;
		
	}
	
	
	
	
} else {
	
	$user_found = false;
	
}



?>


<html>
	<head>
	</head>
	<body>
		
		<?php if( $user_found == true ) { ?>
			
			<h3 style="color: #29541D; text-align: center;">
				Congratulations <?php echo $user->user_login; ?>! Your email address <b>"<?php echo $user->user_email;?>"</b> has been verified and you have been registered as a "Mobile App User" on MyLittleMuffin. Please, login to your app now.
			</h3>
			
		<?php } else { ?>
			
			<h3 style="color: #E12D1E; text-align: center;">
				Sorry! This email verification link has either been expired, is invalid or has already been used. If you want to reset your password then please use the reset password form in the login section of the mobile app.
			</h3>
		
		<?php } ?>
		
	</body>
</html>