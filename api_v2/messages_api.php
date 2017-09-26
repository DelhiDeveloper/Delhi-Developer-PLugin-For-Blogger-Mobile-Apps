<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;








	



/* Creating a new API End Point : Get All Messages */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v2', 
		'/author_profile', 
		array(
			'methods' => 'GET',
			'callback' => 'dd2_retreive_author_profile_json',
		) 
	);
} );
function dd2_retreive_author_profile_json( $request ) {
	
	/* Getting Parameters */
	$email	= $request->get_param("email");
	
	if( is_null( $email ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Author Email Not Specified';
		$response->message	= 'Please specify an author email.';
		$response->author_profile	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	/* Getting Author Users */
	$author = get_user_by( 'email' , $email );
	
	if( ! $author ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'No Such Author';
		$response->message	= 'No such author with email address ' . $email . ' exists in our system.';
		$response->author_profile	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	/* Getting More Details */
	$dd_mobile_app_chat_authors = json_decode( get_option('dd_mobile_app_chat_authors') );
	if( in_array( $author->ID , $dd_mobile_app_chat_authors ) ) {
		$author->allowed_chat = true;
	} else {
		$author->allowed_chat = false;
	}
	$author->description 		= get_user_meta($author->ID, 'description', true);
	$author->avatar 			= get_avatar($author->ID);
	$author->total_articles 	= count_user_posts($author->ID);
	
	
	$response = new stdClass();
	$response->type		= 'Success';
	$response->code		= 'Author Profile Retreived';
	$response->message	= 'Author Profile has been retreived.';
	$response->author_profile	= $author;
	return new WP_REST_Response( $response , 200 );
	
}



	



/* Creating a new API End Point : Get All Authors */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v2', 
		'/author_list', 
		array(
			'methods' => 'GET',
			'callback' => 'dd2_retreive_authors_json',
		) 
	);
} );
function dd2_retreive_authors_json( $request ) {
	
	$dd_mobile_app_chat_authors = json_decode( get_option('dd_mobile_app_chat_authors') );
	
	/* Getting Author Users */
	$author_users = get_users(
		array(
			'role__in'	=> array(
				'administrator',
				'editor',
				'author',
				'contributor',
			),
			'orderby '	=> 'post_count',
			'order'	=> 'ASC',
		)
	);
	
	/* Recreating the array of objects */
	$authors_array = array();
	foreach( $author_users as $author_user ) {
		
		$author_object = new stdClass();
		$author_object->email 			= $author_user->data->user_email;
		$author_object->display_name 	= $author_user->data->display_name;
		$author_object->url 			= $author_user->data->user_url;
		$author_object->nicename 		= $author_user->data->user_nicename;
		$author_object->description 	= get_user_meta($author_user->ID, 'description', true);
		$author_object->avatar 			= get_avatar($author_user->ID);
		
		$author_object->total_articles 	= count_user_posts($author_user->ID);
		
		if( in_array( $author_user->ID , $dd_mobile_app_chat_authors ) ) {
			$author_object->allowed_chat = true;
		} else {
			$author_object->allowed_chat = false;
		}
		
		array_push( $authors_array , $author_object );
		
	}
	
	$response = new stdClass();
	$response->type		= 'Success';
	$response->code		= 'Chat Authors Retreived';
	$response->message	= count($authors_array) . ' authors allowed for chatting have been retreived.';
	$response->author_list	= $authors_array;
	return new WP_REST_Response( $response , 200 );
	
}





	



