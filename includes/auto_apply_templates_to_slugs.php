<?php







/* Using The Template Created For "mobile_app_user_verification_page" Page */
add_filter( 'template_include', 'mobile_app_user_verification_page' );
function mobile_app_user_verification_page( $template ) {
	
	$file_name = 'mobile_app_user_verification_page.php';
	
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
	
	if ( is_page( $mobile_app_user_verification_page_slug ) ) {
		if ( locate_template( $file_name ) ) {
			$template = locate_template( $file_name );
		} else {
			// Template not found in theme's folder, use plugin's template as a fallback
			$template = dirname( __FILE__ ) . '/../templates/' . $file_name;
		}
	}
	
	return $template;
	
}








/* Using The Template Created For "mobile_app_cron_api_updates_page" Page */
add_filter( 'template_include', 'mobile_app_cron_api_updates_page' );
function mobile_app_cron_api_updates_page( $template ) {
	
	$file_name = 'mobile_app_cron_api_updates_page.php';
	
	/* Getting Mobile User Verification Page Slug */
	$dd_mobile_app_important_pages = json_decode( get_option( 'dd_mobile_app_important_pages' ) );
	if( 
			! $dd_mobile_app_important_pages
		||	$dd_mobile_app_important_pages->mobile_app_cron_api_updates_page == ''
	) {
		$mobile_app_user_verification_page_slug = 'mobile_app_cron_api_updates_page';
	} else {
		$mobile_app_cron_api_updates_page = get_post( $dd_mobile_app_important_pages->mobile_app_cron_api_updates_page );
		$mobile_app_user_verification_page_slug = $mobile_app_cron_api_updates_page->post_name;
	}
	
	if ( is_page( $mobile_app_user_verification_page_slug ) ) {
		if ( locate_template( $file_name ) ) {
			$template = locate_template( $file_name );
		} else {
			// Template not found in theme's folder, use plugin's template as a fallback
			$template = dirname( __FILE__ ) . '/../templates/' . $file_name;
		}
	}
	
	return $template;
	
}




























?>