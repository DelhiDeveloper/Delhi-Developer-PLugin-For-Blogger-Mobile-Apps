<?php
/*
	* Template Name: Delhi Developer CRON Updates From APIs
	*/


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;






/*********************************************************************************************************************
*************************** Youtube Channel Videos *********************************************
*********************************************************************************************************************/


$dd_mobile_app_youtube_channel_settings = json_decode(get_option('dd_mobile_app_youtube_channel_settings'));

if( 	$dd_mobile_app_youtube_channel_settings
	&&	$dd_mobile_app_youtube_channel_settings->enable_youtube_channel == 'Yes' 
) {
	
	// Getting Youtube Channel Videos using Google API
	$google_api = new GoogleAPI( $dd_mobile_app_youtube_channel_settings->youtube_api_key );

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























echo 'Code executed till End-of-File!'; exit;


?>