/* Creating a new API End Point : Get Only Authors That Are Allowed Chatting */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v2', 
		'/chat_authors', 
		array(
			'methods' => 'GET',
			'callback' => 'dd2_retreive_chat_authors_json',
		) 
	);
} );
function dd2_retreive_chat_authors_json( $request ) {
	
	$dd_mobile_app_chat_authors = json_decode( get_option('dd_mobile_app_chat_authors') );
	
	if(
			! $dd_mobile_app_chat_authors
		||	empty( $dd_mobile_app_chat_authors )
	) {
		$response = new stdClass();
		$response->type			= 'Failure';
		$response->code			= 'No Authors Set';
		$response->message		= 'No authors have been selected on the server for chatting!';
		$response->chat_authors	= array();
		return new WP_REST_Response( $response , 200 );		
	}
	
	$authors = array();
	foreach( $dd_mobile_app_chat_authors as $dd_mobile_app_chat_authors ) {
		$user = get_user_by( 'ID' , $dd_mobile_app_chat_authors );
		$author = new stdClass();
		$author->email 			= $user->data->user_email;
		$author->display_name 	= $user->data->display_name;
		$author->email 			= $user->data->user_email;
		$author->url 			= $user->data->user_url;
		$author->nicename 		= $user->data->user_nicename;
		$author->description 	= get_user_meta($user->ID, 'description', true);
		$author->avatar 		= get_avatar($user->ID);
		array_push( $authors , $author );
	}
	
	$response = new stdClass();
	$response->type		= 'Success';
	$response->code		= 'Chat Authors Retreived';
	$response->message	= count($authors) . ' authors allowed for chatting have been retreived.';
	$response->chat_authors	= $authors;
	return new WP_REST_Response( $response , 200 );
	
}










