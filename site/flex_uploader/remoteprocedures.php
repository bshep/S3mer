<?php 
	require_once('../util/session.php');
	require_once('../util/dbconfig.php');
	// requite_once('util.php');

	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");
	
	// print_r($_SESSION);
	
	if(isset($_GET['action'])) {
		if($_GET['action'] == 'check_loggedin') {
			if(isset($_SESSION['loggeduser']['id'])) {
				echo '1';
			} else {
				echo '0';
			}
			return;
		}
	}
	
	checkToken();
	
	if(!isset($_SESSION['loggeduser']['id'])) {
		die("Error #0, User not logged in");
	}
	
	if(!isset($_GET['action'])) {
		die("Error #10, no command specified.");
	}
	
	header('Content-Type: text/xml');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

	switch($_GET['action']) {
		case 'library':
			librarySearch();
			break;
		case 'folder-list':
			folderList();
			break;
		case 'user-stats':
			userStats();
			break;
		default:
			echo "<error>Unrecognized action</error>";
	}
	
	function userStats() {
		$sql = "SELECT SUM(filesize) as total_used FROM media WHERE ownerid='" . $_SESSION['loggeduser']['id'] . "'";
	
		$filesize_result = mysql_query($sql);

		$sql = "SELECT quota as total_avail FROM users WHERE id='" . $_SESSION['loggeduser']['id'] . "'";
	
		$quota_result = mysql_query($sql);
		
		if(!$filesize_result) {
			die('Error #2');
		}
		
		if(!$quota_result) {
			die('Error #3');
		}
		
		$filesize_row = mysql_fetch_array($filesize_result);
		$quota_row = mysql_fetch_array($quota_result);
		
		if($filesize_row['total_used'] == "") {
			$filesize_row['total_used'] = "0";
		}
		
		echo "<stats>\n";
		echo "\t<user ".
			 // " id=\"".$_SESSION['loggeduser']['id']."\" ".
			 " quota=\"".($quota_row['total_avail']*1024*1024)."\" ".
			 " used=\"".$filesize_row['total_used']."\" />";
		echo "</stats>";
		
		
	}
	
	function folderList() {
		$sql = "SELECT * FROM mediafolders WHERE owner='" . $_SESSION['loggeduser']['id'] . "'";
	
		$media_result = mysql_query($sql);

		if(!$media_result) {
			die('Error #2');
		}

		echo "<folders>\n";
	
		echo "\t<item id=\"0\" name=\"Main\" />\n";
		while( $media_row = mysql_fetch_array($media_result) ) {
			echo "\t<item id=\"".$media_row['id']."\" name=\"".$media_row['folder']."\" />\n";
			// echo "\t\t<id>".$media_row['id']."</id>\n";
			// echo "\t\t<name>".$media_row['folder']."</name>\n";
			// echo "\t</item>\n";

		}
	
		echo "</folders>\n";		
	}
	
	
	function librarySearch(){ 
		$sql = "SELECT * FROM media WHERE ownerid='" . $_SESSION['loggeduser']['id'] . "'";
	
		$media_result = mysql_query($sql);

		if(!$media_result) {
			die('Error #2');
		}

		echo "<library>\n";
	
		while( $media_row = mysql_fetch_array($media_result) ) {
			echo "\t<item>\n";
			echo "\t\t<id>".$media_row['id']."</id>\n";
			echo "\t\t<name>".$media_row['name']."</name>\n";
			echo "\t\t<thumbnail>".$_SESSION['loggeduser']['username']."/thumbs/".$media_row['name']."</thumbnail>\n";
			echo "\t</item>\n";

		}
	
		echo "</library>\n";
	}
	
?>