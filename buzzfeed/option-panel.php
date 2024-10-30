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
 * This file is responsible for configuring the options of the wp plugin. 
 * 
 * Hints: 
 * - Don't let the pathnames confuse you - this file is _exclusively_ being included by wp-admin/options-general.php, 
 *   thats why we're referring to the config files as ../wp-content/plugins/buzzfeed/*
 * 
 * - HTML Elements here are usually prefixed with bf, for instance "bfColorPicker"
 *   Javascript functions aren't prefixed, for instance setColor()
 *   Form fields that save options aren't prefixed either, for instance ...id="tame" value="true"...
 * 
 */ 


/**
 * Output configuration
 */
function showBuzzFeedConfig(){
	// this is also called we only wanna save...
	if( $_POST[ "bfSave" ] == "true" ){
		saveBuzzFeedConfig(); 
	}
	
	// The constructBuzzFeedConfig() function loads the config 
	// from the wordpress database. it can be found in buzzfeed.php
	$config = constructBuzzFeedConfig(); 
	
	if( $config[ "caching" ] == false ){
		?>
		<div id="message_no_cache" class="error fade">
			<p><b>Your system temp directory is not writeable, therefore caching will not work.</b></p>
			<p>Steps required to fix caching: </p>
			<ol>
				<li>Use your ftp client and connect to your webpage</li>
				<li>Change directory to <i>wordpress</i>/wp-content/plugins/buzzfeed </li>
				<li>Change the permission of cache.php to 777</li>
				<li>Reload the page</li>
			</ol> 
		</div>
		
		<?php
	}
	
	// warn if theres a newer version
	
	if( get_option( "bf.newest_version" ) > get_option( "bf.version" ) ){
		echo "<div class='updated fade' id='bf_newversion'><p>There is a newer version of the buzzfeed widget available. <a href='http://wordpress.org/extend/plugins/buzzfeed-hotness/'>Get it now!</a></p></div>";
	}
	
	echo "<div class='wrap'>"; 
	echo "<h2>BuzzFeed</h2>"; 
	?>
	<style>
		#bfForm .light{
			background-color: #eeeeee; 
		}
		
		#bfForm .dark{
			background-color: #cccccc; 
		}
		
		#bfForm .sizeTable td.light:hover, #bfForm td.dark:hover,  
		#size_small:hover, #size_large:hover{
			background-color: #e32;
			color: white; 
		} 
		
		#bfColorPicker .color{
			margin-right: 5px; 
			width: 25px; 
			height: 20px;
			float: left;  
		}
		
		#bfAppearance input, #bfAppearance label{
			line-height: 1.8em; 
			margin-right: 5px; 
		}
		
		#bfColorPicker label{
			width: 150px; 
			display: block; 
			float: left; 
		}
		
		#bfColorPicker input.text{
			width: 60px; 
		}
		
    h3 { font: bold 20px Georgia,serif; margin: 10px 0 15px;  padding-bottom: 3px; border-bottom: 1px solid #aaa; }
		h4 { margin: 0 0 7px; }
		
	</style>
	<form method="post" id="bfForm" target="bfPreview" action="../wp-content/plugins/buzzfeed/preview.php">
		<p class="submit" style="margin-bottom: 0px; ">
			<input type="submit" name="Submit" onclick="document.getElementById( 'bfForm' ).target = ''; document.getElementById( 'bfForm' ).action = ''; " value="Update Options &raquo;"/>
		</p>
		<fieldset class="options" id="bfAppearance">
			<div style="float: left; display: block; margin-right: 60px; " id="bfDesign">
				<h3>Design your widget! / <a href="#" style="text-decoration: none; border: 0px solid white;" onclick="document.getElementById( 'bfDesign' ).style.display = 'none'; document.getElementById( 'bfTemplateEditor' ).style.display = 'block';safariWarning(); return false">Edit Template <small>(Advanced)</small></a></h3>
				<div style="float: left;">
					<h4>Click the grid!</h4>
					<table border="0" cellspacing="2" cellpadding="2" class='sizeTable' title="Click to choose size" style="cursor: pointer;">
						<tr>
							<td width="25" height="25" class="light" id="grid.1.1" onclick="setGridSize( 1, 1 ); ">&nbsp;</td>
							<td width="25" height="25" class="light" id="grid.1.2" onclick="setGridSize( 1, 2 ); ">&nbsp;</td>
							<td width="25" height="25" class="light" id="grid.1.3" onclick="setGridSize( 1, 3 ); ">&nbsp;</td>
							<td width="25" height="25" class="light" id="grid.1.4" onclick="setGridSize( 1, 4 ); ">&nbsp;</td>
						</tr>
						<tr>
							<td width="25" height="25" class="light" id="grid.2.1" onclick="setGridSize( 2, 1 ); ">&nbsp;</td>
							<td width="25" height="25" class="light" id="grid.2.2" onclick="setGridSize( 2, 2 ); ">&nbsp;</td>
							<td width="25" height="25" class="light" id="grid.2.3" onclick="setGridSize( 2, 3 ); ">&nbsp;</td>
							<td width="25" height="25" class="light" id="grid.2.4" onclick="setGridSize( 2, 4 ); ">&nbsp;</td>
						</tr>
						<tr>
							<td width="25" height="25" class="light" id="grid.3.1" onclick="setGridSize( 3, 1 ); ">&nbsp;</td>
							<td width="25" height="25" class="light" id="grid.3.2" onclick="setGridSize( 3, 2 ); ">&nbsp;</td>
							<td width="25" height="25" class="light" id="grid.3.3" onclick="setGridSize( 3, 3 ); ">&nbsp;</td>
							<td></td>
						</tr>
						<tr>
							<td width="25" height="25" class="light" id="grid.4.1" onclick="setGridSize( 4, 1 ); ">&nbsp;</td>
							<td width="25" height="25" class="light" id="grid.4.2" onclick="setGridSize( 4, 2 ); ">&nbsp;</td>
							<td></td>
							<td></td>
						</tr>
					</table>
				</div>
				<div style="float: left; margin-left: 30px; ">
					<h4>Image Size</h4>
					
					<div id="size_large" class="light" style="cursor: pointer; width: 115px; height: 73px; padding: 5px; float:left; margin-right:20px" onclick="setImageSize( 'large' ); ">
						Large
					</div>

					<div id="size_small" class="light" style="cursor: pointer; width: 80px; height: 50px; padding: 5px; float:left; " onclick="setImageSize( 'small' ); ">
						Small
					</div>
					
				</div>
			
				<br clear="all"/>
				<br/>
				
				<div id="bfColorPicker">
					<h4>Choose a Color Scheme</h4>
					
					<div class="color" onclick="setColor('#eeeeee','#0077ee','#ee3322','#eeeeee');" style="background:#e32;">&nbsp;</div>
					<div class="color" onclick="setColor('#eeeeee','#666666','#999999','#eeeeee');" style="background:#aaa;">&nbsp;</div>
					<div class="color" onclick="setColor('#444444','#eeeeee','#333333','#888888');" style="background:#444;">&nbsp;</div>
					<div class="color" onclick="setColor('#eeeef7','#2255aa','#3366aa','#eeeeee');" style="background:#36a;">&nbsp;</div>
					<div class="color" onclick="setColor('#fedbff','#b24aa9','#9d298b','#eeeeee');" style="background:#b24aa9;">&nbsp;</div>
					
					<a href="#" onclick="el = document.getElementById( 'advanced_color' ); el.style.display = el.style.display == 'block'?'none':'block'; return false;">Advanced</a> 
					<br clear="all"/>		
					<div id="advanced_color" style="display:none">
						<div class="subsection" style="margin-top:5px;">
							<div class="widget-color"><label for="mainBg">Background</label><input class="text" type="text" onblur="setColor();" name="mainBg" value="#eeeeee" id="mainBg" maxlength="7" style="width:80px" /></div>
							<div class="widget-color"><label for="mainText">Text</label><input class="text" type="text" onblur="setColor();" name="mainText" value="#0077ee" id="mainText" maxlength="7" style="width:80px" /></div>
							<div class="widget-color"><label for="headBg">Header/Footer</label><input class="text" type="text" onblur="setColor();" name="headBg" value="#ee3322" id="headBg" maxlength="7" style="width:80px" /></div>
							<div class="widget-color"><label for="headText">Header/Footer Text</label> <input class="text" type="text" onblur="setColor();" name="headText" value="#eeeeee" id="headText" maxlength="7" style="width:80px" /></div>
						</div>
						<p><input type="button" name="apply_advanced_colors" value="Apply Custom Colors" id="apply_advanced_colors" onclick="setColor();"/></p>
					</div>
				</div>
				<br/>
				
				
				<div>
					<h3>Categories</h3>
					<div style="float: left;">
						<input type="checkbox" name="check_culture" value="Culture" id="check_culture" onclick="updateCategories(this);" /><label for="check_culture">Culture</label><br/>
						<input type="checkbox" name="check_style" value="Style" id="check_style" onclick="updateCategories(this);" /> <label for="check_style">Style</label><br/>
						<input type="checkbox" name="check_politics" value="Politics" id="check_politics" onclick="updateCategories(this);" /> <label for="check_politics">Politics</label><br/>
						<input type="checkbox" name="check_food" value="Food" id="check_food" onclick="updateCategories(this);" /> <label for="check_food">Food</label><br/>
					</div> 
					<div style="float: left; margin-left: 25px; ">
						<input type="checkbox" name="check_movie" value="Movie" id="check_movie" onclick="updateCategories(this);" /> <label for="check_movie">Movie</label><br/>
						<input type="checkbox" name="check_music" value="Music" id="check_music" onclick="updateCategories(this);" /> <label for="check_music">Music</label><br/>
						<input type="checkbox" name="check_tv" value="TV" id="check_tv" onclick="updateCategories(this);" /> <label for="check_tv">TV</label><br/>
						<input type="checkbox" name="check_celebrity" value="Celebrity" id="check_celebrity" onclick="updateCategories(this);" /> <label for="check_celebrity">Celebrity</label><br/>
					</div>
					<div style="float: left; margin-left: 25px; ">
						<input type="checkbox" name="check_business" value="Business" id="check_business" onclick="updateCategories(this);" /> <label for="check_business">Business</label><br/>
						<input type="checkbox" name="check_tech" value="Tech" id="check_tech" onclick="updateCategories(this);" /> <label for="check_tech">Tech</label><br/>
						<input type="checkbox" name="check_science" value="Science" id="check_science" onclick="updateCategories(this);" /> <label for="check_science">Science</label><br/>
						<input type="checkbox" name="check_sports" value="Sports" id="check_sports" onclick="updateCategories(this);" /> <label for="check_sports">Sports</label><br/>
					</div>
					
					<br clear="all"/>
				</div>
				
				<br/>
				<div>
					<input type="checkbox" name="tame" id="tame" onclick="setTame();"/><label for="tame">Only show tame buzz - some of my readers are prudes.</label>
				</div>
				
				<br/>
				<div>
					<h3>Show the widget...</h3>
					
					<input type="radio" id="sidebar_yes" name="sidebar" value="yes" <?php if( $config[ "sidebar" ] ) echo 'checked="checked"'?>/>
					<label for="sidebar_yes" style="font-weight:bold;">In the sidebar</label>
					<div style="padding-left: 23px; width: 350px; margin-bottom:15px">
						<?php
							if( function_exists( "register_sidebar_widget" ) ){
								GLOBAL $wp_registered_sidebars;
								if( is_array( $wp_registered_sidebars ) && empty( $wp_registered_sidebars ) ){
									echo "<span style='color:red;'>Warning! It seems the theme you are using does not support sidebar widgets, it is highly recommended to use the php code below to add this widget to your sidebar manually.</span>";
								}  
								else if( file_exists(  "widgets.php" ) ){
									echo "Go to <a href='widgets.php'>Presentation &raquo; Widgets</a> and drag the widget in the place you wish. ";
									echo "If the widget you designed does not fit in your sidebar, you can always return to this page to make it smaller.  ";
								}
								else{
									echo "Go to <i>Presentation &raquo; Widgets</i> and drag the widget in the place you wish.";
								}
							}
							else{   
								echo "Your installation of wordpress it too old and doesn't ";
								echo "support adding content to the sidebar directly.  "; 
								echo "Instead the latest buzzes will be added to your blog's meta section."; 
							}
						?>
						</div>
					
					<input type="radio" id="sidebar_no" name="sidebar" value="no" <?php if( !$config[ "sidebar" ] ) echo 'checked="checked"'?>/>
					<label for="sidebar_no" style="font-weight:bold;">I&rsquo;ll add it myself</label>
					<div style="padding-left: 23px; width:300px">
						Use the <?php echo file_exists( "theme-editor.php" )? "<a href='theme-editor.php'>theme editor</a>":"theme editor"; ?>
						to cut and paste the following code into your blog&rsquo;s template:
						<code style="display:block;margin-top:10px;padding:4px 6px;background:#ffd;border:1px solid #f6f6cc">&lt;?php do_action('showBuzzes'); ?&gt;</code>
					</div>
				</div>
				
			</div>
			<div id="bfTemplateEditor" style="float: left; display: none; margin-right: 10px;">
				<h3><a href="#" style="text-decoration: none; border: 0px solid white;" onclick="document.getElementById( 'bfDesign' ).style.display = 'block'; document.getElementById( 'bfTemplateEditor' ).style.display = 'none';return false">Design your widget!</a> / Edit Template <small>(Advanced)</small></h3>
				
				<?php
					$filename = dirname( __FILE__ ) . "/template.php"; 
					
					if( !is_writable( $filename ) ){
						echo "<div style='color: red; width: 500px; font-size: 12px; '>Warning: The file " . realpath( $filename ) . " is not writeable. Please use your ftp program to make this file writeable before you save, or your changes will be lost!</div>"; 
					}
				?>
				
				<script>
					var lastChange = 0; 
					
					/**
					 * Safari has a strange textarea.scrollTop bug, 
					 * we wanna warn users about it. 
					 */
					function safariWarning(){
						if( navigator.userAgent.indexOf( "Safari" ) >= 0 ){
							document.getElementById( "templateLineNumbers" ).style.display = "none"; 
							document.getElementById( "template" ).style.width = "500px";
							 
							if( document.cookie.indexOf( "warned=true" ) >= 0 ){
								// okay, the user does already know about the issue
								
							}
							else{
								document.cookie = "warned=true"; 
								alert( "Warning: Safari has a bug that prevents line numbering in the template editor from working correctly. \nPlease use Firefox if you need this feature. " ); 
							}
						}
					}
					 
					/**
					 * We keep track of the last keystroke, after a typing break of one second
					 * the temeplate is auto-previewed. 
					 */
					function templateKeyPressed(){
						if( document.getElementById( "previewTemplate" ).checked == true ){
							document.getElementById( "templateChanged" ).value = "true";
							lastChange = new Date().getTime(); 
							window.setTimeout( "submitTemplate()", 1000 );
						} 
					}
					
					function submitTemplate(){
						if( document.getElementById( "previewTemplate" ).checked == true ){
							if( new Date().getTime() - lastChange > 900 ){
								lastChange = new Date().getTime();
								updatePreview();  
							}
						}
					}
					
					/**
					 * Set the template back to the state when the page was loaded + update preview.  
					 */
					function resetTemplate(){
						document.getElementById( "templateChanged" ).value = "false";
						document.getElementById( "template" ).value = document.getElementById( "template" ).defaultValue;
						updatePreview();  
					}
					
					
					/**
					 * This is to show the current line number of the textarea. 
					 * Unfortunately it's not possible to just set a background image on the textarea, 
					 * cause it won't scroll (works only in ie, ff+safari bug) 
					 */
					function templateScrolled(){
						document.getElementById( "templateLineNumbers" ).style.backgroundPosition= "0px -" + document.getElementById( "template" ).scrollTop + "px"; 
					}
					
					window.setInterval( "templateScrolled(); ", 50 );
					 
					// Here we have some editor love by craig from
					// http://www.webdeveloper.com/forum/showthread.php?t=32317
					// It allows you to use the tab key in textareas, which is usally a huge annoyance 
					function setSelectionRange(input, selectionStart, selectionEnd) {if (input.setSelectionRange) {input.focus();input.setSelectionRange(selectionStart, selectionEnd);}else if (input.createTextRange) {var range = input.createTextRange();range.collapse(true);range.moveEnd('character', selectionEnd);range.moveStart('character', selectionStart);range.select();}}
					function replaceSelection (input, replaceString) {if (input.setSelectionRange) {var selectionStart = input.selectionStart;var selectionEnd = input.selectionEnd;input.value = input.value.substring(0, selectionStart)+ replaceString + input.value.substring(selectionEnd);if (selectionStart != selectionEnd){ setSelectionRange(input, selectionStart, selectionStart + replaceString.length);}else{setSelectionRange(input, selectionStart + replaceString.length, selectionStart + replaceString.length);}}else if (document.selection) {var range = document.selection.createRange();if (range.parentElement() == input) {var isCollapsed = range.text == '';range.text = replaceString;if (!isCollapsed)  {range.moveStart('character', -replaceString.length);range.select();}}}}
					function catchTab(item,e){if(navigator.userAgent.match("Gecko")){c=e.which;}else{c=e.keyCode;}if(c==9){replaceSelection(item,String.fromCharCode(9));setTimeout("document.getElementById('"+item.id+"').focus();",0);return false;}}
				</script>
				<div id="templateLineNumbers" style="float: left; background-image: url( ../wp-content/plugins/buzzfeed/line_numbers.png ); background-repeat: repeat-y; width: 40px; height: 408px; margin-top: 1px; ">
				</div>
				<textarea onkeydown="templateKeyPressed();return catchTab(this,event)" wrap="off" id="template" name="template" style="float: left; width: 460px; height: 400px; font-size: 12px; line-height: 15px; font-family: courier new; color: black; background-color: white; "><?php echo htmlspecialchars( implode( "", file( dirname( __FILE__ ) . "/template.php" ) ) );?></textarea>
				<br clear="all"/>
				
				<div style="width: 250px; padding-left: 40px; float: left; ">
					<input type="checkbox" id="previewTemplate" name="previewTemplate" checked="checked" onclick="updatePreview();"/>
					<input type="hidden" id="templateChanged" name="templateChanged" value="false"/>
					<label for="autoUpdate">Auto preview changes</label>
				</div>
				<div style="width: 224px; float: left; text-align: right; ">
					<input type="button" value="Reset Template File" onclick="resetTemplate();"/>
				</div>
				<br clear="all"/>
			</div>
			
			<div style="float: left;" id="bfPreviewDad">
				<h3 style="width: 180px;">Live Preview</h3>
				<div>
				<div style="position: absolute; width: 600px; height: 600px; ">
					<iframe width="320" height="400" id="bfPreview" name="bfPreview" style="border: 0px solid white; padding: 0px; " frameborder="0">
					</iframe>
				</div>
				</div>
			</div>
			
			<br clear="all"/>
			<!-- Settings that are not in form fields by themselves go here... -->
			<input type="hidden" name="cols" id="cols"/>
			<input type="hidden" name="rows" id="rows"/>
			<input type="hidden" name="categories" id="categories"/>
			<input type="hidden" name="image_size" id="image_size"/>
			<input type="hidden" name="bfSave" value="true"/>
			
			<script>
				// Previewing the widget is disabled until all the important settings are here. 
				// That prevents tons of refreshes in the first seconds. 
				var allowUpdatePreview = false; 
				
				/**
				 * Reload the iframe according to the options set
				 */
				function updatePreview(){
					if( !allowUpdatePreview ){
						return false; 
					}
					
					
					document.getElementById( "bfPreviewDad" ).style.height = 
					document.getElementById( "bfPreview" ).style.height = ( 120 + rows * ( image_size == "small"?115:125 ) ) + "px"; 
					document.getElementById( "bfPreview" ).style.width = ( 80 + cols * ( image_size == "small"?110:140 ) ) + "px"; 
					document.getElementById( "bfForm" ).submit(); 
				}
				
				
				/**
				 * Set a new size for the widget, then updates the preview. 
				 */
				function setGridSize( row, col ){
					for( i = 1; i <= 4; i++ ){
						for(  j = 1; j <= 4; j++ ){
							if( document.getElementById( "grid." + i + "." + j ) ){
								if( i <= row && j <= col ){
									document.getElementById( "grid." + i + "." + j ).className = "dark"; 
								}
								else{
									document.getElementById( "grid." + i + "." + j ).className = "light"; 
								}
							}
						}
					}
					
					rows = row; 
					cols = col;
					document.getElementById( "cols" ).value = cols;
					document.getElementById( "rows" ).value = rows;   
					updatePreview(); 
				}
				
				
				/**
				 * Choose image size (large, small), then updates the preview
				 */
				function setImageSize( size ){
					document.getElementById( "size_large" ).className = size == "large"?"dark":"light"; 
					document.getElementById( "size_small" ).className = size == "small"?"dark":"light";
					
					image_size = size;
					document.getElementById( "image_size" ).value = image_size; 
					updatePreview();  
				}
				
				
				/**
				 * Set the color definitions OR reads them from the form fields if no parameter 
				 * passed. 
				 * Also updates the preview. 
				 */
				function setColor( p_mainBg, p_mainText, p_headBg, p_headText ){
					if( p_mainBg == undefined ){
						mainBg = document.getElementById( "mainBg" ).value; 
						mainText = document.getElementById( "mainText" ).value; 
						headBg = document.getElementById( "headBg" ).value; 
						headText = document.getElementById( "headText" ).value; 
					}
					else{
						document.getElementById( "mainBg" ).value = mainBg = p_mainBg; 
						document.getElementById( "mainText" ).value = mainText = p_mainText; 
						document.getElementById( "headBg" ).value = headBg = p_headBg; 
						document.getElementById( "headText" ).vallue = headText = p_headText; 
					}
					
					updatePreview(); 
				}
				
				
				/**
				 * Read the categories from the checkbox and save them in the "categories" form field, 
				 * also updates the preview. 
				 */
				function updateCategories(){
					categories = ""; 
					if( document.getElementById( "check_celebrity" ).checked ) categories += "celebrity,"; 
					if( document.getElementById( "check_business" ).checked ) categories += "business,"; 
					if( document.getElementById( "check_politics" ).checked ) categories += "politics,"; 
					if( document.getElementById( "check_science" ).checked ) categories += "science,"; 
					if( document.getElementById( "check_culture" ).checked ) categories += "culture,"; 
					if( document.getElementById( "check_sports" ).checked ) categories += "sports,"; 
					if( document.getElementById( "check_movie" ).checked ) categories += "movie,"; 
					if( document.getElementById( "check_style" ).checked ) categories += "style,"; 
					if( document.getElementById( "check_music" ).checked ) categories += "music,"; 
					if( document.getElementById( "check_tech" ).checked ) categories += "tech,"; 
					if( document.getElementById( "check_food" ).checked ) categories += "food,"; 
					if( document.getElementById( "check_tv" ).checked ) categories += "tv,"; 
					
					document.getElementById( "categories" ).value = categories; 
					updatePreview(); 
				}
				
				
				/**
				 * Set tame to true/false, 
				 * you guessed it - this function updates the preview too.  
				 */
				function setTame( state ){
					if( state != undefined ){
						document.getElementById( "tame" ).checked = state; 
						tame = state;
					}
					
					updatePreview(); 
				}
				
				
				// These values come from the current config
				setImageSize( "<?php echo $config[ "image_size" ]?>" ); 
				setGridSize( <?php echo $config[ "rows" ] . ", " . $config[ "cols" ]?> ); 
				setColor( <?php echo "'{$config['mainBg']}', '{$config['mainText']}', '{$config[ "headBg" ]}', '{$config['headText']}'"; ?> );
				setTame( <?php echo $config[ "tame" ] ?> ); 
				 
				<?php
					// the categories are a bit stupid...
					$cats = split( ",", $config[ "categories" ] );
					if( $config[ "categories" ] == "all" ){
						$cats = array( "celebrity", "business", "politics", "science", "culture", "sports", "movie", "music", "tech", "food", "tv", "style" ); 
					}
					
					foreach( $cats as $i => $cat ){ 
						echo "if( document.getElementById( 'check_{$cat}' ) ) document.getElementById( 'check_{$cat}' ).checked = true; \n";
					}
				?>
				updateCategories(); 
				allowUpdatePreview = true; 
				updatePreview(); 
			</script>
		</fieldset>
		<br/>
		<!--  <fieldset class="options">
			<legend>
				<input type="checkbox" id="caching" name="caching" <?php if( $config[ "caching" ] == true ) echo 'checked="checked"'; ; ?>/>
				<label for="caching">Use Cache</label>
			</legend>
			Caching can improve your site load time. Instead of fetching data from buzzfeed.com everytime it will be stored in a cache, that 
			is only updated when necessary. It is suggested to enable caching. 
			<br/>
			<br/>
			<input type="text" id="cache_lifetime" name="cache_lifetime" value="<?php echo $config[ "cache_lifetime" ]; ?>"/>
			<label for="cache_lifetime">minutes Cache Lifetime</label>
		</fieldset>-->
		
		<p class="submit">
			<input type="submit" name="Submit" onclick="document.getElementById( 'bfForm' ).target = ''; document.getElementById( 'bfForm' ).action = ''; " value="Update Options &raquo;"/>
		</p>
	</form>
		
	<?php
}


