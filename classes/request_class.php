<?php


use \Firebase\JWT\JWT;


class MobileAppRequest {
	
	public $array;
	
	public function __construct() {
		
		$this->array = $_POST;
		/*
		$_REQUEST_JSON = json_decode(file_get_contents('php://input'), true);
		if( $_REQUEST_JSON ) {
			$this->array = $_REQUEST_JSON;
		} else {
			$this->array = null;
		}
		*/
		
	}
	
	
	public function is_request_valid_json() {
		if( $this->array == null ) {
			return false;
		} else {
			return true;
		}
	}
	
	/* // Probably  Not Needed In This Project
	public function get_request_type() {
		if( ! $this->is_request_valid_json() ) {
			return false;
		}
		return $this->array['request_type'];
	}
	*/
	
	
	
	
	
	
	/**********************************************************************************
						USER TOKEN CLASSES
	**********************************************************************************/
	
	public function user_token_exists() {
		if( 
				isset( $this->array['token'] ) 
			&&	$this->array['token'] != ''
		) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get_user_token() {
		
		if( $this->user_token_exists() ) {
			
			try {
				$token = JWT::decode( $this->array['token'] , JWT_PUBLIC_KEY , array('RS256'));
				//echo json_encode( $token );
				return $token;				
			} catch( Exception $e ) {
				return false;
			}
			
		} else {
			return false;
		}
		
	}
	
	public function user_token_valid() {
		
		$user_token_valid = true;
		
		if( $this->user_token_exists() ) {
			
			try {
				$token = JWT::decode( $this->array['token'] , JWT_PUBLIC_KEY , array('RS256'));
				//echo json_encode( $token );
			} catch( Exception $e ) {
				$user_token_valid = false;
			}
			
		} else {
			$user_token_valid = false;
		}
		
		if( $user_token_valid == false ) {
			
			$response = new MobileAppResponse();
			
			$response->set_type(
				'error',
				'Invalid Token'
			);
			$response->send();
			
		}
		
		return( $user_token_valid );
		
	}
	
	public function get_user_object_from_token( $token ) { /////////////////////////////////////////////////
		
		if( $this->user_token_exists() ) {
			
			try {
				$token = JWT::decode( $token , JWT_PUBLIC_KEY , array('RS256'));
				//echo json_encode( $token );
				
				/* This Was So Simple! */
				return get_user_by( 'email', $token['userdata']['user_email'] );
				
			} catch( Exception $e ) {
				return false;
			}
			
		} else {
			return false;
		}
		
	}
	
	/**********************************************************************************
						/USER TOKEN CLASSES
	**********************************************************************************/
	
	
	
	
	
	
	
	
	
	
	
	
}


















?>