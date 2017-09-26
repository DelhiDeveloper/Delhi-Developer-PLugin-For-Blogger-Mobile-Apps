<?php







// Using The Template Created For "mobile_app_user_verification_page" Page
add_filter( 'template_include', 'dd_apply_template_youtube_video_player_page' );
function dd_apply_template_youtube_video_player_page( $template ) {
	
	// Getting Mobile User Verification Page Slug
	$dd_mobile_app_important_pages = json_decode( get_option( 'dd_mobile_app_important_pages' ) );
	if( 
			! $dd_mobile_app_important_pages
		||	$dd_mobile_app_important_pages->dd_youtube_video_player_page == ''
	) {
		// If no page has been assigned to the template
		$mobile_app_user_verification_page_slug = 'dd_youtube_video_player_page';
	} else {
		// If a page has been assigned to the template in plugin settings
		$dd_youtube_video_player_page = get_post( $dd_mobile_app_important_pages->dd_youtube_video_player_page );
		$dd_youtube_video_player_page_slug = $dd_youtube_video_player_page->post_name;
	}
	
	// Initializing template name and path
	$file_name	= 'dd_youtube_video_player_page.php';
	$file_url	= dirname( __FILE__ ) . '/../templates/' . $file_name;
	
	// If current page request is for the template's page's slug
	if ( is_page( $dd_youtube_video_player_page_slug ) ) {
		// Look for the template in the theme folder
		if ( locate_template( $file_name ) ) {
			// Use theme's version of the template
			$template = locate_template( $file_name );
		} else {
			// Template not found in theme's folder, use plugin's template as a fallback
			$template = $file_url;
		}
	}
	
	return $template;
	
}































?>