<?php
// The default buzzfeed template. 
// You get the following template variables: 
// 
// - $rows:    Number of rows
// - $cols:    Number of columns
// - $buzzes:  A 2dimensional array containg the html of the buzzes.
//             Each element $buzzes[row][col] is itself an Array allowing you to access
//             * link
//             * title
//             * image
//             * description
//             * category
//             * clicks
//             * formatted_clicks
//             * short_description
//
//         So for instance use echo "first title: " . $buzzes[ 0 ][ 0 ][ "title" ];
//
// - $config   Access to the configuration variables (see config.php for details)
// - $bf       You can also use all the function from buzzfeed.class.php, but most likely 
//             you won't need them. Checkout the buzzfeed.class.php for a list of functions.
?>

<style type="text/css">
/*<![CDATA[*/
	#BF_Widget img.bf_image_small{ width: 90px; height: 60px; }
	#BF_Widget img.bf_image_large{ width: 125px; height: 83px; }

	#BF_Widget div.bf_widget_small{ width: 90px; height: 100px; font-size: 11px; }
	#BF_Widget div.bf_widget_large{ width: 120px; height: 120px; font-size: 13px; }

	#BF_Widget a.bf_clickbar_small{ width: 90px; font-size: 9px; }
	#BF_Widget a.bf_clickbar_large{ width: 120px; font-size: 10px; }

	#BF_Widget a.bf_text_small{width: 90px;}
	#BF_Widget a.bf_text_large{width: 120px;}
/*]]>*/
</style>

<!-- This is code helps us tracking clicks/page impressions, please leave it in there! -->
<script type="text/javascript">
//<![CDATA[
	function bfct(ct) {
		var img = new Image();
		img.src = ct;           
	}
//]]>
</script>

<!--[if IE]><style>#BF_widget .BF_Clickbar { letter-spacing:0!important; }</style><![endif]--> 
<!--if there is any whitespace between the two <a> tags for the image and the "click bar" then IE puts vertical space between the two elements -->
<table id="BF_Widget" style="border-collapse:collapse; background:<?php echo $config['mainBg'];?>; ">
	<tr><td colspan="<?php echo $cols; ?>" style="background:<?php echo $config['headBg'];?>"><a style="display:block;text-decoration:none;font:normal 13px 'Lucida Grande',Arial,Helvetica,sans-serif;color:<?php echo $config['headText'];?>;text-align:center" href="http://buzzfeed.com">BuzzFeed</td></tr>
	<tr><td style='height:10px'></td></tr> 
	
	<?php 
	for( $i = 0; $i < $rows; $i++ ){
		echo "<tr>"; 
		for( $j = 0; $j < $cols; $j++ ){
			$buzz = $buzzes[ $i ][ $j ]; 
			
			$buzz['title'] = htmlentities($buzz['title'], ENT_QUOTES);
			
			echo "<td align='center' valign='middle'>\n"; 
			echo "<div align='center' style='overflow:hidden; margin-left: 10px; margin-right: 10px; ' class='bf_widget_{$config['image_size']}'>\n"; 
			
			echo "<a href='{$buzz['link']}' onmousedown='bfct(\"{$buzz['ct']}\");' style='text-decoration:none;border:0;padding:0;margin:0;'><img src='{$buzz['image']}' alt='{$buzz['title']}' class='bf_image_{$config['image_size']}' id='{$buzz['ca']}' border='0' style='border:0;padding:0;margin:0;' /></a>"; 
			echo "<a class='BF_Clickbar bf_clickbar_{$config['image_size']}' style='display:block; position:relative; top:-1.9em; margin:0; padding:0 2px 0 0; font-weight:bold; font-family: Trebuchet,\"Trebuchet MS\",Verdana,sans-serif; font-style:italic; line-height:1.1; text-transform:uppercase; text-align:right; text-decoration:none; letter-spacing:0.1em; color:#fff; background:#e32; border:0' href='{$buzz['link']}' onmousedown='bfct(\"{$buzz['ct']}\");'>{$buzz['formatted_clicks']}</a>\n"; 
			echo "<a style='display:block; position:relative; top:-0.7em; font-family: Georgia,serif;text-decoration:none; letter-spacing:0; color:{$config['mainText']}; border-width:0px;' class='bf_text_{$config['image_size']}' href='{$buzz['link']}' onclick='bfct(\"{$buzz['ct']}\");'>{$buzz['title']}</a>\n"; 
			echo "</div>\n"; 
			echo "</td>\n"; 
		}
		
		echo "</tr>\n"; 
	}
	?>

	<tr><td colspan="<?php echo $cols; ?>" style="background:<?php echo $config['headBg'];?>"><a style="display:block;text-decoration:none;font:normal 10px 'Helvetica Neue',Arial,Helvetica,sans-serif;color:<?php echo $config['headText'];?>;text-align:center" href="http://buzzfeed.com/network/join">Add To Your Site</td></tr>
</table>

<!-- This is code helps us tracking clicks/page impressions, please leave it in there! -->
<?php echo str_replace( "&gt;", ">", str_replace( "&lt;", "<", $bf->parsedXML[ "imp_js" ][ 0 ][ "_text" ] ) ); ?>

