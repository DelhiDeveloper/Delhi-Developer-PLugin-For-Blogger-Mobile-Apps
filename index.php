<?php
    /*
    Plugin Name: Delhi Developer Blog Mobile API
    Plugin URI: https://delhideveloper.com/
    Description: A plugin to support the mobile apps made by delhi developer for Wordpress Bloggers.
    Version: 1.0
    Author: Delhi Developer
    Author URI: https://delhideveloper.com/
    License: Propriety
    */
	
	

	
	/* Securing Plugin From Direct Access through the URL Path */
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	/* Including Composer PHP Libraries */
	include dirname(__FILE__) . '/php_libraries/composer/vendor/autoload.php';
	
	
	/* Including Config Files */
	include dirname(__FILE__) . '/config/defined_constants.php'; // Global Defined Constants
	include dirname(__FILE__) . '/config/jwt_keys.php'; // JWT Public & Private Keys
	
	
	/* Including Plugin Activation Code */
	include dirname(__FILE__) . '/includes/plugin_activation_code.php';
	register_activation_hook( __FILE__, 'dd_mobile_app_plugin_activation_function' );
	register_activation_hook( __FILE__, 'dd_mobile_app_create_database_tables' );
	
	/* Important includes */
	include dirname(__FILE__) . '/includes/important_hooks.php';
	
	/* Including Request & Response Classes */
	include dirname(__FILE__) . '/classes/request_functions.php';
	include dirname(__FILE__) . '/classes/response_functions.php';
	
	/* Important Classes */
	include dirname(__FILE__) . '/classes/google_api_class.php';
	
	
	/* Including Useful Functions */
	include dirname(__FILE__) . '/useful_functions/dd_retrieve_password_email.php';
	include dirname(__FILE__) . '/useful_functions/dd_get_mobile_app_user_password_reset_key.php';
	include dirname(__FILE__) . '/useful_functions/debugging_functions.php';
	
	
	/*******************************
		Commenting this for now solved a major issue of 503 errors returned by the api
	****************************************/
	/* Setting The Header if the request is for REST API */
	// Found this better way here:-
	// https://joshpress.net/access-control-headers-for-the-wordpress-rest-api/
	
	add_action( 'rest_api_init', function() {
		remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
		add_filter( 'rest_pre_serve_request', function( $value ) {
			header( 'Access-Control-Allow-Origin: *' );
			header( 'access-control-allow-origin: *' ); //
			header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
			header( 'Access-Control-Allow-Credentials: true' );
			header( 'content-type: application/json; charset=utf-8' ); //
			return $value;			
		});
	}, 15 );
	/**/
	
	
	
	
	/* Admin Panel Menu & Pages Code */
	include dirname(__FILE__) . '/admin/admin_menus.php';
	
	
	
    /* Custom API V1 Includes */
	include dirname(__FILE__) . '/api/youtube_api.php';
	include dirname(__FILE__) . '/api/posts_api.php';
	include dirname(__FILE__) . '/api/user_api.php';
	include dirname(__FILE__) . '/api/messages_api.php';
	include dirname(__FILE__) . '/api/category_api.php';
	include dirname(__FILE__) . '/api/content_api.php';
	/**/
	
	
	
    /* Custom API V2 Includes */
	include dirname(__FILE__) . '/api_v2/dd_v2_check_mobile_app_user_token.php';
	include dirname(__FILE__) . '/api_v2/user_api.php';
	include dirname(__FILE__) . '/api_v2/posts_api.php'; //
	include dirname(__FILE__) . '/api_v2/messages_api.php'; //
	include dirname(__FILE__) . '/api_v2/youtube_api.php';
	include dirname(__FILE__) . '/api_v2/category_api.php';
	include dirname(__FILE__) . '/api_v2/content_api.php';
    /**/
	
	
	
	
	
	
	
	/* Custom API Includes */
	include dirname(__FILE__) . '/just_testing_some_code.php';
	
	
	
    
?>