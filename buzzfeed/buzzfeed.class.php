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

/**
 * The BuzzFeed API Class. 
 * 
 * This class fetches the latest buzzes from buzzfeed.com and makes them accessible easily to you. 
 * You can choose to either use the getBuzzes() function to use this information and reuse it somewhere 
 * else, or just use the getHTML()/outputHTML() functions to generate ready-to-go html, formatted by a template file. 
 * 
 * Optionally the buzzes can also be cached, so they don't have to be fetched from the server every time. 
 * 
 * 
 * Here's a quick example how to use this class: 
 * 
 * 		$bf = new BuzzFeed( "my-config.php" );  // my-config.php contains all the settings like image size, categories, etc.  
 * 		$bf->outputHTML();                      // fetch buzzes, format using the template specified in config and output
 * 
 * It's that easy! 
 */
class BuzzFeed{
	// Config Array
	var $config; 
	
	// whats buzzfeed-apis working directory? 
	var $dir; 
	
	// Path to the cache file
	var $cacheFile; 
	
	// Path to the config file
	var $configFile; 
	
	// Helps us parse the xml files
	var $rootNode, $currentNode, $nodeStack; 
	
	// Data already parsed? 
	var $parsedXML; 
	
	/**
	 * Initialize a buzzfeed object, 
	 * 
	 * The parameter $config can be either 
	 * 
	 * - left empty
	 *   the file "config-default.php" will be used
	 * 
	 * - a file name 
	 *   the configuration will be loaded from the file
	 * 
	 * - an array 
	 *   the configuration will be taken from the array values
	 * 
	 * @public 
	 */
	function Buzzfeed( $conf = "config.php" ){
		$this->dir = dirname( __FILE__ ) . "/";
		
		if( is_array( $conf ) ){
			$this->config = $conf;
			$this->configFile = NULL; 
			 
		}
		else{
			$this->configFile = $this->locateFile( $conf, true ); 
			
			if( !file_exists( $this->configFile ) ){
				$this->error( "The specified config file [{$this->configFile}] doesn't exist" );
				die(); 
			}
			else{
				$this->config = include( $this->configFile );
			}
		} 
		
		$this->cacheFile = $this->locateFile( $this->config[ "cache_file"] ); 
	}
	
	
	/**
	 * Outputs the current buzzes. 
	 * @public 
	 */
	function outputHTML(){
		$buzzes = $this->getBuzzes();
		$rows = $this->config[ "rows" ];
		$cols = $this->config[ "cols" ]; 
		$config = $this->config; 
		$templateFile = $this->locateFile( $this->config[ "template_file"], true ); 
		$bf = $this; 
		
		if( file_exists( $templateFile ) ){
			include( $templateFile );
		}
		else{
			$this->error( "Template file [<i>$templateFile</i>] could not be found. " );
		}
	}
	
	
	/**
	 * Returns the HTML with current buzzes so you can modify it before 
	 * sending it to the browser.
	 * 
	 * @public 
	 */
	function getHTML(){
		$buzzes = $this->getBuzzes();
		$rows = $this->config[ "rows" ];
		$cols = $this->config[ "cols" ]; 
		$config = $this->config; 
		$templateFile = $this->locateFile( $this->config[ "template_file" ], true );
		$bf = $this; 
		 
	    if( file_exists( $templateFile ) ){
    	    ob_start();
        	include( $templateFile );
        	$contents = ob_get_contents();
        	ob_end_clean();
        	return $contents;
	    }
		else{
			return "Template file [<i>{$templateFile}</i>] could not be found. ";
		}
	}
	
	
	/**
	 * Gets the current list of buzzes (from internet/cache) and returns them as 
	 * two dimensional array (as $rows and $cols specify in the config file).
	 * 
	 * @public 
	 */ 
	function getBuzzes(){
		$xml = $this->getXML(); 
		$buzzes = array();
		if($xml && $xml[ "buzz" ]){
			foreach( $xml[ "buzz" ] as $i => $buzz ){
				$newBuzz = array(); 
			
				foreach( $buzz as $name => $entry ){
					if( is_array( $entry ) ){
						$newBuzz[ $name ] = @$entry[ 0 ][ "_text" ]; 
					}
				}
			
				$buzzes[] = $newBuzz; 
			}
		
			return $this->formatArray( $buzzes ); 
		}
		
		return array();
	}
	
	
	/**
	 * Get the xml, either from cache, or download a fresh copy
	 * 
	 * @public 
	 */
	function getXML(){
		// We only update once a cycle
		if( is_array( $this->parsedXML ) ){
			return $this->parsedXML; 
		}
		// Are we using caching? 
		else if( $this->config[ "caching"] == true ){
			// If the cache file doesn't exist we should definitely create it! 
			if( !file_exists( $this->cacheFile ) )
				$this->updateCache(); 
			
			// The cache should also be updated if it's older than the maximum allowed time. 
			else if( time() - filemtime( $this->cacheFile ) > $this->config[ "cache_lifetime" ]*60 )
				$this->updateCache(); 
			
			// If the cache file is modified in the future, there's usually some strangeness that happened. We update the cache file too. 
			else if( filemtime( $this->cacheFile ) > time() )
				$this->updateCache(); 
			
			// If the configuration file is newer than our cache, then the cache should be updated, but only IF there is a config file 
			// (see initializer, you don't have to provide a config file, it can also  be an array). 
			else if( $this->configFile != NULL && filemtime( $this->cacheFile ) < filemtime( $this->configFile ) )
				$this->updateCache(); 
			
			
			// What if the cache file couldn't be created? 
			if( !file_exists( $this->cacheFile ) ){
				// if it couldn't be created ther must have already been an error displayed further above. 
				$xmlStr = "";  
			}
			else{
				$xmlStr = $this->readCache(); 
			} 
		}
		else{
			// What if the RSS couldn't be downloaded? well, its gonna throw out some error i guess
			$xmlStr = $this->download(); 
		}
		
		$this->parsedXML = $this->xmlstrToArray( $xmlStr ); 
		return $this->parsedXML; 
	}
	
	
	/**
	 * Force a cache update. 
	 * 
	 * @public
	 */
	function updateCache(){
		$cache_file = @fopen( $this->cacheFile, "w" );
		if( !$cache_file ){
			$this->error( "Cache file [<i>{$this->cacheFile}</i>] cannot be written." ); 
			return;  
		}
		
		@flock( $cache_file, LOCK_EX );
		fwrite( $cache_file, $this->download() ); 
		
		fclose( $cache_file ); 
	}
	
	
	/**
	 * Returns the contents of the cache file, also makes  sure file locking is ok. 
	 * @public 
	 */
	function readCache(){
		$cache_file = fopen( $this->cacheFile, "r" );
		
		// This method automatically blocks until the cache file is written
		// (just in case it's being written at the exact same moment)
		flock( $cache_file, LOCK_SH );
		$str = file_get_contents( $this->cacheFile );
		fclose( $cache_file ); 
		
		return $str;
	}
	
	
	/**
	 * Download the latest buzzes from the internet and return the xml 
	 * 
	 * @private
	 */
	function download(){
		$small = $this->config[ "image_size" ] == "small"? 1:0; 
		$tame = $this->config[ "tame" ] == true? 1:0; 
		$cats = ""; 
		if( $this->config[ "categories" ] != "all" ){
			$cats = "c=" . implode( "&c=", split( ",", $this->config[ "categories"] ) );
		}
		$url = 
			"/wd/Widget?". 
			"&rows={$this->config['rows']}". 
			"&small={$small}".
			"&tame={$tame}". 
			"&xml=1".
			"&{$cats}";
  
		
		// Load the xml data... 
		$fp = @fsockopen( "ct.buzzfeed.com", 80, $errno, $errstr, 30);
		if (!$fp) {
			return "";  
		}
		
	    $out = "GET {$url} HTTP/1.0\r\n";
   		$out .= "Host: ct.buzzfeed.com\r\n";
   		$out .= "Connection: Close\r\n\r\n";
   		
    	fwrite( $fp, $out );
	    while ( !feof( $fp ) ) {
        	$in .= fgets( $fp, 10000 );
	    }
	    // remove the http return header
	    list( $a, $in ) = split( "\n\n", str_replace( "\r", "", $in ), 2 ); 
    
    	fclose( $fp );
   	
		

    	return $in; 
	}
	
	
	/**
	 * Parse some xml String and make an array out of it. 
	 * @private
	 */
	function xmlstrToArray( $in ){
		// PHP4 and PHP5 don't have a common method of accessing DOM objects, 
		// So we'll have to use the parser directly... whatever..., it's gonna be okay
		GLOBAL $bfParsing; 
		
		$this->rootNode = array(); 
		$this->currentNode = array(); 
		$this->nodeStack = array();
		$bfParsing = $this;
		
		if( !function_exists( "BF_startElement" ) ){
			function BF_startElement( $parser, $name, $attrs ){
				GLOBAL $bfParsing;
				
				$me = array();
				$me[ "_name" ] = strtolower( $name );
				if( sizeof( $bfParsing->rootNode ) == 0 ){
					$bfParsing->rootNode = &$me;
				}
				else{
					if( !isset( $bfParsing->currentNode[ strtolower( $name ) ] ) )
						$bfParsing->currentNode[ strtolower( $name ) ] = array();
	
					$bfParsing->currentNode[ strtolower( $name ) ][] =  &$me;
				}  
				
				$bfParsing->nodeStack[] = &$me;
				$bfParsing->currentNode = &$me; 
			}
			
			function BF_endElement( $parse, $name ){
				GLOBAL $bfParsing;
						
				array_pop( $bfParsing->nodeStack );
				$bfParsing->currentNode = &$bfParsing->nodeStack[sizeof( $bfParsing->nodeStack) - 1];
			}
			
			function BF_data( $parser, $data ){
				GLOBAL $bfParsing;
				
				// ignore whitespace!
				if( trim( $data ) != "" ){
					if( isset( $bfParsing->currentNode[ "_text" ] ) )  
						$bfParsing->currentNode[ "_text" ] = $bfParsing->currentNode[ "_text" ] . $data;
					else
						$bfParsing->currentNode[ "_text" ] = $data;
				}  
			}
		}; 
		
		$xml_parser = xml_parser_create();
		xml_set_element_handler($xml_parser, "BF_startElement", "BF_endElement");
		xml_set_character_data_handler( $xml_parser, "BF_data" ); 
		xml_parse( $xml_parser, $in ); 
		xml_parser_free($xml_parser);
		
		return( $bfParsing->rootNode ); 
	}
	
	
	
