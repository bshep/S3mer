<?php

function smarty_html_select_gender($params, &$smarty) 
{
	$name = '';
	$extra = '';
	$value = 'M';
	
	foreach($params as $_key => $_val) {
		switch($_key) {
			case 'name':
				$$_key = (string)$_val;
				break;
			case 'value':
				$$_key = (string)$_val;
				break;					
			default:
				if(!is_array($_val)) {
					$extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
				} else {
					$smarty->trigger_error("html_options: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
				}
				break;
		}
	}
	
	$_html_result = '';
	
	$posible_options = array( "M", "F" );
	
	foreach($posible_options as $_val) {
		if( $_val == $value ) {
			$_html_result .= "\t".'<option label="'.$_val.'" value="'.$_val.'" selected="selected">'.$_val.'</option>'."\n";
		} else {
			$_html_result .= "\t".'<option label="'.$_val.'" value="'.$_val.'">'.$_val.'</option>'."\n";
		}
	}
	
	if(!empty($name)) {
		$_html_result = '<select name="' . $name . '"' . $extra . '>' . "\n" . $_html_result . '</select>' . "\n";
	}

	return $_html_result;
}

function smarty_html_select_status($params, &$smarty) 
{
	$name = '';
	$extra = '';
	$value = 'Single';
	
	foreach($params as $_key => $_val) {
		switch($_key) {
			case 'name':
				$$_key = (string)$_val;
				break;
			case 'value':
				$$_key = (string)$_val;
				break;					
			default:
				if(!is_array($_val)) {
					$extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
				} else {
					$smarty->trigger_error("html_options: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
				}
				break;
		}
	}
	
	$_html_result = '';
	
	$posible_options = array( "Single", "Married", "Divorced", "Widowed" );
	
	foreach($posible_options as $_val) {
		if( $_val == $value ) {
			$_html_result .= "\t".'<option label="'.$_val.'" value="'.$_val.'" selected="selected">'.$_val.'</option>'."\n";
		} else {
			$_html_result .= "\t".'<option label="'.$_val.'" value="'.$_val.'">'.$_val.'</option>'."\n";
		}
	}
	
	if(!empty($name)) {
		$_html_result = '<select name="' . $name . '"' . $extra . '>' . "\n" . $_html_result . '</select>' . "\n";
	}

	return $_html_result;
}

function smarty_html_select_contact($params, &$smarty) 
{
	$name = '';
	$extra = '';
	$value = 'Phone';
	
	foreach($params as $_key => $_val) {
		switch($_key) {
			case 'name':
				$$_key = (string)$_val;
				break;
			case 'value':
				$$_key = (string)$_val;
				break;					
			default:
				if(!is_array($_val)) {
					$extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
				} else {
					$smarty->trigger_error("html_options: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
				}
				break;
		}
	}
	
	$_html_result = '';
	
	$posible_options = array( "Phone", "Email", "Postal Mail" );
	
	foreach($posible_options as $_val) {
		if( $_val == $value ) {
			$_html_result .= "\t".'<option label="'.$_val.'" value="'.$_val.'" selected="selected">'.$_val.'</option>'."\n";
		} else {
			$_html_result .= "\t".'<option label="'.$_val.'" value="'.$_val.'">'.$_val.'</option>'."\n";
		}
	}
	
	if(!empty($name)) {
		$_html_result = '<select name="' . $name . '"' . $extra . '>' . "\n" . $_html_result . '</select>' . "\n";
	}

	return $_html_result;
}

?>