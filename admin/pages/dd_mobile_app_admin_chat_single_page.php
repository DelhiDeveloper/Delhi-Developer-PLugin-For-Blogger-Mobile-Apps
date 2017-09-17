<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;




/************************************ AJAX REQUESTS RESPONSES *************************************/




add_action('wp_ajax_dd_mobile_app_admin_chat_single'			, 'dd_mobile_app_admin_chat_single');
add_action('wp_ajax_nopriv_dd_mobile_app_admin_chat_single'	, 'dd_mobile_app_admin_chat_single'); // Was not working in admin panel without this

function dd_mobile_app_admin_chat_single() {
	
	global $wpdb;
	
	/* Was sending back homepage content without this */
	header("Content-Type: application/json", true);
	
	/************************** TOKEN VERIFICATION **********************************/
	$token_validated = true;
	/* Check if token exists */
	$token	= $_POST['token'];
	if( is_null( $token ) ) {
		$token_validated = false;
	}
	/* Get User From Token */
	$user = get_user_object_from_token( $token );
	/* If Token Not Valid */
	if( !$user ) {
		$token_validated = false;
	}
	/************************** /TOKEN VERIFICATION *********************************/
	
	/* Empty Response */
	if( $token_validated == false ) {
		echo json_decode(array());
		exit;
	}
	
	/* Initializations */
	$chat_user_id = $_POST['chat_user_id'];
	$page_no = $_POST['page_no'];
	$limit	= 10;
	$offset = 0;
	$offset = ($page_no - 1) * $limit;
	
	/* Getting Messages */
	$table_name = 'dd_mobile_app_chat_messages';
	$Q1 = "
		SELECT	ID,
				title,
				content,
				IF( receiver_user_id= {$user->ID} , 1 , 0 ) AS received,
				IF( sender_user_id	= {$user->ID} , 1 , 0 ) AS sent,
				IF( receiver_user_id= {$user->ID} AND is_read = 0 , 1 , 0 ) AS un_read,
				creation_time
		From	{$wpdb->prefix}{$table_name}
		WHERE		( sender_user_id = {$user->ID} && receiver_user_id = {$chat_user_id} )
				||	( sender_user_id = {$chat_user_id} && receiver_user_id = {$user->ID} )
		ORDER BY ID DESC
		LIMIT	{$limit}
		OFFSET	{$offset}
	";
	
	$messages_list = $wpdb->get_results( $Q1 );
	
	/* Make All Messages Read If Un-Read */
	foreach( $messages_list as $message ) {
		if( $message->received == 1 && $message->un_read == 1 ) {
			$wpdb->update(
				"{$wpdb->prefix}{$table_name}",
				array( "is_read" => true ),
				array( "ID" => $message->ID )
			);	
		}
	}
	
	/* Used to test LIMIT and OFFSET */
	//$messages_list = $wpdb->get_results( "SELECT * FROM wp_dd_mobile_app_chat_messages LIMIT {$limit} OFFSET {$offset}" );
	
	/* Creating ANGULAR friendly DATES */
	foreach( $messages_list as &$message ) {
		$message->creation_time = date( 'c' , strtotime( $message->creation_time ) );
		$message->content = nl2br( $message->content );
	}
	
	/* Must Reverse The Order NOW because the order was DESC in SQL for using LIMIT OFFSET */
	$messages_list = array_reverse( $messages_list );
	
	echo json_encode( $messages_list );
	exit;
	
	
	// to create token
	// create_user_token( $user );
	
	
}




add_action('wp_ajax_dd_mobile_app_admin_chat_single_new_messages'			, 'dd_mobile_app_admin_chat_single_new_messages');
add_action('wp_ajax_nopriv_dd_mobile_app_admin_chat_single_new_messages'	, 'dd_mobile_app_admin_chat_single_new_messages'); // Was not working in admin panel without this

