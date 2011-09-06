<?php
include('../../../wp-load.php'); 

global $wpdb;
$domains = get_posts( array( 'post_type'=>'domain', 'numberposts'=> -1, 'meta_key' => 'md5' ) );
$output = array();

foreach ( $domains as $domain ) {
	$output[ get_post_meta( $domain->ID, 'md5', true ) ][] = $domain->post_title;	

	if ( $_GET['fix'] )
		$output[ get_post_meta( $domain->ID, 'md5', true ) ][] = $domain->ID;	

}

foreach ( $output as  $ID=>$dup ) {
	if ( sizeof( $dup ) == 1)
		unset( $output[$ID] );
}

if ( $_GET['fix'] ) {
	foreach ( $output['d41d8cd98f00b204e9800998ecf8427e'] as $domain ) {
	
		echo $domain . "\n"; flush();
		$di = DomainInventory::$instance;
		$data = $di->inspect( $domain );
	} 
}

print_r( $output );