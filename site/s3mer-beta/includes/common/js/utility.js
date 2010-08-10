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

// Copyright 2001-2005 Interakt Online. All rights reserved.
var is = new BrowserCheck();

if (typeof utility == 'undefined') utility = {};
/*
utility = {
	'math': {},
	'string': {}, 
	'js': {}, 
	'debug': {}, 
	'url': {},
	'dom': {},
	'window': {}, 
	'cookie': {}, 
	'date': {}, 
	'req': {}, 
	'xml': {}
};
*/

Object_weave_safe(utility, {math: {}});

utility.math.intbgr2hexrgb = function(a) {
	d2h = utility.math.dec2hex;
	pad = utility.math.zeroPad;
	// on mozilla will report rgb(a, b, c) - so the following is not good
	return "#" + pad(d2h(a % 256), 2) + pad(d2h((a / 256) % 256), 2) + pad(d2h((a / 65536) % 256), 2);
}

utility.math.mozcolor2rgb = function(color) {
	return color;
}

utility.math.dec2hex = function(x) {
	return Number(parseInt(x)).toString(16);
}

utility.math.hex2dec = function(x) {
	return parseInt(x, 16);
}

utility.math.zeroPad = function (str, length) {
	if (!str) str = "";
	str = str.toString();
	while (str.length < length) {
		str = "0" + str;
	}
	return str;
}
utility.math.rgb2hexcolor = function(color) {
	var arr = [];
	if (arr = color.match(/^rgb\(([0-9]+),\s*([0-9]+),\s*([0-9]+)\)/i)) {
		var ret = '';
		for (var i = 1; i < 4; i++) {
			var tmp = utility.math.dec2hex(arr[i]);
			while (tmp.length < 2) {
				tmp = "0" + tmp;
			}
			ret += tmp;
		}
		return "#" + ret;
	} else {
		return color;
	}
}

Object_weave_safe(utility, {js: {}});

utility.js.build = function(fun1, fun2) {
	var me = function() {
		if (fun2) { fun2(); }
		if (fun1) { fun1(); }
	}
	return me;
}
utility.js.empty_func = function() {};

Object_weave_safe(utility, {debug: {}});

utility.debug.dump = function (obj, sep) {
	if (sep == undefined) {
		sep = '';
	}
	tm = "";
	if (typeof (obj) == "object") {
		for (var i in obj) {
			tm += sep + i + ":{\n" + utility.debug.dump(obj[i], sep + '  ') + "}\n";
		}
		return tm;
	}
	if (typeof (obj) == "function") return sep + typeof (obj) + "\n";
	return sep + obj + "\n";
}
function al(obj, rx) {
	alert(utility.debug.dumpone(obj, rx));
}

utility.debug.dumpone = function (obj, rx, sep) {
	if (rx == undefined) {
		rx = new RegExp("", "");
	}
	if (sep == undefined) {
		sep = '';
	}
	tm = "";
	if (typeof (obj) == "object" && obj!=null) {
		if (typeof(obj.push) != "undefined" && obj.push.toString().indexOf("[native code]")>0) {
			tm = sep + "Array[" + obj.length + "]\n";
		} else {
			for (i in obj) {
				if (i.toUpperCase() == i) {
					continue;
				}
				if (!rx.test(i)) {
					continue;
				}
				try {
					if (typeof obj[i] != 'function') {
						tm += sep + i + ":{" + obj[i] + "}\n";
					} else {
						//tm += sep + i + ":{\n js function }\n";
					}
				} catch(err){
					tm += sep + i + ":ERROR{" + err.message + "}\n";
				}
			}
		}
		return tm;
	}
	if (typeof (obj) == "function") return sep + typeof (obj) + "\n";
	return sep + obj + "\n";
}

utility.debug.breakpoint = function(evalFunc, msg, initialExprStr) { 
	if (evalFunc == null) 
		evalFunc = function(e){return eval(e)};
    if (msg == null)
        msg = "";
    var result = initialExprStr || "1+2";
    while (true) {
        var expr = prompt("BREAKPOINT: " + msg + "\nEnter an expression to evaluate, or Cancel to continue.", result); 
        if (expr == null || expr == "")
            return;
        try {
            result = evalFunc(expr);
        } catch (e) {
            result = e;
        }
    }
}

Object_weave_safe(utility, {string: {}});

utility.string.htmlspecialchars = function(str) {
	Array_each([	['>', '&gt;'],
		['<', '&lt;'],
		['\xA0', '&nbsp;'],
		['"', '&quot;']
	], function(repl, idx) {
		str = str.replace(new RegExp('['+repl[0]+']', "g"), repl[1]);
	});
	return str;
}

utility.string.getInnerText = function(str) {
	if (typeof getInnerText_tmpDiv == 'undefined') {
		getInnerText_tmpDiv = document.createElement('div');
	}
	var oldstr = str;
	try {
		getInnerText_tmpDiv.innerHTML = str;
		if (is.safari) {
			str = getInnerText_tmpDiv.innerHTML;
			getInnerText_tmpDiv.innerHTML = "";
		} else {
			str = getInnerText_tmpDiv.innerText;
			getInnerText_tmpDiv.innerHTML = "";
		}
	} catch(e) { return oldstr; } 
	if ( typeof str == 'undefined') {
		return oldstr;
	}
	return str;
}

