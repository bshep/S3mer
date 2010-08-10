<?php 

	$DEPLOYMENT_TYPE = "development";

	switch($DEPLOYMENT_TYPE) {
	    case "development":
	      $dbserver = '127.0.0.1';
	      $dbuser = 's3mer';
	      $dbpassword = 'DB_PASSWORD';
	      $db='s3mer_trivia';
	      break;
	    case "remote":
	      $dbserver = 'www.s3mer.com';
	      $dbuser = 's3merproduction';
	      $dbpassword = 'DB_PASSWORD';
	      $db='s3mer_trivia';
	      break;
	    case "production":
	      $dbserver = 'localhost';
	      $dbuser = 's3merproduction';
	      $dbpassword = 'DB_PASSWORD';
	      $db='s3mer_trivia';
	      break;
    }

	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");

	$sql = "SELECT * FROM question";
	$result = mysql_query($sql);
	
	if(!$result) {
		echo "Error! ".mysql_error();
	}
	
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	
	echo "\n<questions>\n";
	while( $question_row = mysql_fetch_array($result) ) {
		$question_row['question'] = str_replace("\"\"","'",$question_row['question']);
		$question_row['question'] = str_replace("\"","",$question_row['question']);
		
		echo "\t<question>\n";
		echo "\t\t<text>" . $question_row['question'] . "</text>\n";
		echo "\t\t<correct>" . $question_row['correct'] . "</correct>\n";
		echo "\t\t<wa1>" . $question_row['wrong1'] . "</wa1>\n";
		echo "\t\t<wa2>" . $question_row['wrong2'] . "</wa2>\n";
		echo "\t\t<wa3>" . $question_row['wrong3'] . "</wa3>\n";
		echo "\t</question>\n";
	}
	echo "</questions>\n";
	


/*
<questions>
	<question id="1">
		<text>Which actor plays John McClane in the "Die Hard" movies?</text>
		<correct>Bruce Willis</correct>
		<wa1>Tom Cruise</wa1>
		<wa2>Billy Bob Thornton</wa2>
		<wa3>Harrison Ford</wa3>
	</question>
	<question id="2">
		<text>What is the name of Jerry Springer's head security guy?</text>
		<correct>Steve</correct>
		<wa1>Rob</wa1>
		<wa2>Al</wa2>
		<wa3>Jim</wa3>
	</question>
</questions>

*/
?>