<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;


	
	



/* Creating a new API End Point : Get All Messages */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v1', 
		'/content', 
		array(
			'methods' => 'POST',
			'callback' => 'dd_retreive_content_json',
			'permission_callback' => function() { return true; },
		) 
	);
} );
function dd_retreive_content_json( $request ) {
	
	$route	= $request->get_param("route");
	
	if( is_null( $route ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Route Not Specified';
		$response->message	= 'Route of the content has not been specified!';
		$response->posts	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	$route	= trim( $route , '/' );
	
	$website_url = DD_WEBSITE_SITEURL;
	$website_url = trim( $website_url , '/' );
	
	$url	= $website_url . '/' . $route;
	
	$post_id = url_to_postid( $url );
	
	//return $post_id;
	
	$post	= get_post( $post_id );
	
	$content = do_shortcode( wpautop( $post->post_content ) );
	
	
	
	
	if( ! $post_id ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Not Found';
		$response->message	= 'No content Could Be Retreived!';
		$response->posts	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	
	$response = new stdClass();
	$response->type		= 'Success';
	$response->code		= 'Content Retreived';
	$response->message	= 'Content Retreived';
	$response->content	= $content;
	return new WP_REST_Response( $response , 200 );
	
	
}






























?>