utility.string.sprintf = function() {
	if (!arguments || arguments.length < 1 || !RegExp) {
		return;
	}
	var str = arguments[0];
	var oldstr = arguments[0];
	var re = /([^%]*)%('.|0|\x20)?(-)?(\d+)?(\.\d+)?(%|b|c|d|u|f|o|s|x|X)(.*)/;
	var a = b = [], numSubstitutions = 0, numMatches = 0;
	while (a = re.exec(str)) {
		var leftpart = a[1], pPad = a[2], pJustify = a[3], pMinLength = a[4];
		var pPrecision = a[5], pType = a[6], rightPart = a[7];
		numMatches++;

		if (pType == '%') {
			subst = '%';
		} else {
			numSubstitutions++;
			if (numSubstitutions >= arguments.length) {
				return oldstr;
			}
			var param = arguments[numSubstitutions];
			var subst = param;

			if (pType == 'c') subst = String.fromCharCode(parseInt(param));
			else if (pType == 'd') subst = parseInt(param) ? parseInt(param) : 0;
			else if (pType == 's') subst = param;
		}
		str = leftpart + subst + rightPart;
	}
	return str;
}

Object_weave_safe(utility, {dom: {}});

utility.dom.setUnselectable = function(el) {
	if (is.ie) {
		for(var i=0; i<el.all.length; i++){
			if(el.all[i].tagName != "INPUT" && el.all[i].tagName != "TEXTAREA"){
				var oldCurr = utility.dom.getStyleProperty(el.all[i], "cursor");
				el.all[i].unselectable = "On";
				if(oldCurr == "auto"){
					el.all[i].style.cursor = "default";
				}
			} else if(el.all[i].type == "text" || el.all[i].tagName == "TEXTAREA"){
				el.all[i].style.cursor = "text";
			}
		}
	} else {
		var allChilds = utility.dom.getElementsByTagName(el,'*');
		Array_each(allChilds, function(child){
			var oldCurr = utility.dom.getStyleProperty(child, "cursor");
			var isHtmlEl = (child.nodeType == 1)? true: false;
			if(/*isHtmlEl*/true){
				var isInput   = ((child.nodeName.toLowerCase()=="input") && 
				                 (child.getAttribute('type') && child.getAttribute('type').toLowerCase()=='text') ||
				                 (child.getAttribute('type') && child.getAttribute('type').toLowerCase()=='password'))? true: false;
				var isTxtArea = (child.nodeName.toLowerCase()=="textarea")? true: false;
				if(!isInput && !isTxtArea ){
					if(oldCurr == "auto"){
						child.style.cursor = "default";
					}
					var hasHTMLChilds = utility.dom.getElementsByTagName(child, '*').length? true: false;
					if(!hasHTMLChilds){
						child.style.MozUserSelect = 'none';
					}
				} else{
					child.style.cursor = "text !important";
				}
			};
		});
	}
};

/**
 * utility.dom.getPixels
 * 	returns the value of a CSS property as Int, converting medium to 2
 * @param {DOMElement} m - elements
 * @param {String} s - 
 */
utility.dom.getPixels = function (m, s) {
	var v = utility.dom.getStyleProperty(m, s);
	if (v == "medium") {
		v = 2;
	} else {
		v = parseInt(v, 10);
	}
	v = isNaN(v)?0:v;
	return v;
};


/**
 * utility.dom.getBorderBox
 * 	returns a border box object (x,y,width,height) which perfectly covers the el element and its borders
 * 	the x, y are absolute coordinates measured from from the window viewport
 * 	use the box as the second parameter in utility.dom.setBorderBox
 * @param {DOMElement or String} el - 
 * @param {DOMDocument,optional} doc - 
 */
utility.dom.getBorderBox = function (el, doc) {
	doc = doc || document;
	if (typeof(el) == 'string') {
		el = doc.getElementById(el);
	}

	if (!el) {
		return false;
	}

	if (el.parentNode === null || utility.dom.getStyleProperty(el, 'display') == 'none') {
		//element must be visible to have a box
		return false;
	}

	var ret = {x:0, y:0, width:0, height:0};
	var parent = null;
	var box;

	if (el.getBoundingClientRect) { // IE
		box = el.getBoundingClientRect();
		var scrollTop = doc.documentElement.scrollTop || doc.body.scrollTop;
		var scrollLeft = doc.documentElement.scrollLeft || doc.body.scrollLeft;
		ret.x = box.left + scrollLeft;
		ret.y = box.top + scrollTop;
		ret.width = box.right - box.left;
		ret.height = box.bottom - box.top;
	} else if (doc.getBoxObjectFor) { // gecko
		box = doc.getBoxObjectFor(el);
		ret.x = box.x;
		ret.y = box.y;
		ret.width = box.width;
		ret.height = box.height;
		var btw = utility.dom.getPixels(el, "border-top-width");
		var blw = utility.dom.getPixels(el, "border-left-width");
		ret.x -= blw;
		ret.y -= btw;
	} else { // safari/opera
		ret.x = el.offsetLeft;
		ret.y = el.offsetTop;
		ret.width = el.offsetWidth;
		ret.height = el.offsetHeight;
		parent = el.offsetParent;
		if (parent != el) {
			while (parent) {
				ret.x += parent.offsetLeft;
				ret.y += parent.offsetTop;
				parent = parent.offsetParent;
			}
		}
		var blw = utility.dom.getPixels(el, "border-left-width");
		var btw = utility.dom.getPixels(el, "border-top-width");
		ret.x -= blw;
		ret.y -= btw;
		// opera & (safari absolute) incorrectly account for body offsetTop
		var ua = navigator.userAgent.toLowerCase();
		if (is.opera || is.safari && utility.dom.getStyleProperty(el, 'position') == 'absolute') {
			ret.y -= doc.body.offsetTop;
		}
	}
	if (el.parentNode) {
			parent = el.parentNode;
	} else {
		parent = null;
	}
	while (parent && parent.tagName != 'BODY' && parent.tagName != 'HTML') {
		ret.x -= parent.scrollLeft;
		ret.y -= parent.scrollTop;
		if (parent.parentNode) {
			parent = parent.parentNode;
		}	else {
			parent = null;
		}
	}
	return ret;
};

/**
 * utility.dom.setBorderBox
 * 	puts the element el to the location specified by box
 * @param {DOMElement} el - the element to be placed
 * @param {Object} box - hash containing the x and y coordinates where to put el
 *
 */
utility.dom.setBorderBox = function (el, box) {
	var pos = utility.dom.getBorderBox(el, el.ownerDocument);
	if (pos === false) {
		return false;
	}

	var delta = {
		x:utility.dom.getPixels(el, 'left'),
	 	y:utility.dom.getPixels(el, 'top')
	};

	var new_pos = {x:0, y:0};
	if (box.x !== null) {
		new_pos.x = box.x - pos.x + delta.x;
	}
	if (box.y !== null) {
		new_pos.y = box.y - pos.y + delta.y;
	}

	if (box.x !== null) {
		el.style.left = new_pos.x + 'px';
	}
	if (box.y !== null) {
		el.style.top = new_pos.y + 'px';
	}
	return true;
};

/**
 * utility.dom.bringIntoView
 * 	set the position of the source element so it is completely visible in the window
 * @param {DOMElemenet} source - the element to be 
 */
utility.dom.bringIntoView = function (source) {
	var box = utility.dom.getBorderBox(source, source.ownerDocument);
	if (box === false) {
		return false;
	}

	var current = {
		x:utility.dom.getPixels(source, 'left'),
	 	y:utility.dom.getPixels(source, 'top')
	};

	var delta = {x:0, y:0};
	var offset_fix = {x:0, y:0};
	var strictm = source.ownerDocument.compatMode == "CSS1Compat";
	var doc = (is.ie && strictm || is.mozilla)?source.ownerDocument.documentElement:source.ownerDocument.body;

 	offset_fix.x = utility.dom.getPixels(doc, 'border-left-width');
 	offset_fix.y = utility.dom.getPixels(doc, 'border-top-width');

	var st = doc.scrollTop;
	var ch = doc.clientHeight;
	var t = box.y + (is.ie?-offset_fix.y:offset_fix.y);
	var b = box.y + box.height + (is.ie?-offset_fix.y:offset_fix.y);

	if ( b - st > ch) {
		delta.y = ch - (b - st);
		if (t + delta.y < st) {
			delta.y = st-t;
		}
	} else if (t < st) {
		delta.y = st - t;
	}

	if (delta.y != 0) {
		source.style.top = (current.y + delta.y) + 'px';
	}
	
	var sl = doc.scrollLeft;
	var cw = doc.clientWidth;
	var l = box.x + (is.ie?-offset_fix.x:offset_fix.x);
	var r = box.x + box.width + (is.ie?-offset_fix.x:offset_fix.x);

	if ( r - sl > cw) {
		delta.x = cw - (r - sl);
		if (l + delta.x < sl) {
			delta.x = sl-l;
		}
	} else if (l < sl) {
		delta.x = sl - l;
	}

	if (delta.x != 0) {
		source.style.left = (current.x + delta.x) + 'px';
	}
};

/**
 * utility.dom.putElementAt
 * 	place an element over another element, at a specified special location (corner over corner)
 * @param {DOMElement} source 
 * @param {DOMElement} target 
 * @param {String} relative - string specifiing the position of source relative to target [0-4]{2}
 * 	'03' - means put source corner 0 over target corner 3, ie:dropdown menu opens below opener button
 * 	0-NW, 1-NE, 2-SE, 3-SW, 4-N (top-center), 5-E (middle-right), 6-S, 7-W, 8-center :)
 * @param {Object} offset - optional, offset.x and offset.y specifies additional amount 
 * @param {Object} biv - optional, offset.x and offset.y specifies additional amount 
 */

utility.dom.putElementAt = function (source, target, relative, offset, biv) {
	offset = util_defaultValue(offset, {x:0, y:0});
	biv = util_defaultValue(biv, true);
	var si = parseInt(relative.charAt(0), 10);
	var ti = parseInt(relative.charAt(1), 10);

	var source_box = utility.dom.getBorderBox(source, source.ownerDocument);
	var target_box = utility.dom.getBorderBox(target, target.ownerDocument);

	var sx = ['0', '-source_box.width', '-source_box.width', '0','-source_box.width/2', '-source_box.width','-source_box.width/2', '0','-source_box.width/2'];
	var tx = ['target_box.x', 'target_box.x+target_box.width', 'target_box.x+target_box.width', 'target_box.x', 'target_box.x+target_box.width/2', 'target_box.x+target_box.width', 'target_box.x+target_box.width/2', 'target_box.x', 'target_box.x+target_box.width/2'];

	var sy = ['0', '0', '-source_box.height', '-source_box.height', '0', '-source_box.height/2', '-source_box.height', '-source_box.height/2', '-source_box.height/2'];
	var ty = ['target_box.y', 'target_box.y', 'target_box.y+target_box.height', 'target_box.y+target_box.height', 'target_box.y', 'target_box.y+target_box.height/2', 'target_box.y+target_box.height', 'target_box.y+target_box.height/2', 'target_box.y+target_box.height/2'];

	var box = {x:0, y:0};
	box.x = eval(sx[si] + ' + ' + tx[ti]) + offset.x;
	box.y = eval(sy[si] + ' + ' + ty[ti]) + offset.y;

	utility.dom.setBorderBox(source, box);
	if (biv) {
		utility.dom.bringIntoView(source);
	}
	return true;
};


utility.dom.put = function(el, left, top) {
	el.style.left = left + 'px';
	el.style.top = top + 'px';
}

utility.dom.resize = function(el, width, height) {
	el.style.width = width + 'px';
	el.style.height = height + 'px';
}

utility.dom.focusElem =function(elem) {
	var d;
	d = this.getElem(elem);
	if (!d) return;
	if (d.focus) d.focus();
}

utility.dom.hideElem = function(elem) {
	this.setCssProperty(elem, "display", "none");
}

utility.dom.showElem = function(elem, force) {
	var tag_display = {
		table: 'table',
		tr: 'table-row',
		td: 'table-cell'
	}
	elem = utility.dom.getElem(elem);
	var tn = elem.tagName.toLowerCase();
	var t;
	if (!force) {
		if (typeof tag_display[tn] != 'undefined') {
			t = tag_display[tn];
		} else {
			t = "block";
		}
	} else {
		t = 'force';
	}
	try {
		this.setCssProperty(elem, "display", t);
	} catch(e) {
		// default to block if first try doesn't work
		// this happens on explorer when trying to set table-row and friends
		this.setCssProperty(elem, "display", "block");
	}
}

// because we can't find out on the first call the real state, we assume the element is hidden
utility.dom.toggleElem = function(elem, force) {
	elem = utility.dom.getElem(elem);
	try {
		if (!elem.style.display || elem.style.display == 'none') {
			utility.dom.showElem(elem, force);
		} else {
			utility.dom.hideElem(elem);
		}
	} catch(e) { }
}

// select the option that has the given value
utility.dom.selectOption = function(sel, val) {
	var i;
	if (!sel) return;
	for (i=0; i<sel.options.length; i++) {
		sel.options[i].removeAttribute('selected');
	}
	for (i=0; i<sel.options.length; i++) {
		if (sel.options[i].value == val) {
			sel.options[i].setAttribute('selected','selected');
			sel.options[i].selected = true;
			return;
		} else {
			sel.options[i].removeAttribute('selected');
		}
	}
}

// get value of the selected option
utility.dom.getSelected = function(sel) {
	return sel.options[sel.selectedIndex].value;
}



utility.dom.getPositionRelativeTo00 = function(x, y, w, h) {
	var bw, bh, sw, sh, d;
	if (is.mozilla) {
		bw = document.width;
		bh = document.height;
		sw = window.pageXOffset;
		sh = window.pageYOffset;
	} else {
		var strictm = document.compatMode == "CSS1Compat";
	
		d = strictm?document.documentElement:document.body;
		bw = d.offsetWidth - 20;
		bh = d.offsetHeight;
		sw = d.scrollLeft;
		sh = d.scrollTop;
	}
	if (x + w > bw + sw) {
		x = bw + sw - w;
	}
	if (y + h > bh + sh) {
		y = bh + sh - h;
	}
	if (x < 0) x = 0;
	if (y < 0) y = 0;
	return { x: x, y: y };
}
utility.dom.setCssProperty = function(elem, name, value) {
	var d;
	// sanity
	if (!elem || !name || !value) return;
	d = this.getElem(elem);
	if (!d) return;
	d.style[name] = value;
}

utility.dom.getElem = function(elem) {
	var d;
	if (typeof(elem) == "string") {
		d = document.getElementById(elem);
	} else {
		d = elem;
	}
	return d;
}

// return 
utility.dom.getClassNames = function(o) {
	o = utility.dom.getElem(o);
	if (!o) return false;
	var className = typeof(o.className) == 'undefined'?'':o.className;
	var cn = String_trim(String_normalize_space(className));
	if (cn == '') {
		return [];
	}
	return cn.split(" ");
}

utility.dom.classNameAdd = function(obj, toadd) {
	var cls = utility.dom.getClassNames(obj);
	if (typeof toadd == 'string') {
		toadd = toadd.split(',');
	}
	Array_each(toadd, function(item, i){
		if (Array_indexOf(cls, item) == -1) {
			Array_push(cls, item);
		}
	});
	cls = String_trim(cls.join(' '));
	var className = typeof(obj.className) == 'undefined'?'':obj.className;
	if (String_trim(className) != cls) {
		obj.className = cls;
	}
}

utility.dom.classNameRemove = function(obj, toremove) {
	var cls = utility.dom.getClassNames(obj);
	var result = [];
	/* can't use ruby.js and all those nice things yet 
	 * since ie5 doesn't have .hasOwnProperty
	cls = cls.reject(function(item, i) {
		return (item == str);
	});
	*/
	if (typeof toremove == 'string') {
		toremove = toremove.split(',');
	}
	Array_each(cls, function(item, i){
		if (Array_indexOf(toremove, item) == -1) {
			Array_push(result, item);
		}
	});
	
	cls = String_trim(result.join(' '));
	var className = typeof(obj.className) == 'undefined'?'':obj.className;
	if (String_trim(className) != cls) {
		obj.className = cls;
	}
}

utility.dom.insertAfter = function(newElement, targetElement) {
	var sibling = targetElement.nextSibling
	var parent = targetElement.parentNode;
	if (sibling == null) {
		var toret = parent.appendChild(newElement);
	} else {
		var toret = parent.insertBefore(newElement, sibling);
	}
	return toret;
}

utility.dom.getPreviousSiblingByTagName = function(t, siblingName, allowSameTag) {
	if ((t.nodeName.toLowerCase()==siblingName.toLowerCase()) && !allowSameTag) {
		return t;
	}

	while (t.previousSibling
			&& t.previousSibling.nodeName.toLowerCase() != siblingName.toLowerCase()
			) {
		t = t.previousSibling;
	}

	if (t.previousSibling && t.previousSibling.nodeName.toLowerCase() == siblingName.toLowerCase()) {
		return t.previousSibling;
	} else {
		return null;
	}
}

utility.dom.getNextSiblingByTagName = function(t, siblingName, allowSameTag) {
	if ((t.nodeName.toLowerCase()==siblingName.toLowerCase()) && !allowSameTag) {
		return t;
	}

	while (t.nextSibling
			&& t.nextSibling.nodeName.toLowerCase() != siblingName.toLowerCase()
			) {
		t = t.nextSibling;
	}

	if (t.nextSibling && t.nextSibling.nodeName.toLowerCase() == siblingName.toLowerCase()) {
		return t.nextSibling;
	} else {
		return null;
	}
}


utility.dom.getParentByTagName = function(t, parentName) {
	if (t.nodeName.toLowerCase() == parentName.toLowerCase()) {
		return t;
	}

	while (t.parentNode
			&& t.parentNode.nodeName.toLowerCase() != parentName.toLowerCase()
			&& t.parentNode.nodeName != 'BODY') {
		t = t.parentNode;
	}

	if (t.parentNode && t.parentNode.nodeName.toLowerCase() == parentName.toLowerCase()) {
		return t.parentNode;
	} else {
		return null;
	}
}

// should refactor this to take the tagname as a list or array of tagnames
// TODO : parameter order
utility.dom.getElementsByTagName = function(o, sTagName) {
	var el;
	if (typeof o == 'undefined') {
		o = document;
	} else {
		o = utility.dom.getElem(o);
	}

	if (sTagName == '*' || typeof sTagName == 'undefined') {
		el = utility.dom.getAllChildren(o);
	} else {
		el = o.getElementsByTagName(sTagName.toLowerCase());
	}
	return el;
}

// actually, this should be a front for a more generic method that gets elements filtered by an attribute
// or, we should try to use more of ruby.js to make this things easier to do (include Enumerable)
utility.dom.getElementsByClassName = function(o, sClassName, sTagName) {
	var elements = [];
	Array_each(utility.dom.getElementsByTagName(o, sTagName), function(elem, i) {
		if (Array_indexOf(utility.dom.getClassNames(elem), sClassName) != -1) { 
			Array_push(elements, elem);
		}
	});
	return elements;
}

utility.dom.getElementById = function(o, sId, sTagName) {
	var elements = [];
	Array_each(utility.dom.getElementsByTagName(o, sTagName), function(elem, i) {
		if (typeof elem.id != "undefined" && elem.id != null && elem.id.toString() == sId) { 
			Array_push(elements, elem);
		}
	});
	return elements;
}

utility.dom.getElementsByProps = function(start, props_hash) {
	var unfiltered, toret = [];
	if (typeof(start) == 'undefined') {
		start = document;
	} else {
		start = utility.dom.getElem(o);
	}
	if (o.all) {
		unfiltered = o.all;
	} else {
		unfiltered = o.getElementsByTagName('*');
	}
	//unfiltered.each = Array.prototype.each;
	Array_each(unfiltered, function(item) {
		var cond = true;
		for (i in props_hash) {
			try {
				var value = item[i];
			} catch(e) { value = null; }
			cond = cond && (value == props_hash[i]);
		}
		if (cond) {
			Array_push(toret, item);
		}
	});
	return toret;
}

// get all children of elem that have the "tag" tagName
utility.dom.getChildrenByTagName = function(elem, tag) {
	var result = [];
	var x;
	if (typeof(tag) == 'undefined') tag = '*';
	tag = tag.toLowerCase();
	if (!elem.childNodes) return result;
	for (var i=0; i<elem.childNodes.length; i++) {
		x = elem.childNodes[i];
		try {
			if (
				(typeof(x) != 'undefined' && 
				typeof(x.tagName) != 'undefined' && 
				x.tagName.toLowerCase() == tag) || tag == '*'
				
			) {
				Array_push(result, x);
			}
		} catch(e) { 
			// nick the error 
		}
	}
	return result;
}

// get all children of elem that have the class "sClassName"
// sTagName is optional, defaults to *
utility.dom.getChildrenByClassName = function(elem, sClassName, sTagName) {
	var result = [];
	result = Array_each(utility.dom.getChildrenByTagName(sTagName), function(elem, i) {
		if (Array_indexOf(utility.dom.getClassNames(item), sClassName) != -1) { 
			Array_push(result, elem);
		}
	});
}

utility.dom.getAllChildren = function(e) {
	// Returns all children of element. Workaround required for IE5/Windows. Ugh.
	return e.all ? e.all : e.getElementsByTagName('*');
}

utility.dom.getElementsBySelector = function(selector, startfrom) {
	if (typeof startfrom == 'undefined') {
		startfrom = document;
	}

	if (!document.getElementsByTagName) {
		return [];
	}
	// Split selector in to tokens
	var tokens = selector.split(' ');
	var currentContext = new Array(startfrom);
	for (var i = 0; i < tokens.length; i++) {
		token = tokens[i].replace(/^\s+/,'').replace(/\s+$/,'');
		if (token.indexOf('#') > -1) {
			// Token is an ID selector
			var bits = token.split('#');
			var tagName = bits[0];
			var id = bits[1];
			var element = document.getElementById(id);
			if (element && tagName && element.nodeName.toLowerCase() != tagName) {
				// tag with that ID not found, return false
				return [];
			}
			// Set currentContext to contain just this element
			currentContext = new Array(element);
			continue; // Skip to next token
		}
		if (token.indexOf('.') > -1) {
			// Token contains a class selector
			var bits = token.split('.');
			var tagName = bits[0];
			var className = bits[1];
			if (!tagName) {
				tagName = '*';
			}
			// Get elements matching tag, filter them for class selector
			var found = new Array;
			var foundCount = 0;
			for (var h = 0; h < currentContext.length; h++) {
				var elements;
				if (tagName == '*') {
					elements = utility.dom.getAllChildren(currentContext[h]);
				} else {
					elements = currentContext[h].getElementsByTagName(tagName);
				}
				for (var j = 0; j < elements.length; j++) {
				  found[foundCount++] = elements[j];
				}
			}
			currentContext = new Array;
			var currentContextIndex = 0;
			for (var k = 0; k < found.length; k++) {
				var cclassName = typeof(found[k].className) == 'undefined'?'':found[k].className;
				if (cclassName && cclassName.match(new RegExp('\\b'+className+'\\b'))) {
				  currentContext[currentContextIndex++] = found[k];
				}
			}
			continue; // Skip to next token
		}
		// Code to deal with attribute selectors
		if (token.match(/^(\w*)\[(\w+)([=~\|\^\$\*]?)=?"?([^\]"]*)"?\]$/)) {
			var tagName = RegExp.$1;
			var attrName = RegExp.$2;
			var attrOperator = RegExp.$3;
			var attrValue = RegExp.$4;
			if (!tagName) {
				tagName = '*';
			}
			// Grab all of the tagName elements within current context
			var found = new Array;
			var foundCount = 0;
			for (var h = 0; h < currentContext.length; h++) {
				var elements;
				if (tagName == '*') {
					elements = utility.dom.getAllChildren(currentContext[h]);
				} else {
					elements = currentContext[h].getElementsByTagName(tagName);
				}
				for (var j = 0; j < elements.length; j++) {
					found[foundCount++] = elements[j];
				}
			}
			currentContext = new Array;
			var currentContextIndex = 0;
			var checkFunction; // This function will be used to filter the elements
			switch (attrOperator) {
				case '=': // Equality
					checkFunction = function(e) { try {return (e.getAttribute(attrName).toString() == attrValue);} catch(ex) { } };
				break;
				case '~': // Match one of space seperated words 
					checkFunction = function(e) { try {return (e.getAttribute(attrName).toString().match(new RegExp(attrValue)));} catch(ex) { return false; }  };
				break;
				case '|': // Match start with value followed by optional hyphen
					checkFunction = function(e) { return (e.getAttribute(attrName).toString().match(new RegExp('^'+attrValue+'-?'))); };
				break;
				case '^': // Match starts with value
					checkFunction = function(e) { return (e.getAttribute(attrName).toString().indexOf(attrValue) == 0); };
				break;
				case '$': // Match ends with value - fails with "Warning" in Opera 7
					checkFunction = function(e) { return (e.getAttribute(attrName).toString().lastIndexOf(attrValue) == e.getAttribute(attrName).length - attrValue.length); };
				break;
				case '*': // Match ends with value
					checkFunction = function(e) { return (e.getAttribute(attrName).toString().indexOf(attrValue) > -1); };
				break;
				default :
					// Just test for existence of attribute
					checkFunction = function(e) { return e.getAttribute(attrName); };
			}
			currentContext = new Array;
			var currentContextIndex = 0;
			for (var k = 0; k < found.length; k++) {
				if (checkFunction(found[k])) {
					currentContext[currentContextIndex++] = found[k];
				}
			}
			//alert('Attribute Selector: '+tagName+' '+attrName+' '+attrOperator+' '+attrValue);
			continue; // Skip to next token
		}
		// If we get here, token is JUST an element (not a class or ID selector)
		tagName = token;
		var found = new Array;
		var foundCount = 0;
		for (var h = 0; h < currentContext.length; h++) {
			if (currentContext[h] != null) {
				var elements = currentContext[h].getElementsByTagName(tagName);
				for (var j = 0; j < elements.length; j++) {
					found[foundCount++] = elements[j];
				}
			}
		}
		currentContext = found;
	}
	return currentContext;
}

utility.dom.createForm = function(options, inputs, doc) {
	if (typeof options == 'undefined') options = {};
	if (typeof inputs == 'undefined') inputs = [];
	if (typeof doc == 'undefined') doc = document;

	var default_options = {
		name: '', 
		id: '', 
		action: '',
		method: 'POST', // form method
		target: ''
	}

	options = Object_weave_safe(options, default_options);

	var frm = utility.dom.createElement( "FORM", {
		name: options.name, 
		id: options.id, 
		action: options.action, 
		method: options.method, 
		style: "display: none"
	});

	Array_each(inputs, function(input, i) {
		frm.appendChild(utility.dom.createElement('INPUT', {
			'type': 'hidden', 
			'id': input[0], 
			'name': input[0], 
			'value': input[1]
		}));
	});

	frm = doc.body.appendChild(frm);
	frm.target = options.target;
	return frm;
}

utility.dom.createIframe = function(options, doc) {
	if (typeof options == 'undefined') options = {};
	if (typeof doc == 'undefined') doc = document;

	var default_options = {
		name: '', 
		id: '', 
		src: options.src
	}

	options = Object_weave_safe(options, default_options);

	if (is.mozilla) {
		var ifr = utility.dom.createElement('iframe', {
			'id': options.id, 
			'name': options.name, 
			'style': 'display: none;'
		});
		ifr.src = options.src;
		ifr = doc.body.appendChild(ifr);
		ifr.name = options.name;
		ifr.id = options.id;
	} else if (is.ie) {
		var str = '<iframe name="'+options.name+'" src="' + options.src + '" id="'+options.id+'" style="display: none;"></iframe>';
		var dv = doc.createElement('div');
		doc.body.appendChild(dv);
		dv.innerHTML = str;
	}
	var ifr = doc.getElementById(options.id);
	return ifr;
}

utility.dom.addIframeLoad = function(ifr, functor) {
	if (is.mozilla) {
		ifr.onload = function() {
			functor();
		}
	} else {
		ifr.onreadystatechange = function() {
			if (ifr.readyState == 'complete') {
				functor();
			}
		}
	}
}

utility.dom.removeIframeLoad = function(ifr) {
	if (is.ie) { ifr.onreadystatechange = function() { }; }
	if (is.mozilla) {ifr.onload = function() { }; }
}

utility.dom.buildUrl = function() {
}

utility.dom.stripAttributes = function(el, additional_arr) {
	var cearElementProps = [
		'onload', 'data', 'onmouseover', 'onmouseout', 'onmousedown', 
		'onmouseup', 'ondblclick', 'onclick', 'onselectstart', 
		'oncontextmenu', 'onkeydown',   'onkeypress', 'onkeyup',
		'onblur', 'onfocus', 'onbeforedeactivate', 'onchange'];
	if (typeof el == 'undefined' || el == null) {
		return true;
	}
	for (var c = cearElementProps.length; c--; ) {
		el[cearElementProps[c]] = null;
	}
	if (typeof additional_arr != 'undefined') {
		for (var c = additional_arr.length; c--; ) {
			el[additional_arr[c]] = null;
		}
	}
}
// use attachEvent for ie
utility.dom.attachEvent2 = function(where, type, what, when) {
	utility.dom.attachEvent_base(where, type, what, when, 1);
}
// use on. for ie
utility.dom.attachEvent = function(where, type, what, when) {
	utility.dom.attachEvent_base(where, type, what, when, 0);
}

// don't use attachEvent for ie since we can't get 
// to the element where the handler is added from the handler
utility.dom.attachEvent_base = function(where, type, what, when, add_first) {
	if (typeof(when) == 'undefined') when = 1;
	var doNotAdd = type.match(/unload$/i);
	var real_type = type.match(/^on/) ? type : 'on' + type ;
	var logical_type = type.replace(/^on/, '');

	if (typeof where.__eventHandlers == 'undefined') {
		where.__eventHandlers = {};
	}
	var place = null;
	if (typeof where.__eventHandlers[logical_type] == 'undefined') {
		where.__eventHandlers[logical_type] = [];
		place = where.__eventHandlers[logical_type];

		var raiseEvent = function(e) {
			if (!e && window.event) {
				e = window.event;
			}
			for (var i=0;i < where.__eventHandlers[logical_type].length; i++) {
				var f = where.__eventHandlers[logical_type][i];
				if (typeof f == 'function') {
					f.apply(where, [e]);
					f = null;
				}
			}
		}

		/*
		var oldHandler = function() { };
		if (where[real_type] != null && 
			typeof where[real_type] != "undefined") {
			oldHandler = where[real_type];
			place[place.length] = oldHandler;
			oldHandler = null;
		}
		where[real_type] = null;
		*/
		if (where.addEventListener) {
			where.addEventListener(logical_type, raiseEvent, false);
		}
		else if (where.attachEvent) {
			where.attachEvent("on" + logical_type, raiseEvent);
		}
		else {
			where["on" + logical_type] = raiseEvent;
		}
		if ( (! (is.ie && is.mac)) && !doNotAdd) {
			EventCache.add(where, logical_type, raiseEvent, 1);
		}
	} else {
		place = where.__eventHandlers[logical_type];
	}


	for (var i=0;i<place.length;i++) {
		if (place[i] == what) {
			return;
		}
		try {
			 if (place[i] && what && place[i].toString() == what.toString()) {
			 	return;
			 }
		} catch(err) { }
	}
	place[place.length] = what;

	// add the event
}

var EventCache = function(){
	var listEvents = [];
	
	return {
		listEvents : listEvents,
	
		add : function(node, sEventName, fHandler, bCapture){
			Array_push(listEvents, arguments);
		},
	
		flush : function(){
			var i, item;
			for(i = listEvents.length - 1; i >= 0; i = i - 1){
				item = listEvents[i];
				if(!item) {
					continue;
				}
				if(item[0].removeEventListener){
					item[0].removeEventListener(item[1], item[2], item[3]);
				};
				
				/* From this point on we need the event names to be prefixed with 'on" */
				var logical_type = '';
				if(item[1].substring(0, 2) != "on") {
					logical_type = item[1];
					item[1] = "on" + item[1];
				} else {
					logical_type = item[1].substring(2, event_name_without_on.length);
				};
				//delete from __eventHandlers
				if (typeof item[0].__eventHandlers != 'undefined' && typeof item[0].__eventHandlers[logical_type] != 'undefined') {
					item[0].__eventHandlers[logical_type] = null;
				}
				if(item[0].detachEvent){
					item[0].detachEvent(item[1], item[2]);
				};
				
				item[0][item[1]] = null;
			};
			listEvents = null;
		}
	};
}();


utility.dom.getStyleProperty = function(el, property) {
	try{
		var value = el.style[property];
	}catch(e) {
		return "";
	}
	if (!value) {
		if (el.ownerDocument.defaultView && 
			typeof (el.ownerDocument.defaultView.getComputedStyle) == "function") { 
			// moz, opera
			value = el.ownerDocument.defaultView.getComputedStyle(el, "").getPropertyValue(property);
		} else if (el.currentStyle) {
			// IE
			var m = property.split(/-/);
			if (m.length>0) {
				property = m[0];
				for(var i=1;i<m.length;i++) {
					property += m[i].charAt(0).toUpperCase() + m[i].substring(1);
				}
			}
			value = el.currentStyle[property];
		} else if (el.style) {
			value = el.style[property];
		}
	}

	return value;
}

utility.dom.getLink = function(link) {
	if (!is.ie) {
		href = link.getAttribute('href');
	} else {
		if (!is.mac) {
			href = link.outerHTML.toString().replace(/.*href="([^"]*)".*/, "$1");
		} else {
			href = link.getAttribute('href');
		}
	}
	return href;
}

