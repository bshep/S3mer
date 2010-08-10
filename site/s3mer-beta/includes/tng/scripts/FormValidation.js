//Javascript UniVAL Module

/*
 * ADOBE SYSTEMS INCORPORATED
 * Copyright 2007 Adobe Systems Incorporated
 * All Rights Reserved
 * 
 * NOTICE:  Adobe permits you to use, modify, and distribute this file in accordance with the 
 * terms of the Adobe license agreement accompanying it. If you have received this file from a 
 * source other than Adobe, then your use, modification, or distribution of it requires the prior 
 * written permission of Adobe.
 */

if (typeof KT_FVO == 'undefined') {
	KT_FVO = {};
	KT_FVO_properties = {
		noTriggers: 0,
		noTransactions: 0
	}
}
//configuration variables
$UNI_GLOBALVARNAME = 'KT_FVO';
$UNI_GLOBALVARNAME_MESSAGES = 'UNI_Messages';

$UNI_ATTRNAME_ERRORMESSAGE = 'errormessage';
$UNI_DEFAULTERRORMESSAGE = 'The field \'%s\' has an invalid value !';
$UNI_FORM_SUBMIT_PRIORITY = 10;

$UNI_CLASSNAME_ERROR_LABEL = 'form_validation_field_error_label';
$UNI_CLASSNAME_ERROR_CONTAINER = 'form_validation_field_error_container';
$UNI_CLASSNAME_ERROR_ELEMENT = 'form_validation_field_error_text';
$UNI_CLASSNAME_ERROR_ERROR_ELEMENT = 'form_validation_field_error_error_message';
$UNI_CLASSNAME_ERROR_SS = 'KT_field_error';
$UNI_CLASSNAME_ERROR_FORM = 'form_validation_form_error';

$UNI_DELETE_BUTTON_NAME = /delete/i;
$UNI_INSERT_BUTTON_NAME = /insert/i;
$UNI_UPDATE_BUTTON_NAME = /update/i;
$UNI_CANCEL_BUTTON_NAME = /cancel/i;
$UNI_LOGIN_BUTTON_NAME = /login/i;

/*
Recognized date time masks:
Date
	m/d/yy
	d/m/yy
	d.m.yy
	mm/dd/yyyy
	dd/mm/yyyy
	dd.mm.yyyy
	yyyy-mm-dd
Time 
	h:ii:ss t
	HH:ii:ss
	H:ii:ss
	hh:ii:ss tt
*/

function UNI_isktml(el) {
	var is_ktml = false;
	if (typeof el.name != 'undefined') {
		if (typeof ktmls != 'undefined' && ktmls != null && ktmls.length) {
			var is_ktml = false;
			Array_each(ktmls, function(ktml) {
				if (ktml.name == el.name) {
					is_ktml = ktml;
				}
			});
		}
	}
	return is_ktml;
}

function UNI_date2regexp(txt) {
	return utility.date.date2regexp(txt);
}

function UNI_mask2regexp(txt) {
	txt = txt.replace(/([-\/\[\]()\*\+])/g, '\\$1');
	txt = txt.replace(/9/g, '\\d');
	txt = txt.replace(/\?/g, '.');
	txt = txt.replace(/X/g, '\\w');
	txt = txt.replace(/A/g, '[A-Za-z]');
	var re = new RegExp('^' + txt + '$');
	return re;
}

function UNI_regexp2regexp(txt) {
	var sep = txt.substring(0, 1);
	var pos = txt.lastIndexOf(sep);

	var reg = txt.substring(1, pos);
	var mods = '';
	if (pos+1 <= txt.length-1) {
		var mods = txt.substring(pos+1, txt.length);
	}
	var re = new RegExp(reg, mods);
	return re;
}

function UNI_init_error_elements(el) {
	var ret = {};
	var el_name = el.name;

	var allLabels = document.getElementsByTagName("LABEL");
	for(var i=0; i < allLabels.length; i++) {
		if (allLabels[i].htmlFor == el.id) {
			ret['label'] = allLabels[i];
			break;
		}
	}
	var is_widget = el.getAttribute("wdg:type")!=null;

	if (is_widget) {
		var zel = el;
		while(zel) {
			if(typeof(zel.className) != "undefined" && zel.className.indexOf("widget_container")>=0) {
				break;
			}
			zel = zel.parentNode;
		}
		if (zel) {
			el = zel;
		}
	}

	ret['container'] = el.parentNode;
	while(ret['container'].nodeType != 1) {
		ret['container'] = ret['container'].parentNode;
	}

	var dv = document.getElementById(el_name + '_error_element');
	if (dv != null) {
		ret['error_element'] = dv;
	} else {
		ret['error_element'] = utility.dom.createElement("DIV", {
			'id': el_name + '_error_element'
		});
		var server_side = utility.dom.getElementsByClassName(el.parentNode, $UNI_CLASSNAME_ERROR_SS);
		if (server_side.length > 0) {
			el.parentNode.removeChild(server_side[0]);
		}
		var spn = utility.dom.getElementsByClassName(el.parentNode, 'KT_field_hint')[0];
		if (typeof spn == 'undefined') {
			spn = el;
			if (el.type) {
				if (el.type.toString().toLowerCase() == 'radio') {
					try {
						spn = el.parentNode;
					} catch(e) { spn = el; }
				}
			}
		}
		//find the last element in the container element to insert the error_element after (it should be the last)
		while (spn.nextSibling) {
			spn = spn.nextSibling;
		}
		ret['error_element'] = utility.dom.insertAfter(ret['error_element'], spn);
	}

	return ret;
}

