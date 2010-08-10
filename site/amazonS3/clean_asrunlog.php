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
	
	clean_asrunlog($_SERVER['argv'][1]);


	$sql="UPDATE batchcontrol SET batchrunning=0 WHERE id=4";
	mysql_query($sql);
  
    function clean_asrunlog($dirpath) {
	
		$sql="SELECT MAX(id) as max_id, MIN(id) as min_id FROM asrunlog";
		$result=mysql_query($sql);
		
		if(!$result) {
			return;
		}
		
		$max_id=mysql_result($result,0,"max_id");
		$min_id=mysql_result($result,0,"min_id");
		
		// $max_id=85162;
		
		if($max_id - $min_id < 1000) {
			return;
		}
				
		$filepath = "$dirpath/asrunlog_".sprintf("%020u",$min_id)."-".sprintf("%020u",$max_id).".sql";
		
		if(file_exists($filepath.".gz")) {
			print "Error file already exists\n";
			return;
		}
	
		exec("mysqldump s3mer --user=s3merproduction --password=DB_PASSWORD --opt --table asrunlog -w 'id < $max_id' > $filepath",$out,$stat);
		
		if($stat != 0) {
			print "Error dumping from DB\n";
			print "---------------------\n";
			print_r($out);
			unlink($filepath);
			return;
		}
		
		exec("gzip $filepath");
		$filepath .= ".gz";
		
		$sql="DELETE FROM asrunlog WHERE id < $max_id";
		$result=mysql_query($sql);

		$sql="OPTIMIZE TABLE asrunlog";
		$result=mysql_query($sql);
		
		
	
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
		
        while(!$s3->putObject($bucket,'asrunlog/'.$filename,$filepath,
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

		//unlink($filepath);
         
        return $ret;
    }
                                
    function printError($parsed_xml, $responseCode){
        echo "Operation Failed<br>";
        echo "Error: ".$responseCode."<br>" . $parsed_xml->Message;
        if(isset($parsed_xml->StringToSignBytes)) echo "<br>Hex-endcoded string to sign: " . $parsed_xml->StringToSignBytes;
    }
		
?>
