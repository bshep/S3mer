<?php 

header("Content-Type: text/xml");

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

if(!isset($_GET['version'])) {
	echo "<appinfo>\n";
	echo "\t<name>S3merApp</name>\n";
	echo "\t<version>1.003</version>\n";
	echo "\t<url>app/S3mer_1003.air</url>\n";
	echo "</appinfo>\n";
} else {
	if($_GET['version'] == 0) {
		echo "<appinfo>\n";
		echo "\t<name>S3merApp</name>\n";
		echo "\t<version>0.992</version>\n";
		echo "\t<url>app/S3mer_0992.air</url>\n";
		echo "</appinfo>\n";
	} elseif($_GET['version'] == 1) {
		echo "<appinfo>\n";
		echo "\t<name>S3merApp</name>\n";
		echo "\t<version>1.003</version>\n";
		echo "\t<url>app/S3mer_1003.air</url>\n";
		echo "</appinfo>\n";
	} elseif($_GET['version'] == 2) {
		?>
			     <update xmlns="http://ns.adobe.com/air/framework/update/description/1.0"> 
			       <version>1.011</version> 
			       <url>http://media1.s3mer.com/app/S3mer_1011.air</url> 
			       <description><![CDATA[
			            This version adds support for: 
							* Better RSS Scrolling
							* Better Status while loading shows and configuration
							
						Plus these updates from 1.010:
			                * New: Ability to save window size and position by pressing the "W" key
			                * New: Side scrolling rss / atom feeds
			                * Better on-screen error reporting
			                * Better support for dynamic data loading swf movies
			                * Better RSS / Atom feeds parsing
			                * Better handling of RSS / Atom feed updates
			         ]]></description> 
			    </update>
		<?php
	}
}

?>
