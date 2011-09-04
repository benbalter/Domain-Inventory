<?php
//in conjunction with Count Shortcode and Faceted Search Widget, generates HTML of initial post to aid in browsing inventory
include('../../../wp-load.php'); 
$skip = array( 'agency', 'server_software', 'ga');
?>

<strong>Domains</strong>: [count post_type=domain]

<?php

$taxs = get_taxonomies( array('object_type' => array( 'domain' ) ),  'objects' );

foreach ($taxs as $tax) { 
		
		if ( array_search( $tax->query_var, $skip ) !== false )
			continue;
		
		//verify taxonomy is public and queryable
		if ( !$tax->query_var || !$tax->public )
			continue;
			
		//verify taxonomy has terms associated with it
		$terms = get_terms( $tax->name );
		if ( sizeof( $terms ) == 0)
			continue;
 		?>
<strong><?php echo $tax->labels->name; ?></strong>
	<ul>
<?php foreach ( $terms as $term ) { ?>
		<li><strong><?php echo $term->name; ?></strong>: [count <?php echo $tax->query_var; ?>=<?php echo $term->slug; ?>]</li>
<?php } ?>
	</ul>
<?php } ?>