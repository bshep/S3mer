<?php

//$path = ".\libs";
//
//set_include_path(get_include_path() . PATH_SEPARATOR . $path);

// load Smarty library
require_once('libs/Smarty.class.php');
require_once('lang/language_class.php');
require_once('smarty_functions.php');

class smarty_config extends Smarty 
{
   function smarty_config()
   {
        // Class Constructor. 
        // These automatically get set with each new instance.

		$this->Smarty();

		$this->template_dir = './templates';
		$this->config_dir = './smarty/configs';
		$this->cache_dir = './smarty/smarty_cache';
		$this->compile_dir = './smarty/smarty_templates_c';

		$this->assign('app_name', 's3mer');
		
		// Setup Language translator
		$this->register_block("t", "smarty_t", false);

		$this->register_function("html_select_gender","smarty_html_select_gender");
		$this->register_function("html_select_status","smarty_html_select_status");
		$this->register_function("html_select_contact","smarty_html_select_contact");
		
		//$this->caching = true;		
		
   }
   
}

?>