utility.dom.getDisplay = function(el) {
	return utility.dom.getStyleProperty(el, 'display');
}

utility.dom.getVisibility = function(el) {
	return utility.dom.getStyleProperty(el, 'visibility');
}
var first_getAbsolutePos_caller_element = null;
utility.dom.getAbsolutePos = function(el) {
	var scrollleft = 0, scrolltop = 0, tn = el.tagName.toUpperCase();
	if (utility.dom.getAbsolutePos.caller!=utility.dom.getAbsolutePos) {
		//do not substract the scrollLeft of the target element if you want to find it's left...
		first_getAbsolutePos_caller_element = el;
	}
	if (Array_indexOf(['BODY', 'HTML'], tn) == -1 && first_getAbsolutePos_caller_element!=el) { // ?
		if (el.scrollLeft) {
			scrollleft = el.scrollLeft;
		}

		if (el.scrollTop) {
			scrolltop = el.scrollTop;
		}
	}

	var r = { x: el.offsetLeft - scrollleft, y: el.offsetTop - scrolltop };

	if (el.offsetParent && tn != 'BODY') {
		var tmp = utility.dom.getAbsolutePos(el.offsetParent);
		r.x += tmp.x;
		r.y += tmp.y;
	}

	return r;
}

/**
*	FF : stopping the onsubmit event seems to alter the event.type (accessing the property after stopping the event raise an error)
*/
utility.dom.setEventVars = function(e) {
	var targ; var relTarg; var posx=0; var posy=0;
	if (!e){
		e = window.event;
	}
	if (!e){
		return {'e':null,'relTarg':null,'targ':null,'posx':0,'posy':0,'leftclick':false,'middleclick':false,'rightclick':false,'type':''};
	}
	if(e.relatedTarget) {
		relTarg = e.relatedTarget;
	} else if(e.fromElement) {
		relTarg = e.fromElement;
	}

	if(e.target) { 
		targ = e.target;
	} else if(e.srcElement) { 
		targ = e.srcElement;
	}

	var st = utility.dom.getPageScroll();
	if (e.pageX || e.pageY) {
		posx = e.pageX;
		posy = e.pageY;
	} else if (e.clientX || e.clientY) {
		posx = e.clientX + st.x;
		posy = e.clientY + st.y;
	}

	if (window.event) {
		var leftclick = (e.button == 1);
		var middleclick = (e.button == 4);
		var rightclick = (e.button == 2);
	} else {
		var leftclick = (e.button == 0);
		var middleclick = (e.button == 1);
		var rightclick = (e.button == 2 || e.button == 0 && is.mac && e.ctrlKey);
	}

	var o = {
		'e':e,'relTarg':relTarg,'targ':targ,'posx':posx,'posy':posy,'leftclick':leftclick,'middleclick':middleclick,'rightclick':rightclick
	}
	try {
		o.type = e.type;
	} catch (err) {
		o.type = '';
	}
	return o;
}

