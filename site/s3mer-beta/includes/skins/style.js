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

function show_as_buttons_func() {
	var toret = false;
	if (!(typeof $NXT_LIST_SETTINGS == 'undefined' || typeof $NXT_LIST_SETTINGS['show_as_buttons'] == 'undefined' || $NXT_LIST_SETTINGS['show_as_buttons'] == false)) {
		toret = true;
	}
 	if (!(typeof $NAV_SETTINGS == 'undefined' || typeof $NAV_SETTINGS['show_as_buttons'] == 'undefined' || $NAV_SETTINGS['show_as_buttons'] == false)) {
		toret = true;
	}
	return toret;
}
show_as_buttons = "show_as_buttons_func()";
not_show_as_buttons = "!" + show_as_buttons;
/*
 * transforms a link to a button, keeping the link inner text, and adding the onclick event
 */
function KT_style_replace_with_button(el, add_event) {
	if (typeof add_event == 'undefined') {
		add_event = false;
	}
	var elnou = utility.dom.createElement('input', {
		'type' : 'button', 
		'value': el.innerHTML
	});

	el.style.display = 'none';
	elnou = utility.dom.insertAfter(elnou, el);

	if (add_event) {
		var onclick = el.onclick;
		elnou.onclick = onclick;
	}

	elnou.style.visibility = el.style.visibility;
	if (el.innerHTML == '') {
		elnou.style.display = 'none';
	}

	return elnou;
}

function KT_style_modify_custom_links(el) {
    var classes = utility.dom.getClassNames(el);
    if (1
    &&  Array_indexOf(classes, 'KT_link') < 0
    ) {
        return;
    }

    var elnou = KT_style_replace_with_button(el);
    /*utility.dom.attachEvent(*/elnou.onclick = function(e) {
        var a = this.previousSibling;
        if (!a.onclick) {
            var act = utility.dom.getLink(a);
            var parts = act.toString().split('?');
            if (parts.length == 1) {
                parts[1] = '';
            }
            var qs = new QueryString(parts[1]); var action_url = parts[0], variables = [];
            Array_each(qs.keys, function(key, i) {
                Array_push(variables, [key, qs.values[i]]);
            });

            var frm = utility.dom.createElement(
                "FORM", 
                {'action': act, 'method': 'GET', 'style': "display: none"}
            );
            Array_each(variables, function(input, i){
                frm.appendChild(utility.dom.createElement('INPUT', {'type': 'hidden', 'id': input[0], 'name': input[0], 'value': input[1]}));
            });

            frm = document.body.appendChild(frm);
            frm.submit();
        } else {
            var to_exec = a.onclick;
            a.onclick();
        }
    };/*);*/
    //elnou.className = 'button_big';
}

//[mtm]_[detail_key_value]
var tng_mtm_detail_key_re = /^mtm_(\d+)$/;
function tng_form_enable_details (checkbox_name) {
	var cbx = document.getElementById(checkbox_name);
	var state = !cbx.checked;
	var parts = checkbox_name.match(tng_mtm_detail_key_re);
	var related_input_re = new RegExp("^mtm_(.+?)_" + parts[1] + "$", "");

	Array_each(cbx.form.elements, function(input) {
		var input_name = input.name;
		if (input_name && related_input_re.test(input_name)) {
			if (typeof(input.widget_id) == 'undefined') {
				if (input.disabled != state) {
					input.disabled = state;
				}
			} else {
				try {
					window[input.widget_type][input.widget_id].setEnabled(!state);
				} catch(err) {}
			}
		}
	});
}


/*
 * this array holds the transformations for the list / form elements
 * each array item is an object with tthe following properties:
 * 	* selector : the string passwd to utility.dom.getElementsBySelector
 * 	* transform : transformation function whichch has a single parameter : the element to handle
 * 	* eval : condition which tells if the transformation is executed
 */
