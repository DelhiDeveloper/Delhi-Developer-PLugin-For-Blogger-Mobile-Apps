<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;


	
	



/* Creating a new API End Point : User Registration */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v1', 
		'/posts', 
		array(
			'methods' => 'POST',
			'callback' => 'dd_retreive_posts_json',
		) 
	);
} );
function dd_retreive_posts_json( $request ) {
	
	
	
	$posts_per_page = 10;
	$current_page = 1;
	
	$current_page	= $request->get_param("current_page");
	
	$posts = get_posts(
		array(
			'posts_per_page' => $posts_per_page,
			'offset'=> ($current_page-1) * $posts_per_page ,
		)
	);
	
	if( ! $posts ) {
		$response = new stdClass();
		$response->code		= 'Failure';
		$response->message	= 'No Posts Could Be Retreived!';
		$response->posts	= array();
		return $response;
	}
	
	$posts_array = array();
	foreach( $posts as $post ) {
		
		$posts_object = new stdClass();
		$posts_object->id = $post->ID;
		$posts_object->title = $post->post_title;
		$posts_object->thumbnail = get_the_post_thumbnail($post->ID);
		//$posts_object->url = 'https://prachyakarma.com/'. $post->post_name;
		//$posts_object->content = $post->post_content;
		//$posts_object->excerpt = mb_substr( $post->post_content , 0 , 100 , 'UTF-8' );
		//$posts_object->date = date( 'M j, Y' , strtotime($post->post_date) );
		
		array_push( $posts_array , $posts_object );
		
	}
	
	$response = new stdClass();
	$response->code		= 'Success';
	$response->message	= 'Posts Retreived';
	$response->posts	= $posts_array;
	return $response;
	
	
}




/* Creating a new API End Point : User Registration */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v1', 
		'/posts_by_category', 
		array(
			'methods' => 'POST',
			'callback' => 'dd_retreive_posts_by_category_json',
		) 
	);
} );
function dd_retreive_posts_by_category_json( $request ) {
	
	
	
	$posts_per_page = 10;
	$current_page = 1;
	
	$current_page	= $request->get_param("current_page");
	$category_id	= $request->get_param(category_id);
	
	$posts = get_posts(
		array(
			'posts_per_page' => $posts_per_page,
			'offset'=> ($current_page-1) * $posts_per_page ,
			'category' => $category_id
		)
	);
	
	if( ! $posts ) {
		$response = new stdClass();
		$response->code		= 'Failure';
		$response->message	= 'No Posts Could Be Retreived!';
		$response->posts	= array();
		return $response;
	}
	
	$posts_array = array();
	foreach( $posts as $post ) {
		
		$posts_object = new stdClass();
		$posts_object->id = $post->ID;
		$posts_object->title = $post->post_title;
		$posts_object->thumbnail = get_the_post_thumbnail($post->ID);
		//$posts_object->url = 'https://prachyakarma.com/'. $post->post_name;
		//$posts_object->content = $post->post_content;
		//$posts_object->excerpt = mb_substr( $post->post_content , 0 , 100 , 'UTF-8' );
		//$posts_object->date = date( 'M j, Y' , strtotime($post->post_date) );
		
		array_push( $posts_array , $posts_object );
		
	}
	
	$response = new stdClass();
	$response->code		= 'Success';
	$response->message	= 'Posts Retreived';
	$response->posts	= $posts_array;
	return $response;
	
	
}











/* Creating a new API End Point : User Registration */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v1', 
		'/post', 
		array(
			'methods' => 'POST',
			'callback' => 'dd_retreive_single_post_json',
		) 
	);
} );
function dd_retreive_single_post_json( $request ) {
	
	
	
	
	$comments_per_page = 10;
	$current_comment_page = 1;
	$current_comment_page	= $request->get_param("current_comment_page");
	
	
	$id	= $request->get_param("id");
	
	$post = get_post( $id );
	
	if( ! $post ) {
		$response = new stdClass();
		$response->code		= 'Failure';
		$response->message	= 'No such post could be found!';
		$response->post	= array();
		return $response;
	}
	
	$posts_object = new stdClass();
	$posts_object->id = $post->ID;
	$posts_object->title = $post->post_title;
	$posts_object->thumbnail = get_the_post_thumbnail($post->ID);
	$posts_object->url = 'https://prachyakarma.com/'. $post->post_name;
	$posts_object->content = do_shortcode( wpautop( $post->post_content ) );
	$posts_object->excerpt = mb_substr( $post->post_content , 0 , 100 , 'UTF-8' );
	$posts_object->date = date( 'M j, Y' , strtotime($post->post_date) );
	
	
	
	$posts_object->author_id = $post->post_author;
	$posts_object->author_image = get_avatar( get_the_author_meta( 'user_email' , $post->post_author ) );
	$posts_object->author_name =  get_the_author_meta( 'display_name' , $post->post_author );
	$posts_object->author_description =  get_the_author_meta( 'description' , $post->post_author );
	
	$comments = get_comments(array(
		'number'	=> '10',
		'offset'	=> ($current_comment_page-1) * $comments_per_page ,
		'post_id'	=> $id,
		'status'	=> 'approve' // Approved Comments Only
	));
	
	$comments_array = array();
	foreach( $comments as $comment ) {
		$comments_object = new stdClass();
		$comments_object->content = $comment->comment_content;
		$comments_object->author = $comment->comment_author;
		$comments_object->date = date( 'M j, Y' , strtotime($comment->comment_date) );
		array_push( $comments_array , $comments_object );
	}
	$posts_object->comments = $comments_array;
	
	
	$response = new stdClass();
	$response->code		= 'Success';
	$response->message	= 'Posts Retreived';
	$response->post	= $posts_object;
	return $response;
	
	
}





