<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;




/************************************ AJAX REQUESTS RESPONSES *************************************/




add_action('wp_ajax_dd_mobile_app_admin_chats_list'			, 'dd_mobile_app_admin_chats_list');
add_action('wp_ajax_nopriv_dd_mobile_app_admin_chats_list'	, 'dd_mobile_app_admin_chats_list'); // Was not working in admin panel without this

function dd_mobile_app_admin_chats_list() {
	
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
	$page_no = $_POST['page_no'];
	$limit	= 10;
	$offset = 0;
	$offset = ($page_no - 1) * $limit;
	
	/* Getting Messages */
	$table_name = 'dd_mobile_app_chat_messages';
	$Q1 = "
		SELECT	{$wpdb->prefix}{$table_name}.ID AS message_id,
				IF( receiver_user_id= {$user->ID} , 1 , 0 ) AS received,
				IF( sender_user_id	= {$user->ID} , 1 , 0 ) AS sent,
				IF( receiver_user_id= {$user->ID} AND is_read = 0 , 1 , 0 ) AS un_read,
				IF( receiver_user_id= {$user->ID} , sender_user_id , receiver_user_id ) AS other_id,
				creation_time
		From	{$wpdb->prefix}{$table_name}
		WHERE	( sender_user_id = {$user->ID} || receiver_user_id = {$user->ID} )
		ORDER BY creation_time DESC
	";
	// Note other is used to represent the other user who may be sender or recceiver but is definitely not the author
	
	$Q2 = "
		
		SELECT		{$wpdb->prefix}users.ID,
					{$wpdb->prefix}users.user_email,
					{$wpdb->prefix}users.display_name,
					{$wpdb->prefix}users.user_nicename,
					SUM( received )	AS total_received,
					SUM( sent )		AS total_sent,
					SUM( un_read )	AS total_un_read,
					MAX( creation_time )	AS last_message_creation_time
					
		FROM		({$Q1}) Q1
		JOIN		{$wpdb->prefix}users
		ON			other_id = {$wpdb->prefix}users.ID
		GROUP BY	other_id
		ORDER BY	MAX( creation_time ) DESC
		LIMIT		{$limit}
		OFFSET		{$offset}
		
	";
	
	$chats_list = $wpdb->get_results( $Q2 );
	
	/* Creating ANGULAR friendly DATES */
	foreach( $chats_list as &$chat ) {
		$chat->last_message_creation_time = date( 'c' , strtotime( $chat->last_message_creation_time ) );
	}
	
	/* Used to test LIMIT and OFFSET */
	//$chats_list = $wpdb->get_results( "SELECT * FROM wp_dd_mobile_app_chat_messages LIMIT {$limit} OFFSET {$offset}" );
	
	echo json_encode( $chats_list );
	exit;
	
	
	// to create token
	// create_user_token( $user );
	
	
}







/************************************ AJAX REQUESTS RESPONSES *************************************/











