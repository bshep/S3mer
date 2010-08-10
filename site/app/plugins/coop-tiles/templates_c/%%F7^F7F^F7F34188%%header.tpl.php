<?php /* Smarty version 2.6.19, created on 2008-07-03 02:08:01
         compiled from header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'header.tpl', 6, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title><?php echo ((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, 's3mer') : smarty_modifier_default($_tmp, 's3mer')); ?>
</title>
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.5.1/build/reset-fonts-grids/reset-fonts-grids.css" />
	<link rel="stylesheet" href="styles/tiles.css" type="text/css" charset="utf-8" />
	</head>
	<body>