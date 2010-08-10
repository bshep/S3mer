<?php

	global $smarty;
	global $userlang;

	require_once('util/application.php');

	if(!isLoggedIn()) {
		header('Location: login.php');
		return;
	}

	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");

	if(isset($_GET['showid'])){
		
		$sql="SELECT * FROM `show` WHERE id=" . $_GET['showid'];
		$result=mysql_query($sql);
		$show=array();

		if ($result != null) {
		    $i=0;

		    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

		      $nrow=array();

		      foreach($row as $ind=>$r){
		        $nrow[$ind]=htmlentities($r);
		      }

		      $show=$nrow;

		      $i++;
		    }
		}	

		$proplayers=0;

		if($show['backgroundimage']!=0){

			$sql = "SELECT name FROM media WHERE id=" . $show['backgroundimage'];
			$result = mysql_query($sql);

			if($result != null){
				$backgroundthumbnailimage = mysql_result($result,0);
				$smarty->assign('backgroundthumbnailimage',$backgroundthumbnailimage);
			}

		}
		
		
        $uname = $_SESSION['loggeduser']['username'];

        $smarty->assign('uname',$uname);
		$smarty->assign('show',$show);
		$smarty->display('edit-show-backgroundimage.html');
		
		
	}




?>