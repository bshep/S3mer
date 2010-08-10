<?php

//	error_reporting(0);
    require_once ('../util/dbconfig.php');
    require_once ('s3.php');


	global $dbserver, $dbuser, $dbpassword, $db;
	$dbconn = mysql_connect($dbserver,$dbuser,$dbpassword);

	if (!$dbconn) {
	    die("Unable to connect to database.");
	}

	$ret = mysql_select_db($db,$dbconn);
    
	if (!$ret) {
	    die( "Unable to select database");
	}       
	
	// echo "backing up ".$_SERVER['argv'][1]."\n";
	// 
	// return;
    
    // This select statement checks to see if the batchrunning variable is set
	// it will return true if it was set less than an hour ago, otherwise it will return false
	// this prevents the process from getting stuck if there is an error, like a power outage
	
	$sql="SELECT 
			(batchrunning && (DATE_ADD(updated, INTERVAL 1 DAY) > NOW())) AS batchrunning 
				FROM batchcontrol WHERE id = 4";
	$result=mysql_query($sql);
	$batchrunning=mysql_result($result,0,"batchrunning");

	if($batchrunning == "1") {
		// echo "Already Running\n";
		return;
	}

	$sql="UPDATE batchcontrol SET batchrunning=1 WHERE id=4";
	mysql_query($sql);
	
	uploadbackup($_SERVER['argv'][1]);


	$sql="UPDATE batchcontrol SET batchrunning=0 WHERE id=4";
	mysql_query($sql);
  
    function uploadbackup($filepath) {
        //Account Identifiers
        $keyId = "S3_KEY_ID";    
        $secretKey = "S3_KEY_SECRET";
        
        $filename = basename($filepath);
 
        $s3 = new s3($keyId, $secretKey);
        $bucket = "backups.s3mer.com";
        
        $ret = true;
		$retries = 0;
		
		// echo "uploading: ".$filepath."\n";
		// echo "to: ".'database/'.$filename."\n";
		
        while(!$s3->putObject($bucket,'database/'.$filename,$filepath,
                            'application/x-gzip', filesize($filepath),
                            'private', array(), false))
		{
			if($retries == 5 || $s3->responseCode != 500) {
	            printError($s3->parsed_xml, $s3->responseCode);
	            $ret = false;               
				break;
			}
			// echo '.';
			$retries += 1;
        }
         
        return $ret;
    }
                                
    function printError($parsed_xml, $responseCode){
        echo "Operation Failed<br>";
        echo "Error: ".$responseCode."<br>" . $parsed_xml->Message;
        if(isset($parsed_xml->StringToSignBytes)) echo "<br>Hex-endcoded string to sign: " . $parsed_xml->StringToSignBytes;
    }
		
?>
