<?php


/* // This is not needed anymore as it has been made a parameter of the constructor and property of class.
define( 
	'GOOGLE_API_KEY', 
	'AIzaSyDmk99JcHUPCUb2erC1yD9rBQXn0o9xQ0g'
);
*/


class GoogleAPI {
	
	public $google_api_key; // Google API Key
	
	function __construct( $google_api_key ) {
		$this->google_api_key = $google_api_key;
	}
	
	function getAPIJSON( $url , $parameters ) {
		
		
		// $url  = 'https://maps.googleapis.com/';
		// $url .= $uri;
		
		$first = true;
		foreach( $parameters as $key => $value ) {
			if( $first ) {
				$url .= '?';
				$first = false;
			} else {
				$url .= '&';
			}
			$url .= $key . '=' . $value;
		}
		$url .= '&key=' . $this->google_api_key;
		
		//$url = 'https://maps.googleapis.com/maps/api/timezone/json?location=39.6034810,-119.6822510&timestamp=1331766000&key=' . $this->google_api_key;
		
		$json = file_get_contents($url);
		
		
		return $json;
		
	}
	
	
	function getYoutubeChannelList( $channel_id ) {
		
		
		$url = 'https://www.googleapis.com/youtube/v3/search'
			. '?key=' . $this->google_api_key
			. '&part=snippet'
			. '&channelId=' . $channel_id
			. '&order=date'
			. '&maxResults=50'
			;
		
		$json = file_get_contents($url);
		$data = json_decode($json, true);
		
		$items = $data['items'];
		
		//halt($url);
		
		$videos = array();
		foreach( $items as $item ) {
			
			if( $item['id']['kind'] == 'youtube#video' ) {
				
				$description = file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=snippet"
					.	"&id=" . $item['id']['videoId']
					.	"&key=" . $this->google_api_key);
				$description = json_decode($description, true);
				if( isset($description['items'][0]) ) {
					$description = $description['items'][0]['snippet']['description'];
				}
				
				$item['description'] = $description;

				array_push( $videos , $item );
			}
		}
		
		//halt($videos);
		
		return($videos);
		
		
	}
	
	
	function getCitiesAutocomplete( $place ) {
		
		
		$url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?input='
			. $place
			. '&types=(cities)&key=' . $this->google_api_key;
		
		$json = file_get_contents($url);
		$array = json_decode($json, TRUE);
		
		$status			= $array['status'];
		$predictions	= $array['predictions'];
		
		//echo $array['status'] ;exit;
		$json = '';
		if($array['status']=="OK") {
			
			$json .= '[';
			$first = true;
			foreach( $predictions as $prediction ) {
				if (!$first) { $json .=  ','; } else { $first = false; }
				$json .= '{';
				$json .= ' "display" : ' . json_encode($prediction['description']);
				$json .= ' , "value" : ' . json_encode($prediction['description']);
				$json .= ' }';
			}
			$json .= ']';
			
		} else {
			
			$json .= json_encode( $status );
			
		}
		
		return $json;
		
	}
	
	
	
	
}
























?>