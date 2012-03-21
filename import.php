<?php 
/**
 * Imports .gov list from CSV into Wordpress
 * Data: http://explore.data.gov/Federal-Government-Finances-and-Employment/Federal-Executive-Branch-Internet-Domains/k9h8-e98h
 * 
 * Put csv in same folder and run this script directly
 *
 */

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

global $wpdb;
$db = $wpdb->get_col( "SELECT post_title FROM $wpdb->posts WHERE post_type = 'domain'" ); 

//loop through domains and instert into WP
$i = 0;
foreach ( $domains as $domain ) {

	//domain already added
	if ( in_array( $domain['domain'], $db ) )
		continue;
		
	//set up post object
	$post = array();
	$post['post_type'] = 'domain';
	$post['post_title'] = $domain['domain'];
	$post['post_status'] = 'publish';
	
	//insert post
	$postID = wp_insert_post( $post );
	
	//add post to proper agency term-taxonomy
	wp_set_post_terms( $postID, array( $domain['agency'] ), 'agency', false );
	
	//output some progress
	echo "<P>Added " . $domain['domain'] . ' (' . $domain['agency'] . ")</p>"; flush();
	$i++;
}

//let us know we're done and we got them all
echo "<p>$i domains added.</p>";
?>