function UNI_fieldok_action(el, props) {
	var errorElements = UNI_init_error_elements(el);
	try {
		utility.dom.classNameRemove(errorElements['label'], $UNI_CLASSNAME_ERROR_LABEL); 
		utility.dom.classNameRemove(errorElements['container'], $UNI_CLASSNAME_ERROR_CONTAINER); 
		utility.dom.classNameRemove(el, $UNI_CLASSNAME_ERROR_ELEMENT); 
		errorElements['error_element'].parentNode.removeChild(errorElements['error_element']);
	} catch(e) { }
	errorElements = undefined;
}

function UNI_required_action(el, props) {
	var errorElements = UNI_init_error_elements(el);
	if (errorElements.label) 
		utility.dom.classNameAdd(errorElements.label, $UNI_CLASSNAME_ERROR_LABEL);
	if (errorElements.container)
		utility.dom.classNameAdd(errorElements.container, $UNI_CLASSNAME_ERROR_CONTAINER);
	if (el)	
		utility.dom.classNameAdd(el, $UNI_CLASSNAME_ERROR_ELEMENT);

	var errorMessage = props['errorMessage'];
	if (errorMessage == '') {
		//TODO : the required message has ONE %s : the element name
		errorMessage = utility.string.sprintf(window[$UNI_GLOBALVARNAME_MESSAGES]["required"], el.name);
	}

	try {
		errorElements['error_element'].innerText = errorMessage;
		errorElements['error_element'].innerHTML = errorMessage;
	} catch(e) { }
	if (errorElements.error_element)
		utility.dom.classNameAdd(errorElements.error_element, $UNI_CLASSNAME_ERROR_ERROR_ELEMENT);
}

function UNI_format_action(el, props) {
	var errorElements = UNI_init_error_elements(el);

	if (errorElements.label) 
		utility.dom.classNameAdd(errorElements.label, $UNI_CLASSNAME_ERROR_LABEL);
	if (errorElements.container)
		utility.dom.classNameAdd(errorElements.container, $UNI_CLASSNAME_ERROR_CONTAINER);
	if (el)	
		utility.dom.classNameAdd(el, $UNI_CLASSNAME_ERROR_ELEMENT);

	var errorMessage = props['errorMessage'];
	if (errorMessage == '') {
		//TODO : the format message has ONE %s : the format name
		//TODO : we only get the interned format name, but we should have the screen format name
		var mesg1 = window[$UNI_GLOBALVARNAME_MESSAGES][props['type'] + '_' + props['format'] ];
		var mesg2 = window[$UNI_GLOBALVARNAME_MESSAGES][props['type'] + '_'];
		if (typeof mesg1 != 'undefined') {
			errorMessage = utility.string.sprintf(window[$UNI_GLOBALVARNAME_MESSAGES]['format'], mesg1);
		} else if (typeof mesg2 != 'undefined') {
			errorMessage = utility.string.sprintf(window[$UNI_GLOBALVARNAME_MESSAGES]['format'], mesg2);;
		} else {
			errorMessage = utility.string.sprintf(window[$UNI_GLOBALVARNAME_MESSAGES]['format'], props['format']);
		}
	}
		
	try {
		errorElements['error_element'].innerText = errorMessage;
		errorElements['error_element'].innerHTML = errorMessage;
	} catch(e) { }
	if (errorElements.error_element)
		utility.dom.classNameAdd(errorElements.error_element, $UNI_CLASSNAME_ERROR_ERROR_ELEMENT);
}

