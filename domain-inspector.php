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
		
		
	}

}

require_once( 'site-inspector/class-site-inspector.php' );
new SiteInspector;
new DomainInventory;


?>