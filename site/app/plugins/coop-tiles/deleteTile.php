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

// ==================
// = Post Variables =
// ==================


$idtodelete = $_POST["idtodelete"];

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

if (isset($idtodelete)) {
	
	$sql="DELETE FROM `tiles` where id =".$idtodelete;
	$result=mysql_query($sql);
	
}

header('Location: admin.php');

?>