/* Creating a new API End Point : Create Message */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v2', 
		'/messages', 
		array(
			'methods' => 'GET',
			'callback' => 'dd2_retreive_messages_json',
		) 
	);
} );
function dd2_retreive_messages_json( $request ) {
	
	global $wpdb;
	
	/************************** TOKEN VERIFICATION **********************************/
	/* Check if token exists */
	$token	= $request->get_param("token");
	if( is_null( $token ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code	= 'Token Not Sent';
		$response->message	= 'Token has not been sent with the message.';
		return new WP_REST_Response( $response , 200 );
	}
	/* Get User From Token */
	$user = get_user_object_from_token( $token );
	/* If Token Not Valid */
	if( !$user ) {
		
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code	= 'Invalid Token';
		$response->message	= 'Token is not valid. Please, login to get a new token.';
		return new WP_REST_Response( $response , 200 );
	}
	/************************** /TOKEN VERIFICATION *********************************/
	
	
	$per_page = 10;
	$current_page = 1;
	$current_page	= ( 
		! is_null( $request->get_param("current_page") ) 
		? 
		$request->get_param("current_page")
		: 
		$current_page
	);
	$limit	= $per_page;
	$offset	= ($current_page-1)*$per_page;
	
	$author_email	= $request->get_param("author_email");
	if( is_null( $author_email ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Author Email Not Specified';
		$response->message	= 'Please specify an author email.';
		$response->author_profile	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	/* Getting author_user from email */
	$author_user = get_user_by( 'email' , $author_email );
	
	
	/* Getting Messages */
	$table_name = 'dd_mobile_app_chat_messages';
	$messages = $wpdb->get_results($wpdb->prepare(
		$sql = "
			SELECT	ID,
					title, 
					content,
					IF( receiver_user_id= {$user->ID} , 1 , 0 ) AS received,
					IF( sender_user_id	= {$user->ID} , 1 , 0 ) AS sent,
					IF( receiver_user_id= {$user->ID} AND is_read = 0 , 1 , 0 ) AS un_read,
					creation_time,
					UNIX_TIMESTAMP( creation_time ) AS unix_creation_timestamp
			FROM	{$wpdb->prefix}{$table_name}
			WHERE	(
							sender_user_id		= {$user->ID}
						AND	receiver_user_id	= {$author_user->ID}
					)
					OR
					(
							sender_user_id		= {$author_user->ID}
						AND	receiver_user_id	= {$user->ID}
					)
			ORDER BY 	ID DESC
			LIMIT		{$limit}
			OFFSET		{$offset}
		",
		array()
	));
	/* Note that in the third IF clause we are considering only received messages as unread, not the sent ones */
	
	
	foreach( $messages as &$message ) {
		$message->creation_time = date( 'c' , strtotime( $message->creation_time ) );
		$message->content = nl2br( $message->content );
	}
	
	/* Must Reverse The Order NOW because the order was DESC in SQL for using LIMIT OFFSET */
	$messages = array_reverse( $messages );
	
	if( empty($messages) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code	= 'No Messages';
		$response->message	= 'No previous messages have been found for chat with this author.';
		$response->messages	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	
	$response = new stdClass();
	$response->type		= 'Success';
	$response->code	= 'Messages Found';
	$response->message	= count($messages) . ' have been found of previous messages have been found for chat with this author.';
	$response->messages	= $messages;
	return new WP_REST_Response( $response , 200 );
	
	
	
}










/* Creating a new API End Point : Create Message */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v2', 
		'/new_messages', 
		array(
			'methods' => 'GET',
			'callback' => 'dd2_retreive_new_messages_json',
		) 
	);
} );
function dd2_retreive_new_messages_json( $request ) {
	
	global $wpdb;
	
	/************************** TOKEN VERIFICATION **********************************/
	/* Check if token exists */
	$token	= $request->get_param("token");
	if( is_null( $token ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code	= 'Token Not Sent';
		$response->message	= 'Token has not been sent with the message.';
		return new WP_REST_Response( $response , 200 );
	}
	/* Get User From Token */
	$user = get_user_object_from_token( $token );
	/* If Token Not Valid */
	if( !$user ) {
		
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code	= 'Invalid Token';
		$response->message	= 'Token is not valid. Please, login to get a new token.';
		return new WP_REST_Response( $response , 200 );
	}
	/************************** /TOKEN VERIFICATION *********************************/
	
	
	
	$author_email	= $request->get_param("author_email");
	if( is_null( $author_email ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Author Email Not Specified';
		$response->message	= 'Please specify an author email.';
		$response->author_profile	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	$last_message_id	= $request->get_param("last_message_id");
	if( is_null( $last_message_id ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Last Message ID Not Specified';
		$response->message	= 'Please specify ID of the last message sent/received.';
		$response->author_profile	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	/* Getting author_user from email */
	$author_user = get_user_by( 'email' , $author_email );
	
	
	/* Getting Messages */
	$table_name = 'dd_mobile_app_chat_messages';
	$messages = $wpdb->get_results($wpdb->prepare(
		$sql = "
			SELECT	ID,
					title, 
					content,
					IF( receiver_user_id= {$user->ID} , 1 , 0 ) AS received,
					IF( sender_user_id	= {$user->ID} , 1 , 0 ) AS sent,
					IF( receiver_user_id= {$user->ID} AND is_read = 0 , 1 , 0 ) AS un_read,
					creation_time,
					UNIX_TIMESTAMP( creation_time ) AS unix_creation_timestamp
			FROM	{$wpdb->prefix}{$table_name}
			WHERE	(
						(		sender_user_id		= {$user->ID}
							AND	receiver_user_id	= {$author_user->ID}
						)
						OR
						(
								sender_user_id		= {$author_user->ID}
							AND	receiver_user_id	= {$user->ID}
						)
					)
					AND		ID > {$last_message_id}
		",
		array()
	));
	/* Note that in the third IF clause we are considering only received messages as unread, not the sent ones */
	
	
	foreach( $messages as &$message ) {
		$message->creation_time = date( 'c' , strtotime( $message->creation_time ) );
		$message->content = nl2br( $message->content );
	}
	
	if( empty($messages) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code	= 'No Messages';
		$response->message	= 'No previous messages have been found for chat with this author.';
		$response->messages	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	
	$response = new stdClass();
	$response->type		= 'Success';
	$response->code	= 'Messages Found';
	$response->message	= count($messages) . ' have been found of previous messages have been found for chat with this author.';
	$response->messages	= $messages;
	return new WP_REST_Response( $response , 200 );
	
	
	
}



	

	
	
	
	
	
	


/* Creating a new API End Point : Get All Messages */
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'delhideveloper/v2', 
		'/message_create', 
		array(
			'methods' => 'POST',
			'callback' => 'dd2_create_a_message',
			'permission_callback' => function() { return true; },
		) 
	);
} );
function dd2_create_a_message( $request ) {
	
	global $wpdb;
	
	/************************** TOKEN VERIFICATION **********************************/
	/* Check if token exists */
	$token	= $request->get_param("token");
	if( is_null( $token ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code	= 'Token Not Sent';
		$response->message	= 'Token has not been sent with the message.';
		return new WP_REST_Response( $response , 200 );
	}
	/* Get User From Token */
	$user = get_user_object_from_token( $token );
	/* If Token Not Valid */
	if( !$user ) {
		
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code	= 'Invalid Token';
		$response->message	= 'Token is not valid. Please, login to get a new token.';
		return new WP_REST_Response( $response , 200 );
	}
	/************************** /TOKEN VERIFICATION *********************************/
	
	
	
	$author_email	= $request->get_param("author_email");
	if( is_null( $author_email ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Author Email Not Specified';
		$response->message	= 'Please specify an author email.';
		$response->author_profile	= array();
		return new WP_REST_Response( $response , 200 );
	}
	$title			= $request->get_param("title");
	if( is_null( $title ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Title Missing';
		$response->message	= 'Please specify title of the messsage.';
		$response->author_profile	= array();
		return new WP_REST_Response( $response , 200 );
	}
	$content		= $request->get_param("content");
	if( is_null( $content ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Content Missing';
		$response->message	= 'Please specify content of the messsage.';
		$response->author_profile	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	/* Getting author_user from email */
	$author_user = get_user_by( 'email' , $author_email );
	
	
	/* If no such author_user found */
	if( is_wp_error( $author_user ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Author User Not Found';
		$response->message	= 'No such author user found on the server!';
		$response->posts	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	
	/* If the email does not belong to an 'author' (or similar) user */
	if(
		count(
			array_intersect(
				$author_user->roles,
				array(
					'administrator',
					'editor',
					'author',
					'contributor',
				)
			)
		) == 0
	) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'User Is Not Author';
		$response->message	= 'Message is blocked as it is being sent to a non author user!';
		$response->posts	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	
	/* Checking if this author is allowed to Chat With Mobile App Users */
	$dd_mobile_app_chat_authors	= json_decode( get_option('dd_mobile_app_chat_authors') );
	if( ! in_array( $author_user->ID , $dd_mobile_app_chat_authors ) ) {
		$response = new stdClass();
		$response->type		= 'Failure';
		$response->code		= 'Author Not Allowed To Chat';
		$response->message	= 'This author has not been allowed on the server to chat with mobile app users!';
		$response->posts	= array();
		return new WP_REST_Response( $response , 200 );
	}
	
	
	/* Creating a Message */
	$table_name = 'dd_mobile_app_chat_messages';
	$wpdb->insert(
		"{$wpdb->prefix}{$table_name}",
		array(
			"sender_user_id"	=> $user->ID,
			"receiver_user_id"	=> $author_user->ID,
			"title"				=> $title,
			"content"			=> $content,
		)
	);
	
	$message_id = $wpdb->insert_id;
	
	$message = $wpdb->get_results(
		"
			SELECT	ID,
					title, 
					content,
					IF( receiver_user_id= {$user->ID} , 1 , 0 ) AS received,
					IF( sender_user_id	= {$user->ID} , 1 , 0 ) AS sent,
					IF( receiver_user_id= {$user->ID} AND is_read = 0 , 1 , 0 ) AS un_read,
					creation_time,
					UNIX_TIMESTAMP( creation_time ) AS unix_creation_timestamp
			FROM	{$wpdb->prefix}{$table_name}
			WHERE	ID = {$message_id}
		"
	);
	
	/* Sending a successful response */
	$response = new stdClass();
	$response->type			= 'Success';
	$response->code			= 'Message Sent';
	$response->message		= 'Message has been sent to the author "'. $author_user->data->display_name .'".';
	$response->message_details		= $message;
	return new WP_REST_Response( $response , 200 );
	
	
}






































?>