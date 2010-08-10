<?php

	global $smarty;
	global $userlang;

	require_once('util/application.php');

	force_https("undo");

	if(!isLoggedIn()) {
		header('Location: login.php');
		return;
	}

	mysql_connect($dbserver,$dbuser,$dbpassword);
	@mysql_select_db($db) or die( "Unable to select database");

	if(isset($_SESSION['lastpage'])){
		$_SESSION['prevlastpage']=$_SESSION['lastpage'];
	}
	$_SESSION['lastpage']=$_SERVER["REQUEST_URI"];

	if(isset($_SESSION['userlang'])){
		$lang=$_SESSION['userlang'];
		
	}
	else{
		$lang='en';	
	}
	
	
	
	
	$sql="SELECT * FROM mediafolders WHERE `owner`=" . $loggeduser['id'] . " order by folder";
	$result=mysql_query($sql);
	$mediafolderids=array();
	$mediafoldernames=array();
	if ($result != null) {
	    
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	      
	      $nrow=array();
	      
	      foreach($row as $ind=>$r){
	        $nrow[$ind]=htmlentities($r);
	      }
	          
	      $mediafolderids[]='mediafolder_' . $nrow['id'];
	      $mediafoldernames[]=$nrow['folder'];
	      
	    }
	}
	
	
	
	
	
	
	$smarty->assign('buttonselect',$_GET['buttonselect']);
	$smarty->assign('mediafolderids',$mediafolderids);
	$smarty->assign('mediafoldernames',$mediafoldernames);

	$smarty->display('minilibrary.html');


?>