utility.dom.stopEvent = function(e) {
	if (typeof is == 'undefined') {
		is = new BrowserCheck();
	}
	if (typeof e != "undefined" && e!=null) {
		if(is.ie) {
			e.cancelBubble = true;
		} 
		if (e.stopPropagation) {
			e.stopPropagation();
		}

		if(is.ie) {
			e.returnValue = false;
		}
		if (e.preventDefault) {
			e.preventDefault();
		}
	}
	return false;
}

utility.dom.toggleSpecialTags = function(el, exclude, mode, documentObject, boxRecipient) {
	//var t1 = new Date();
	var hide_tags = ['select'];
	if (mode==1) {
		var boxObject = utility.dom.getBox(el);
	}
	for (var i = 0; i < hide_tags.length; i++) {
		var _document = null;
		if( documentObject && documentObject.nodeType && (documentObject.nodeType==9) ) {
			_document = documentObject;
			utility.dom.toggleSpecialTags._saved_DOC = documentObject;
		} else if ( documentObject && utility.dom.toggleSpecialTags._saved_DOC &&
		           utility.dom.toggleSpecialTags._saved_DOC.nodeType &&
		           (utility.dom.toggleSpecialTags._saved_DOC.nodeType==9) ) {
			_document = utility.dom.toggleSpecialTags._saved_DOC;
		} else {
			_document = document;	
		};
		var arr = _document.getElementsByTagName(hide_tags[i]);
		for (var j = 0; j < arr.length; j++) {
			if (exclude == arr[j]) {
				continue;
			}
			if (mode == 1) {
				var cVisibility = utility.dom.getVisibility(arr[j]);
				var cDisplay = utility.dom.getDisplay(arr[j]);
				if (cDisplay=="none" || cVisibility=="hidden") {
					continue;
				}
				var boxSelect =	utility.dom.getBox(arr[j]);
				if(boxRecipient){
					var parentBox = utility.dom.getBox(boxRecipient);
					boxSelect.x += parentBox.x;
					boxSelect.y += parentBox.y;
				}
				var overlap = utility.dom.boxOverlap(boxObject, boxSelect);
				if (overlap) {
					if(documentObject && boxRecipient){
						if(!arr[j].oldPosition){
							var cPosition = utility.dom.getStyleProperty(arr[j], "position");
							arr[j].oldPosition = cPosition;
							}
						if(!arr[j].oldLeft){
							var cLeft = utility.dom.getStyleProperty(arr[j], "left");
							arr[j].oldLeft = cLeft;
							}
						arr[j].style.position = "relative";
						arr[j].style.left = "-1000px";
					}
					else {
						if(!arr[j].oldvisibility) {
							arr[j].oldvisibility = cVisibility;
						}
						arr[j].style.visibility = 'hidden';					
					}
				}
			} else {
				if(documentObject && boxRecipient){
					if(arr[j].oldPosition){
						arr[j].style.position = arr[j].oldPosition;
						arr[j].removeAttribute("oldPosition");
						}
					if(arr[j].oldLeft){
						arr[j].style.left = arr[j].oldLeft;
						arr[j].removeAttribute("oldLeft");
						}
				}
				else {
					if (arr[j].oldvisibility) {
						arr[j].style.visibility = arr[j].oldvisibility;
					}				
				}
			}
		}
	}
}

utility.dom.boxOverlap = function(b1, b2) {
	//boxes do not overlap when b1:
	//is in the left of b2
	//or in the right of b2
	//or above b2
	//or below b2

	if( (b1.x+b1.width) < b2.x || b1.x > (b2.x+b2.width) ||
		(b1.y+b1.height) < b2.y || b1.y > (b2.y+b2.height) || false) {
		return false;
	}
	return true;
}

