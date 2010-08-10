<?php

	error_reporting(0);
    require_once ('util/dbconfig.php');
    require_once ('amazonS3/s3.php');

    // print('ABCCCC');
    createAll();
    
    
		//     function createOne($username, $filename) {
		//         global $dbserver, $dbuser, $dbpassword, $db;
		//     
		//         $dbconn = mysql_connect($dbserver,$dbuser,$dbpassword);
		//         
		//         if (!$dbconn) {
		//             die("Unable to connect to database.");
		//         }
		//         
		//         $ret = mysql_select_db($db,$dbconn);
		//             
		//         if (!$ret) {
		//             die( "Unable to select database");
		//         }       
		// 
		// $sql="SELECT batchrunning FROM batchcontrol WHERE id=2";
		// $result=mysql_query($sql);
		// $batchrunning=mysql_result($result,"batchrunning");
		// 
		// if($batchrunning == "0") {
		// 	return;
		// }
		//         
		//         $sql = "SELECT media.id, media.name, users.username FROM media 
		//                     INNER JOIN users ON users.id = media.ownerid 
		//                     INNER JOIN mediatype ON mediatype.id = media.mediatype 
		//                     WHERE users.username = '" . mysql_escape_string($username) . 
		// 			"' AND media.name = '" . mysql_escape_string($filename) . "'";
		//         
		//         $result = mysql_query($sql,$dbconn);
		//     
		//         if (!$result) {
		//             die("Unable to run query: ".mysql_error());
		//         }
		//         
		//         while ($row = mysql_fetch_assoc($result)) {
		//             $url = 'http://media1.s3mer.com.s3.amazonaws.com/user/'.urlencode($row['username'].'/'.$row['name']);
		//             //echo $url;
		//             if (createThumbnail($url,$row['username'],'.',70,80)) {
		//                 $sql = "UPDATE media SET thumbnailstate = 1 WHERE id = ".$row['id'];
		//                 mysql_query($sql,$dbconn);
		//                 echo 'Success';
		//             } else {
		//                 echo 'Failed!!!';
		//             }
		//             
		//         }   
		//     }
    
    
    function createAll()
    {   
        global $dbserver, $dbuser, $dbpassword, $db;
        $dbconn = mysql_connect($dbserver,$dbuser,$dbpassword);
        
        if (!$dbconn) {
            die("Unable to connect to database.");
        }
        
        $ret = mysql_select_db($db,$dbconn);
            
        if (!$ret) {
            die( "Unable to select database");
        }       
        
		$sql="SELECT batchrunning FROM batchcontrol WHERE id=2";
		$result=mysql_query($sql);
		$batchrunning=mysql_result($result,0,"batchrunning");

		if($batchrunning == "1") {
			// echo "Already Running\n";
			return;
		}

		$sql="UPDATE batchcontrol SET batchrunning=1 WHERE id=2";
		mysql_query($sql);
		
		// print "Error: ".mysql_error();
        
        $sql = "SELECT media.id, media.name, users.username FROM media 
                    INNER JOIN users ON users.id = media.ownerid 
                    INNER JOIN mediatype ON mediatype.id = media.mediatype 
                    WHERE thumbnailstate = 0 AND deleteflag = 0 AND mediatype.mediatype = 'image'";
        
        $result = mysql_query($sql,$dbconn);
        
        if (!$result) {
            die("Unable to run query: ". mysql_error());
        }
        
        
        // echo "<pre>";
        // echo 'Need to create ' . mysql_num_rows($result) . "thumbnails<br>\n";
        while ($row = mysql_fetch_assoc($result)) {
            $url = 'http://media1.s3mer.com.s3.amazonaws.com/user/'.urlencode($row['username'].'/'.$row['name']);
            
            // echo 'Creating Thumbnail for: ' . $row['username'] . ' - ' . $row['name'] . "<br>\n";
            
            if (createThumbnail($url,$row['username'],'.',70,80)) {
                $sql = "UPDATE media SET thumbnailstate = 1 WHERE id = ".$row['id'];
                mysql_query($sql,$dbconn);
            
            } else {
				echo "Failed for $url\n";
	    	}
            
        }   
        
		$sql="UPDATE batchcontrol SET batchrunning=0 WHERE id=2";
		mysql_query($sql);
        // echo 'Done!<br>';
        // echo '</pre>';
    }
    
    function createThumbnail($url,$username, $thumbDirectory, $thumbHeight, $thumbWidth) {
        //Account Identifiers
        $keyId = "S3_KEY_ID";    
        $secretKey = "S3_KEY_SECRET";
        
        $filename = basename(urldecode($url));
        $extension = pathinfo($filename);
        
        if(!isset($extension['extension'])) {
            return false;
        }
    
        $extension = $extension['extension'];
        
        switch (strtolower($extension)) {
            case 'jpg':
                $srcImg = imagecreatefromjpeg($url);
                break;
            case 'gif':
                $srcImg = imagecreatefromgif($url);
                break;
            case 'png':
                $srcImg = imagecreatefrompng($url);
                break;
            default:
				echo "Extension not recognized...";
                return false;
                break;
        }

		if($srcImg == false) {
			echo "Invalid Image...";
			return false;
		}
    
        //$srcImg = imagecreatefromjpeg($url);
        $origWidth = imagesx($srcImg);
        $origHeight = imagesy($srcImg);
        
    /*  if($origWidth > $origHeight) {
            $thumbHeight = ($thumbWidth/$origWidth)*$origHeight;
        } else {
            $thumbWidth = ($thumbHeight/$origHeight)*$origWidth;
        }*/
        
        $thumbImg = ImageCreateTrueColor($thumbWidth,$thumbHeight);
        
        if(!$thumbImg) {
            return false;
        }
        
        $tempfile = tempnam('.',$filename);
        imagecopyresampled($thumbImg,$srcImg,0,0,0,0,$thumbWidth,$thumbHeight,$origWidth,$origHeight); 
        imagejpeg($thumbImg, $tempfile);
        
        $s3 = new s3($keyId, $secretKey);
        $bucket = "media1.s3mer.com";
        
        $ret = true;
		$retries = 0;
        while(!$s3->putObject($bucket,'user/'.$username.'/thumbs/'.$filename,$tempfile,
                            'image/jpeg', filesize($tempfile),
                            'public-read', array(), false))
		{
			if($retries == 5 || $s3->responseCode != 500) {
	            printError($s3->parsed_xml, $s3->responseCode);
	            $ret = false;               
				break;
			}
			// echo '.';
			$retries += 1;
        }
        
        unlink($tempfile);
        
        return $ret;
    }
                                
    function printError($parsed_xml, $responseCode){
        echo "Operation Failed<br>";
        echo "Error: ".$responseCode."<br>" . $parsed_xml->Message;
        if(isset($parsed_xml->StringToSignBytes)) echo "<br>Hex-endcoded string to sign: " . $parsed_xml->StringToSignBytes;
    }
		
?>
