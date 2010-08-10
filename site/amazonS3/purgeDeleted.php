<?php 
	require_once 'util/dbconfig.php';
	require_once 'amazonS3/s3.php';
	
	//Account Identifiers
	$keyId = "S3_KEY_ID";	
	$secretKey = "S3_KEY_SECRET";
	
	$dbconn = mysql_connect($dbserver,$dbuser,$dbpassword);
	
	if (!$dbconn) {
		die("Unable to connect to database.");
	}
	
	$ret = mysql_select_db($db,$dbconn);
		
	if (!$ret) {
		die( "Unable to select database");
	}		
	
	$sql="SELECT batchrunning FROM batchcontrol WHERE id=3";
	$result=mysql_query($sql);
	$batchrunning=mysql_result($result,0,"batchrunning");

	if($batchrunning == "1") {
		// echo "Already Running\n";
		return;
	}

	$sql="UPDATE batchcontrol SET batchrunning=1 WHERE id=3";
	mysql_query($sql);

	$sql = "SELECT media.id, media.name, users.username FROM media INNER JOIN users ON users.id = media.ownerid WHERE deleteFlag = 1";
	
	$result = mysql_query($sql,$dbconn);
	
	if (!$result) {
		die("Unable to run query: ".mysql_error());
	}
	
	$s3 = new s3($keyId, $secretKey);
	$bucket = "media1.s3mer.com";
	
//	echo '<pre>';
	while ($row = mysql_fetch_assoc($result)) {
		$keyToDelete = 'user/'.$row['username'].'/'.$row['name'];
		$keyToDelete_thumb = 'user/'.$row['username'].'/thumbs/'.$row['name'];
		
		$s3->deleteObject($bucket,$keyToDelete_thumb);
		
		if($s3->deleteObject($bucket,$keyToDelete)){
//			echo 'deleted: '.$keyToDelete.'<br>';
			
			$sql = "DELETE FROM media WHERE id = '".$row['id']."'";
			
			mysql_query($sql,$dbconn);
			
		} else {
			echo 'failed to delete: '.$keyToDelete.'<br>'."\n";
		}
	}
	
	$sql = "DELETE FROM playlistitem WHERE deleteflag = 1";
	
	mysql_query($sql,$dbconn);
//	echo '</pre>';
	
	$sql="UPDATE batchcontrol SET batchrunning=0 WHERE id=3";
	mysql_query($sql);




?>
