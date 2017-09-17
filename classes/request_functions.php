<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;


use \Firebase\JWT\JWT;




/**********************************************************************************
					USER TOKEN CLASSES
**********************************************************************************/


function get_user_object_from_token( $token ) { /////////////////////////////////////////////////
	
	try {
		$token_array = JWT::decode( $token , JWT_PUBLIC_KEY , array('RS256'));
		//echo json_encode( $token );
		
		/* This Was So Simple! */
		return get_user_by( 'email', $token_array->userdata->user_email );
		
	} catch( Exception $e ) {
		return false;
	}
	
}

/**********************************************************************************
					/USER TOKEN CLASSES
**********************************************************************************/



















?>