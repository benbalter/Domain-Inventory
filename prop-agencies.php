<?php
//not the best way, but only running this once and it gets the job done
include('../../../wp-load.php'); 

//init array
$domains = array();

//loop and parse domain list CSV into an assoc array
if (($handle = fopen("domain_list.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$domains[] = array( 'domain' => strtolower( trim( $data[0] ) ), 'agency' => $data[1] );
    }
    fclose($handle);
}

foreach ( $domains as $domain ) {
	
	$slug = str_ireplace( '.', '-', $domain['domain'] );
	$post = get_page_by_path( $slug, null, 'domain' );

	wp_set_post_terms( $post->ID, array( $domain['agency'] ), 'agency', false );

}
?>