/* Creating a new API End Point : User Registration */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v1', 
		'/comments', 
		array(
			'methods' => 'POST',
			'callback' => 'dd_retreive_comments_json',
		) 
	);
} );
function dd_retreive_comments_json( $request ) {
	
	
	
	
	$comments_per_page = 10;
	$current_comment_page = 1;
	$current_comment_page	= $request->get_param("current_comment_page");
	
	$id	= $request->get_param("id");
	
	$post = get_post( $id );
	
	if( ! $post ) {
		$response = new stdClass();
		$response->code		= 'Failure';
		$response->message	= 'No such post could be found!';
		$response->post	= array();
		return $response;
	}
	
	$comments = get_comments(array(
		'number'	=> '10',
		'offset'	=> ($current_comment_page-1) * $comments_per_page ,
		'post_id'	=> $id,
		'status'	=> 'approve' // Approved Comments Only
	));
	
	$comments_array = array();
	foreach( $comments as $comment ) {
		$comments_object = new stdClass();
		$comments_object->content = $comment->comment_content;
		$comments_object->author = $comment->comment_author;
		$comments_object->date = date( 'M j, Y' , strtotime($comment->comment_date) );
		array_push( $comments_array , $comments_object );
	}
	
	
	$response = new stdClass();
	$response->code		= 'Success';
	$response->message	= 'Comments Retreived';
	$response->comments	= $comments_array;
	return $response;
	
	
}








/* Creating a new API End Point : User Registration */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v1', 
		'/comment_create', 
		array(
			'methods' => 'POST',
			'callback' => 'dd_create_a_comment',
		) 
	);
} );
function dd_create_a_comment( $request ) {
	
	
	/************************** TOKEN VERIFICATION **********************************/
	/* Check if token exists */
	$token	= $request->get_param("token");
	if( is_null( $token ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->message	= 'Token Not Sent';
		return $response;
	}
	/* Get User From Token */
	$user = get_user_object_from_token( $token );
	/* If Token Not Valid */
	if( !$user ) {
		
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->message	= 'Invalid Token';
		return $response;
		
	}
	/************************** /TOKEN VERIFICATION *********************************/
	
	
	$posts_per_page = 10;
	$current_page = 1;
	
	$comment	= $request->get_param("comment");
	$post_id	= $request->get_param("post_id");
	
	
	
	$commentdata = array(
		'comment_post_ID' => $post_id, // to which post the comment will show up
		'comment_author' => $user->data->display_name, // 'Another Someone', //fixed value - can be dynamic 
		'comment_author_email' => $user->data->user_email, //'someone@example.com', //fixed value - can be dynamic 
		'comment_author_url' => $user->data->user_url, // 'http://example.com', //fixed value - can be dynamic 
		'comment_content' => $comment, //  'Comment messsage...', //fixed value - can be dynamic 
		'comment_type' => '', //empty for regular comments, 'pingback' for pingbacks, 'trackback' for trackbacks
		'comment_parent' => 0, //0 if it's not a reply to another comment; if it's a reply, mention the parent comment ID here
		'user_id' => $user_id, //passing current user ID or any predefined as per the demand
	) ;
	
	
	
	/* Check if this ne comment is allowed */
	$comment_allowed = wp_allow_comment( $commentdata, true );
	
	
	/* if comment is allowed */
	if( is_wp_error( $comment_allowed ) ) {
		return $comment_allowed;
	}
	
	// Insert new comment and get the comment ID
	$comment_id = wp_new_comment( $commentdata );
	$comment = get_comment( $comment_id );
	/* Approve the new comment */
	wp_set_comment_status( $comment_id, 'approve' );
	//return $comment;
	
	$comment_object = new stdClass();
	$comment_object->author = $comment->comment_author;
	$comment_object->date = date( 'M j, Y' , strtotime($comment->comment_date) );
	$comment_object->content = $comment->comment_content;
	
	$response = new stdClass();
	$response->code		= 'Success';
	$response->message	= 'Comment created.';
	$response->comment	= $comment_object;
	return $response;
	
	
}
































?>