function UNI_boundary_action(el, props, test_min, test_max) {
	sprintf = utility.string.sprintf;
	var errorElements =  UNI_init_error_elements(el);

	if (errorElements.label) 
		utility.dom.classNameAdd(errorElements.label, $UNI_CLASSNAME_ERROR_LABEL);
	if (errorElements.container)
		utility.dom.classNameAdd(errorElements.container, $UNI_CLASSNAME_ERROR_CONTAINER);
	if (el)	
		utility.dom.classNameAdd(el, $UNI_CLASSNAME_ERROR_ELEMENT);

	var prefix = (props['type'] == 'text') ? 'text' : 'other';
	var errorMessage = props['errorMessage'];
	if (errorMessage == '') {
		//TODO : we only get the interned format name, but we should have the screen format name
		if (test_min != null && test_max != null) {
			errorMessage = sprintf(window[$UNI_GLOBALVARNAME_MESSAGES][prefix + '_between'], props['min'], props['max']);
		} else {
			if (test_min != null) {
				errorMessage = sprintf(window[$UNI_GLOBALVARNAME_MESSAGES][prefix + '_min'], props['min']);
			} else {
				errorMessage = sprintf(window[$UNI_GLOBALVARNAME_MESSAGES][prefix + '_max'], props['max']);
			}
		}
	}

	try {
		errorElements['error_element'].innerText = errorMessage;
		errorElements['error_element'].innerHTML = errorMessage;
	} catch(e) { }
	if (errorElements.error_element)
		utility.dom.classNameAdd(errorElements.error_element, $UNI_CLASSNAME_ERROR_ERROR_ELEMENT);
}

function UNI_validateRegExp(el, props) {
	var toret = true;
	var re = UNI_regexp2regexp(props['additional_params']);
	if (!re.exec(el.value)) {
		toret = false;
	}
	return toret;
}

function UNI_validateMask(el, props) {
	var toret = true;
	var re =  UNI_mask2regexp(props['additional_params']);
	if (!re.exec(el.value)) {
		toret = false;
	}
	return toret;
}

function UNI_parse_date(arr, dateMask) {
	return utility.date.parse_date(arr, dateMask);
}


function UNI_dateBuilder(year, month, day, hour, minutes, seconds) {
	var month_length = [31,28,31,30,31,30,31,31,30,31,30,31];
	if (! (parseInt(year) > 0)) { return false; }
	if (! (parseInt(month) > 0 && parseInt(month) <= 12)) { return false; }
	if ((
			(parseInt(year) % 4 == 0) 
			&& 
			(parseInt(year) % 100 != 0)
		) 
		|| 
			(parseInt(year) % 400 == 0)
		) {
			month_length[1] = 29;
	}
	if (! (parseInt(day) > 0 && parseInt(day) <= month_length[parseInt(month)-1])) { return false; }

	month_length[1] = 28;
	if (! (parseInt(hour) >= 0 && parseInt(hour) <= 23)) { return false; }
	if (! (parseInt(minutes) >= 0 && parseInt(minutes) <= 59)) { return false; }
	if (! (parseInt(seconds) >= 0 && parseInt(seconds) <= 59)) { return false; }

	year = utility.math.zeroPad(year, 4);
	month = utility.math.zeroPad(month, 2);
	day = utility.math.zeroPad(day, 2);
	hour = utility.math.zeroPad(hour, 2);
	minutes = utility.math.zeroPad(minutes, 2);
	seconds = utility.math.zeroPad(seconds, 2);

	return year + month + day + hour + minutes + seconds;
}

function UNI_validateDate(el, props) {
	var toret = true;
	var dateMask = props['additional_params'];
	var re = utility.date.date2regexp(dateMask);
	var arr = re.exec(el.value);
	if (!arr) {
		toret = false;
	} else {
		var o = utility.date.parse_date(arr, dateMask);
		var newDate = UNI_dateBuilder(o['year'], o['month'], o['day'], o['hour'], o['minutes'], o['seconds']);
		if (newDate == false) {
			toret = false;
		}
	}

	return toret;
}

function UNI_validate_format_text_color_generic(el, props) {
	var toret = false;
	var validColors = [
			"black",
			"green",
			"silver",
			"lime",
			"gray",
			"olive",
			"white",
			"yellow",
			"maroon",
			"navy",
			"red",
			"blue",
			"purple",
			"teal",
			"fuchsia",
			"aqua"
	];

	var userColor = el.value.toString().toLowerCase();
	Array_each(validColors, function(color, i) {
		if(userColor == color) {
			toret = true;
		}
	});
	
	if(toret == false) {
		UNI_format_action(el, props);
	}
	
	return toret;
}

function UNI_validate_required(el, props) {
	var toret = true;
	if (el.type.toString().toLowerCase() == 'radio') {
		var arr = [];
		Array_each(el.form.elements, function(el2, i2) {
			if (el2.name == el.name) {
				Array_push(arr, el2);
			}
		});
		toret = false;
		Array_each(arr, function(el, i) {
			if (el.checked) {
				toret = true;
			}
		});
		if (!toret) {
			UNI_required_action(el, props);
		}
	} else if (
		el.value == '' ||
		el.value.match(/^<br[^>]*>$/gi) ||
		el.value.match(/^<p[^>]*>(&nbsp;|)<\/p>$/gi) ||
		el.value.match(/^<div[^>]*>(&nbsp;|)<\/div>$/gi) ||
		el.value.match(/^<span[^>]*>(&nbsp;|)<\/span>$/gi) ||
		(el.type.toLowerCase() == 'checkbox' && el.checked == false)
		) {
		UNI_required_action(el, props);
		toret = false;
	}
	return toret;
}

