<?php

    // Force www.s3mer.com
    if($_SERVER['HTTP_HOST']=='s3mer.com'){
        if(isset($_SERVER['HTTPS'])){
            header("Location:https://www.s3mer.com". $_SERVER['PHP_SELF']);
            return;
        } else {
            header("Location:http://www.s3mer.com". $_SERVER['PHP_SELF']);
            return;
        }
    }

	global $smarty, $userlang, $link_args;
	
    // error_reporting(0);

	require_once('dbconfig.php');

	ini_set('display_errors',true);
	
	// We include this to initialize all necesary variables and read configs

	// Load other libraries
		
	require_once('session.php');

	// load Smarty library
	require_once('smarty/smarty_config.php');

	$smarty = new smarty_config;

  $smarty->assign('dbserver',$dbserver);
  $smarty->assign('DEPLOYMENT_TYPE',$DEPLOYMENT_TYPE);
  
  if( isset($_SESSION['loggeduser'])) {
    $smarty->assign('user_loggedin','true');
  }
  
  if( isset($_SESSION['language_seen']) ) {
  if( $_SESSION['language_seen'] > 1 ) {
    $_SESSION['language_selected'] = true;
    unset($_SESSION['language_seen']);
  }
  } else {
    $_SESSION['language_seen'] = 0;
  }
  
	if( session_id() == "" ) {
		//Session not started... wonder what happened... maybe no cookies?
	} else {
		if(isset($_GET['lang'])) {
			$_SESSION['userlang'] = $_GET['lang'];
			$userlang = $_SESSION['userlang'];
		}elseif(isset($_SESSION['userlang'])) {
			$userlang = $_SESSION['userlang'];
		}elseif(isset($my_defaultlang)) {
			$userlang = $my_defaultlang;
		}elseif ($userlang == "") {
			if(isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				$userlang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
				$userlang = substr($userlang,0,2);
			}
		}
	}
	
  if( isset($_SESSION['language_selected']) || isset($_COOKIE['language_selected'])) {
    $smarty->assign('language_selected','true');
    setcookie('language_selected', 'true', time()+2592000,'/');
    $_SESSION['userlang'] = $userlang;
  } else {
    $_SESSION['language_seen'] += 1;
  }

    // Get current url on browser
    $link_args = $_SERVER['QUERY_STRING'];
    
    // Remove any lang options already set on query string
    $link_args = ereg_replace("lang=[a-zA-Z]*", "", $link_args);
    // Remove any consecutive &&
    $link_args = ereg_replace("&&", "&", $link_args);
    // Remove an & after the ?
    $link_args = ereg_replace("&$", "", $link_args);
   
	function force_https($action="do") {
		if($action == "do" && !isset($_SERVER['HTTPS'])) {
			header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			exit();
		}
		if($action == "undo" && isset($_SERVER['HTTPS'])) {
			header("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			exit();
		}
	}

	function setup_navmenus() {
		global $smarty, $dbserver,$dbuser,$dbpassword,$db;
			
		mysql_connect($dbserver,$dbuser,$dbpassword);
		@mysql_select_db($db) or die( "Unable to select database");
		
		if(isset($_SESSION['lastpage'])){
			$_SESSION['prevlastpage']=$_SESSION['lastpage'];
		}
		$_SESSION['lastpage']=$_SERVER["REQUEST_URI"];
				
		if(isset($_SESSION['language'])){
			$lang=$_SESSION['language'];
			
		}
		else{
			$lang='en';	
		}	
		
		
		$userlang=$lang;
	
		
		$sql="SELECT " . $lang . " AS menu, link FROM menus WHERE menuid=1 ORDER BY id";
		$result=mysql_query($sql);
		$topmenus=array();
		
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			
			$nrow=array();
			foreach($row as $ind=>$r){
				$nrow[$ind]=htmlentities($r);
			}
			$topmenus[]=$nrow;
			
		}
		
		
		$sql="SELECT " . $lang . " AS menu, link FROM menus WHERE menuid=2 ORDER BY id";
		$result=mysql_query($sql);
		$navmenus=array();
			
		$i=0;
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			
			$nrow=array();
			
			foreach($row as $ind=>$r){
				$nrow[$ind]=htmlentities($r);
			}
			
			$nrow['selected']=0;
			
			$navmenus[]=$nrow;
			
			$i++;
		}
		
		
		$sql="SELECT " . $lang . " AS menu, link FROM menus WHERE menuid=3 ORDER BY id";
		$result=mysql_query($sql);
		$bottommenus=array();
			
		$i=0;
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			
			$nrow=array();
			
			foreach($row as $ind=>$r){
				$nrow[$ind]=htmlentities($r);
			}
			if($i==0){
				$nrow['selected']=1;	
			}
			else{
				$nrow['selected']=0;
			}
			
			$bottommenus[]=$nrow;
			
			$i++;
		}
		
		
		$smarty->assign('topmenus', $topmenus);
		$smarty->assign('navmenus', $navmenus);
		$smarty->assign('bottommenus',$bottommenus);
		
		if($lang=='en'){
			$language='English';
		}
		else if($lang=='es'){
			$language='Espa&ntilde;ol';
		}
		else if($lang=='pt'){
			$language='Portugu&ecirc;s';
		}
		
		
		$smarty->assign('language',$language);	
	}
?>