$TRANSFORMATIONS = [
	{
		'selector': function() {
			//'div.KT_tnglist div.KT_bottombuttons a.KT_edit_op_link'
			//'div.KT_tnglist div.KT_bottombuttons a.KT_delete_op_link', 
			//'div.KT_tnglist div.KT_bottombuttons a.KT_additem_op_link', 
			//'div.KT_tnglist div.KT_opbuttons a.KT_edit_op_link'
			//'div.KT_tnglist div.KT_opbuttons a.KT_delete_op_link', 
			//'div.KT_tnglist div.KT_opbuttons a.KT_additem_op_link', 
			var toret = [];
			for (var i = 0; i < $lists.length; i++) {
				if ($lists[i].kt_styles_attached) {
					continue;
				}
				var as = $lists[i].bottombuttons.getElementsByTagName('a');
				for (var j = 0; j < as.length; j++) {
					if (/(KT_edit_op_link|KT_delete_op_link|KT_additem_op_link)/.test(as[j].className)) {
						toret.push(as[j]);
					}
				}
				if ($lists[i].topbuttons) {
					var as = $lists[i].topbuttons.getElementsByTagName('a');
					for (var j = 0; j < as.length; j++) {
						if (/(KT_edit_op_link|KT_delete_op_link|KT_additem_op_link)/.test(as[j].className)) {
							toret.push(as[j]);
						}
					}
				}
			}
			return toret;
		}, 
		'transform': function(el) { 
			var elnou = KT_style_replace_with_button(el, true);
		}, 
		'eval': show_as_buttons
	}, 
	{
		'selector': function() {
			//'div.KT_tnglist th.KT_sorter a.KT_move_op_link', 
			var toret = [];
			for (var i = 0; i < $lists.length; i++) {
				if ($lists[i].kt_styles_attached) {
					continue;
				}
				var ths = $lists[i].table.getElementsByTagName('th');
				for (var j = 0; j < ths.length; j++) {
					if (/KT_sorter/.test(ths[i].className)) {
						var as = ths[i].getElementsByTagName('a');
						for (var k = 0; k < as.length; k++) {
							if (/KT_move_op_link/.test(as[k].className)) {
								toret.push(as[k]);
								break;
							}
						}
					}
				}
			}
			return toret;
		}, 
		'transform': function(el) {
			var elnou = KT_style_replace_with_button(el, true);
			elnou.style.display = 'none';
		}, 
		'eval': show_as_buttons
	}, 
	{
		'selector' : function() {
			var toret = [];
			for (var i = 0; i < $lists.length; i++) {
				if ($lists[i].kt_styles_attached) {
					continue;
				}
				var as = $lists[i].table.getElementsByTagName('a');
				for (var j = 0; j < as.length; j++) {
					if (/(KT_edit_link|KT_moveup_link|KT_movedown_link|KT_delete_link|KT_link)/.test(as[j].className)) {
						toret.push(as[j]);
					}
				}
			}
			return toret;
		}, 
		'transform': function(el) {
			var elnou = KT_style_replace_with_button(el);
			elnou.onclick = function(e) {
				var a = this.previousSibling;
				if (/(KT_movedown_link|KT_moveup_link|KT_delete_link)/.test(a.className)) {
					var to_exec = a.onclick;
					try {
						a.onclick(e);
					} catch(e) { }
				} else if (/(KT_link)/.test(a.className)) {
					if (!a.onclick) {
						var act = utility.dom.getLink(a);
						var parts = act.toString().split('?');
						if (parts.length == 1) {
							parts[1] = '';
						}
						var qs = new QueryString(parts[1]); var action_url = parts[0], variables = [];
						Array_each(qs.keys, function(key, i) {
							Array_push(variables, [key, qs.values[i]]);
						});

						var frm = utility.dom.createElement(
							"FORM", 
							{'action': act, 'method': 'GET', 'style': "display: none"}
						);
						Array_each(variables, function(input, i){
							frm.appendChild(utility.dom.createElement('INPUT', {'type': 'hidden', 'id': input[0], 'name': input[0], 'value': input[1]}));
						});

						frm = document.body.appendChild(frm);
						if (typeof PanelForm_overrideSubmit == 'function') {
							frm.submit = PanelForm_overrideSubmit;
						}
						frm.submit();
					} else {
						var to_exec = a.onclick;
						a.onclick();
					}
				} else if (/(KT_edit_link)/.test(a.className)) {
					try {
						var o = utility.dom.setEventVars(e);
						var table = utility.dom.getParentByTagName(this, 'table');
						var row = utility.dom.getParentByTagName(this, 'tr');

						var tmp = utility.dom.getElementsByClassName(row, 'id_checkbox')[0];
						var myinput = null;
						if (tmp.type && tmp.type.toLowerCase() == 'checkbox' && tmp.name.toString().match(/^kt_pk/)) {
							myinput = tmp;
						}

						var inputs = utility.dom.getElementsByClassName(table, 'id_checkbox');
						Array_each(inputs, function(input) {
							if (input.type && input.type.toLowerCase() == 'checkbox' && 
								input.name.toString().match(/^kt_pk/)) {
								input.checked = (input == myinput);
							}
						});
						nxt_list_edit_link_form(this, myinput.previousSibling.href);
					} catch(e) {
						window.location.href = a.href;
					}					
				} else {
					window.location.href = a.href;
				}
			};/*);*/
			var move_up = /KT_moveup_link/.test(el.className);
			var move_down = /KT_movedown_link/.test(el.className);
			if (move_up || move_down) {
				if (move_up && typeof $nxt_move_up_background_image != 'undefined' || move_down && typeof $nxt_move_down_background_image != 'undefined') {
					elnou.value = "";
				}
				elnou.className = 'button_smallest KT_button_move_' + (move_up?'up':'down');
			} else {
				elnou.className = 'button_big';
			}

		}, 
		'eval': show_as_buttons
	},
	{
		/* ajaxify the back to master link*/
		'selector' : function() {
			var toret = [];
			if (typeof $ctrl != 'undefined') {
				for (var i = 0; i < $lists.length; i++) {
					if ($lists[i].kt_styles_attached) {
						continue;
					}
					var trs = utility.dom.getElementsByClassName($lists[i].inner, "KT_masterlink", "TR");
					for (var j=0; j<trs.length; j++) {
						var links = trs[j].getElementsByTagName("A");
						for (var k=0; k < links.length; k++) {
							if (links[k].href.indexOf('includes/nxt/back.php') != -1) {
								toret.push(links[k]);
							}
						}
					}
				}
			}
			return toret;
		},
		'transform': function(el) {
			el.onclick = function() {
				$ctrl.loadPanels(el.href);	
				return false;
			}
		},
		'eval': 1
	},
	{
		'selector' : function() {
			var toret = [];
			for (var i = 0; i < $lists.length; i++) {
				if ($lists[i].kt_styles_attached) {
					continue;
				}
				var as = $lists[i].bottombuttons.getElementsByTagName('a');
				for (var j = 0; j < as.length; j++) {
					if (/KT_link/.test(as[j].className)) {
						toret.push(as[j]);
					}
				}
				if ($lists[i].topbuttons) {
					var as = $lists[i].topbuttons.getElementsByTagName('a');
					for (var j = 0; j < as.length; j++) {
						if (/KT_link/.test(as[j].className)) {
							toret.push(as[j]);
						}
					}
				}
			}
			return toret;
		}, 
		'transform': KT_style_modify_custom_links, 
		'eval': show_as_buttons
	}, 
	{
		'selector' : function() {
			var toret = [];
			if ($lists.length > 0) {
				for (var i = 0; i < $lists.length; i++) {
					if ($lists[i].kt_styles_attached) {
						continue;
					}
					if ($lists[i].toptextnav) {
						var as = $lists[i].toptextnav.getElementsByTagName('a');
						for (var j = 0; j < as.length; j++) {
							if (/(first|prev|next|last)/.test(as[j].parentNode.className)) {
								toret.push(as[j]);
							}
						}
					}
					if ($lists[i].bottomtextnav.getAttribute("kt_styles_attached")) {
						continue;
					}
					$lists[i].bottomtextnav.setAttribute("kt_styles_attached", true);
					var as = $lists[i].bottomtextnav.getElementsByTagName('a');
					for (var j = 0; j < as.length; j++) {
						if (/(first|prev|next|last)/.test(as[j].parentNode.className)) {
							toret.push(as[j]);
						}
					}
				}
			} else {
				var divs = utility.dom.getElementsByClassName(document, 'KT_textnav', 'div');
				if (divs) {
					for (var i = 0; i < divs.length; i++) {
						if (divs[i].getAttribute("kt_styles_attached")) {
							continue;
						}
						divs[i].setAttribute("kt_styles_attached", true);
						var as = divs[i].getElementsByTagName('a');
						for (var j = 0; j < as.length; j++) {
							if (/(first|prev|next|last)/.test(as[j].parentNode.className)) {
								toret.push(as[j]);
							}
						}
					}
				}
			}
			return toret;		
		}, 
		'transform' : function(el) {
			var li = el.parentNode;
			var elnou = KT_style_replace_with_button(el);
			if (!el.href.match(/void\(0\)/)) {
				elnou.onclick = function(e) {
					if (typeof $ctrl != 'undefined') {
						$ctrl.loadPanels(el.href);
					} else {
						window.location.href = el.href;
					}
				};
			} else {
				//utility.dom.classNameAdd(el.parentNode, 'disabled');
				var inp = el.parentNode.getElementsByTagName('input');
				if (inp.length > 0) {
					inp[0].disabled = true;
				}
			}
			var values = {'first': '<<', 'prev': '<', 'next': '>', 'last': '>>'};
			elnou.value = values[li.className.toString().replace(/ disabled/, '')];
			elnou.className = 'button_smallest' + (el.href.match(/void\(0\)/) ? ' disabled' : '');
		}, 
		'eval': show_as_buttons
	}, 
	{
		'selector' : function() {
			var toret = [];
			if ($lists.length > 0) {
				for (var i = 0; i < $lists.length; i++) {
					if ($lists[i].kt_styles_attached) {
						continue;
					}
					if ($lists[i].toptextnav) {
						var as = $lists[i].toptextnav.getElementsByTagName('a');
						for (var j = 0; j < as.length; j++) {
							if (/(first|prev|next|last)/.test(as[j].parentNode.className)) {
								toret.push(as[j]);
							}
						}
					}
					var as = $lists[i].bottomtextnav.getElementsByTagName('a');
					for (var j = 0; j < as.length; j++) {
						if (/(first|prev|next|last)/.test(as[j].parentNode.className)) {
							toret.push(as[j]);
						}
					}
				}
			} else {
				var divs = utility.dom.getElementsByClassName(document, 'KT_textnav', 'div');
				if (divs) {
					for (var i = 0; i < divs.length; i++) {
						if (divs[i].getAttribute("kt_styles_attached")) {
							continue;
						}
						divs[i].setAttribute("kt_styles_attached", true);
						var as = divs[i].getElementsByTagName('a');
						for (var j = 0; j < as.length; j++) {
							if (/(first|prev|next|last)/.test(as[j].parentNode.className)) {
								toret.push(as[j]);
							}
						}
					}
				}
			}
			return toret;		
		}, 
		'transform' : function(el) {
			if (!el.href.match(/void\(0\)/)) {
			} else {
				utility.dom.classNameAdd(el, 'disabled');
			}
		}, 
		'eval': not_show_as_buttons
	}, 
	{
		'selector': function() { 
			var toret = [];
			var div = document.getElementById('KT_tngtrace');
			if (div && !div.getAttribute("kt_styles_attached")) {
				div.setAttribute("kt_styles_attached", true);
				var as = div.getElementsByTagName('a');
				for (var i = 0; i < as.length; i++) {
					toret.push(as[i]);
				}
			}
			return toret;
		}, 
		'transform': function(el) { 
			var elnou = KT_style_replace_with_button(el, true);
		}, 
		'eval': show_as_buttons
	}, 
	{
		'selector_text': 'div.KT_tnglist table.KT_tngtable tr.KT_row_filter input[type="submit"]', 
		'selector' : function() {
			var toret = [];
			for (var i = 0; i < $lists.length; i++) {
				if ($lists[i].kt_styles_attached) {
					continue;
				}
				var inps = $lists[i].table.getElementsByTagName('input');
				for (var j = 0; j < $lists[i].table.rows.length; j++) {
					var row = $lists[i].table.rows[i];
					if (/KT_row_filter/.test(row.className)) {
						var inps = row.getElementsByTagName('input');
						var type = '';
						for (var k = 0; k < inps.length; k++) {
							type = inps[k].getAttribute('type');
							if (type == null) {
								type = 'text';
							}
							if (type.toString().toLowerCase == 'submit') {
								toret.push(inps[k]);
							}
						}
					}
				}
			}
			return toret;
		}, 
		'transform': function(el) {
			el.className = 'KT_row_filter_submit_button';
		}, 
		'eval': "1"
	}, 
	{
		'selector' : function() {
			//'div.KT_tng input[type="text"]'
			//'div.KT_tng input[type="widget"]'
			//'div.KT_tng input[type="password"]'
			var toret = [];
			for (var i = 0; i < $lists.length; i++) {
				if ($lists[i].kt_styles_attached) {
					continue;
				}
				var inps = $lists[i].main.getElementsByTagName('input');
				var type = '';
				for (var j = 0; j < inps.length; j++) {
					type = inps[j].getAttribute('type');
					if (type == null) {
						type = 'text';
					}
					if (/(text|widget|password)/i.test(type.toString())) {
						toret.push(inps[j]);
					}
				}
			}
			return toret;
		}, 
		'transform': function(el) {
			utility.dom.classNameAdd(el, 'input_text');
		}, 
		'eval': "1"
	}, 
	{
		'selector' : 'table.KT_tngtable', 
		'transform' : function(el) {
			if (el.getAttribute("kt_checkboxes_attached")) {
				return;
			}
			el.setAttribute("kt_checkboxes_attached", true);
			var labels = utility.dom.getElementsByTagName(el, 'label');
			var visited_labels = [];
			//ZZZZ
			Array_each(labels, function(label) {
				var normal = label.htmlFor.toString().replace(/_\d+$/, '');
				var normal_re = new RegExp('^' + normal + "_\\d+$", 'g');
			
				var first = document.getElementById(normal+'_1');
				if (typeof first == 'undefined' || first == null || !first.tagName || first.tagName == 'undefined') {
					return;
				}
				if (first.tagName.toLowerCase() == 'input' && first.type && first.type.toLowerCase() == 'file') {
					return;
				}
			
				var inp = document.getElementById(label.htmlFor.toString());
			
				var show_cond = true;
				if (typeof inp == 'undefined' || inp == null) {
					show_cond = false;
				}
				if (show_cond && typeof inp.type != 'undefined' && inp.type != null) {
					if (inp.type.toString().toLowerCase() == 'radio') {
						show_cond = false;
					}
				}
				if (!show_cond) {
					return;
				}
			
				if (tng_mtm_detail_key_re.test(label.htmlFor)) {
					// this is a detail checkbox
					// attach the onclick disable/enable detail row behavior 
					inp.onclick = function(e) {
						tng_form_enable_details(inp.name);
					}
					if (!inp.checked) {
						tng_form_enable_details(inp.name);
					}
				}
			});
		},
		'eval': "1"
	},
	{
		'selector' : 'div.KT_tngform', 
		'transform' : function(el) {
			if (el.getAttribute("kt_styles_attached")) {
				return;
			}
			el.setAttribute("kt_styles_attached", true);
			if (is.mozilla) {
				utility.dom.classNameAdd(el, 'fix_content_enlarge');
			}

			if (typeof window['ktmls'] != 'undefined' && is.mozilla && typeof(ktml_isElementVisible) == 'undefined') {
				return;
			}

			multiple_edits = false;
			var tables = utility.dom.getElementsBySelector('div.KT_tngform table.KT_tngtable');
			if (tables.length && tables.length > 1) {
				multiple_edits = true;
			}
			var show_as_grid = !(typeof $NXT_FORM_SETTINGS == 'undefined' || typeof $NXT_FORM_SETTINGS['show_as_grid'] == 'undefined' || $NXT_FORM_SETTINGS['show_as_grid'] == false);
			if (tables.length == 1 || !show_as_grid) {
				return true;
			}
			multiple_edits = true;
			var num_of_columns = tables[0].rows.length;

			var tbl = document.createElement('table', {
				'className' : 'KT_tngtable'
			});
			tbl.className = 'KT_tngtable';
			
			//el.appendChild(tbl);
			//	STEP n-3 : create the header table
			var row_head = tbl.insertRow(-1);
			var cell_head = row_head.insertCell(-1);
			cell_head.innerHTML = NXT_Messages['Record_FH'];
			cell_head.className = 'KT_th';
			Array_each(tables[0].rows, function(row) {
				var label = row.getElementsByTagName('label')[0];

				var cell_head = row_head.insertCell(-1);
				cell_head.className = 'KT_th';
				if (label) {
					cell_head.appendChild(label);
				} else {
					cell_head.innerHTML = row.getElementsByTagName('td')[0].innerHTML;
				}
			})


			//	STEP n-2 : create the new table and hide it
			var hidden_ids = utility.dom.getElementsByClassName(el, 'id_field');
			var hidden_ids_index = 0;
			Array_each(tables, function(table_to_copy, index) {
				var row_content = tbl.insertRow(-1);
				
				var cell_record_no = row_content.insertCell(-1);
				cell_record_no.innerHTML = (index+1)+'';
				cell_record_no.noWrap = true;
				cell_record_no.style.verticalAlign = "top";

				Array_each(table_to_copy.rows, function(row) {
					//var cell_content = row_content.insertCell(-1);
					//cell_content.innerHTML = row.getElementsByTagName('td')[1].innerHTML;
					var td = row_content.appendChild(row.getElementsByTagName('td')[1]);//.cloneNode(true)
					td.style.verticalAlign = "top";
					var hint = utility.dom.getElementsByClassName(td, 'KT_field_hint', 'span');
					if (hint.length) {
						for (var i = 0; i < hint.length; i++) {
							hint[i].parentNode.removeChild(hint[i]);
						}
					}
				})

				var hidden = hidden_ids[hidden_ids_index++];
				/*
				while (hidden && (hidden.nodeType == 3 || hidden.tagName.toLowerCase() != 'input')) {
					hidden = hidden.nextSibling;
				}
				*/
				if (hidden) {
					cell_record_no.appendChild(hidden);
				} else {
					alert('could not find hidden !');
				}

			})

			// STEP n-1 : delete the old tables 
			Array_each(tables, function(table_to_copy, index) {
				//remove previous h2
				var heading = table_to_copy.previousSibling;
				try {
					while (heading.previousSibling && (heading.nodeType == 3 ||  heading.tagName.toLowerCase() != 'h2')) {
						heading = heading.previousSibling;
					}
				} catch(e) { heading = null; } 
				if (heading) {
					heading.parentNode.removeChild(heading);
				}

				//remove next input
				var hidden = table_to_copy.nextSibling;
				try {
					while (hidden && hidden.nodeType != 3 && hidden.tagName.toLowerCase() != 'input') {
						hidden = hidden.nextSibling;
					}
				} catch(e) { hidden = null; }
				if (hidden) {
					hidden.parentNode.removeChild(hidden);
				}
				table_to_copy.parentNode.removeChild(table_to_copy);
			})

			//	STEP 4 : find the bottombuttons div, and add the element
			var bottom_buttons = utility.dom.getElementsBySelector('div.KT_bottombuttons')[0];
			bottom_buttons.parentNode.insertBefore(tbl, bottom_buttons);
		}, 
		'eval': '(true)'
	}
];