utility.dom.getBox = function(el) {
	var box = { 
		"x": 0, "y": 0, 
		"width": 0, "height": 0, 
		"scrollTop": 0, "scrollLeft": 0 
	};
	var strictm = el.ownerDocument.compatMode == "CSS1Compat";
	if (el.ownerDocument.getBoxObjectFor) {
		var rect = el.ownerDocument.getBoxObjectFor(el);
		box.x = rect.x - el.parentNode.scrollLeft;
		box.y = rect.y - el.parentNode.scrollTop;
		box.width = rect.width;
		box.height = rect.height;
		box.scrollLeft = (strictm?el.ownerDocument.documentElement:el.ownerDocument.body).scrollLeft;
		box.scrollTop = (strictm?el.ownerDocument.documentElement:el.ownerDocument.body).scrollTop;
	} else if (el.getBoundingClientRect) {
		var rect = el.getBoundingClientRect();
		box.x = rect.left;
		box.y = rect.top;
		box.width = rect.right - rect.left;
		box.height = rect.bottom - rect.top;
		box.scrollLeft = 0; //el.document.body.scrollLeft;
		box.scrollTop = 0;  //el.document.body.scrollTop;
	} else {
		var tmp = utility.dom.getAbsolutePos(el);
		box.x = tmp.x - el.parentNode.scrollLeft;
		box.y = tmp.y - el.parentNode.scrollTop;
		box.width = parsetInt(utility.dom.getStyleProperty(el, 'width'), 10);
		box.height = parsetInt(utility.dom.getStyleProperty(el, 'height'), 10);
		box.scrollLeft = el.ownerDocument.body.scrollLeft;
		box.scrollTop = el.ownerDocument.body.scrollTop;
	}
	return box;
}

utility.dom.getBBox = function(el) {
	var box = { 
		"x": 0, "y": 0, 
		"width": 0, "height": 0, 
		"scrollTop": 0, "scrollLeft": 0 
	};
	var strictm = el.ownerDocument.compatMode == "CSS1Compat";
	if (el.ownerDocument.getBoxObjectFor) {
		var doc = strictm?el.ownerDocument.documentElement:document;
		var bt = parseInt(utility.dom.getStyleProperty(el, "border-top-width"));
		var bl = parseInt(utility.dom.getStyleProperty(el, "border-left-width"));
		var br = parseInt(utility.dom.getStyleProperty(el, "border-right-width"));
		var bb = parseInt(utility.dom.getStyleProperty(el, "border-bottom-width"));

		var rect = el.ownerDocument.getBoxObjectFor(el);
		var sl = 0;
		var st = 0;
		while(el.parentNode) {
			if (el.scrollTop) {
				st += el.scrollTop;
			}
			if (el.scrollLeft) {
				sl += el.scrollLeft;
			}
			el = el.parentNode;
		}
		box.scrollLeft = sl;
		box.scrollTop = st;
		box.x = rect.x - bl - sl;
		box.y = rect.y - bt - st;
		box.width = rect.width;
		box.height = rect.height;
	} else if (el.getBoundingClientRect) {
//	var ss = '';
//	var zel = el;
//	var b = null;
//	while(zel) {
//		b = zel.getBoundingClientRect()
//		ss += zel.tagName+"\t" + b.top + "\t" + zel.scrollTop + "\r\n";
//		zel = zel.offsetParent;
//	}
//	al(ss);
		var pel = strictm?el.ownerDocument.documentElement:document.body;//el.offsetParent;
		var bt = parseInt(utility.dom.getStyleProperty(el, "border-top-width")) || 0;
//		var br = parseInt(utility.dom.getStyleProperty(el, "border-right-width")) || 0;
//		var bb = parseInt(utility.dom.getStyleProperty(el, "border-bottom-width")) || 0;
		var bl = parseInt(utility.dom.getStyleProperty(el, "border-left-width")) || 0;

//		var pbt = parseInt(utility.dom.getStyleProperty(pel, "border-top-width")) || 0;
//		var pbr = parseInt(utility.dom.getStyleProperty(pel, "border-right-width")) || 0;
//		var pbb = parseInt(utility.dom.getStyleProperty(pel, "border-bottom-width")) || 0;
//		var pbl = parseInt(utility.dom.getStyleProperty(pel, "border-left-width")) || 0;

		var rect = el.getBoundingClientRect();
		//al(rect);
		box.x = rect.left - bl;
		box.y = rect.top - bt;
//		box.y = rect.top - pbt;
//		box.x = rect.left - pbl;
		box.width = rect.right - rect.left;
		box.height = rect.bottom - rect.top;
		box.scrollLeft = 0; //el.document.body.scrollLeft;
		box.scrollTop = 0;  //el.document.body.scrollTop;
	} else {
		var pel = el.ownerDocument.documentElement;//el.offsetParent;
		var mt = parseInt(utility.dom.getStyleProperty(pel, "margin-top"));
		var ml = parseInt(utility.dom.getStyleProperty(pel, "margin-left"));
		var bt = parseInt(utility.dom.getStyleProperty(pel, "border-top-width"));
		var bl = parseInt(utility.dom.getStyleProperty(pel, "border-left-width"));
		var pt = parseInt(utility.dom.getStyleProperty(pel, "padding-top"));
		var pl = parseInt(utility.dom.getStyleProperty(pel, "padding-left"));

		pel = el.offsetParent;
		var mt2 = parseInt(utility.dom.getStyleProperty(pel, "margin-top"));
		var ml2 = parseInt(utility.dom.getStyleProperty(pel, "margin-left"));
		var bt2 = 0;//parseInt(utility.dom.getStyleProperty(pel, "border-top-width"));
		var bl2 = 0;//parseInt(utility.dom.getStyleProperty(pel, "border-left-width"));
		var pt2 = 0;//parseInt(utility.dom.getStyleProperty(pel, "padding-top"));
		var pl2 = 0;//parseInt(utility.dom.getStyleProperty(pel, "padding-left"));

		var tmp = utility.dom.getAbsolutePos(el);
		box.x = tmp.x;
		box.y = tmp.y;
		box.width = parseInt(utility.dom.getStyleProperty(el, 'width'));
		box.height = parseInt(utility.dom.getStyleProperty(el, 'height'));
		box.scrollLeft = el.ownerDocument.body.scrollLeft;
		box.scrollTop = el.ownerDocument.body.scrollTop;
		if (is.opera) {
			box.x -= (ml + bl + pl + ml2);
			box.y -= mt + bt + pt + mt2;
		}
	}
	return box;
}

// from quirksmode, fixed to properly differentiate between IE versions
utility.dom.getPageInnerSize = function() {
	var x, y;
	if (typeof self.innerHeight != "undefined") {
		x = self.innerWidth;
		y = self.innerHeight;
	} else if (typeof document.compatMode != 'undefined' && document.compatMode == 'CSS1Compat') {
		x = document.documentElement.clientWidth;
		y = document.documentElement.clientHeight;
	} else if (document.body) {
		x = document.body.clientWidth;
		y = document.body.clientHeight;
	}
	return {x: x, y: y};
}
// from quirksmode, fixed to properly differentiate between IE versions
utility.dom.getPageScroll = function() {
	var x, y;
	if (typeof self.pageYOffset != 'undefined') {
		x = self.pageXOffset;
		y = self.pageYOffset;
	} else if (typeof document.compatMode != 'undefined' && document.compatMode == 'CSS1Compat') {
		x = document.documentElement.scrollLeft;
		y = document.documentElement.scrollTop;
	}
	else if (document.body) {
		x = document.body.scrollLeft;
		y = document.body.scrollTop;
	}
	return {x: x, y: y};
}

utility.dom.createElement = function(type, attribs, wnd) {
	if (typeof is == 'undefined') {
		is = new BrowserCheck();
	}
	if (typeof wnd != 'undefined') {
		var elem = wnd.document.createElement( type );
	} else {
		var elem = document.createElement( type );
	}
	if ( typeof attribs != 'undefined' ) {
		for (var i in attribs) {
			switch ( true ) {
				case ( i == 'text' )  : 
					elem.appendChild( document.createTextNode( attribs[i] ) ); 
					break;
				case ( i == 'class' ) : 
					elem.className = attribs[i]; 
					break;
				case ( i == 'id' ) : 
					elem.id = attribs[i]; 
					break;
				case ( i == 'type' ) : 
					if (type.toLowerCase()=="input" && is.ie && is.mac) {
						//IE MAC cant set the type
						var tspn = document.createElement("SPAN");
						document.body.appendChild(tspn);
						tspn.style.display= "none";
						tspn.innerHTML = elem.outerHTML.replace(/<input/i, "<input type=\""+attribs[i]+"\"");
						elem = tspn.firstChild;
						document.body.removeChild(tspn);
					} else if (type.toLowerCase()=="input" && is.mac && is.safari) {
						elem.setAttribute('type', attribs[i]);
					} else {
						elem.type = attribs[i]; 
					}
					break;
				case ( i == 'style' ) : 
					elem.style.cssText = attribs[i]; 
					break;
				default : 
					try{
						elem.setAttribute(i, attribs[i] );
						elem[i] = attribs[i];
					}catch(e) {}
			}
		}
	}
	if (attribs['value']) {
		elem.value = attribs['value'];
	}
	return elem;	
};


utility.dom.getImports = function(s) {
	//var ss = document.styleSheets;
	try {
		if (is.ie) {
			return s.imports;
		} else {
			var toret = [];
			for (var i = 0; i < s.cssRules.length; i++) {
				if (is.safari) {
					if (typeof s.cssRules[i].href != 'undefined') {
						Array_push(toret, s.cssRules[i].styleSheet);
					}
				} else {
					if (s.cssRules[i].toString().match('CSSImportRule')) {
						Array_push(toret, s.cssRules[i].styleSheet);
					}
				}
			}
			return toret;
		}
	} catch(e) { return []; }
}

utility.dom.getRuleBySelector = function(s, rx) {
	try {
		var koll = [];
		if (is.ie) {
			koll = s.rules;
		} else {
			koll = s.cssRules;
		}
		var toret = [];
		for (var i = 0; i < koll.length; i++) {
			var rule = koll[i];
			if (rule.selectorText.toString().match(rx)) {
				Array_push(toret, rule);
			}
		}
		return toret;
	} catch(e) { return []; }
}

utility.dom.createStyleSheet = function(doc, path) {
	if (is.ie) {
		return doc.createStyleSheet(path);
	} else if (is.mozilla) {
	  // load the xml
		var theHeadNode = doc.getElementsByTagName("head")[0];

		var theStyleNode = doc.createElement('style');
		theStyleNode.type ="text/css"
		theStyleNode.rules = new Array();

		theHeadNode.appendChild(theStyleNode);

		if (path != "") {
			var xmlHttp = new XMLHttpRequest();
			try {
				xmlHttp.open("GET", path, false);
				xmlHttp.send(null);
			}
			catch (e) {
				alert('Cannot load a stylesheet from a server other than the current server.\r\nThe current server is "'+doc.location.hostname+'".\r\nThe requested stylesheet URL is "'+path+'".');
				return null;
			}

			if(xmlHttp.status==404){
				prompt('Stylesheet was not found:', path);
				return null;
			}
			var theTextNode = doc.createTextNode(xmlHttp.responseText);
			theStyleNode.appendChild(theTextNode);

			var re = /\s*\{([^\}]*)\}\s*/;
			nameList = xmlHttp.responseText.split (re);
			for(var i=0; i<nameList.length; i=i+2) {
				var rul = new Object();
				rul.selectorText = nameList[i];
				rul.cssText = nameList[i+1]
				theStyleNode.rules.push(rul);
			}

		} else {
			var theTextNode = doc.createTextNode('u');
			theStyleNode.appendChild(theTextNode);
		}
		return theStyleNode;
	}
}

