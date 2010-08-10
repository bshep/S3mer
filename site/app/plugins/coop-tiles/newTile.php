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


$type = $_POST["TileType"];
$cat = $_POST["Category"];
$content = $_POST["description"];
$url = $_POST["url"];

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

if (isset($type)) {
	
	$sql="INSERT INTO tiles (category, type, content, url) VALUES ('$cat', '$type', '$content', '$url')";
	$result=mysql_query($sql);
	
}

header('Location: index.php');

?>