function UNI_validate_generic(el, props) {
	var toret = true;
	if (el.value != '') {
		if (props['additional_params'] != '') {
			var is_a_regexp =/^([^0-9A-Za-z]).*\1[gism]*$/;
			if (props['additional_params'].match(is_a_regexp)) {
				var validator = UNI_validateRegExp;
			} else {
				var validator = UNI_validateMask;
			}
			if (!validator(el, props)) {
				UNI_format_action(el, props);
				toret = false;
			}	
		}

	}
	return toret;
}

function UNI_validate_format_regexp(el, props) {
	var toret = true;
	if (el.value != '') {
		if (!UNI_validateRegExp(el, props)) {
			UNI_format_action(el, props);
			toret = false;
		}
	}
	return toret;
}

function UNI_validate_format_mask(el, props) {
	var toret = true;
	if (el.value != '') {
		if (!UNI_validateMask(el, props)) {
			UNI_format_action(el, props);
			toret = false;
		}
	}
	return toret;
}

function UNI_validate_format_date(el, props) {
	var toret = true;
	if (el.value != '') {
		if (!UNI_validateDate(el, props)) {
			UNI_format_action(el, props);
			toret = false;
		}
	}
	return toret;
}

function UNI_validate_format_text_ip(el, props) {
	var toret = true;
	if (el.value != '') {
		if (!UNI_validateRegExp(el, props)) {
			UNI_format_action(el, props);
			toret = false;
		} else {
			Array_each(el.value.toString().split('.'), function(match) {
				if (parseInt(match) > 255) {
					UNI_format_action(el, props);
					toret = false;
					return;
				}
			});
		}
	}
	return toret;
}

function UNI_validate_minmax(el, props) {
	var toret = true, test_max = true, test_min = true;
	if (el.value != '') {
		if (props['min'] != '') {
			test_min = el.value >= props['min'];
			toret = toret && test_min;
		}
		if (props['max'] != '') {
			test_max = el.value <= props['max'];
			toret = toret && test_max;		
		}
		if (! (test_max && test_min)) {
			UNI_boundary_action(el, props, test_min, test_max);
		}
	}
	return toret;
}

function UNI_validate_minmax_text(el, props) {
	var toret = true, test_max = null, test_min = null;
	if (props['min'] != '') {
		var test_min = (el.value.length >= parseInt(props['min']));
		toret = toret && test_min;
	}
	if (props['max'] != '') {
		var test_max = (el.value.length <= parseInt(props['max']));
		toret = toret && test_max;
	}

	if (! (test_max && test_min)) {
		UNI_boundary_action(el, props, test_min, test_max);
	}
	return toret;
}

function UNI_validate_minmax_numeric(el, props) {
	var toret = true, test_max = null, test_min = null;
	if (props['min'] != '') {
		var test_min = (parseFloat(el.value) >= parseFloat(props['min']));
		toret = toret && test_min;
	}
	if (props['max'] != '') {
		var test_max = (parseFloat(el.value) <= parseFloat(props['max']));
		toret = toret && test_max;		
	}
	if (! (test_max && test_min)) {
		UNI_boundary_action(el, props, test_min, test_max);
	}
	return toret;
}
function UNI_validate_minmax_double(el, props) {
	var toret = true, test_max = null, test_min = null;
	if (props['min'] != '') {
		var test_min = (parseFloat(el.value) >= parseFloat(props['min']));
		toret = toret && test_min;
	}
	if (props['max'] != '') {
		var test_max = (parseFloat(el.value) <= parseFloat(props['max']));
		toret = toret && test_max;		
	}
	if (! (test_max && test_min)) {
		UNI_boundary_action(el, props, test_min, test_max);
	}
	return toret;
}


function UNI_validate_minmax_date(el, props) {
	var toret = true, test_max = null, test_min = null;

	var dateMask = props['additional_params'];
	var re = utility.date.date2regexp(dateMask);
	var arr = re.exec(el.value);
	var date1 = utility.date.parse_date(arr, dateMask);
	date1 = UNI_dateBuilder(date1['year'], date1['month'], date1['day'], date1['hour'], date1['minutes'], date1['seconds']);
	if (props['min'] != '') {
		var arr = re.exec(props['min']);
		var date_min = utility.date.parse_date(arr, dateMask);
		var date_min = UNI_dateBuilder(date_min['year'], date_min['month'], date_min['day'], date_min['hour'], date_min['minutes'], date_min['seconds']);
		var test_min = (parseInt(date1) >= parseInt(date_min));
		toret = toret && test_min;
	}
	if (props['max'] != '') {
		var arr = re.exec(props['max']);
		var date_max = utility.date.parse_date(arr, dateMask);
		var date_max = UNI_dateBuilder(date_max['year'], date_max['month'], date_max['day'], date_max['hour'], date_max['minutes'], date_max['seconds']);
		
		var test_max = (parseInt(date1) <= parseInt(date_max));
		toret = toret && test_max;		
	}
	if (! (test_max && test_min)) {
		UNI_boundary_action(el, props, test_min, test_max);
	}
	return toret;
}