function dd_mobile_app_admin_chat_single_new_messages() {
	
	global $wpdb;
	
	/* Was sending back homepage content without this */
	header("Content-Type: application/json", true);
	
	/************************** TOKEN VERIFICATION **********************************/
	$token_validated = true;
	/* Check if token exists */
	$token	= $_POST['token'];
	if( is_null( $token ) ) {
		$token_validated = false;
	}
	/* Get User From Token */
	$user = get_user_object_from_token( $token );
	/* If Token Not Valid */
	if( !$user ) {
		$token_validated = false;
	}
	/************************** /TOKEN VERIFICATION *********************************/
	
	/* Empty Response */
	if( $token_validated == false ) {
		echo json_decode(array());
		exit;
	}
	
	/* Initializations */
	$chat_user_id = $_POST['chat_user_id'];
	$last_message_id = $_POST['last_message_id'];
	
	/* Getting Messages */
	$table_name = 'dd_mobile_app_chat_messages';
	$Q1 = "
		SELECT	ID,
				title,
				content,
				IF( receiver_user_id= {$user->ID} , 1 , 0 ) AS received,
				IF( sender_user_id	= {$user->ID} , 1 , 0 ) AS sent,
				IF( receiver_user_id= {$user->ID} AND is_read = 0 , 1 , 0 ) AS un_read,
				creation_time
		From	{$wpdb->prefix}{$table_name}
		WHERE	(
						( sender_user_id = {$user->ID} && receiver_user_id = {$chat_user_id} )
					||	( sender_user_id = {$chat_user_id} && receiver_user_id = {$user->ID} )
				)
				&&	ID > {$last_message_id}
				
		ORDER BY ID DESC
	";
	
	$messages_list = $wpdb->get_results( $Q1 );
	
	/* Make All Messages Read If Un-Read */
	foreach( $messages_list as $message ) {
		if( $message->received == 1 && $message->un_read == 1 ) {
			$wpdb->update(
				"{$wpdb->prefix}{$table_name}",
				array( "is_read"=> true ),
				array( "ID"=>$message->ID )
			);			
		}
	}
	
	/* Used to test LIMIT and OFFSET */
	//$messages_list = $wpdb->get_results( "SELECT * FROM wp_dd_mobile_app_chat_messages LIMIT {$limit} OFFSET {$offset}" );
	
	/* Creating ANGULAR friendly DATES */
	foreach( $messages_list as &$message ) {
		$message->creation_time = date( 'c' , strtotime( $message->creation_time ) );
		$message->content = nl2br( $message->content );
	}
	
	/* Must Reverse The Order NOW because the order was DESC in SQL for using LIMIT OFFSET */
	$messages_list = array_reverse( $messages_list );
	
	echo json_encode( $messages_list );
	exit;
	
	
	// to create token
	// create_user_token( $user );
	
	
}




add_action('wp_ajax_dd_mobile_app_admin_chat_single_create_message'			, 'dd_mobile_app_admin_chat_single_create_message');
add_action('wp_ajax_nopriv_dd_mobile_app_admin_chat_single_create_message'	, 'dd_mobile_app_admin_chat_single_create_message'); // Was not working in admin panel without this

function dd_mobile_app_admin_chat_single_create_message() {
	
	global $wpdb;
	
	/* Was sending back homepage content without this */
	header("Content-Type: application/json", true);
	
	/************************** TOKEN VERIFICATION **********************************/
	$token_validated = true;
	/* Check if token exists */
	$token	= $_POST['token'];
	if( is_null( $token ) ) {
		$token_validated = false;
	}
	/* Get User From Token */
	$user = get_user_object_from_token( $token );
	/* If Token Not Valid */
	if( !$user ) {
		$token_validated = false;
	}
	/************************** /TOKEN VERIFICATION *********************************/
	
	/* Empty Response */
	if( $token_validated == false ) {
		echo json_decode(array());
		exit;
	}
	
	/* Initializations */
	$chat_user_id = $_POST['chat_user_id'];
	$content = $_POST['content'];
	
	/* Getting Messages */
	$table_name = 'dd_mobile_app_chat_messages';
	
	$wpdb->insert(
		"{$wpdb->prefix}{$table_name}",
		array(
			"sender_user_id"	=> $user->data->ID,
			"receiver_user_id"	=> $chat_user_id,
			"title"	=> "",
			"content"	=> $content
		)
	);
	
	$message_id = $wpdb->insert_id;
	
	$Q1 = "
		SELECT	ID,
				title,
				content,
				IF( receiver_user_id= {$user->ID} , 1 , 0 ) AS received,
				IF( sender_user_id	= {$user->ID} , 1 , 0 ) AS sent,
				IF( receiver_user_id= {$user->ID} AND is_read = 0 , 1 , 0 ) AS un_read,
				creation_time
		From	{$wpdb->prefix}{$table_name}
		WHERE	ID = {$message_id}
	";
	
	$messages_list = $wpdb->get_results( $Q1 );
	
	/* Used to test LIMIT and OFFSET */
	//$messages_list = $wpdb->get_results( "SELECT * FROM wp_dd_mobile_app_chat_messages LIMIT {$limit} OFFSET {$offset}" );
	
	/* Creating ANGULAR friendly DATES */
	foreach( $messages_list as &$message ) {
		$message->creation_time = date( 'c' , strtotime( $message->creation_time ) );
		$message->content = nl2br( $message->content );
	}
	
	/* Must Reverse The Order NOW because the order was DESC in SQL for using LIMIT OFFSET */
	$messages_list = array_reverse( $messages_list );
	
	echo json_encode( $messages_list );
	exit;
	
	
	// to create token
	// create_user_token( $user );
	
	
}







