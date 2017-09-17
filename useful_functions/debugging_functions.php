<?php


/* Securing Plugin From Direct Access through the URL Path */
if ( ! defined( 'ABSPATH' ) ) exit;



function dd_halt() {
	/* Funtion Overloading is being implemented according to the number of arguments */
	
	if( func_num_args() == 0 ) {
		/* function log_in() */
		echo '<br />Halted!<br />';
		exit;
	}
	
	if( func_num_args() == 1 ) {
			
		$args = func_get_args();
		$print_before_halt = $args[0];
		
		echo '<br />';
		print_r( $print_before_halt );
		echo '<br />';
		
		halt();
		
	}
	
	
}





function dd_haltq() {
	global $wpdb;
	
	halt(
		$wpdb->last_query
	);
	
}





function dd_haltj( $variable ) {
	
	halt(
		json_encode( $variable )
	);
	
}





















?>