//Attaches itself to the form submit buttons and sets checking = on/off, depending on the button name
//TODO : redo insert / update / delete logic ??? 
function UNI_buttonHandler(e) {
	var o = utility.dom.setEventVars(e);
	if (o.targ.name.match($UNI_DELETE_BUTTON_NAME) 
		||o.targ.name.match($UNI_INSERT_BUTTON_NAME) 
		||o.targ.name.match($UNI_UPDATE_BUTTON_NAME) 
		||o.targ.name.match($UNI_LOGIN_BUTTON_NAME) 
		||(typeof o.targ.tagName != 'undefined' && o.targ.type.toLowerCase() == 'submit')) {
		o.targ.form.removeAttribute('haschanged');
	}
	if (o.targ.name.match($UNI_DELETE_BUTTON_NAME) || o.targ.name.match($UNI_CANCEL_BUTTON_NAME)) {
		o.targ.form.setAttribute('donotcheck', '1');
		window.UNI_buttonpressed = o.targ.name;
	} else if((o.targ.name.match($UNI_INSERT_BUTTON_NAME) || o.targ.name.match($UNI_UPDATE_BUTTON_NAME))) {
		o.targ.form.setAttribute('donotcheck', '0');
		window.UNI_buttonpressed = o.targ.name;
	} else {
		o.targ.form.setAttribute('donotcheck', '0');
		window.UNI_buttonpressed = o.targ.name;
	}

}

