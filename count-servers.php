<?php
include('../../../wp-load.php'); 

$terms = get_terms( 'server_software' );
$results = array();

foreach ( $terms as $term ) {
	$query = new WP_Query( 'server_software=' . $term->slug );
	$parts = explode( '/', $term->name );
	$software = $parts[0];
	$parts = explode( ' ', $parts[1] );
	$version = $parts[0];
	if ( $version == '')
		$version = 'unknown';
	$results[ $software ][ $version ] += $query->found_posts;	
}

foreach ( $results as &$software ) {
	if ( sizeof( $software ) == 1 ) 
		$software = $software['unknown'];
	else 
		$software['total'] = array_sum( $software );
	
}

print_r( $results );