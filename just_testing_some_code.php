<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;







/*

// Code For Testing JWT Tokens

use \Firebase\JWT\JWT;
echo JWT::decode( 
	JWT::encode( 
		'Testing Firebase JWT Successful' , 
		JWT_PRIVATE_KEY , 
		'RS256'
	) , 
	JWT_PUBLIC_KEY , 
	array('RS256')
);
exit;
*/





/*

// Code For Testing JWT Tokens

$r = new MobileAppRequest();
$token = $r->get_customer_token();
halt($r->customer_token_valid());



$r = new MobileAppResponse();
$r->create_customer_token( 'jasmeet' , 'kkhkhkhk' );

echo $r->array['token']; exit;


*/
























?>