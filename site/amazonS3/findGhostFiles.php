<?php 
	require_once 'util/dbconfig.php';
	require_once 'HTTP/Request.php';
	
	$dbconn = mysql_connect($dbserver,$dbuser,$dbpassword);
	
	if (!$dbconn) {
		die("Unable to connect to database.");
	}
	
	$ret = mysql_select_db($db,$dbconn);
		
	if (!$ret) {
		die( "Unable to select database");
	}
	
	$sql = "SELECT media.id, media.name, users.username FROM media INNER JOIN users ON users.id = media.ownerid WHERE deleteFlag = 0 OR deleteflag = 2";
	
	$result = mysql_query($sql,$dbconn);
	
	if (!$result) {
		die("Unable to run query: ".mysql_error());
	}
	
	$req =& new HTTP_Request('');
	$req->setMethod('HEAD');
	
	while ($row = mysql_fetch_assoc($result)) {
		$keyToCheck = 'user/'.$row['username'].'/'.$row['name'];
		
		$url = 'http://media1.s3mer.com/' . urlencode($keyToCheck);
		
		$req->setURL($url);
		$req->setMethod('HEAD');
		
		$retries = 0;
		do {
			$req->sendRequest();
		
			$code = $req->getResponseCode();
			$body = $req->getResponseBody();
		
			if($body) { echo "B ";}
		
			if($code >= 400 && $code < 500){
				echo 'Code: '.$code.'  URL: ' . $url."\n";
			
				$sql = "UPDATE media SET deleteflag = 2 WHERE id = '".$row['id']."'";
			
			
				mysql_query($sql,$dbconn);
				break;
			} else {
				//echo 'Code: '.$code.'  URL: ' . $url . "\n";
				// echo 'failed to mark: '.$keyToCheck.'<br>'."\n";
				if($code == 200) {
					$sql = "UPDATE media SET deleteflag = 3 WHERE id = '".$row['id']."'";
			
					mysql_query($sql,$dbconn);
				} else {
					echo '.';
				}
			}
			$retries += 1;
			if($retries > 10) {
				echo "G\n";
				echo 'Code: '.$code.'  URL: ' . $url . "\n";
				break;
			}
			
		} while($code != 200);
	}
//	echo '</pre>';
	




?>