function nxt_style_set_globals() {
	$lists = [];
	var tmp = utility.dom.getElementsByClassName(document, 'KT_tng', 'div');
	for (var k = 0; k < tmp.length; k++) {
		var list_obj = {};
		var kt_styles_attached = tmp[k].getAttribute("kt_styles_attached");
		if (kt_styles_attached) {
			continue;
		}
		list_obj.kt_styles_attached = false;
		list_obj.name = tmp[k].id;
		list_obj.main = tmp[k];
		list_obj.inner = utility.dom.getElementsByClassName(tmp[k], 'KT_tnglist', 'div');
		if (typeof list_obj.inner == 'object' && list_obj.inner != null && list_obj.inner.length && list_obj.inner.length > 0) {
			list_obj.inner = list_obj.inner[0];
			if (is.mozilla) {
				utility.dom.classNameAdd(list_obj.inner, 'fix_content_enlarge');
			}
		
			var frm = list_obj.inner.getElementsByTagName('form')[0];
			for (var i = 0; i < frm.childNodes.length; i++) {
				if (frm.childNodes[i].nodeType == 1) {
					var n = frm.childNodes[i];
					var t = n.tagName.toLowerCase();
					var c = n.className;
					if (t == 'table') {
						list_obj.table = n;
					}
					if (/KT_topbuttons/.test(c)) {
						list_obj.topbuttons = n;
					}
					if (/KT_bottombuttons/.test(c)) {
						list_obj.bottombuttons = n;
					}
					if (/KT_topnav/.test(c)) {
						list_obj.topnav = n;
						var divs = list_obj.topnav.getElementsByTagName('div');
						for (var j = 0; j < divs.length; j++) {
							if (/KT_textnav/.test(divs[j].className)) {
								list_obj.toptextnav = divs[j];
								break;
							}
						}
					}
					if (/KT_bottomnav/.test(c)) {
						list_obj.bottomnav = n;
						var divs = list_obj.bottomnav.getElementsByTagName('div');
						for (var j = 0; j < divs.length; j++) {
							if (/KT_textnav/.test(divs[j].className)) {
								list_obj.bottomtextnav = divs[j];
								break;
							}
						}
					}
				}
			}
			$lists.push(list_obj);
		}
	}
}

function nxt_style_attach() {
	if (is.ie && is.mac) {
		return;
	}
	styles_arr = [];
	nxt_style_set_globals();
	nxt_perform_transformations = function() {
		Array_each($TRANSFORMATIONS, function(t) {
			var obj = {};
			obj.selector = t;
			obj.start = new Date();
			if (eval(t['eval'])) {
				var sel = t.selector;
				if (typeof sel == 'function') {
					var arr = sel();
				} else {
					var arr = utility.dom.getElementsBySelector(t['selector']);
				}
				Array_each(arr, t['transform']);
			}
			obj.end = new Date();
			obj.diff = obj.end - obj.start;
			styles_arr.push(obj);
		});
		for(var i=0; i<$lists.length; i++) {
			$lists[i].kt_styles_attached = true;
			$lists[i].main.setAttribute("kt_styles_attached", true);
		}
		KT_style_executed = true;
		$style_executed = true;
		if (typeof nxt_list_attach != 'undefined') {
			nxt_list_attach();
		}
	}
	nxt_perform_transformations();
}

utility.dom.attachEvent2(window, 'onload', nxt_style_attach);
