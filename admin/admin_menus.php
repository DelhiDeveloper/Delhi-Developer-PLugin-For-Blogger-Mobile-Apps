<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;







/* Adding Menus */
add_action( 'admin_menu', 'dd_mobile_app_admin_menu' );
function dd_mobile_app_admin_menu() {
	
	/* Main Page */
	add_menu_page( 
		'Delhi Developer Mobile App Settings Page', // Page Title
		'Delhi Developer Mobile App', 				// Menu Title
		'manage_options', 							// User Capability Required
		'dd_mobile_app_admin_main_page',			// Slug 
		'dd_mobile_app_admin_main_page',			// Function
		'dashicons-smartphone', 					// Icon
		2 											// Position
	);
	
	/* Chats List Page */
	add_submenu_page( 
		'dd_mobile_app_admin_main_page', 				// Parent Slug
		'Delhi Developer Mobile App Author Chats Page', // Page Title
		'Mobile App Author Chats', 						// Menu Title
		'manage_options', 								// User Capability Required 
		'dd_mobile_app_admin_chats_list_page',  		// Slug
		'dd_mobile_app_admin_chats_list_page' 			// Function
	);
	
	/* Chat Single Page */
	add_submenu_page( 
		NULL, // Parent Slug is NULL : Because this page will not be in the Admin Menu
		'Delhi Developer Mobile App Single Chat Page', // Page Title
		'Mobile App Author Chats', 						// Menu Title
		'manage_options', 								// User Capability Required 
		'dd_mobile_app_admin_chat_single_page',  		// Slug
		'dd_mobile_app_admin_chat_single_page' 			// Function
	);
	
}
/* Including Pages */
include dirname(__FILE__) . '/pages/dd_mobile_app_admin_main_page.php';
include dirname(__FILE__) . '/pages/dd_mobile_app_admin_chats_list_page.php';
include dirname(__FILE__) . '/pages/dd_mobile_app_admin_chat_single_page.php';

















































?>