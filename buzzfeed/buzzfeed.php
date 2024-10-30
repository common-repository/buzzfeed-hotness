<?php
// "Copyright 2007 Contagious Media, LLC"
//
// This file is part of the BuzzFeed Widget Builder 
//
// The BuzzFeed Widget Builder is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 3 of the License, or 
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/ >.

/*
Plugin Name: BuzzFeed
Plugin URI: http://www.buzzfeed.com
Description: Show the latest web buzz on your site! Go to <b>Options &raquo; BuzzFeed</a> to configure me!
Author: Hansi, Eric
Version: 1.0.7
Author URI: http://www.buzzfeed.com
*/ 

/**
 * This is THE function, you will want to use this to  
 * display buzzfeed in the template. Just like this: 
 * <?php do_action( "showBuzzes" ); ?>
 */
function showBuzzes() {
    require_once( dirname( __FILE__ ) . "/buzzfeed.class.php" );
    
    $bf = new BuzzFeed( constructBuzzFeedConfig() );
    $bf->outputHTML();
    
    // See if we have the newest version... 
    if( $bf->parsedXML[ "wp_v" ][ 0 ][ "_text"] > get_option( "bf.newest_version" ) ){
    	// So apperently some people had trouble with the upate notifier. 
    	// Looking at the code i can't really see how that happens - well, we'll just 
    	// disable it anyway. 
    	/*$blog_title = get_option( "blogname" );
    	$message  = "There is a new version of the buzzfeed widget available (Version " . $bf->parsedXML[ "wp_v" ][ 0 ][ "_text"] . ") \n";
    	$message .= "You can see whats new and how to update on \n"; 
    	$message .= "http://wordpress.org/extend/plugins/buzzfeed-hotness/\n\n"; 
    	$message .= "If you don't want to update you can safely ignore this message. \n\n";
    	$message .= "- The BuzzFeed Team, http://www.buzzfeed.com"; 
    	$message_headers = 'From: WordPress <wordpress@' . $_SERVER['SERVER_NAME'] . '>';
    	
    	@wp_mail(get_option( "admin_email" ), "$blog_title: widget update available", $message, $message_headers);*/
    	update_option( "bf.newest_version", $bf->parsedXML[ "wp_v" ][ 0 ][ "_text" ] );
    }
}

// Create the action hook
add_action( "showBuzzes", "showBuzzes", 1 ); 


/**
 * Adds buzzfeed to the meta informtation (if the plugin is configured to use the sidebar)
 * 
 * Here's the story: if the wordpress installation is older than 2.2 the function
 * register_sidebar_widget will not exist, we will therefore attach it to the meta 
 * information. 
 */
function addBuzzFeedToMeta(){
	if( get_option( "bf.sidebar") == true && !function_exists( register_sidebar_widget ) ){
		echo "<br/>"; 
		showBuzzes();
	} 
}

// Attach BuzzFeed widget to the meta information
add_action('wp_meta', 'addBuzzFeedToMeta');


/**
 * Add the BuzzFeed widget
 */
function addBuzzFeedWidget() {

	if ( !function_exists('register_sidebar_widget') || get_option( "bf.sidebar" ) == false )
		return;

	// This is the function that outputs our little Google search form.
	function showBuzzFeedWidget($args) {

		extract($args);
		echo "<style>#buzzfeed-widget .buzzfeed{ margin: auto; }</style>";
		echo $before_widget;
		showBuzzes(); 
		echo $after_widget;
	}

	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget('BuzzFeed Widget', 'showBuzzFeedWidget');
}

// Run our code later in case this loads prior to any required plugins.
add_action('plugins_loaded', 'addBuzzFeedWidget');




/**
 * Add some configuration options for the backend
 */
function addBuzzFeedOptions(){
	include( dirname( __FILE__ ) . "/option-panel.php" ); 
	add_options_page( 'Customize BuzzFeed', 'BuzzFeed', 0, "buzzfeed.php", 'showBuzzFeedConfig');
}

// This is executed only if the admin menu is there... 
add_action('admin_menu', 'addBuzzFeedOptions');

#

