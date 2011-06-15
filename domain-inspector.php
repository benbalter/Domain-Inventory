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
	
	function __construct() {
		self::$instance = $this;
		
		add_action( 'init', array( $this, 'register_cpt' ) );
		
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
	
}

require_once( 'site-inspector/class-site-inspector.php' );
new SiteInspector;
new DomainInventory;


?>