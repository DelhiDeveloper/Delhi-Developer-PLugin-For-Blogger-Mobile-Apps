<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;







/* Creating a new API End Point : User Registration */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v2', 
		'/top_level_categories', 
		array(
			array(
				'methods'=>'GET',
				'callback' => 'dd2_retreive_top_level_categories_json',
			)
		) 
	);
} );
function dd2_retreive_top_level_categories_json( $request ) {
	
	$categories = get_terms( 
		'category',
		array(
			'orderby' => 'name',
			'parent' => 0
		)
	);
	
	if( is_wp_error( $categories ) ) {
		$response = new stdClass();
		$response->type		= 'Success';
		$response->code		= 'No Top Level Categories Found';
		$response->message	= 'No top level categories found in the server.';
		$response->top_level_categories	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	$categories_array = array();
	foreach( $categories as $category ) {
		$categories_object = new stdClass();
		$categories_object->id = $category->term_id;
		$categories_object->name = $category->name;
		$categories_object->slug = $category->slug;
		array_push( $categories_array , $categories_object );
	}
	
	
	$response = new stdClass();
	$response->type		= 'Success';
	$response->code		= 'Top Level Categories Retreived';
	$response->message	= 'Top level categories have been retreived.';
	$response->top_level_categories	= $categories_array;
	return new WP_REST_Response( $response , 200 );
	
	
}


























?>