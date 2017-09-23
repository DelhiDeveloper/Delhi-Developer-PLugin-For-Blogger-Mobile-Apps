<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;




	
/* Initialising Some Constants */
define( 'DD_WEBSITE_NAME'		, get_bloginfo('name') );
define( 'DD_WEBSITE_TAGLINE'	, get_bloginfo('description') );
define( 'DD_WEBSITE_URL'		, get_bloginfo('url') );
define( 'DD_FROM_EMAIL'		, get_bloginfo('admin_email') );
define( 'DD_MOBILE_APP_USER_VERIFICATION_PAGE_SLUG'	, 'mobile_app_user_verification' );














?>