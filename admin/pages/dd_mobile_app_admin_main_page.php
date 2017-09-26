<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;




/* Main Page Function */
function dd_mobile_app_admin_main_page() {

	
	/************************************ HANDLE FORM SUBMISSIONS *************************************/
	$settings_saved = false;
	$new_jwt_token_keys_generated = false;
	if( !empty($_POST)
		&&	isset($_POST['action'])
		&&	$_POST['action'] == "dd_testing"
	){
		
		/* JSON Encode Options */
		$dd_mobile_app_chat_authors = json_encode( $_POST['dd_mobile_app_chat_authors'] );
		$dd_mobile_app_youtube_channel_settings = json_encode(
			array( // Though an array is being used but while decoding it will become an stdClass object
				"enable_youtube_channel"	=> $_POST['enable_youtube_channel'],
				"youtube_api_key"			=> $_POST['youtube_api_key'],
				"youtube_channel_id"		=> $_POST['youtube_channel_id']
			)
		);
<<<<<<< HEAD
		$dd_mobile_app_important_pages = json_encode(
			array(
				'dd_youtube_video_player_page'	=>	$_POST['dd_youtube_video_player_page']
			)
		);
=======
>>>>>>> origin/master
		$dd_mobile_app_jwt_keys = json_encode(
			array(
				'jwt_public_key'	=>	$_POST['jwt_public_key'],
				'jwt_private_key'	=>	$_POST['jwt_private_key']
			)
		);
		
		
		/* If Auto-Generate Requested Then Generate New Keys */
		$dd_mobile_app_regenerate_jwt_keys = $_POST['dd_mobile_app_regenerate_jwt_keys'];
		if( $dd_mobile_app_regenerate_jwt_keys ) {
			$rsa = new \phpseclib\Crypt\RSA();
			$keys = $rsa->createKey();
			$dd_mobile_app_jwt_keys = json_encode(
				array(
					'jwt_public_key'	=>	$keys['publickey'],
					'jwt_private_key'	=>	$keys['privatekey']
				)
			);
			$new_jwt_token_keys_generated = true;
		}
		
		
		
		/* Update Options */
		update_option(
			'dd_mobile_app_chat_authors',
			$dd_mobile_app_chat_authors
		);
		update_option(
			'dd_mobile_app_youtube_channel_settings',
			$dd_mobile_app_youtube_channel_settings
		);
		update_option(
			'dd_mobile_app_jwt_keys',
			$dd_mobile_app_jwt_keys
		);
		
		$settings_saved = true;
		
	}
	
	/************************************ /HANDLE FORM SUBMISSIONS ************************************/
	
	
	/************************************* GET DATA FOR VIEW ******************************************/
	
	//delete_option('dd_mobile_app_chat_authors');
	//delete_option('dd_mobile_app_youtube_channel_settings');
<<<<<<< HEAD
	//delete_option('dd_mobile_app_important_pages');
	//delete_option('dd_mobile_app_jwt_keys');
=======
>>>>>>> origin/master
	
	
	/* Geting Saved Options */
	$dd_mobile_app_chat_authors				= json_decode( get_option('dd_mobile_app_chat_authors') );
	$dd_mobile_app_youtube_channel_settings	= json_decode( get_option('dd_mobile_app_youtube_channel_settings') );
	$dd_mobile_app_jwt_keys					= json_decode( get_option('dd_mobile_app_jwt_keys') );
	
	
	//print_r( $dd_mobile_app_chat_authors );
	//print_r( $dd_mobile_app_youtube_channel_settings );
	
	/* Getting Author Users */
	$author_users = get_users(
		array(
			'role__in'	=> array(
				'administrator',
				'editor',
				'author',
				'contributor',
			),
		)
	);
	
	/* Getting Pages */
	$pages = get_pages();
	
	//echo "<pre>" . json_encode( $pages , JSON_PRETTY_PRINT ) . "</pre>";
	
	
	?>
	
	
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	
	<div class="col-md-12">
	
		<h2>Delhi Developer Mobile App Settings</h2>
		
		<form method="POST" action="">
		<input type="hidden" name="action" value="dd_testing" />
			
			
			<?php if( $settings_saved == true ) { ?>
			<div class="alert alert-success alert-dismissable">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				Settings Saved <strong>Successfully!</strong>.
			</div>
			<?php } ?>
			
			<?php if( $new_jwt_token_keys_generated == true ) { ?>
			<div class="alert alert-info alert-dismissable">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				New JWT token RSA keys have been generated <strong>Successfully!</strong>.
			</div>
			<?php } ?>
			
			
			<div class="col-md-6">
			
				<h3>Step1:</h3>
				<h4>Select the authors who can chat with Mobile App Subscribers:</h4>
			
				<table class="table">
					<thead style="font-weight: bold;">
						<tr>
							<th>
								S.No.
							</th>
							<th>
								User Display Name
							</th>
							<th>
								User Role
							</th>
							<th class="text-center">
								Allow Chatting
								<br />
								(With Mobile App Subscriers)
							</th>
						</tr>
					</thead>
					<?php $i=1; foreach( $author_users as $author_user ) { ?>
					<tbody>
						<tr>
							<td>
								<?php echo $i; ?>.
							</td>
							<td>
								<label for="dd_mobile_app_chat_authors_<?php echo $i++; ?>" style="font-weight: 400;">
									<?php echo $author_user->data->display_name; ?>
								</label>
							</td>
							<td>
								<?php
									$first = true;
									foreach( $author_user->roles as $role ) {
										echo ucwords($role);
										if( $first == true ) {
											echo '<br />';
											$first = false;
										}
									}
								?>
							</td>
							<td class="text-center">
								<input 
									type="checkbox" 
									id="dd_mobile_app_chat_authors_<?php echo $i++; ?>" 
									name="dd_mobile_app_chat_authors[]" 
									value="<?php echo $author_user->data->ID; ?>"
									style="
										  width: 20px;
										  height: 20px;
									"
									<?php 
										if( 
											$dd_mobile_app_chat_authors
											&&	in_array( $author_user->data->ID , $dd_mobile_app_chat_authors ) 
										) { 
									?>
										checked="checked"
									<?php } ?>
								/>
							</td>
						</tr>
					</tbody>
				<?php } ?>
				</table>
				
			</div>
			
			
			
			
			
			
			
			<div class="col-md-6">
			
				<h3>Step2:</h3>
				<h4>YouTube Video Channel Settings:</h4>
				
				<div class="form-group">
					<label>Enable Youtube Channel On Mobile App: </label>
					<div class="radio-inline">
						<label style="font-weight: 400;">
							<input type="radio" name="enable_youtube_channel" value="Yes"
								<?php 
									if( 	$dd_mobile_app_youtube_channel_settings
										&&	$dd_mobile_app_youtube_channel_settings->enable_youtube_channel == 'Yes' 
									) { 
								?>
									checked="checked"
								<?php } ?>
							>
							Yes
						</label>
					</div>
					<div class="radio-inline">
						<label style="font-weight: 400;">
							<input type="radio" name="enable_youtube_channel" value="No"
								<?php 
									if(  	! $dd_mobile_app_youtube_channel_settings
										||	$dd_mobile_app_youtube_channel_settings->enable_youtube_channel == 'No' 
									) { 
								?>
									checked="checked"
								<?php } ?>
							>
							No
						</label>
					</div>
				</div>
				
				<div class="form-group">
					<label>Youtube API Key</label>
					<input 
						class="form-control input-sm" 
						type="text" 
						id="youtube_api_key" 
						name="youtube_api_key" 
						value="<?php echo $dd_mobile_app_youtube_channel_settings->youtube_api_key; ?>"
					/>
				</div>
				
				<div class="form-group">
					<label>Youtube Video Channel ID</label>
					<input 
						class="form-control input-sm" 
						type="text" 
						id="youtube_channel_id" 
						name="youtube_channel_id" 
						value="<?php echo $dd_mobile_app_youtube_channel_settings->youtube_channel_id; ?>"
					/>
				</div>
				
				<div class="form-group">
					<label>Update Youtube Channel Videos List Now</label>
					<a 
						class="btn btn-primary" 
						target="_blank"
						href="https://mylittlemuffin.com/wp-admin/admin-post.php/?action=dd_youtube_videos_list_update"
					>
						Get New Videos
					</a>
				</div>
				
			</div>
			
			
			
			
			
			
			
<<<<<<< HEAD
			<div class="col-md-6" style="clear: both;">
			
				<h3>Step 3:</h3>
				<h4>Create Important Pages Required For The Mobile App:</h4>
				<p>You are required to create some pages for mobile app to work properly. Please create the belowmentioned pages and delect them from the dropdown.</p>
				
				<div class="form-group">
					
					<label>1. Videos Page</label>
					<p>This page will show your youtube videos in form of a youtube like video player.</p>
					
					<select 
						class="form-control input-sm" 
						id="dd_youtube_video_player_page" 
						name="dd_youtube_video_player_page" 
					>
						<option value="">-- Select a Page --</option>
						<?php foreach( $pages as $page ) { ?>
						<option 
							value="<?php echo $page->ID; ?>"
							<?php if( $dd_mobile_app_important_pages->dd_youtube_video_player_page == $page->ID ) { ?>
								selected="selected"
							<?php } ?>
						>
							<?php echo $page->post_title; ?>
						</option>
						<?php } ?>
					</select>
					
					<?php					
					if( 
							! $dd_mobile_app_important_pages
						||	$dd_mobile_app_important_pages->dd_youtube_video_player_page == ''
					) {
					?>
					<a href="<?php echo DD_WEBSITE_SITEURL; ?>/wp-admin/post-new.php?post_type=page" target="_blank">
						Or create a new page for this.
					</a>
					(Then
					<a href="<?php echo DD_WEBSITE_SITEURL . '/wp-admin/admin.php?page=dd_mobile_app_admin_main_page'; ?>">
						refresh
					</a>
					to select that new page.)
					<?php } ?>
					
				</div>
				
				<?php /*
				<div class="form-group">
					
					<label>2. Cron Page for Updates From API's</label>
					<p>This page is required to create a CRON Job for getting the updates from API's like Youtube Videos list for a channel. You will be required to create a cron job for this page to get updates of youtube channel regularly.</p>
					
					<select 
						class="form-control input-sm" 
						id="mobile_app_cron_api_updates_page" 
						name="mobile_app_cron_api_updates_page" 
					>
						<option value="">-- Select a Page --</option>
						<?php foreach( $pages as $page ) { ?>
						<option 
							value="<?php echo $page->ID; ?>"
							<?php if( $dd_mobile_app_important_pages->mobile_app_cron_api_updates_page == $page->ID ) { ?>
								selected="selected"
							<?php } ?>
						>
							<?php echo $page->post_title; ?>
						</option>
						<?php } ?>
					</select>
					
					<?php					
					if( 
							! $dd_mobile_app_important_pages
						||	$dd_mobile_app_important_pages->mobile_app_cron_api_updates_page == ''
					) {
					?>
					<a href="<?php echo DD_WEBSITE_SITEURL; ?>/wp-admin/post-new.php?post_type=page" target="_blank">
						Or create a new page for this.
					</a>
					(Then
					<a href="<?php echo DD_WEBSITE_SITEURL . '/wp-admin/admin.php?page=dd_mobile_app_admin_main_page'; ?>">
						refresh
					</a>
					to select that new page.)
					<?php } ?>
				</div>
				*/ ?>
				
				
				
			</div>
			
			
			
			
			
			
			
=======
>>>>>>> origin/master
			
			
			
			
			<div class="col-md-6">
			
				<h3>Step 3:</h3>
				<h4>Create JWT Token Keys:</h4>
				<p>You are required to create an RS256 public & private keys. These are the keys that will secure your mobile application API's. They will ensure secure communication between the mobile apps and the server.</p>
				
				<div class="form-group">
					
					<label>Option A. Autogenerate New Keys</label>
					<p>This plugin can autogenerate these keys for you. Just check this checkbox and save the page. Do not use this functionality too often because whenever you regenerate these keys all your mobile app users have to login to their apps again.</p>
					
					<label style="font-weight: 400;">
						<input 
							type="checkbox" 
							id="dd_mobile_app_regenerate_jwt_keys" 
							name="dd_mobile_app_regenerate_jwt_keys" 
							value="regenerate"
							style="
								  width: 20px;
								  height: 20px;
							"
						/>
						Check This Check Box To Generate New JWT Keys Automatically
					</label>
					
					
				</div>
				
				<div class="form-group">
					
					<label>Option B. Create Your Keys Yourself</label>
					<p>
						You can also use your own keys which you can create using a software like puttygen or you can also use 
						<a href="" target="_blank">this link</a>
						. While using this link just remember to select key size 1024-bit and check the 'Async' checkbox.
					</p>
					
					<label for="jwt_public_key">Public Key :</label>
					<textarea
						class="form-control input-sm" 
						id="jwt_public_key" 
						name="jwt_public_key" 
						rows="4"
					><?php if( $dd_mobile_app_jwt_keys ) {
							echo $dd_mobile_app_jwt_keys->jwt_public_key;
							}
					?></textarea>
					
					<label for="jwt_private_key">Private Key :</label>
					<p>As the name suggests. Keep this one a big secret.</p>
					<textarea
						class="form-control input-sm" 
						id="jwt_private_key" 
						name="jwt_private_key" 
						rows="8"
					><?php if( $dd_mobile_app_jwt_keys ) {
							echo $dd_mobile_app_jwt_keys->jwt_private_key;
							}
					?></textarea>
					
					
				</div>
				
			</div>
			
			
			
			
			
			
			
			
			<div class="col-md-12">
				<button type="submit" class="btn btn-success btn-lg">
					Save Settings
				</button>
			</div>
			
			
			
		</form>
		
		
		
	</div>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
<?php } ?>