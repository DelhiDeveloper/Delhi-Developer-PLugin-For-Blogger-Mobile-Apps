<?php






/* Code To Verify User as soon as the new password is saved (using a hook) */
add_action( 'password_reset', 'dd_mobile_app_subscriber_verify_on_password_reset', 10, 2 );
function dd_mobile_app_subscriber_verify_on_password_reset( $user, $new_pass ) {
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







/* User Email Verification GET Hook */
add_action(
	'admin_post_dd_mobile_app_user_email_verification',
	'dd_mobile_app_user_email_verification'
);
add_action( /* For logged out users also */
	'admin_post_nopriv_dd_mobile_app_user_email_verification',
	'dd_mobile_app_user_email_verification'
);
function dd_mobile_app_user_email_verification() {
	
	/* Registering User for a "Mobile APP User Role" */
	$user_found = true;
	if(
			!empty( $_GET )
		&&	isset( $_GET['username'] )
		&&	isset( $_GET['activation_key'] )
	) {
		
		$username = $_GET['username'];
		$activation_key = $_GET['activation_key'];
		
		/* Getting The User */
		$user = check_password_reset_key( $activation_key , $username );
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
			$activation_key = dd_get_mobile_app_user_password_reset_key( $user );

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
	<?php
	
}













/* Youtube Videos Fetching And storing Hook */
add_action(
	'admin_post_dd_youtube_videos_list_update',
	'dd_youtube_videos_list_update'
);
add_action( /* For logged out users also */
	'admin_post_nopriv_dd_youtube_videos_list_update',
	'dd_youtube_videos_list_update'
);
function dd_youtube_videos_list_update() {
	
	$dd_mobile_app_youtube_channel_settings = json_decode(get_option('dd_mobile_app_youtube_channel_settings'));

	if( 	$dd_mobile_app_youtube_channel_settings
		&&	$dd_mobile_app_youtube_channel_settings->enable_youtube_channel == 'Yes' 
	) {
		
		// Getting Youtube Channel Videos using Google API
		$google_api = new DDGoogleAPI( $dd_mobile_app_youtube_channel_settings->youtube_api_key );

		$videos = $google_api->getYoutubeChannelList( $dd_mobile_app_youtube_channel_settings->youtube_channel_id );
		
		// Making the $videos object similar to the one that has already been save using json_encode
		$videos = json_decode( json_encode( $videos	) );
		
		/* Comparing the old and new objects */
		if( $videos != json_decode( get_option( 'dd_mobile_app_youtube_videos' ) ) ) {
			/* Again encoding the object to save */
			$videos = json_encode( $videos );
			/* Saving the object */
			update_option(
				'dd_mobile_app_youtube_videos',
				$videos
			);

			echo 'New youtube Videos List Updated SUCCESSFULLY.<br />';	
			
		} else {
			
			echo 'No new youtube videos are available to be updated.<br />';
			
		}

		
		
	} else {
		
		echo 'Youtube videos list has NOT BEEN updated because youtube videos have not been enabled in the plugin settings.<br />';
		
	}

	echo 'Code executed without errors!'; exit;
	
	
}




















?>