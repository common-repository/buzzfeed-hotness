<html>
<head>
</head>
<body style="margin: 0px; padding: 0px; ">
<?php

include( "buzzfeed.class.php" );
 
// FIRST OF ALL... create the virtual preview configuration file
$config = array( 
	'template_file' => "template.php", 

	'rows' => (int)$_POST['rows'],
	'cols' => (int)$_POST['cols'],
	'image_size' => ($_POST['image_size'] == "small"?"small":"large" ),
	'categories' => preg_replace( "/[^a-zA-Z0-9,]/", "", $_POST['categories'] ),
	'caching' => false,
	'cache_file' => "",
	'cache_lifetime' => 0,   
	'tame' => ( ( isset( $_POST[ "tame" ] ) && $_POST[ "tame" ] == "on")?"true":"false" ),

	'mainBg' => preg_replace( "/[^0-9A-Fa-f#]/", "", $_POST[ 'mainBg'] ),  
	'mainText' => preg_replace( "/[^0-9A-Fa-f#]/", "", $_POST[ 'mainText'] ),  
	'headBg' => preg_replace( "/[^0-9A-Fa-f#]/", "", $_POST[ 'headBg'] ), 
	'headText' => preg_replace( "/[^0-9A-Fa-f#]/", "", $_POST[ 'headText'] )
); 



if( isset( $_POST[ "previewTemplate" ] ) && $_POST[ "previewTemplate" ] == "on" && $_POST[ "templateChanged" ] == "true" ){
	$cols = $config[ "cols" ]; 
	$rows = $config[ "rows" ];
	$tempTemplate = ""; 
	
	$bf = new BuzzFeed( $config );
	$buzzes = $bf->getBuzzes(); 
	
	ob_start(); 
	eval( "?>{$_POST['template']}" );
	$contents = ob_get_contents(); 
	ob_end_clean(); 
	
	$pattern = '/on line <b>([0-9]+)<\/b>/';
	preg_match($pattern, str_replace( '\n', '', $contents ), $matches );
		
	if( sizeof( $matches ) >= 2 ){
		// Let's find at which character position the error line is and how long it is
		$errorLine = (int)$matches[ 1 ] - 1; 
		$lines = split( "\n", $_POST[ "template" ] );
		$errorStart = 0; 
		for( $i = 0; $i < $errorLine; $i++ ){ 
			$errorStart += strlen( $lines[ $i ] ); 
		}
		$errorEnd = $errorStart + strlen( $lines[ $errorLine ] );

		echo "<span style='font-family: arial, helvetica; font-size: 12px; '>";
		echo "	<span style='color: red; '>";
		echo "		You have a syntax error in line " . ($errorLine+1) . " or above! <br/>"; 
		echo "		<a href='#' style='color: #555555;' onclick=\"parent.setSelectionRange( parent.document.getElementById( 'template' ), $errorStart, $errorEnd ); parent.document.getElementById( 'template' ).scrollTop = " . ( $errorLine*15 - 45 ) . "; return false;\">Highlight line &#8617;</a>";
		echo "	</span>"; 
		echo "	<br/>";  
		echo "	<br/>";
		echo "	<div style='border: 1px solid #555555; background-color: #eeeeee; padding: 2px; '>";
		echo "		Exact message: <br/>"; 
		echo "		<span style='font-family: courier new;'>{$contents}</span>";
		echo "	</div>";
		echo "</span>"; 
	}
	else{
		echo $contents; 
	}
	
}
else{
	$bf = new BuzzFeed( $config );
	$bf->outputHTML(); 
}
?>
</body>
</html>