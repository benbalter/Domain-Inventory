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
		require_once( 'site-inspector/class-site-inspector.php' );
		$this->inspector = new SiteInspector;

		add_action( 'init', array( &$this, 'register_cpt' ) );
		add_action( 'init', array( &$this, 'register_cts' ) );
		add_action( 'admin_init', array( &$this, 'check_get' ) );
		add_action( 'admin_init', array( &$this, 'meta_cb' ) );
		add_filter( 'the_content', array( &$this, 'content_filter') , 10, 2 );
		add_action( 'wp_head', array( &$this, 'css' ) );
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

		//get post
		$post = get_post( $post_id );

		//verify post
		if ( !$post )
			return false;

		//get data
		$data = $this->inspector->inspect( $post->post_title );
	
		//md5 body
		add_post_meta( $post->ID, 'md5', $data['md5'], true );
		
		//ip
		add_post_meta( $post->ID, 'ip', $data['ip'], true );
				
		foreach ( $this->cts as $ct=>$foo ) {
			
			if ( !isset( $data[$ct] ) ) 
				wp_set_post_terms( $post->ID, 'No', $ct, true);
			else 
				wp_set_post_terms( $post->ID, $data[$ct], $ct, true);
		
		}
		
		//inspected flag
		add_post_meta( $post->ID, 'inspected', true , true );
		
		//return data
		return $data;
		
			}
	
	function inspect_the_uninspected() {
	
		//remove time limit
		set_time_limit( 0 );
	
		global $wpdb;
		$domains = $wpdb->get_col( "SELECT id FROM wp_posts WHERE wp_posts.post_type = 'domain' AND id NOT IN (SELECT post_id from wp_postmeta WHERE meta_key = 'inspected')" );
				
		foreach ($domains as $domain) 
			$this->inspect( $domain );
	
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
			$list = get_the_term_list( $post->ID, $ct);
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