/************************************ AJAX REQUESTS RESPONSES *************************************/











/* Main Page Function */
function dd_mobile_app_admin_chat_single_page() {

	global $wpdb;
	
	/************************************ RESTRICTING UNWELCOME USERS *************************************/
	
	$user = wp_get_current_user();
	
	if( ! $user  ) {
		echo 'Wrong user. You are not allowed to access this page.';
		exit;
	} 
	
	if(
		count(
			array_intersect(
				$user->roles,
				array(
					'administrator',
					'editor',
					'author',
					'contributor',
				)
			)
		) == 0
	) {
		
		echo 'You are not allowed to access this page as you are not an author.';
		exit;
		
	}
	
	
	$dd_mobile_app_chat_authors	= json_decode( get_option('dd_mobile_app_chat_authors') );
	if( ! in_array( $user->ID , $dd_mobile_app_chat_authors ) ) {
		
		echo 'Though you are an author by role. But chatting has not been enabled for you in the plugiin settings.';
		exit;
		
	}
	
	/************************************ /RESTRICTING UNWELCOME USERS ************************************/
	
	
	
	
	
	/************************************ HANDLE FORM SUBMISSIONS *************************************/
	
	
	
	/************************************ /HANDLE FORM SUBMISSIONS ************************************/
	
	
	/************************************* GET DATA FOR VIEW ******************************************/
	
	
	$chat_user_id = $_GET['chat_user_id'];
	$chat_user = get_user_by( 'ID' , $chat_user_id );
	
	
	
	
	
	?>
	
	
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	
	<style>
		
		.message_received {
			display: inline-block;
			float: left;
			border-color: #ccc;
		}
		.message_sent {
			display: inline-block;
			float: right;
			border-color: #84b2ff;
		}
		
	</style>
	
	<div class="col-md-12" ng-app="myApp" ng-controller="myCtrl">
	
		<h2>
			Delhi Developer Mobile App Chat
			<br />
			With <b><?php echo $chat_user->data->display_name; ?></b>
			<span ng-if="loading_old_messages" style="
				font-size: 14px;
			">
				( Refreshing...<i class="fa fa-refresh fa-spin"></i> )
			</span>
		</h2>
		
		
		
		<div class="alert alert-danger alert-dismissable" ng-if="messages.length == 0">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			Sorry, no messages were found.
		</div>
		
		
		
		<div class="col-md-12">
			
			<a class="btn btn-default"
				href="<?php echo DD_WEBSITE_SITEURL; ?>/wp-admin/admin.php?page=dd_mobile_app_admin_chats_list_page"
			>
				<i class="fa fa-arrow-left"></i>
				Go Back To Chats List
			</a>
			&nbsp;
			<button ng-click="refresh_messages_list()" class="btn btn-default">
				Refresh Messages
				<i class="fa fa-refresh"></i>
			</button>
			<span style="
				font-size: 14px;
			">
				( New messages are automatically checked for every 5 seconds. )
			</span>
			
			
			<div class="messages_container"
				style="
					height: 350px;
					width: 100%;
					position: relative;
					overflow-y: scroll;
					border: 2px #999 solid;
					border-radius: 10px 10px 0px 0px;
					padding: 20px 10px;
					text-align: center;
					margin-top: 20px;
				"
			>
				<div ng-if="show_load_more_button" style="padding-bottom: 10px;">
					<button ng-click="load_old_messages()" class="btn btn-default">
						<i class="fa fa-arrow-circle-down"></i>
						Load Older Messages
					</button>
				</div>
				
				<div ng-if="!show_load_more_button">
					<h5 style="color: #999; text-align: center;">
						You have reached the end of messages list.
					</h5>
				</div>
				
				<div class="col-md-12" ng-repeat="m in messages_list"
					style="
					"
				>
					<div
						style="
							width: 70%;
							margin: 10px;
							padding: 10px;
							border-width: 2px;
							border-style: solid;
							border-radius: 5px;
							display: inline-block;
							clear: both;
						"
						ng-class="{ 'message_received': m.received == 1 , 'message_sent' : m.sent == 1 }"
					>
						<div style="color: #ccc; text-align: right;">
							{{ m.creation_time | date : 'd-MMM-yyyy h:m a' }}
						</div>
						<!--div>
							<b>{{m.title}}</b>
						</div-->
						<div 
							style="
								text-align: justify;
							"
							ng-bind-html="m.content"
						>
						</div>
					</div>
					
				</div>
			</div>
			
			<div
				style="
					height: auto;
					width: 100%;
					position: relative;
					border: 2px #999 solid;
					border-top: none;
					border-radius: 0px 0px 10px 10px;
					padding-bottom: 0px;
				"
			>
				<!--input type="text" class="form-control" ng-model="title" placeholder /-->
				<textarea class="form-control" 
					placeholder="Type your message here... ( Use Shift+Enter For Multiple Lines )"
					ng-model="content"
					ng-keydown="textarea_enter_pressed($event)"
					style="
						border-radius: 0px;
					"
					rows="3"
				></textarea>
				<button class="btn btn-default"
					ng-click="send_message()"
					ng-disabled="sending_message"
					style="
						width: 100%; 
						background-color: #eee;
						border-radius: 0px 0px 5px 5px;
					"
				>
					{{ sending_message ? "Sending Message..." : "Send Message" }}
				</button>
			</div>
			
			
			

			
			
			
			
			
			
			
			
			
			
		</div>
		
		
	</div>
	
	
	
	
	
	
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-sanitize.min.js"></script>
	
	
	<script type="text/javascript">
	
	$j = jQuery.noConflict();
	
	var global_scope;
	
	var app = angular.module('myApp', ['ngSanitize']);
	app.controller('myCtrl', function( $scope , $http , $window ) {
		
		global_scope = $scope;
		
		$scope.messages_list = [];
		
		$scope.title = '';
		
		$scope.content = '';
		
		$scope.loading_old_messages = false;
		
		$scope.getting_new_meessages = false;
		
		$scope.sending_message = false;
		
		$scope.message_list_page = 1;
		
		$scope.show_load_more_button = true;
		
		
		
		
		
		
		/* This refreshes the loading_old_messages from PAGE (offset) 1 */
		$scope.refresh_messages_list = function(){
			
			$scope.message_list_page = 1;
			$scope.messages_list = [];
			$scope.load_old_messages();
			$scope.show_load_more_button = true;
		}
		
		/* This is used to load more results */
		$scope.load_old_messages = function(){
			
			if( $scope.loading_old_messages == true ) {
				return;
			}
			
			$scope.loading_old_messages = true;
			
			var previous_scrollHeight = $j('.messages_container')[0].scrollHeight;
			var previous_top = $j('.messages_container').offset().top;
			
			$http({
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				transformRequest: function(obj) {
					var str = [];
					for(var p in obj)
					str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
					return str.join("&");
				},
				method 	: "POST",
				url		: '<?php echo DD_WEBSITE_SITEURL; ?>/wp-admin/admin-ajax.php', // ajaxurl,
				data	: {
					'action': 'dd_mobile_app_admin_chat_single',
					'token': '<?php echo create_user_token( $user ); ?>',
					'chat_user_id' : '<?php echo $chat_user_id; ?>',
					'page_no': $scope.message_list_page
				},
				cache: false,
				dataType: 'json'
			}).then(function mySuccess(response) {
				
				console.log(response.data);
				console.log(JSON.stringify(response.data));
				
				$scope.messages_list = response.data.concat($scope.messages_list);
				$scope.loading_old_messages = false;
				
				/* if first time messages loaded */
				if( $scope.message_list_page == 1 ) {
					setTimeout(function(){
						var height = $j('.messages_container').outerHeight();
						var scrollHeight = $j('.messages_container')[0].scrollHeight;
						var max_scroll_possible = scrollHeight - height + 4; // For some reason 4 needs to be added | maybe because of the 2 pc border
						//alert(max_scroll_possible);
						$j('.messages_container').scrollTop( max_scroll_possible );
					}, 500);
					 /***********************************************************/
				
				/* If previous messages being loaded */
				} else if( $scope.message_list_page > 1 ) {
					setTimeout(function(){
						var height = $j('.messages_container').outerHeight();
						var scrollHeight = $j('.messages_container')[0].scrollHeight;
						var still_position = scrollHeight - previous_scrollHeight; // For some reason 4 needs to be added | maybe because of the 2 pc border
						//alert(previous_top);
						$j('.messages_container').scrollTop( still_position );
					});
				}
				
				$scope.message_list_page += 1;
				
				if( response.data.length < 10 ) {
					$scope.show_load_more_button = false;
				}
				
			}, function myError(response) {
				
				$scope.messages_list = [];
				$scope.loading_old_messages = false;
				
			});
			
		}
		/* calling once on page load */
		$scope.load_old_messages();
		
		
		
		
		
		
		/* New function to get only new messages every 15 seconds */
		$scope.check_for_new_messages = function() {
			
			if( $scope.getting_new_meessages == true ) {
				return;
			}
			//alert($scope.messages_list[ $scope.messages_list.length -1 ].ID);
			$scope.getting_new_meessages = true;
			
			$http({
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				transformRequest: function(obj) {
					var str = [];
					for(var p in obj)
					str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
					return str.join("&");
				},
				method 	: "POST",
				url		: '<?php echo DD_WEBSITE_SITEURL; ?>/wp-admin/admin-ajax.php', // ajaxurl,
				data	: {
					'action': 'dd_mobile_app_admin_chat_single_new_messages',
					'token': '<?php echo create_user_token( $user ); ?>',
					'chat_user_id' : '<?php echo $chat_user_id; ?>',
					'last_message_id': $scope.messages_list[ $scope.messages_list.length -1 ].ID
				},
				cache: false,
				dataType: 'json'
			}).then(function mySuccess(response) {
				
				console.log(response.data);
				console.log(JSON.stringify(response.data));
				
				$scope.messages_list = $scope.messages_list.concat( response.data );
				$scope.getting_new_meessages = false;
				
				setTimeout(function(){
					var height = $j('.messages_container').outerHeight();
					var scrollHeight = $j('.messages_container')[0].scrollHeight;
					var max_scroll_possible = scrollHeight - height + 4; // For some reason 4 needs to be added | maybe because of the 2 pc border
					//alert(max_scroll_possible);
					$j('.messages_container').scrollTop( max_scroll_possible );
				}, 500);
				
			}, function myError(response) {
				
				//alert('Error getting new messages! Check your internet connection.');
				
				$scope.getting_new_meessages = false;
				
			});
		}
		setInterval(function(){
			$scope.check_for_new_messages();
		}, 15000);
		
		
		
		
		
		
		
		$scope.textarea_enter_pressed = function($event){
		
			if($event.keyCode == 13 && !$event.shiftKey) {
				$event.preventDefault();
				$scope.send_message();
			}
		
		}
		
		$scope.send_message = function() {
			
			if( $scope.content == '' ) {
				return;
			}
			
			if( $scope.sending_message == true ) {
				return;
			}
			//alert($scope.messages_list[ $scope.messages_list.length -1 ].ID);
			$scope.sending_message = true;
			
			$http({
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				transformRequest: function(obj) {
					var str = [];
					for(var p in obj)
					str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
					return str.join("&");
				},
				method 	: "POST",
				url		: '<?php echo DD_WEBSITE_SITEURL; ?>/wp-admin/admin-ajax.php', // ajaxurl,
				data	: {
					'action': 'dd_mobile_app_admin_chat_single_create_message',
					'token': '<?php echo create_user_token( $user ); ?>',
					'chat_user_id' : '<?php echo $chat_user_id; ?>',
					'content' : $scope.content
				},
				cache: false,
				dataType: 'json'
			}).then(function mySuccess(response) {
				
				console.log(response.data);
				console.log(JSON.stringify(response.data));
				
				$scope.messages_list = $scope.messages_list.concat( response.data );
				
				$scope.title = '';
				$scope.content = '';
				$scope.sending_message = false;
				
				setTimeout(function(){
					var height = $j('.messages_container').outerHeight();
					var scrollHeight = $j('.messages_container')[0].scrollHeight;
					var max_scroll_possible = scrollHeight - height + 4; // For some reason 4 needs to be added | maybe because of the 2 pc border
					//alert(max_scroll_possible);
					$j('.messages_container').scrollTop( max_scroll_possible );
				}, 500);
				
			}, function myError(response) {
				
				//alert('Error getting new messages! Check your internet connection.');
				
				$scope.sending_message = false;
				
			});
		
		
		
			/*
			alert( 
				'sending message "' 
				+ $scope.content 
				+ '" to the user "' 
				+ '<?php echo $chat_user->data->display_name; ?>' 
				+ '"'
			);
			*/
			
		}
		
		
		
	});
	
	
	
	
	
	</script>
	
	
	
	
	
	
	


	
<?php } ?>