<?php
/*
Plugin Name: Domain Inventory
Plugin URI: 
Description: 
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

class DomainInventory {
	
	static $instance;
	static $inspector;
	
	function __construct() {
		
		self::$instance = $this;
		
		//grab site inspector
		require_once( 'site-inspector/class-site-inspector.php' );
		self::$inspector = new SiteInspector;

		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'init', array( $this, 'register_cts' ) );

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
    'menu_name' => 'Domains'

  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'page',
    'has_archive' => true, 
    'hierarchical' => true,
    'menu_position' => null,
    'supports' => array('title', 'comments', 'custom-fields','page-attributes')
  ); 
  register_post_type('domain',$args);
	
	}
	
	function register_cts() {
	
		$cts = array(
			'status'=>array( 
				'singluar' => 'Server Status', 
				'plural' => 'Server Statuses',
			),
			'non-www'=>array( 
				'singluar' => 'Non-www. Support', 
				'plural' => 'Non-www. Support',
			),
			'ipv6'=>array( 
				'singluar' => 'IPv6 support', 
				'plural' => 'IPv6',
			),
			'cdn'=>array( 
				'singluar' => 'CDN Provider', 
				'plural' => 'CDN Providers',
			),
			'ga'=>array( 
				'singluar' => 'Google Apps', 
				'plural' => 'Google Apps',
			),
			'cms'=>array( 
				'singluar' => 'CMS', 
				'plural' => 'CMSs',
			),
			'server'=>array( 
				'singluar' => 'Server Software', 
				'plural' => 'Servers Software',
			),
			'cloud'=>array( 
				'singluar' => 'Cloud Provider', 
				'plural' => 'Cloud Providers',
			),
			'analytics'=>array( 
				'singluar' => 'Analytics Source', 
				'plural' => 'Analytics Sources',
			),
			'scripts'=>array( 
				'singluar' => 'Script Library', 
				'plural' => 'Scripts Libraries',
			),
		);
		
		foreach ( $cts as $ct=>$names ) {
					  
			 $labels = array(
				'name' => _x( $names['singular'], 'taxonomy general name' ),
				'singular_name' => _x( $names['singular'], 'taxonomy singular name' ),
				'search_items' =>  __( 'Search ' . $names['plural'] ),
				'all_items' => __( 'All ' . $names['plural'] ),
				'parent_item' => __( 'Parent ' . $names['singular'] ),
				'parent_item_colon' => __( 'Parent ' . $names['singular']. ':' ),
				'edit_item' => __( 'Edit ' . $names['singluar'] ), 
				'update_item' => __( 'Update ' . $names['singluar'] ),
				'add_new_item' => __( 'Add New ' . $names['singluar']),
				'new_item_name' => __( 'New ' . $names['singluar'] .' Name' ),
				'menu_name' => __( $names['singluar'] ),
			  ); 	
			 
			register_taxonomy( $ct, 'domain', array( 'labels' => $labels ) );
		}
	}
	
}


new DomainInventory;


?>