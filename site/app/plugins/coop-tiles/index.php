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

$dbserver = 's3mer.com';
$dbuser = 'cooptiles';
$dbpassword = 'demopass';
$db='cooptiles';

mysql_connect($dbserver,$dbuser,$dbpassword);
@mysql_select_db($db) or die( "Unable to select database");

// ====================
// = Select all tiles =
// ====================

$sql="SELECT `tiles`.id, categories.category,`tiles`.content,`tiles`.url, `tiles`.type FROM `tiles` INNER JOIN categories ON categories.id = `tiles`.category ORDER BY `tiles`.id DESC";
$result=mysql_query($sql);


if (!$result) {
	echo (mysql_error());
}

$alltiles=array();

$i=0;

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	
	$nrow=array();
	
	foreach($row as $ind=>$r){
		
		$nrow[$ind]= $r;
		
	}
	
	$alltiles[]=$nrow;
	
	$i++;
	
} 


// ======================================
// = Assign results to smarty variables =
// ======================================
$smarty->assign('alltiles', $alltiles);



// ====================
// = Display template =
// ====================
$smarty->display('index.tpl');

?>