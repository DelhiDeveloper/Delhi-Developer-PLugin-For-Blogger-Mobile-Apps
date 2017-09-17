<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;



	
	
	
	
	/* Creating a new API End Point : User Registration */
	add_action( 'rest_api_init', function () {
		register_rest_route( 
			'delhideveloper/v1', 
			'/top_level_categories', 
			array(
				'methods' => 'POST',
				'callback' => 'dd_retreive_top_level_categories_json',
			) 
		);
	} );
	function dd_retreive_top_level_categories_json( $request ) {
		
		
		$categories = get_terms( 
			'category',
			array(
				'parent' => 0
			)
		);
		
		if( is_wp_error( $categories ) ) {
			$response = new stdClass();
			$response->code		= 'Success';
			$response->message	= 'No Top Level Categories Found';
			$response->top_level_categories	= array();
			return $response;
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
		$response->message	= 'Categories Retreived';
		$response->top_level_categories	= $categories_array;
		return $response;
		
		
	}
	
	
	
	























?>