/**
 * Create BuzzFeed Configuration from wordpress Options
 */
function constructBuzzFeedConfig(){
	// First make sure the cache file exists and is writable
	$cacheFile = get_option( "bf.cache_file" ); 
	
	// We try enabling the cache every single time, until it works! 
	if( get_option( "bf.caching" ) == false ){
		update_option( "bf.caching", true ); 
	}
	
	if( $cacheFile == "" || !is_writable( $cacheFile ) ){
		$cacheFile = tempnam( "", "buzzfeed-cache-" );
		if( !is_writable( $cacheFile ) ){
			if( is_writable( dirname( __FILE__ ) . "/cache.php" ) ){
				update_option( "bf.caching", true ); 
				update_option( "bf.cache_file", realpath( dirname( __FILE__ ) . "/cache.php" ) ); 
			}
			else{
				update_option( "bf.caching", false );
			} 
		}
		else{
			update_option( "bf.caching", true ); 
			update_option( "bf.cache_file", $cacheFile );
			
			// This might feel wrong, but we have to delete the cache file again, 
			// so that it actually gets regenerated by the buzzfeed.class.php
			unlink( $cacheFile ); 
		}
	}
	
	return array(
		'template_file' => 'template.php', 

		'rows' => (int)get_option( "bf.rows" ), 
		'cols' => (int)get_option( "bf.cols" ),
		'image_size' => get_option( "bf.image_size" ),
		'categories' => get_option( "bf.categories" ),
		'caching' => (bool)get_option( "bf.caching" ), 
		'cache_lifetime' => (int)get_option( "bf.cache_lifetime" ),
		'cache_file' => get_option( "bf.cache_file" ), 
		'tame' => (bool)get_option( "bf.tame" ),

		'mainBg' => get_option( "bf.mainBg" ), 
		'mainText' => get_option( "bf.mainText" ), 
		'headBg' => get_option( "bf.headBg" ), 
		'headText' => get_option( "bf.headText" ),
		
		'sidebar' => (bool)get_option( "bf.sidebar" )
	);
}


/**
 * Create the buzzfeed options...
 */
function createBuzzFeedOptions(){
	if( get_option( "bf.version" ) != "1.0.7" ){
		// BuzzFeeds Options/Defaults
		add_option( "bf.version", "1.0.7", "BuzzFeed: Version", "no" );
		add_option( "bf.newest_version", "1.0.7", "BuzzFeed: Newest (online) Version", "no" ); 
		update_option( "bf.version", "1.0.7" );
		update_option( "bf.newest_version", "1.0.7" );
		
		add_option( "bf.rows", 1, "BuzzFeed: Number of Rows", "no" ); 
		add_option( "bf.cols", 1, "BuzzFeed: Number of Columns", "no" );
		add_option( "bf.image_size", "large", "BuzzFeed: Image Size", "no" ); 
		add_option( "bf.categories", "all", "BuzzFeed: Subscribed Categories", "no" );
		add_option( "bf.caching", true, "BuzzFeed: Enable Caching", "no" ); 
		add_option( "bf.cache_file", "", "BuzzFeed: Cache File Location", "no" ); 
		add_option( "bf.cache_lifetime", 6, "BuzzFeed: Cache Lifetime in Minutes", "no" );
		add_option( "bf.tame", false, "BuzzFeed: Only show tame content", "no" ); 
		add_option( "bf.mainBg", "#dddddd", "BuzzFeed: Main Background Color", "no" );
		add_option( "bf.headBg", "#e32", "BuzzFeed: Header/Footer Background Color", "no" );
		add_option( "bf.mainText", "#0077ee", "BuzzFeed: Main Text Color", "no" ); 
		add_option( "bf.headText", "#ffffff", "BuzzFeed: Header/Footer Text Color", "no" ); 
		add_option( "bf.sidebar", true, "BuzzFeed: Display BuzzFeed in Sidebar", "no" );
		add_option( "bf.cache_file", "", "BuzzFeed: Cache File Location", "no" ); 
	}
}


// Make sure buzzfeeds options are in the database
createBuzzFeedOptions();