Object_weave_safe(utility, {date: {}});

$UNI_DATETIME_MASK_SEPARATORS = ['-', '/', '[', ']', '(', ')', '*', '+', '.', '\s', ':'];
$UNI_DATETIME_MASK_REGEXP = '[';
for(var zi=0;zi<$UNI_DATETIME_MASK_SEPARATORS.length; zi++) {
	$UNI_DATETIME_MASK_REGEXP += "\\"+$UNI_DATETIME_MASK_SEPARATORS[zi]+'|';
}
$UNI_DATETIME_MASK_REGEXP += ']';
$UNI_DATETIME_MASK_REGEXP = new RegExp($UNI_DATETIME_MASK_REGEXP,"g");

utility.date.date2regexp = function(txt) {
	txt = txt.replace(/[\/\-\.]/g, 'DATESEPARATOR');
	txt = txt.replace(/([-\/\[\]\(\)\*\+\.\:])/g, '\\$1');
	txt = txt.replace(/DATESEPARATOR/g, '[\\/\\-\\.]');
	txt = txt.replace(/(\\s)/g, '\s');
	txt = txt.replace(/yyyy/gi, '([0-9]{1,4})');
	txt = txt.replace(/yy/gi, '([0-9]{1,4})');
	txt = txt.replace(/y/gi, '([0-9]{1,4})');

	txt = txt.replace(/mm/g, '([0-9]{1,2})');
	txt = txt.replace(/m/g, '([0-9]{1,2})');

	txt = txt.replace(/dd/g, '([0-9]{1,2})');
	txt = txt.replace(/d/g, '([0-9]{1,2})');

	txt = txt.replace(/HH/g, '([0-9]{1,2})*');
	txt = txt.replace(/H/g, '([0-9]{1,2})*');

	txt = txt.replace(/hh/g, '([0-9]{1,2})*');
	txt = txt.replace(/h/g, '([0-9]{1,2})*');

	txt = txt.replace(/ii/g, '([0-9]{1,2})*');
	txt = txt.replace(/i/g, '([0-9]{1,2})*');

	txt = txt.replace(/ss/g, '([0-9]{1,2})*');
	txt = txt.replace(/s/g, '([0-9]{1,2})*');

	txt = txt.replace(/tt/g, '(AM|PM|am|pm|A|P|a|p)*');
	txt = txt.replace(/t/g, '(AM|PM|am|pm|A|P|a|p)*');

	txt = txt.replace(/ /g, ' *');
	txt = txt.replace(/:/g, ':*');

	var re = new RegExp('^' + txt + '$');
	return re;
}

utility.date.parse_date = function(arr, dateMask) {
	var vYear = vMonth = vDay = null;
	var vHour = vHour12h = vHour24H = vMinutes = vSeconds = vTimeMarker1C = vTimeMarker2C = null;

	var groups = dateMask.split($UNI_DATETIME_MASK_REGEXP);
	var groupIdx = 0;

	var vTimeMarkerUpdate = 0;
	
	for (var i = 0; i< groups.length; i++) {
		var currentGroupMask = groups[i];
		groupIdx++;
		var groupValue = arr[groupIdx];

		if (Array_indexOf('HH,H,ii,i,ss,s'.split(','), currentGroupMask) >= 0) {
			if (groupValue == '' || typeof groupValue == 'undefined') {
				groupValue = '0';
			}
		}
		if (Array_indexOf('hh,h'.split(','), currentGroupMask) >= 0) {
			var tmpValue = parseInt(groupValue, 10);
			if (groupValue == '' || typeof groupValue == 'undefined') {
				groupValue = '12';
			} else if (tmpValue > 12 && tmpValue < 24) {
				var index = (Array_indexOf(groups, 't') >= 0 ? Array_indexOf(groups, 't')+1 : Array_indexOf(groups, 'tt')+1);
				if (arr[index] == '') {
					groupValue = tmpValue - 12;
					vTimeMarkerUpdate = 1;
				}
			}
		}
		if (Array_indexOf('tt,t'.split(','), currentGroupMask) >= 0) {
			if (groupValue == '') {
				groupValue = [['A', 'AM'], ['P', 'PM']][vTimeMarkerUpdate][currentGroupMask.length - 1];
			}
		}

		switch(currentGroupMask) {
		case 'yyyy':
		case 'YYYY':
			vYear = parseInt(groupValue, 10);
			break;
		case 'yy': 
		case 'YY': 
		case 'y':
			vYear = parseInt(groupValue, 10);
			if (vYear < 1000) {
				if (vYear < 10) {
					vYear = 2000 + vYear;
				} else {
					if (vYear < 70) {
						vYear = 2000 + vYear;
					} else {
						vYear = 1900 + vYear;
					}
				}
			}
			break;
		case 'mm':
		case 'm':
			vMonth = parseInt(groupValue, 10);
			//vMonth;
			break;
		case 'dd': 
		case 'd':
			vDay = parseInt(groupValue, 10);
			break;
		case 'HH': 
		case 'H':
			vHour24H = parseInt(groupValue, 10);
			break;
		case 'hh': 
		case 'h':
			vHour12h = parseInt(groupValue, 10);
			break;
		case 'ii':
		case 'i':
			vMinutes = parseInt(groupValue, 10);
			break;
		case 'ss':
		case 's':
			vSeconds = parseInt(groupValue, 10);
			break;
		case 't':
			vTimeMarker1C = groupValue;
			break;
		case 'tt':
			vTimeMarker2C = groupValue;
			break;
		}
	}


	vYear = vYear == null?1900:vYear;
	vMonth = vMonth == null?0:vMonth;
	vDay = vDay == null?1:vDay;

	vMinutes = vMinutes == null?0:vMinutes;
	vSeconds = vSeconds == null?0:vSeconds;
	var vHourOffset = 0;

	if (vHour12h != null) {
		if (vHour12h >= 1 && vHour12h <= 12) {
			vHour = vHour12h;
			if ((vTimeMarker1C || vTimeMarker2C || "").charAt(0)=="P") {
				if (vHour12h < 12) {
					vHour = vHour12h + 12;
				}
			} else {
				if (vHour12h == 12) {
					vHour = 0;
				}
			}
			//must add 12 to hour if time is PM
			//also, must add 12 if vHour12h in 12h format is greater than 11, which is invalid
			//vHourOffset = ( (vTimeMarker1C || vTimeMarker2C || "").charAt(0)=="P" || vHour12h>11)?12:0;
			//vHour = vHour12h + vHourOffset;
		} else {
			vHour = -1000;
		}
	} else if(vHour24H != null) {
		vHour = vHour24H;
	} else {
		vHour = 0;
	}

	var o = {
		'year': vYear, 
		'month': vMonth, 
		'day': vDay,
		'hour': vHour, 
		'minutes': vMinutes, 
		'seconds': vSeconds
	};

	if (dateMask.indexOf('y') < 0
		&& dateMask.indexOf('m') < 0 
		&& dateMask.indexOf('d') < 0 ) {
		o['year'] = '1900';
		o['month'] = '1';
		o['day'] = 1;
	}
	return o;
}

Object_weave_safe(utility, {window: {}});

utility.window.openWindow = function(target, url, width, height) {
	var wndHandler;
	var left = (screen.width - width) / 2;
	var top = (screen.height - height) / 2;
	var winargs = "width=" + width + ",height=" + height + ",resizable=No,scrollbars=No,status=Yes,modal=yes,dependent=yes,dialog=yes,left=" + left + ",top=" + top;

	wndHandler = window.open(url, target, winargs);
	if (wndHandler) {
		utility.window.reference = wndHandler;
		var ctrlModalBlocker = document.getElementById('modalBlocker');
		if (!ctrlModalBlocker) {
			var ctrlModalBlocker = utility.dom.createElement("DIV", {
				'id'      : 'modalBlocker',
				'style'   : 'display: block'
			});
			var pos = utility.dom.getPageInnerSize();
			ctrlModalBlocker.style.zIndex = 999;
			ctrlModalBlocker.style.width = (pos.x) + 'px';
			ctrlModalBlocker.style.height = (pos.y) + 'px';
			prepfixieinsertnodescrollup();
			ctrlModalBlocker = document.body.insertBefore(ctrlModalBlocker, document.body.firstChild);
			utility.dom.attachEvent(ctrlModalBlocker, 'onmousedown', function() {
				return utility.window.focusmodal();
			});
			utility.dom.attachEvent(ctrlModalBlocker, 'ondblclick', function() {
				return utility.window.focusmodal();
			});
			utility.dom.attachEvent(ctrlModalBlocker, is.ie?'onbeforeactivate':'onfocus', function() {
				return utility.window.focusmodal();
			});
			utility.dom.attachEvent(is.mozilla?window.document.body:window, is.ie?'onbeforeactivate':'focus', function() {
				return utility.window.focusmodal();
			});
			fixieinsertnodescrollup();
		} else {
			ctrlModalBlocker.style.display = 'block';
		}
		wndHandler.focus();
	}

	if (!wndHandler) {
		alert(translate('Cannot open dialog. Please allow site popups.'));
	}

	return wndHandler;
};
function prepfixieinsertnodescrollup() {
	//IE will scrollup inside all iframes after a DOM node insert
	if (is.ie && typeof(ktmls)!="undefined") {
		prepfixieinsertnodescrollup.scrolls = [];
		for (var i=0;i<ktmls.length; i++) {
			if (ktmls[i].destroyed) {
				continue;
			}
			prepfixieinsertnodescrollup.scrolls[i] = ktmls[i].edit.body.scrollTop;
		}
	}
}
function fixieinsertnodescrollup() {
	//it doesn't work without an timeout (IE needs some 
	window.setTimeout("fixieinsertnodescrollup_late()", 1);
};

function fixieinsertnodescrollup_late() {
	if (is.ie && typeof(ktmls)!="undefined") {
		for (var i=ktmls.length-1;i>=0; i--) {
			if (ktmls[i].destroyed) {
				continue;
			}
			ktmls[i].edit.body.scrollTop = prepfixieinsertnodescrollup.scrolls[i];
		}
	}
};

utility.window.focusmodal = function() {
	if (utility.window.reference && !utility.window.reference.closed) {
		utility.window.reference.focus();
		return;
	}
	utility.window.hideModalBlocker();
};

utility.window.hideModalBlocker = function (wnd) {
	if (!wnd) {
		wnd = window;
	}
	utility.window.reference = null;
	if (wnd.closed) {
		return;
	}
	var ctrlModalBlocker = wnd.document.getElementById('modalBlocker');
	if (ctrlModalBlocker) {
		ctrlModalBlocker.style.display = 'none';
	}
};

utility.window.close = function() {
	window.close();
};

utility.popup = {};
utility.popup.stiva = [];
//one may need the keyboard events while having the popup open
//so instruct utility.popup to not block keyboard while popup is open
//but then he must treat the ESC key himself
utility.popup.makeModal = function(clickCallBack, elementOnTop, stopEvents) {
	if(typeof(stopEvents) == "undefined") {
		stopEvents = true;
	}
	utility.popup.stiva.push({'element' : elementOnTop, 'callback': clickCallBack, 'stopEvents':stopEvents});
};

