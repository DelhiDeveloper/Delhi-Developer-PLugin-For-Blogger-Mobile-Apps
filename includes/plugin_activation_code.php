<?php

/* Plugin Activation Code */






/* Plugin Activation Code */
function dd_mobile_app_plugin_activation_function() {
	
	/* Initializations */
	$activation_successful = true;
	$notice_message = 'Activating "Delhi Developer Blog Mobile API" Plugin!';
	
	/* Creating a new Role "Mobile App Subscriber" */
	$role_created = add_role(
		'mobile_app_subscriber',
		__( 'Mobile App Subscriber' ),
		array(
			'read'      => true
		)
	);
	if ( $role_created == null ) {
		$notice_message .= '<br/>1. New user role "Mobile App Subscriber" has been created.';
	} else {
		$activation_successful = false;
		$notice_message .= '<br/>1. WARNING: New user role "Mobile App Subscriber" could NOT be created.';
	}
	
	/* Create Mobile User Verification Page */
	// change the Sample page to the home page
	/*
	$page_title = 'Mobile App User Verification Page';
	$page_content = '';
	$page = array(
		'post_type' => 'page',
		'post_title' => $page_title,
		'post_content' => $page_content,
		'post_status' => 'publish',
		'post_author' => 1,
		'ID' => 2,
		'post_slug' => 'mobile_app_user_verification'
	);
	$page_check = get_page_by_title($page_title);
	if(!isset($page_check->ID) && !the_slug_exists('mobile_app_user_verification')){
		$page_id = wp_insert_post($page);
		$notice_message .= '<br/>2. New "Mobile App User Verification" page has been created.';
	} else {
		$notice_message .= '<br/>2. NOTICE: "Mobile App User Verification" page was already existing';
	}
	*/
	
	/* Setting Activation Notice Class */
	if( $activation_successful ) {
		$notice_class = 'notice notice-success';
	} else {
		$notice_class = 'notice notice-error';
	}
	
	
	// Had to remove this transient way of showing notifications as it was causing errors in mobile rest api
	/* Cannnot directly create admin notice hook here. It does not work. */
	/*
	set_transient( 'plugin_just_activated', true, 5 );
	set_transient( 'activation_notice_class', $notice_class, 5 );
	set_transient( 'activation_notice_message', $notice_message, 5 );
	*/
	
}



/* Plugin Activation Notice */
/*
function plugin_activation_notice(){
	if( get_transient( 'plugin_just_activated' ) ){
		?>
			<div class="<?php echo get_transient( 'activation_notice_class' ); ?>">
				<p><?php echo get_transient( 'activation_notice_message' ); ?></p>
			</div>
		<?php
		/* Delete transient, only display this notice once. * /
		delete_transient( 'plugin_just_activated' );
	}
}
add_action( 'admin_notices', 'plugin_activation_notice' );
*/








/**************************** Database Tables Creation  *********************************/

/* FOR TESTING ONLY */
/* But this works only after activating the plugin */
//delete_option( 'dd_mobile_app_database_version' );



function dd_mobile_app_create_database_tables() {
	
	global $wpdb;
	
	
	$dd_mobile_app_database_version = "1.0";
	$dd_mobile_app_database_version_installed = get_option( "dd_mobile_app_database_version" );
	
	
	/* If the installed version of database is not equal to the new version then run the queries */
	if( $dd_mobile_app_database_version != $dd_mobile_app_database_version_installed ) {
		
		$table_name = 'dd_mobile_app_chat_messages';
		
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql = "
			CREATE TABLE {$wpdb->prefix}{$table_name} (
				ID					bigint(20) NOT NULL AUTO_INCREMENT,
				sender_user_id		bigint(20) NOT NULL,
				receiver_user_id	bigint(20) NOT NULL,
				title				TINYTEXT NOT NULL,
				content				TEXT NOT NULL,
				is_read				BOOLEAN DEFAULT 0 NOT NULL,
				creation_time		TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				update_time			DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY			(ID),
				FOREIGN KEY			(sender_user_id)	REFERENCES {$wpdb->prefix}user(ID),
				FOREIGN KEY			(receiver_user_id)	REFERENCES {$wpdb->prefix}user(ID)
			)
			$charset_collate;
		";
		
		/* Create Table if it does not already exist */
		if($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table_name}'") != $table_name) {
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			
			dbDelta( $sql );
			
		}
		
		update_option( 'dd_mobile_app_database_version', $dd_mobile_app_database_version );
		
	}
	
	
	
}













/**************************** /Database Tables Creation *********************************/



























?>