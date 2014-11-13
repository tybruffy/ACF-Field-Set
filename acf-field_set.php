<?php

/*
Plugin Name: Advanced Custom Fields: Field Set
Plugin URI:  https://github.com/tybruffy/ACF-Field-Set
ACF Field that lets you add sub fields like a repeater.: ACF Field that lets you add sub fields like a repeater.
Version: 1.0.0
Author: Tyler Bruffy
Author URI: https://github.com/tybruffy/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/




// 1. set text domain
// Reference: https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
load_plugin_textdomain( 'acf-field_set', false, dirname( plugin_basename(__FILE__) ) . '/lang/' ); 




// 2. Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_field_set( $version ) {
	
	include_once('acf-field_set-v5.php');
	
}

add_action('acf/include_field_types', 'include_field_types_field_set');	




// 3. Include field type for ACF4
function register_fields_field_set() {
	
	include_once('acf-field_set-v4.php');
	
}

add_action('acf/register_fields', 'register_fields_field_set');	



	
?>