	/**
	 * "Format" a flat array to fit in the $cols x $rows two dimensional array. 
	 * This way it's easier to write the templates. 
	 * 
	 * @private
	 */
	function formatArray( $buzzes ){
		$newBuzzes = Array(); 
		
		for( $i = 0; $i < $this->config[ "rows" ]; $i ++ ){
			$row = Array(); 
			for( $j = 0; $j < $this->config[ "cols" ]; $j++ ){
				$row[] = $buzzes[ $i*$this->config[ "cols"] + $j ]; 
			}
			
			$newBuzzes[] = $row; 
		}
		
		return $newBuzzes; 
	}
	
	
	/**
	 * Output a simple error message
	 * 
	 * @private
	 */
	function error( $msg ){
		print( "<span style='color: red; background-color: white; font-family: fixed; font-size: 12px; font-weight: bold; '>Buzzfeed API: $msg</span><br/>" ); 
	}
	
	/**
	 * Output a simple error message TO THE LOG
	 * 
	 * @private
	 */
	function stderror( $msg ){
		trigger_error( $msg ); 
	}
	
	/**
	 * Finds a file. 
	 * 
	 * First looks in this folder, then in the working directory.
	 *  
	 * If neither of them exist you use the parameter $forceApiDir 
	 * to select which pathname you want to have returned. If you use
	 * $forceApiDir it will be the folder the buzzfeed class file is in,
	 * else the current working directory. 
	 * 
	 * @public
	 */
	function locateFile( $fileName, $preferApiDir = false ){
		if( file_exists( dirname( __FILE__ ) . "/" . $fileName ) ){
			return dirname( __FILE__ ) . "/" . $fileName; 
		}
		else if( file_exists( $fileName ) ){
			return $fileName; 
		}
		else if( $preferApiDir == true ){
			return dirname( __FILE__ ) . "/" . $fileName; 
		}
		else{
			return $fileName; 
		}
	}
}


?>