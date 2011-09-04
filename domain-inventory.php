<?php
/*
Plugin Name: Domain Inventory
Plugin URI: 
Description: Set of WordPress Custom Taxonomies and Custom Post Type to curate a list of domains
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

class DomainInventory {
	
	static $instance;
	static $inspector;
	
	public $cts = array(
			'agency'=>array( 
				'singular' => 'Agency',
				'plural' => 'Agencies',
			),
			'status'=>array( 
				'singular' => 'Server Status', 
				'plural' => 'Server Statuses',
			),
			'nonwww'=>array( 
				'singular' => 'Non-www. Support', 
				'plural' => 'Non-www. Support',
			),
			'ipv6'=>array( 
				'singular' => 'IPv6 support', 
				'plural' => 'IPv6',
			),
			'cdn'=>array( 
				'singular' => 'CDN Provider', 
				'plural' => 'CDN Providers',
			),
			'ga'=>array( 
				'singular' => 'Google Apps', 
				'plural' => 'Google Apps',
			),
			'cms'=>array( 
				'singular' => 'CMS', 
				'plural' => 'CMSs',
			),
			'server_software'=>array( 
				'singular' => 'Server Software', 
				'plural' => 'Servers Software',
			),
			'cloud'=>array( 
				'singular' => 'Cloud Provider', 
				'plural' => 'Cloud Providers',
			),
			'analytics'=>array( 
				'singular' => 'Analytics Source', 
				'plural' => 'Analytics Sources',
			),
			'scripts'=>array( 
				'singular' => 'Script Library', 
				'plural' => 'Scripts Libraries',
			),
			'https'=>array( 
				'singular' => 'HTTPs Support', 
				'plural' => 'HTTPs Support',
			),
		);
	
	function __construct() {
		
		self::$instance = $this;
		
		//grab site inspector
		if ( !class_exists('SiteInspector') ) {
			require_once( 'site-inspector/class-site-inspector.php' );
			$this->inspector = new SiteInspector;
		}
		
		add_action( 'init', array( &$this, 'register_cpt' ) );
		add_action( 'init', array( &$this, 'register_cts' ) );
		add_action( 'admin_init', array( &$this, 'check_get' ) );
		add_action( 'admin_init', array( &$this, 'meta_cb' ) );
		add_filter( 'the_content', array( &$this, 'content_filter') , 10, 2 );
		add_action( 'wp_head', array( &$this, 'css' ) );
		
		register_activation_hook( __FILE__ , 'flush_rewrite_rules');

	}

	function register_cpt() {
	
		$labels = array(
	    'name' => _x('Domains', 'post type general name'),
	    'singular_name' => _x('Domain', 'post type singular name'),
	    'add_new' => _x('Add New', 'domain'),
	    'add_new_item' => __('Add New Domain'),
	    'edit_item' => __('Edit Domain'),
	    'new_item' => __('New Domain'),
	    'view_item' => __('View Domain'),
	    'search_items' => __('Search Domains'),
	    'not_found' =>  __('No domains found'),
	    'not_found_in_trash' => __('No domains found in Trash'), 
	    'parent_item_colon' => '',
	    'menu_name' => 'Domains',
	  );
	  $args = array(
	    'labels' => $labels,
	    'public' => true,
	    'has_archive' => true, 
	    'supports' => array('title', 'comments', 'custom-fields',	),
	  ); 
	  register_post_type('domain',$args);
	
	}
	
	function register_cts() {
		
		foreach ( $this->cts as $ct=>$names ) {
					  
			 $labels = array(
				'name' => _x( $names['singular'], 'taxonomy general name' ),
				'singular_name' => _x( $names['singular'], 'taxonomy singular name' ),
				'search_items' =>  __( 'Search ' . $names['plural'] ),
				'all_items' => __( 'All ' . $names['plural'] ),
				'parent_item' => __( 'Parent ' . $names['singular'] ),
				'parent_item_colon' => __( 'Parent ' . $names['singular']. ':' ),
				'edit_item' => __( 'Edit ' . $names['singular'] ), 
				'update_item' => __( 'Update ' . $names['singular'] ),
				'add_new_item' => __( 'Add New ' . $names['singular']),
				'new_item_name' => __( 'New ' . $names['singular'] .' Name' ),
				'menu_name' => __( $names['singular'] ),
			  ); 	
			 
			register_taxonomy( $ct, 'domain', array( 'labels' => $labels ) );
		}
	}
	
	function inspect( $post_id ) {

		$post = get_post( $post_id );

		if ( !$post )
			return false;

		$data = $this->inspector->inspect( $post->post_title );
	
		add_post_meta( $post->ID, 'md5', $data['md5'], true );
		add_post_meta( $post->ID, 'ip', $data['ip'], true );
				
		foreach ( $this->cts as $ct=>$foo ) { 
			
			if ( isset( $data[$ct] ) ) {
				
				if ( $data[$ct] === false )
					$data[$ct] = 'none';
					
				if ( $data[$ct] === true )
					$data[$ct] = 'yes';
			
				wp_set_post_terms( $post->ID, $data[$ct] , $ct, true);
			
			} else { 
				
				if ( $ct == 'agency' )
					continue; 
				
				wp_set_post_terms( $post->ID, array( 'none' ), $ct, true);		
			
			}
		}
		
		add_post_meta( $post->ID, 'inspected', true , true );
		
		return $data;
		
	}
	
	function inspect_the_uninspected() {
	
		set_time_limit( 0 );
	
		//get a random uninspected domain and inspect it
		//this allows us to thread the inspections
		while ( $domain = $this->get_uninspected_domain() )
				$this->inspect( $domain );
	
	}
	
	function get_uninspected_domain( ) {
	
		global $wpdb;
		
		$sql = "SELECT id FROM wp_posts WHERE wp_posts.post_type = 'domain' AND id NOT IN (SELECT post_id from wp_postmeta WHERE meta_key = 'inspected') ORDER BY RAND() LIMIT 1";
			
		return $wpdb->get_var( $sql );

	}
	
	function check_get( ) {
		if ( !isset( $_GET['domain-inspect'] ) )
			return;

		if ( $_GET['post'] ) 
			$this->inspect( $_GET['post'] );
		else
			$this->inspect_the_uninspected();
			
	}
	
	function meta_cb() {
		add_meta_box( 'refresh', 'Refresh', array(&$this, 'refresh_metabox'), 'domain' );
	}
	
	function refresh_metabox( $post ) { ?>
		<a href="<?php echo add_query_arg( 'domain-inspect', true ); ?>">Refresh Data</a>
	<?php }

	function content_filter( $content ) {
		global $post;

		if ( $post->post_type != 'domain' )
			return $content; 
		
		ob_start();
		
		foreach ( $this->cts as $ct=>$foo) {
			$tax = get_taxonomy( $ct );
			$list = get_the_term_list( $post->ID, $ct, null, ', ');
			if ( strlen( $list ) == 0 )
				continue;
			?>
			<span class="label"><?php echo $tax->labels->name; ?></span>: <?php echo $list;?><br />
			<?php
		}
		
		$metas = array( 'ip' );
		foreach ( $metas as $meta ) { ?>
			<span class="label"><?php echo $meta ?></span>: <?php echo get_post_meta( $post->ID, $meta, true); ?><br />
		<?php 
		}
		
		$md5 = get_post_meta( $post->ID, 'md5', true);
		
		$others = get_posts( array( 'meta_key' => 'md5', 'meta_value' => $md5, 'post_type' => 'domain' ) );
		if ( sizeof( $others) > 1 ) {
			echo '<span class="label">Duplicate Domains</span>: ';
			$array = array();
			foreach ( $others as $other ) {
				
				if ( $other->ID == $post->ID )
					continue;
					
				$array[] = '<a href="' . get_permalink( $other->ID ) . '">' . $other->post_title . '</a>';
			}
			echo implode(', ', $array);
			echo "<br />";
		}
		
		//visit link
		echo '<span class="label">Visit</span>: <a href="http://';
		
		if ( !is_object_in_term( $post->ID, 'nonwww', '1') )
			echo 'www.';
			
		echo $post->post_title . '" target="_blank">' . $post->post_title . '</a><br />';
		
		echo "<br />";
		
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	function css() { ?>
	<style>.domain .label {font-weight: bold; }</style>
	<?php }
	
}


new DomainInventory;


?>