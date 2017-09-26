<?php



function dd_v2_check_mobile_app_user_token( $request ) {
	
	
	return true;
	
	/************************** TOKEN VERIFICATION **********************************/
	/* Check if token exists */
	$token	= $request->get_param("token");
	if( is_null( $token ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->message	= 'Token Not Sent';
		return new WP_REST_Response( $response , 200 );
	}
	/* Get User From Token */
	$user = get_user_object_from_token( $token );
	/* If Token Not Valid */
	if( !$user ) {
		
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->message	= 'Invalid Token';
		return new WP_REST_Response( $response , 200 );
		
	}
	/************************** /TOKEN VERIFICATION *********************************/
	
	
	
}

























?>