function UNI_navigateCancel(event, str) {
	var o = utility.dom.setEventVars(event);
	if (o.targ.form.getAttribute('haschanged') != null) {
		if (confirm(utility.string.getInnerText(UNI_Messages['form_was_modified']))) {
			o.targ.form.removeAttribute('haschanged');
			var loc = str;
			try {
				if (loc.indexOf("/") == 0) {
					//use the absolute path
				} else if (!loc.match(/\w+:\/\//)) {
					loc = document.getElementsByTagName('base')[0].href.toString() + loc;
				}
			} catch(e) { loc = str };
			if (typeof $ctrl != 'undefined') {
				$ctrl.loadPanels(loc);
			} else {
				window.location.href = loc;
			}
			return true;
		} else {
			utility.dom.stopEvent(o.e);
			return false;
		}
	} else {
		var loc = str;
		try {
			if (loc.indexOf("/") == 0) {
				//use the absolute path
			} else if (!loc.match(/\w+:\/\//)) {
				loc = document.getElementsByTagName('base')[0].href.toString() + loc;
			}
		} catch(e) { loc = str };

		if (typeof $ctrl != 'undefined') {
			$ctrl.loadPanels(loc);
		} else {
			window.location.href = loc;
		}
		return true;
	}
}
var UNI_navigateAway_locked = false;
function UNI_navigateAway(e) {
	if (UNI_navigateAway_locked) {
		UNI_navigateAway_locked = false;
	} else {
		var frms = document.forms;
		var toret = false;
		for ( var i = 0; i < frms.length; i++) {
			var frm = frms[i];
			if (frm.getAttribute('haschanged') != null) {
				toret = true;
			}
		}
		if (toret == true) {
			return utility.string.getInnerText(UNI_Messages['form_was_modified']);
		}
	}
}

//attach events to form submittal buttons
//TODO : redo insert / update / delete logic ??? 
function UNI_attachToButtons() {
	if (is.ie && is.mac) {
		return;
	}
	var frms = document.getElementsByTagName('form');
	for (var i = 0; i < frms.length; i++) {
		var frm = frms[i];
		for (var j = 0; frm.elements.length; j++) {
			var el = frm.elements[j];
			if (el != null) {
				if (el.name) {
					var attached = false;
					//var str = 'on' + (is.safari?'mousedown':'focus');
					//alert(str);
					if (el.name.toString().match($UNI_DELETE_BUTTON_NAME) 
					 || el.name.toString().match($UNI_CANCEL_BUTTON_NAME)
					 ) {
						attached = true;
						el['on' + (is.safari?'mousedown':'focus')] = UNI_buttonHandler;
					}
					if (el.name.toString().match($UNI_INSERT_BUTTON_NAME)
					 || el.name.toString().match($UNI_UPDATE_BUTTON_NAME)
					 ) {
						attached = true;
						el['on' + (is.safari?'mousedown':'focus')] = UNI_buttonHandler;
					}
					if (el.type.toLowerCase() == 'submit' && !attached) {
						el['on' + (is.safari?'mousedown':'focus')] = UNI_buttonHandler;
					}
				}
			} else {
				break;
			}
		}
	}
}

//attach to all the forms in the page, called on "onload"
function UNI_attachToForm() {
	GLOBAL_registerFormSubmitEventHandler('UNI_formSubmittalHandler', $UNI_FORM_SUBMIT_PRIORITY);
	if (is.windows && is.ie) {
		GLOBAL_registerFormSubmitEventHandler('UNI_enableButtonsIEBug', $UNI_FORM_SUBMIT_PRIORITY+1);
	}
	UNI_attachToButtons();
}


function UNI_attachEmptyProps(props) {
	Array_each(['colname', 'required', 'type', 'format', 'additional_params', 'min', 'max', 'errorMessage'], 
		function(prop_name) {
		if (typeof props[prop_name] == 'undefined') {
			props[prop_name] = '';
		}
	});	
	return props;
}

// for the moment : error 0 : no error, error 1 : required, error 2 : illegal value
function UNI_workOnElement(el) {
	var toret = true;

	if (el && el.type && el.type.toLowerCase() == 'hidden') {
		if (!UNI_isktml(el)) {
			return true;
		}
	}
	if (typeof window[$UNI_GLOBALVARNAME] == 'undefined') {
		return true;
	}
	
	var elname = el.name;
	if (!window[$UNI_GLOBALVARNAME][el.name]) {
		elname = elname.replace(/_\d+$/, '');
	}
	var props = UNI_attachEmptyProps(window[$UNI_GLOBALVARNAME][elname]);

	// Validation Step 1 : REQUIRED
	if (props['required']) {
		toret = toret && UNI_validate_required(el, props);
	}
	if (!toret) {
		return toret;
	}

	// Validation Step 2 : FORMAT UNI_validate_[type]_format
	//UNI_validate_text_ip, UNI_validate_text_cc_generic
		//if they don't exist, try to exec UNI_validate_text
			//if they don't exist, try to exec UNI_validate_generic
	if (props['format'] != '' || props['type'] == 'mask' || props['type'] == 'regexp') {
		var functor_specific = window['UNI_validate_format_' + props['type'] + '_' + props['format']];
		var functor_type = window['UNI_validate_format_' + props['type']];
		var functor_generic = UNI_validate_generic;
		if (typeof functor_specific == 'function') {
			toret = toret && functor_specific(el, props);
		} else if (typeof functor_type == 'function') {
			toret = toret && functor_type(el, props);
		} else {
			toret = toret && functor_generic(el, props);
		}
	}
	if (!toret) {
		return toret;
	}
	//Validation Step 3 : min, max
	if (el.value != '') {
		if (props['min'] != '' || props['max'] != '') {
			var functor_specific = window['UNI_validate_minmax_' + props['type']];
			var functor_generic = UNI_validate_minmax;
			if (typeof functor_specific == 'function') {
				toret = toret && functor_specific(el, props);
			} else if (typeof functor_generic == 'function') {
				toret = toret && functor_generic(el, props);
			}
		}
	}
	if (!toret) {
		return toret;
	}

	//Validation Step 4 : delete errors from old fields that have them
	if (toret == true) {
		UNI_fieldok_action(el, props);
	}
	return toret;
}

function UNI_disableButtons(frm, regexp, flag) {
	Array_each(frm.getElementsByTagName('input'), function(button, i) {
		if (button.type && Array_indexOf(['submit', 'button'], button.type.toLowerCase()) >= 0) {
			if (button.className == 'mxw_v' || button.className == 'mxw_add') {
				return true;
			}
			if (button.name.match(regexp)) {
				button.disabled = flag;
			}
		}
	});
}

//what to do when form is submitted
function UNI_formSubmittalHandler(e) {
	if (is.ie && is.mac) {
		return true;
	}

	focus_happened = false;
	var o = utility.dom.setEventVars(e);
	var frm = o.targ;
	frm = utility.dom.getParentByTagName(frm, 'form');
	var returnHandler = true;

	frm.removeAttribute('haschanged');

	//this code "gets" the depressed button in the form, 
	//or finds it, if the enter key has been pressed (no button defined)
	if (typeof window.UNI_buttonpressed != 'undefined') {
		var button_regexp = window.UNI_buttonpressed;
	} else {
		var form_buttons = [];
		Array_each(frm.getElementsByTagName('input'), function(button, i) {
		if (button.type && (
			button.type.toLowerCase() == 'submit' || 
			button.type.toLowerCase() == 'button' 
			)) {
				Array_push(form_buttons, button);
			}
		});
		if (form_buttons.length == 1) {
			var button_regexp = form_buttons[0].name;
		} else {
			var update = false;
			var insert = false;
			var login = false;
			for (var i = 0; i < form_buttons.length; i++) {
				var button = form_buttons[i];
				if (button.name.toString().match($UNI_UPDATE_BUTTON_NAME)) {
					update = true;
				}
				if (button.name.toString().match($UNI_INSERT_BUTTON_NAME)) {
					insert = true;
				}
				if (button.name.toString().match($UNI_LOGIN_BUTTON_NAME)) {
					login = true;
				}
			}
			if (update) {
				var button_regexp = $UNI_UPDATE_BUTTON_NAME;
			} else {
				if (insert) {
					var button_regexp = $UNI_INSERT_BUTTON_NAME;
				} else {
					if (login) {
						var button_regexp = $UNI_LOGIN_BUTTON_NAME;
					}
				}
			}
		}
	}
	//
	Array_each(frm.getElementsByTagName('input'), function(button, i) {
		if (button.type && (
			button.type.toLowerCase() == 'submit' || 
			button.type.toLowerCase() == 'button' 
			)) {
			if (button.className == 'mxw_v' || button.className == 'mxw_add') {
				return true;
			}
			if (button.name.match(button_regexp)) {
				var hd = utility.dom.createElement('input', {
					'type' : 'hidden', 
					'name': button.name, 
					'value': button.value
				});
				hd = frm.appendChild(hd);
			}
		}
	});

	if (frm.getAttribute('donotcheck') == '1') {
		return true;
	}

	var is_update_form = true;
	Array_each(utility.dom.getElementsByTagName(document, 'input'), function(input) {
		if (typeof input.type != 'undefined' && typeof input.name != 'undefined') {
			if (input.type.toLowerCase() == 'hidden' && input.name.toString().match(/^kt_pk/)) {
				if (input.value && input.value == 'KT_NEW') {
					is_update_form = false;
				}
			}
			
		}
	});

	var arr = frm.elements;
	var already_checked = [];
	Array_each(arr, function(el, i) {
		if (Array_indexOf(already_checked, el) >= 0) {
			return;
		}
		if (typeof el.tagName != 'undefined' && el.tagName.toLowerCase() == 'fieldset') {
			return;
		}

		if (el.type.toString().toLowerCase() == 'radio') {
			Array_each(frm.elements, function(el2, i2) {
				if (el2.name == el.name) {
					Array_push(already_checked, el2);
				}
			});
		}
		var tagname = el.tagName;
		var elname = el.name;
		if (!elname) return;
		if (typeof window[$UNI_GLOBALVARNAME] != 'undefined' && typeof window[$UNI_GLOBALVARNAME][elname] == 'undefined') {
			if (!is_update_form) {
				elname = elname.replace(/_1$/, '');
			} else {
				elname = elname.replace(/_\d+$/, '');
			}
		}
		if (typeof window[$UNI_GLOBALVARNAME] != 'undefined' && typeof window[$UNI_GLOBALVARNAME][elname] != 'undefined') {
			var tmp = UNI_workOnElement(el);
			if (!tmp) {
				if (!focus_happened) {
					try { el.focus(); focus_happened = true;} catch(e) { focus_happened = false; }
				}
			}
			returnHandler = returnHandler && tmp;
		}
	});

	if (!returnHandler) {
		frm.setAttribute('fvo_error', '1');
		button_regexp = '';
		if (Array_indexOf(utility.dom.getClassNames(frm), 'KT_tngformerror') >= 0) {
			utility.dom.classNameAdd(frm, $UNI_CLASSNAME_ERROR_FORM);
		}
		return false;
	}

	try {
		utility.dom.classNameRemove(frm, $UNI_CLASSNAME_ERROR_FORM);
	} catch(e) { }

	return returnHandler;
}

function UNI_enableButtonsIEBug(e) {
	var o = utility.dom.setEventVars(e);
	var frm = o.targ;
	frm = utility.dom.getParentByTagName(frm, 'form');
	var regexps = [/[a-z]:\\.*$/i, /\\\\.*$/i];
	var invalid_file = true;
	Array_each(frm.getElementsByTagName('input'), function(el) {
		if (el.type) {
			if (el.type.toLowerCase() == 'file') {
				var flag = false;
				for (var i = 0; i < regexps.length; i++) {
					if (el.value.match(regexps[i])) {
						flag = true;
					}
				}
				invalid_file = invalid_file && flag;
			}
		}
	});
	if (!invalid_file) {
		UNI_disableButtons(frm, /.*/, false);	
	}
	return true;
}

function UNI_handle_required(form_label) {
	var htmlFor = form_label.htmlFor;
	var focus_happened = false;
	if (htmlFor) {
		var el = (is.ie&&htmlFor.toLowerCase()=="description")?document.body.all(htmlFor):document.getElementById(htmlFor);
		if (typeof el != 'undefined' && el != null && typeof window[$UNI_GLOBALVARNAME] != 'undefined') {
			if (typeof el.name == 'undefined' || el.name == null) {
				return;
			}
			var elname = el.name;

			if (!window[$UNI_GLOBALVARNAME][el.name]) {
				elname = elname.replace(/_\d+$/, '');
			}
			var validate_object = window[$UNI_GLOBALVARNAME][elname];
			var ignore_this = false;
			if (el.type.toLowerCase() == 'radio') {
				if (form_label && form_label.parentNode && 
					el && el.parentNode && 
					form_label.parentNode == el.parentNode) {
					var ignore_this = true;
				}
			}
			if (
				typeof validate_object != 'undefined' && 
				typeof validate_object.required != 'undefined' && 
				validate_object.required && 
				!ignore_this
			) {
				var txt = utility.dom.createElement("SPAN", {className: 'KT_required'});
				txt.innerHTML = "*";
				form_label.appendChild(txt);
			}
			var server_side = utility.dom.getElementsByClassName(el.parentNode, $UNI_CLASSNAME_ERROR_SS);
			if (server_side.length > 0 && !focus_happened) {
				try {
					el.focus();
				} catch(e) { }
				focus_happened = true;
				//login form
				if (Array_indexOf(utility.dom.getClassNames(el.form), 'KT_tngformerror') >= 0) {
					//this is a login form
					utility.dom.classNameAdd(el.form, $UNI_CLASSNAME_ERROR_FORM);
				}
			}
		}
	}
}

function UNI_handle_changed(form_label) {
	var htmlFor = form_label.htmlFor;
	var focus_happened = false;
	if (htmlFor) {
		var el = (is.ie&&htmlFor.toLowerCase()=="description")?document.body.all(htmlFor):document.getElementById(htmlFor);
		if (typeof el != 'undefined' && el != null) {
			if (typeof el.name == 'undefined' || el.name == null) {
				return;
			}

			var elname = el.name;
			var ignore_this = false;
			if (el.type.toLowerCase() == 'radio') {
				if (form_label && form_label.parentNode && 
					el && el.parentNode && 
					form_label.parentNode == el.parentNode) {
					var ignore_this = true;
				}
			}
			if (!ignore_this) {
				utility.dom.attachEvent(el, 'change', function(e) {
					try {
						var form = utility.dom.getParentByTagName(this, 'form');
						form.setAttribute('haschanged', '1');
					} catch(e) { }
				});
			}
		}
	}
}

function UNI_form_attach() {
	var tmp, has_update = false;
	var div = utility.dom.getElementsByClassName(document, 'KT_tng', 'div');
	var start_el = null;
	if (div.length == 1) {
		start_el = div[0];
	} else {
		start_el = utility.dom.getElementsByClassName(document, 'KT_tngtable', 'table')[0];
	}
	if (typeof start_el != 'undefined') {
	Array_each(start_el.getElementsByTagName('input'), function(button, i) {
		if (button.type && Array_indexOf(['submit', 'button'], button.type.toLowerCase()) >= 0) {
			if (button.className == 'mxw_v' || button.className == 'mxw_add') { return; }
			if (button.name.match($UNI_UPDATE_BUTTON_NAME)) { has_update = true; }
		}
	});
	if (has_update) {
		var forms = document.getElementsByTagName('form');
		if (typeof KT_FVO_properties != 'undefined' && typeof KT_FVO_properties['noTriggers'] && typeof KT_FVO_properties['noTransactions'] != 'undefined') {
			var noTriggers = parseInt(KT_FVO_properties['noTriggers'], 10);
			var noTransactions = parseInt(KT_FVO_properties['noTransactions'], 10);
			if (noTriggers == 1 && noTransactions > 1) {
				for (i in KT_FVO) {
					var re = new RegExp('^' + i, 'g');
					Array_each(forms, function(form) {
						Array_each(form.elements, function(el) {
							if (el && el.name && el.name.match(re)) {
								if (el.tagName.toLowerCase() == 'input' && el.type) {
									if (Array_indexOf(['file', 'password'], el.type.toLowerCase())>= 0) {
										KT_FVO[i]['required'] = false;
									}
								}
							}
						});
					});
				}
			}
		}
	}
	}

	try {
		if (typeof KT_FVO['kt_login_user']!='undefined') {
			var user_name_ctrl = document.getElementById("kt_login_user");
			if (user_name_ctrl) {
				user_name_ctrl.focus();
			}
		}
	} catch(err) {}

	Array_each(utility.dom.getElementsByClassName(document, 'KT_tngtable', 'TABLE'), function(table, table_index){
		if (table.getAttribute('kt_uni_attached') == null) {
			table.setAttribute('kt_uni_attached', 'true');
			// add classes to containers
			Array_each(utility.dom.getElementsByTagName(table, 'label'), function(form_label) {
				UNI_handle_required(form_label);
				UNI_handle_changed(form_label);
			});
		}
	});
	window["UNI_uniqueid"] = new UIDGenerator();
	UNI_attachToForm();
}

if (typeof UNI_form_attach_executed == 'undefined') {
	utility.dom.attachEvent2(window, 'onload', UNI_form_attach);
	UNI_form_attach_executed = true;
}

window.onbeforeunload = UNI_navigateAway;
