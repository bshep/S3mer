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

// =========================
// = Select all categories =
// =========================
$sql="SELECT * FROM `categories` ORDER BY `id` DESC";
$result=mysql_query($sql);
$allCats=array();

$i=0;

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	
	$nrow=array();
	
	foreach($row as $ind=>$r){
		
		$nrow[$ind]= $r;
		
	}
	
	$allCats[]=$nrow;
	
	$i++;
	
} 

// =========================
// = Select all tile types =
// =========================
$sql="SELECT id,type FROM `types` ORDER BY `id` ASC";
$result=mysql_query($sql);
$allTypesName=array();
$allTypesId=array();

if ($result != null) {
	$i=0;
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$allTypesId[]=$row['id'];
		$allTypesName[]=$row['type'];
		
		$i++;
	}
}

// =========================
// = Select all categories =
// =========================
$sql="SELECT id,category FROM `categories` ORDER BY `id` ASC";
$result=mysql_query($sql);
$allCatsName=array();
$allCatsId=array();

if ($result != null) {
	$i=0;
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$allCatsId[]=$row['id'];
		$allCatsName[]=$row['category'];
		
		$i++;
	}
}

// ====================
// = Select all tiles =
// ====================

$sql="SELECT `tiles`.id, categories.category,`tiles`.content,`tiles`.url, types.type FROM `tiles` INNER JOIN categories ON categories.id = `tiles`.category INNER JOIN types on types.id = `tiles`.type ORDER BY `tiles`.id DESC";
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

$smarty->assign('allCats', $allCats);
$smarty->assign('allTypesId', $allTypesId);
$smarty->assign('allTypesName', $allTypesName);
$smarty->assign('allCatsId', $allCatsId);
$smarty->assign('allCatsName', $allCatsName);
$smarty->assign('alltiles', $alltiles);


// ====================
// = Display template =
// ====================
$smarty->display('TileAdmin.tpl');

?>