utility.popup.removeModal = function(e) {
	if (utility.popup.stiva.length == 0) {
		return;
	}
	if (utility.popup.force || e) {
		var tmp = utility.popup.stiva[utility.popup.stiva.length-1];
		if (e) {
			var o = utility.dom.setEventVars(e);
			var clickedElement = o.targ;
			while (clickedElement) {
				if (tmp.element && clickedElement == tmp.element) {
					break;
				}
				if (clickedElement.mi && clickedElement.mi['action_event'] != 'mousedown') {
					//must not close the open context menu if mousedown on an open submenu
					//modals should be closed inside the close menu function call
					break;
				}
				clickedElement = clickedElement.parentNode;
			}
			if (clickedElement) {
				// the user clicked on the elementOnTop
				return;
			}
		}
		if (tmp.callback) {
			tmp.callback();
		}
		utility.popup.stiva.pop();
		utility.popup.removeModal(e);
	}
	utility.dom.toggleSpecialTags(null, false, 0, true, true);
};

utility.popup.escapeModal = function(e) {
	if (utility.popup.stiva.length > 0) {
		if (!utility.popup.stiva[utility.popup.stiva.length-1].stopEvents) {
			return true;
		}
		var o = utility.dom.setEventVars(e);
		if (e.keyCode == 27) {
			utility.popup.force = true;
			utility.popup.removeModal(o.e);
			utility.popup.force = false;
		}
		if (is.ie && !o.e.ctrlKey) {
			try{o.e.keyCode = 90909090;}catch(e){};
		}
		utility.dom.stopEvent(o.e);
		return false;
	}
	return true;
}

utility.window.blockInterface = function(cursor, el, customId) {
	if (typeof(cursor) == "undefined") {
		cursor = "wait";
	}
	var ctrlInterfaceBlocker = utility.dom.createElement('div', {});
	ctrlInterfaceBlocker.className = 'interfaceBlocker';
	ctrlInterfaceBlocker.id = customId || 'interfaceBlocker';
	prepfixieinsertnodescrollup();
	ctrlInterfaceBlocker = document.body.appendChild(ctrlInterfaceBlocker);
	fixieinsertnodescrollup();
	ctrlInterfaceBlocker.style.cursor = cursor;
	var pos;
	if(!el)	{
		pos = utility.dom.getPageInnerSize();
		ctrlInterfaceBlocker.style.width = pos.x + 'px';
		ctrlInterfaceBlocker.style.height = pos.y + 'px';
	}
	else{
		pos = utility.dom.getBox(el);
		ctrlInterfaceBlocker.style.top = pos.y + 'px';
		ctrlInterfaceBlocker.style.left = pos.x + 'px';
		ctrlInterfaceBlocker.style.width = pos.width + 'px';
		ctrlInterfaceBlocker.style.height = pos.height + 'px';
	}
};

utility.window.unblockInterface = function() {
	var ctrlInterfaceBlocker = document.getElementById('interfaceBlocker');
	if (ctrlInterfaceBlocker) {
		document.body.removeChild(ctrlInterfaceBlocker);
	}
};

utility.window.setModal = function(set_unselectable) {
	if (typeof set_unselectable == "undefined") {
		set_unselectable = true;
	}
	window.isloading = false;
	window.focus();
	if (!window.dialogArguments) {
		window.onbeforeunload = function() {
			if (!window.opener.closed) {
				utility.window.hideModalBlocker(window.opener);
			}
		}
		if (set_unselectable) {
			utility.dom.setUnselectable(window.document.body);
		}
	} else {
		window.opener = dialogArguments;
	}
	if (!window.opener) {
		document.body.innerHTML = "<center>Invalid context! No opener.</center>" + '<div style="display:none !important">' + document.body.innerHTML + '</div>';
		return;
	}
	if (window.opener.topOpener) {
		window.topOpener = window.opener.topOpener;
	} else {
		window.topOpener = window.opener;
	}
	utility.dom.attachEvent(is.ie?window.document.body:window, 'keydown', function(e) {
		var ret = utility.popup.escapeModal(e);
		if (ret && e.keyCode == 27) {
			utility.window.close();
		}
	});
	utility.dom.attachEvent2(window.document.body, 'mousedown', utility.popup.removeModal);
};

Object_weave_safe(utility, {cookie: {}});

utility.cookie.set = function(name, value, lifespan, access_path) {
	var cookietext = name + "=" + escape(value);
	if (lifespan != null) {
		var date = new Date();
		date.setTime(date.getTime() + (1000*60*60*24*lifespan));
		cookietext += "; expires=" + date.toGMTString();
	}
	if (access_path != null) {
		cookietext += "; path=" + access_path;
	}
	document.cookie = cookietext;
	return null;
}

utility.cookie.get = function(name) {
	var nameeq = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') {
			c = c.substring(1,c.length);
		}
		if (c.indexOf(nameeq) == 0) {
			return unescape(c.substring(nameeq.length,c.length));
		}
	}
	return null;
}

utility.cookie.del = function(name, path) {
	utility.cookie.set(name, "", -1, path);
}

// simple UID generator
UIDGenerator = function(name) {
	if (typeof(name) == 'undefined') {
		name = 'iaktuid_' + Math.random().toString().substring(2, 6) + '_';
	}
	this.name = name;
	this.counter = 1;
}
UIDGenerator.prototype.generate = function(detail) {
	if (typeof(detail) == 'undefined') {
		detail = '';
	}
	return (this.name + detail + this.counter++ + '_');
}

ObjectStorage = function (name) {
	this.storage = {};
	this.gen = new UIDGenerator(name + "_reference_by_id_");
}

