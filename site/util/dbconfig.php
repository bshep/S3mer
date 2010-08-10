<?php
  global $dbserver,$dbuser,$dbpassword,$db,$DEPLOYMENT_TYPE;

  $DEPLOYMENT_TYPE = "remote";

  	switch($DEPLOYMENT_TYPE) {
	    case "development":
	      $dbserver = '127.0.0.1';
	      $dbuser = 's3mer';
	      $dbpassword = 'DB_PASSWORD';
	      $db='s3mer';
	      break;
	    case "remote":
	      $dbserver = 'www.s3mer.com';
	      $dbuser = 's3merproduction';
	      $dbpassword = 'DB_PASSWORD';
	      $db='s3mer';
	      break;
	    case "production":
	      $dbserver = 'localhost';
	      $dbuser = 's3merproduction';
	      $dbpassword = 'DB_PASSWORD'; //production_password
	      $db='s3mer';
	      break;
      }
	
	function GET_MYSQL_ESCAPED($value, $var = '')
	{
		$ret = "";
		
		if ($var == '') {
			$var = $_GET;
		}
		
		if(isset($var[$value])) {
			$ret = mysql_escape_string($var[$value]);
		}
		
		return $ret;
	}
?>
