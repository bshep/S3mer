<?php

// =================
// = Smarty Config =
// =================

require('smarty/libs/Smarty.class.php');
$smarty = new Smarty();

$smarty->template_dir = 'templates';
$smarty->compile_dir = 'templates_c';
$smarty->cache_dir = 'cache';
$smarty->config_dir = 'configs';

// =======================
// = Connection Settings =
// =======================

$dbserver = 'www.s3mer.com';
$dbuser = 'cooptiles';
$dbpassword = 'demopass';
$db='cooptiles';

mysql_connect($dbserver,$dbuser,$dbpassword);
@mysql_select_db($db) or die( "Unable to select database");

// ====================
// = Select all tiles =
// ====================

$sql="SELECT * FROM actividades WHERE salon='1'";
$result=mysql_query($sql);


if (!$result) {
	echo (mysql_error());
}

$allacts=array();

$i=0;

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	
	$nrow=array();
	
	foreach($row as $ind=>$r){
		
		$nrow[$ind]= $r;
		
	}
	
	$allacts[]=$nrow;
	
	$i++;
	
} 


// ======================================
// = Assign results to smarty variables =
// ======================================
$smarty->assign('allacts', $allacts);


// ====================
// = Display template =
// ====================
$smarty->display('coopPartial.tpl');

?>