/* Main Page Function */
function dd_mobile_app_admin_chats_list_page() {
	
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
		return;
		
	}
	
	$dd_mobile_app_chat_authors	= json_decode( get_option('dd_mobile_app_chat_authors') );
	
	
	if( 
			! $dd_mobile_app_chat_authors
		||	empty($dd_mobile_app_chat_authors)
		||	! in_array( $user->ID , $dd_mobile_app_chat_authors ) 
	) {
		
		echo 'Though you are an author by role. But chatting has not been enabled for you in the plugiin settings.';
		return;
		
	}
	
	/************************************ /RESTRICTING UNWELCOME USERS ************************************/
	
	
	
	
	
	/************************************ HANDLE FORM SUBMISSIONS *************************************/
	
	
	
	/************************************ /HANDLE FORM SUBMISSIONS ************************************/
	
	
	/************************************* GET DATA FOR VIEW ******************************************/
	
	
	
	?>
	
	
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	
	<style>
		.text-bold {
			font-weight: bold;
		}
	</style>
	
	<div class="col-md-12" ng-app="myApp" ng-controller="myCtrl">
	
		<h2>
			Delhi Developer Mobile App Chats
			<span ng-if="loading" style="
				font-size: 14px;
			">
				( Refreshing...<i class="fa fa-refresh fa-spin"></i> )
			</span>
		</h2>
		
		
		
		<div class="alert alert-danger alert-dismissable" ng-if="chats_list.length == 0">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			Sorry, no messages were found.
		</div>
		
		
		
		<div class="col-md-12">
			
			<button ng-click="refresh_chats_list()" class="btn btn-default">
				Refresh Chats List
				<i class="fa fa-refresh"></i>
			</button>
			<span style="
				font-size: 14px;
			">
				( List refreshes automatically every 15 seconds. )
			</span>
			
			
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>
							S.No
						</th>
						<th>
							User Display Name
						</th>
						<th>
							User Email
						</th>
						<th>
							Total Sent To
						</th>
						<th>
							Total Received From
						</th>
						<th>
							Total Un-Read
						</th>
						<th>
							Last Message Time
						</th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="c in chats_list" 
						style="
							cursor: pointer;
						"
						ng-click="open_chat_single_page( c.ID )"
					>
						<td>
							S.No
						</td>
						<td>
							{{c.display_name}}
						</td>
						<td>
							{{c.user_email}}
						</td>
						<td>
							{{c.total_sent}}
						</td>
						<td>
							{{c.total_received}}
						</td>
						<td ng-class="{ 'text-bold' : c.total_un_read > 0 }">
							<!-- TODO : Add the functionality for text to be bold only if (c.total_un_read > 0) -->
							{{c.total_un_read}}
						</td>
						<td ng-class="{ 'text-bold' : c.total_un_read > 0 }">
							{{ c.last_message_creation_time | date : 'd-MMM-yyyy' }}
							<br />
							{{ c.last_message_creation_time | date : 'h:m a' }}
						</td>
					</tr>
				</tbody>
			</table>
			
			<div ng-if="show_load_more_button">
				<button ng-click="load_chats_list()" class="btn btn-default">
					+ Load Previous Chats
				</button>
			</div>
			
			<div ng-if="!show_load_more_button">
				<h5 style="color: #999;">
					You have reached the end of chats list.
				</h5>
			</div>
			
			
			
			
			
			
			
			
			
			
		</div>
		
		
	</div>
	
	
	
	
	
	
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
	
	
	<script type="text/javascript">
	
	var global_scope;
	
	var app = angular.module('myApp', []);
	app.controller('myCtrl', function( $scope , $http , $window ) {
		
		global_scope = $scope;
		
		$scope.chats_list = [];
		
		$scope.loading = false;
		
		$scope.char_list_page = 1;
		
		$scope.show_load_more_button = true;
		
		/* This refreshes the loading from PAGE (offset) 1 */
		$scope.refresh_chats_list = function(){
			
			$scope.char_list_page = 1;
			$scope.chats_list = [];
			$scope.load_chats_list();
		}
		
		/* This is used to load more results */
		$scope.load_chats_list = function(){
			
			if( $scope.loading == true ) {
				return;
			}
			
			$scope.loading = true;
			
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
					'action': 'dd_mobile_app_admin_chats_list',
					'token': '<?php echo create_user_token( $user ); ?>',
					'page_no': $scope.char_list_page
				},
				cache: false,
				dataType: 'json'
			}).then(function mySuccess(response) {
				
				console.log(response.data);
				console.log(JSON.stringify(response.data));
				
				$scope.chats_list = $scope.chats_list.concat(response.data);
				$scope.loading = false;
				$scope.char_list_page += 1;
				
				if( response.data.length < 10 ) {
					$scope.show_load_more_button = false;
				}
				
			}, function myError(response) {
				
				$scope.chats_list = [];
				$scope.loading = false;
				
			});
			
		}
		
		/* Updating The Chats List every 15 secoonds */
		$scope.load_chats_list();
		setInterval(function(){
			$scope.load_chats_list();
		}, 15000);
		
		
		$scope.open_chat_single_page = function( target_chat_user_id ) {
			
			//alert( target_chat_user_id );
			
			$window.location.href = '<?php echo DD_WEBSITE_SITEURL; ?>'
				+ '/wp-admin/admin.php?page=dd_mobile_app_admin_chat_single_page&chat_user_id='
				+ target_chat_user_id
			;
			
		}
		
		
		
	});
	
	
	
	
	
	</script>
	
	
	
	
	
	
	


	
<?php } ?>