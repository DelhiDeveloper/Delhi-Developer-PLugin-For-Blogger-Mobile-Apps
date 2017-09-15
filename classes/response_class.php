<?php


use \Firebase\JWT\JWT;

class MobileAppResponse {
	
	public $array;
	
	public function __construct() {
		$this->array = array();
	}
	
	
	/* // Probably Not Needed
	public function send() {
		
		completely_destroy_php_session();
		
		//echo json_encode( $this->array );
		echo json_encode( $this->array , JSON_PRETTY_PRINT ); // To Temporarily Beautify JSON Response
		exit;
		
	}
	*/
	
	
	public function set_type( $type, $description ) {
		
		$this->array['type'] = $type;
		$this->array['description'] = $description;
		
	}
	
	public function key_value( $key , $value ) {
		
		$this->array[$key] = $value;
		
	}
	
	
	
	
	/**********************************************************************************
						USER TOKEN CLASSES
	**********************************************************************************/
	
	public function create_user_token( $user ) {
		
		$token_array = array(
			'jti'  => base64_encode(mcrypt_create_iv(32)),	// Json Token Id: an unique identifier for the token
			'iss'  => WEBSITE_NAME,					// Issuer
			'iat'  => time(),						// Issued at: time when the token was generated
			'nbf'  => time(),// + 3,				// Not to be used before 3 Seconds : To stop robots
			'exp'  => time() + 31104000,			// Expire After An Year
			'aud'  => WEBSITE_NAME . ' Mobile App',	// Audiance
			'sub'  => 'User Identification',	// Subject
			'userdata' => array(							// Data related to the signer user
				'user_login'			=> $user->data->user_login,		// 
				'user_email'			=> $user->data->user_email,		// 
				'display_name'			=> $user->data->display_name,	// 
				'user_nicename'			=> $user->data->user_nicename,	// 
				'user_url'				=> $user->data->user_url		// 
			)
		);
		
		return $this->set_user_token( $token_array );
		
	}
	
	public function set_user_token( $token ) {
		
		$this->array['token'] = JWT::encode( $token , JWT_PRIVATE_KEY , 'RS256');
		return $this->array['token'];
	}
	
	
	/**********************************************************************************
						/USER TOKEN CLASSES
	**********************************************************************************/
	
	
	
	
	
	
	
	
	
	
	
	
	
}


















?>