/**
 * Save config...
 */
function saveBuzzFeedConfig(){
	update_option( "bf.version", "1.0.7" ); 
	update_option( "bf.rows", (int)$_POST['rows'] ); 
	update_option( "bf.cols", (int)$_POST['cols'] );
	update_option( "bf.image_size", ($_POST['image_size'] == "small"?"small":"large" ) ); 
	update_option( "bf.categories", preg_replace( "/[^a-zA-Z0-9,]/", "", $_POST['categories'] ) );
	//update_option( "bf.caching", $_POST[ "caching"]=="on"?true:false ); 
	//update_option( "bf.cache_lifetime", (int)$_POST['cache_lifetime'] );
	update_option( "bf.tame", $_POST[ "tame" ] == "on"?true:false ); 
	update_option( "bf.mainBg", preg_replace( "/[^0-9A-Fa-f#]/", "", $_POST[ 'mainBg'] ) );
	update_option( "bf.headBg", preg_replace( "/[^0-9A-Fa-f#]/", "", $_POST[ 'headBg'] ) );
	update_option( "bf.mainText", preg_replace( "/[^0-9A-Fa-f#]/", "", $_POST[ 'mainText'] ) ); 
	update_option( "bf.headText", preg_replace( "/[^0-9A-Fa-f#]/", "", $_POST[ 'headText'] ) ); 
	update_option( "bf.sidebar", $_POST[ "sidebar" ] == "yes"?true:false );
	
	/**
	 * Can we write the template file, should we even write it? 
	 */
	if( isset( $_POST[ "templateChanged" ] ) && $_POST[ "templateChanged"] == "true" ){
		if( is_writable( "../wp-content/plugins/buzzfeed/template.php" ) ){
			$file = fopen( "../wp-content/plugins/buzzfeed/template.php", "w" );
			fwrite( $file, stripslashes( $_POST[ "template" ] ) );  
			fclose( $file );
		}
		else{ 
			echo "<div class='error fade' id='template_error'>"; 
			echo "  <p color='red'>";
			echo "    The template file was modified, but could not be saved. Please use your ftp client and change the file permission of ["; 
			echo      realpath( "../wp-content/plugins/buzzfeed/template.php" ) . "] to 777!";
			echo "  </p>";   	
			echo "  <p>";
			echo "  All your other options were saved. <br/>";
			echo "  </p>";   	
			echo "</div>"; 
		}
	}
	else{
		echo "<div class='updated fade' id='bf_saved'><p>Options saved.</p></div>";
	}
	
	// Update the cache file! 
	if( (bool)get_option( "bf.caching" ) == true ){
		require_once( dirname( __FILE__ ) . "/buzzfeed.class.php" ); 
		$bf = new BuzzFeed( constructBuzzFeedConfig() );
		$bf->updateCache();
	}  
}


?>