ObjectStorage.prototype.add = ObjectStorage.prototype.storeObject = function (obj) {
	var type = obj.constructor.toString().match(/^\s*function\s*([^\s\(]*)\s*\(/i);
	if (!type) {
		type = "unknown_contructor";
	} else {
		type = type[1];
	}
	var newId = this.gen.generate(type);
	obj.id = newId;
	this.storage[newId] = obj;
}

ObjectStorage.prototype.get = ObjectStorage.prototype.getObject = function (id) {
	return this.storage[id];
}

ObjectStorage.prototype.deleteObject = function (id) {
	delete this.storage[id];
}

ObjectStorage.prototype.dispose = function () {
	this.storage = null;
}

QueryString = function(str) {
	if (typeof str == 'undefined') {
		var str = window.location.search.toString();
	}
	this.keys = new Array();
	this.values = new Array();
	var query = str;
	if (str.indexOf('?') == 0) {
		query = str.substring(1);
	}
	query = query.replace(/&amp;/g, '&');
	var pairs = query.split("&");

	for (var i = 0; i < pairs.length; i++) {
		var pos = pairs[i].indexOf('=');

		if (pos >= 0) {
			var argname = pairs[i].substring(0, pos);
			var value = pairs[i].substring(pos + 1);
			this.keys[this.keys.length] = argname;
			this.values[this.values.length] = value;
		}
	}
}

QueryString.prototype.find = function(key) {
	var value = null;
	for (var i = 0; i < this.keys.length; i++) {
		if (this.keys[i] == key) {
			value = this.values[i];
			break;
		}
	}
	return value;
}

KT_Tooltips = {
	cname: 'kt_add_tooltips', 
	worked: [], 
	cancel:false,
	gen: new UIDGenerator(), 
	show: function (id, x, y) {
		var div = document.getElementById(id);
		if (!div) {
			return;
		}
		//show it somewhere out of sight, so it gets a box
		div.style.left = '-1000px';
		div.style.top = '-1000px';
		div.style.display = 'block';
		var pos = utility.dom.getBBox(div);

		var pos2 = utility.dom.getPositionRelativeTo00(x, y, pos.width + 2, pos.height + 2);

		div.style.left = pos2.x + 'px';
		div.style.top = pos2.y + 'px';
		//KT_Tooltips.set_timeout(id, "hide", 3250);
	}, 
	hide: function (id) {
		var div = document.getElementById(id);
		if (!div) {
			return;
		}
		div.style.display = 'none';
	}, 
	clear_timeout: function(id, mode) {
		var to = id + mode + "timeout";
		if (typeof window[to] != 'undefined') {
			clearTimeout(window[to]);
		}
	}, 
	clear_showtimeout: function(id) {
		KT_Tooltips.clear_timeout(id, 'show');
	}, 
	clear_hidetimeout: function(id) {
		KT_Tooltips.clear_timeout(id, 'hide');
	}, 
	set_timeout: function(id, mode, time) {
		var params_str = '', params_arr = [];
		if (arguments.length > 3) {
			for (var i = 3; i < arguments.length; i++) {
				Array_push(params_arr, arguments[i]);
			}
		}
		params_str = params_arr.join(', ');
		if (params_str != '') {
			params_str = ', ' + params_str;
		}
		var str = "KT_Tooltips." + mode + "('" + id + "'"+ params_str+")";
		var to = id + mode + "timeout";
		window[to] = setTimeout(str, time);
	}, 
	set_showtimeout: function(id, vars) {
		KT_Tooltips.set_timeout(id, "show", 1000, vars.x, vars.y);
	}, 
	set_hidetimeout: function(id) {
		KT_Tooltips.set_timeout(id, "hide", 250);
	}, 
	attach_single: function(link) {
		if (is.ie || is.safari) {
			return;
		}
		var title = link.title;
		var mytip = null;
		if (link.getAttribute('divid')) {
			mytip = document.getElementById(link.getAttribute('divid'));
			if (mytip) {
				document.body.removeChild(mytip);
			}
			link.removeAttribute('divid');
		}
		if (/[\r\n]/.test(title)) {
			var divid = KT_Tooltips.gen.generate("tooltip");
			var div = utility.dom.createElement("div", {
				'class': 'tooltip_div', 
				'id': divid
			});
			div.innerHTML = link.getAttribute("title").toString().replace(/\r\n/g, "<br />").replace(/[\r|\n]/g, "<br />");
			link.divid = divid;
			div = document.body.appendChild(div);
			link.removeAttribute("title");
			link.setAttribute("divid", divid);
			if (!mytip) {//attach events only once
				utility.dom.attachEvent(link, 'mouseover', function(e) {		
					var id = link.getAttribute("divid");
					var pos = utility.dom.getBBox(link);
					var vars = utility.dom.setEventVars(e);
					KT_Tooltips.clear_hidetimeout(id);
					var obj = {x: pos.x + Math.round(pos.width / 2), y: pos.y + Math.round(pos.height / 2) + 10};
					KT_Tooltips.set_showtimeout(id, obj);
					utility.dom.stopEvent(e);
				});
				utility.dom.attachEvent(link, 'mouseout', function(e) {
					var id = link.getAttribute("divid");
					KT_Tooltips.clear_showtimeout(id);
					KT_Tooltips.set_hidetimeout(id);
					utility.dom.stopEvent(e);
				});
			}
		}
	}, 
	attach: function () {
		KT_Tooltips.worked = [];
		if (is.ie || is.safari) {
			return;
		}
		Array_each(utility.dom.getElementsByClassName(document.body, KT_Tooltips.cname), function(el) {
			Array_each(el.getElementsByTagName('a'), KT_Tooltips.attach_single);
		}) 
	}
};
utility.dom.attachEvent(window, 'load', KT_Tooltips.attach);

/*
 * class XmlHttp
*/
//MsXML on Mozilla
function getDomDocumentPrefix() {
	if (getDomDocumentPrefix.prefix) return getDomDocumentPrefix.prefix;
	var prefixes = ["MSXML2", "Microsoft", "MSXML", "MSXML3"];
	var o;

	for (var i = 0; i < prefixes.length; i++) {
		try {
			o = new ActiveXObject(prefixes[i] + ".DomDocument");
			return getDomDocumentPrefix.prefix = prefixes[i];
		} catch (ex) { }
	}
	throw new Error("Could not find an installed XML parser");
}
function getXmlHttpPrefix() {
	if (getXmlHttpPrefix.prefix) return getXmlHttpPrefix.prefix;

	var prefixes = ["MSXML2", "Microsoft", "MSXML", "MSXML3"];
	var o;

	for (var i = 0; i < prefixes.length; i++) {
		try {
			// try to create the objects
			o = new ActiveXObject(prefixes[i] + ".XmlHttp");
			return getXmlHttpPrefix.prefix = prefixes[i];
		} catch (ex) { }
	}

	throw new Error("Could not find an installed XML parser");
}

// XmlHttp factory
function XmlHttp() { }
XmlHttp.create = function() {
	try {
		if (window.XMLHttpRequest) {
			var req = new XMLHttpRequest();
			if (req.readyState == null) {
				req.readyState = 1;
				req.addEventListener("load", 
					function() {
						req.readyState = 4;
						if (typeof req.onreadystatechange == "function") 
							req.onreadystatechange();
					}, false);
			}
			return req;
		}
		if (window.ActiveXObject) {
			var ax = new ActiveXObject(getXmlHttpPrefix() + ".XmlHttp");
			return ax;
		}
	} catch (ex) { }

	// fell through
	throw new Error("Your browser does not support XmlHttp objects");
}
XmlHttp.post = function(rpc, url, postStr) {
	try {
		rpc.open("POST", url, false);
		rpc.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		rpc.send(postStr);
	} catch(e) {
		return false;
	}
	return rpc;
}
XmlHttp.get = function(rpc, url, getStr) {
	try {
		rpc.open("GET", getStr, false);
		rpc.send(null);
	} catch(e) {
		return false;
	}
	return rpc;
}

// XmlDocument factory
function XmlDocument() {}

XmlDocument.create = function () {
	try {
		// DOM2
		if (document.implementation && document.implementation.createDocument) {
			var doc = document.implementation.createDocument("", "", null);
			
			// some versions of Moz do not support the readyState property
			// and the onreadystate event so we patch it!
			if (doc.readyState == null) {
				doc.readyState = 1;
				doc.addEventListener("load", function () {
					doc.readyState = 4;
					if (typeof doc.onreadystatechange == "function")
						doc.onreadystatechange();
				}, false);
			}
			
			return doc;
		}
		if (window.ActiveXObject)
			return new ActiveXObject(getDomDocumentPrefix() + ".DomDocument");
	}
	catch (ex) {}
	throw new Error("Your browser does not support XmlDocument objects");
};

// Create the loadXML method and xml getter for Mozilla
if (window.DOMParser &&
	window.XMLSerializer &&
	window.Node && Node.prototype && Node.prototype.__defineGetter__) {

	// XMLDocument did not extend the Document interface in some versions
	// of Mozilla. Extend both!
	//XMLDocument.prototype.loadXML = 
	Document.prototype.loadXML = function (s) {
		
		// parse the string to a new doc	
		var doc2 = (new DOMParser()).parseFromString(s, "text/xml");
		
		// remove all initial children
		while (this.hasChildNodes())
			this.removeChild(this.lastChild);
			
		// insert and import nodes
		var ret = false;
		for (var i = 0; i < doc2.childNodes.length; i++) {
			this.appendChild(this.importNode(doc2.childNodes[i], true));
			ret = true;
		}
		return ret;
	};
	
	
	/*
	 * xml getter
	 *
	 * This serializes the DOM tree to an XML String
	 *
	 * Usage: var sXml = oNode.xml
	 *
	 */
	// XMLDocument did not extend the Document interface in some versions
	// of Mozilla. Extend both!
	/*
	XMLDocument.prototype.__defineGetter__("xml", function () {
		return (new XMLSerializer()).serializeToString(this);
	});
	*/
/*@cc_on @*/
/*@if (@_jscript_version >= 3)
	//hide the next block of code from the IE compiler ;)
@else @*/
	var documentProto = Document.prototype;
	var documentGrandProto = documentProto.__proto__ = {
		__proto__: documentProto.__proto__
	};
	
	if (documentGrandProto) {
		documentGrandProto.__defineGetter__('xml',
			function () { return (new XMLSerializer()).serializeToString(this); }
		);
	}

	var elementProto = Element.prototype;
	var elementGrandProto = elementProto.__proto__ = {
		__proto__: elementProto.__proto__
	};
	if (elementGrandProto) {
		elementGrandProto.__defineGetter__('text',
			function () { return this.textContent; }
		);
		elementGrandProto.__defineGetter__('innerText',
			function () { return this.textContent; }
		);
		elementGrandProto.__defineSetter__('innerText',
			function (new_value) { 
				var tn = this.ownerDocument.createTextNode(new_value);
				this.innerHTML = "";
				this.appendChild(tn);
			}
		);
	}

/*@end @*/
}

function evaluateXPath(aNode, aExpr) {
	var found = [];
	if (is.mozilla) {
		if (typeof evaluateXPath.xpe == "undefined") {
			evaluateXPath.xpe = new XPathEvaluator();
		}
		//var nsResolver = xpe.createNSResolver(aNode.ownerDocument == null ? aNode.documentElement : aNode.ownerDocument.documentElement);
		//var result = xpe.evaluate(aExpr, aNode, nsResolver, XPathResult.ANY_TYPE, null);
		var result = evaluateXPath.xpe.evaluate(aExpr, aNode, null, XPathResult.ANY_TYPE, null);
		while (res = result.iterateNext()) {
			found.push(res);
		}
	} else if (is.ie) {
		var result = aNode.selectNodes(aExpr);
		for(var i=0; i<result.length; i++) {
			found.push(result[i]);
		}
	}
	if (found.length == 0) {
		found = false;
	}
	return found;
};

function BrowserCheck() {
	var b = navigator.appName.toString();
	var up = navigator.platform.toString();
	var ua = navigator.userAgent.toString();

	this.mozilla = this.ie = this.opera = r = false;
	var re_opera = /Opera.([0-9\.]*)/i;
	var re_msie = /MSIE.([0-9\.]*)/i;
	var re_gecko = /gecko/i;
	var re_safari = /safari\/([\d\.]*)/i;
	
	if (ua.match(re_opera)) {
		r = ua.match(re_opera);
		this.opera = true;
		this.version = parseFloat(r[1]);
	} else if (ua.match(re_msie)) {
		r = ua.match(re_msie);
		this.ie = true;
		this.version = parseFloat(r[1]);
	} else if (ua.match(re_safari)) {
		this.mozilla = true;
		this.safari = true;
		this.version = 1.4;
	} else if (ua.match(re_gecko)) {
		var re_gecko_version = /rv:\s*([0-9\.]+)/i;
		r = ua.match(re_gecko_version);
		this.mozilla = true;
		this.version = parseFloat(r[1]);
	}
	this.windows = this.mac = this.linux = false;

	this.Platform = ua.match(/windows/i) ? "windows" :
					(ua.match(/linux/i) ? "linux" :
					(ua.match(/mac/i) ? "mac" :
					ua.match(/unix/i)? "unix" : "unknown"));
	this[this.Platform] = true;
	this.v = this.version;
	this.valid = this.ie && this.v >= 6 || this.mozilla && this.v >= 1.4;
	if (this.safari && this.mac && this.mozilla) {
		this.mozilla = false;
	}
};

function sortFormHandlers(arr) {
	for(var i=0; i<arr.length; i++) {
		var fh1 = arr[i];
		for(var j=i+1;j<arr.length;j++) {
			var fh2 = arr[j];
			if (fh2[0]<fh1[0]) {
				var tmp = fh1;
				arr[i] = fh2;
				arr[j] = tmp;
			}
		}
	}
};

function GLOBAL_registerFormSubmitEventHandler(function_name, priority) {
	var frms = document.getElementsByTagName('form');
	for (var i = 0; i < frms.length; i++) {
		var frm = frms[i];
		if (typeof frm.onsubmit != 'undefined' && frm.onsubmit != null) {
			var form_handlers = frm.form_handlers;
			if (form_handlers) {
				form_handlers[form_handlers.length] = [priority, function_name];
				sortFormHandlers(form_handlers);
			} else {
				//user defined onsubmit handler
				frm.__kt_onsubmit = frm.onsubmit;
				frm.onsubmit = new Function('e', 'if (!KT_formSubmittalHandler(e)) return false;');
				form_handlers = [];
				form_handlers[form_handlers.length] = [priority, function_name];
			}
			frm.form_handlers = form_handlers;
		} else {
			frm.onsubmit = new Function('e', 'return KT_formSubmittalHandler(e);');
			var form_handlers = [];
			form_handlers[form_handlers.length] = [priority, function_name];
			frm.form_handlers = form_handlers;
		}
	}
};


/**
* Fix the KT_formSubmittalHandler from utility.js
*	FF : stopping the onsubmit event seems to alter the event.type (accessing the property after stopping the event raise an error)
*	IE : if the call to KT_formSubmittalHandler is not triggered by a submit, there's no way to find the FORM to handle; setEventVars(e) returns some other event
*/
var fire_starter = null;
var global_form_submit_lock = false;
function KT_formSubmittalHandler(e) {
	var frm = null;
	var o = utility.dom.setEventVars(e);
	if (!o.e) {
		return true;
	}
	try {
		if (global_form_submit_lock) {
			utility.dom.stopEvent(o.e);
			return false;
		}
		frm = o.targ;
		if (!frm) {
			return true;
		}
		if (!frm.tagName) {
			return true;
		}
		if (frm.tagName.toLowerCase()!="form") {
			frm = frm.form;
		}
	} catch(err) { }

	if(!frm){
		frm = fire_starter;
	}
	if (!frm) {
		return true;
	}

	if (typeof(UNI_disableButtons) != 'undefined') {
		UNI_disableButtons(frm, /.*/, true);
	}

	var ret = true;
	var form_handlers = frm.form_handlers;
	if (form_handlers) {
		for(var i=0; i<form_handlers.length; i++) {
			var fun = form_handlers[i];
			eval("ret = " + fun[1] + "(o.e);");
			//alert("KT_formSubmittalHandler ret = "+fun[1]+"(o.e);, the result is: " + ret);
			if (!ret) {
				break;
			}
		}
	}
	if (is.ie && is.mac && typeof(UNI_disableButtons) != 'undefined') {
		UNI_disableButtons(frm, /.*/, false);
	}

	if (!ret) {
		try {
			utility.dom.stopEvent(o.e);
		} catch(err) { }
		//the form submit lock does not enable the buttons
		if (!global_form_submit_lock && typeof(UNI_disableButtons) != 'undefined') {
			UNI_disableButtons(frm,/.*/,false);
		}
		return false;
	} else {
		if (frm.__kt_onsubmit) {
			var ret = frm.__kt_onsubmit(o.e);
			if (typeof(ret) == 'undefined' || ret) {
				return true;
			} else {
				return false;
			}
		}
		return true;
	}
};

utility.dom.attachEvent(window, 'unload', EventCache.flush);
