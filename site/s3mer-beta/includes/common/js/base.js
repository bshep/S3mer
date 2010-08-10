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

/* *
 * base classes extensions
 * */

/* *
fix ecma compliance
 * */
if (!Function.prototype.apply) {
	Function.prototype.apply = function (o,a) {
		var r;
		if(!o){ o = {}; } // in case func.apply(null, arguments).
		o.___apply=this;
		switch((a && a.length) || 0) {
			case 0: r = o.___apply(); break;
			case 1: r = o.___apply(a[0]); break;
			case 2: r = o.___apply(a[0],a[1]); break;
			case 3: r = o.___apply(a[0],a[1],a[2]); break;
			case 4: r = o.___apply(a[0],a[1],a[2],a[3]); break;
			case 5: r = o.___apply(a[0],a[1],a[2],a[3],a[4]); break;
			case 6: r = o.___apply(a[0],a[1],a[2],a[3],a[4],a[5]); break;
			default: 
				for(var i=0, s=""; i<a.length;i++){
					if(i!=0){ s += ","; }
					s += "a[" + i +"]";
				}
				r = eval("o.___apply(" + s + ")");
		}
		o.__apply = null;
		return r;
	}
};

if (!Function.prototype.call) {
	Function.prototype.call = function(o) {
		// copy arguments and use apply
		var args = new Array(arguments.length - 1);
		for(var i=1;i<arguments.length;i++){
			args[i - 1] = arguments[i];
		}
		return this.apply(o, args);
	}
};

Function_bind = function(_this, object) {
	var __method = _this;
	return function() {
		__method.apply(object, arguments);
	}
};

Function_bindEventListener = function(_this, object) {
	var __method = _this;
	return function(event) {
		__method.call(object, event || window.event);
	}
};

if (!Array.prototype.push) {
	Array_push = function(_this, obj) {
		for (var i=1; i<arguments.length; i++) {
			_this[_this.length] = arguments[i];
		}
		return _this.length;
	}
} else {
	Array_push = function(_this, obj) {
		for (var i=1; i< arguments.length; i++) {
			_this.push(arguments[i]);
		}
		return _this.length;
	}
};

if (!Array.prototype.pop) {
	Array_pop = function(_this) {
		if(_this.length == 0){ 
			try{
				return undefined; 
			} catch(e) {
				return null;
			}
		}
		return _this[_this.length--];
	}
} else {
	Array_pop = function(_this) {
		return _this.pop();
	}
};

if (!Array.prototype.shift) {
	Array_shift = function(_this) {
		_this.reverse();
		var lastv = Array_pop(_this);
		_this.reverse();
		return lastv;
	};
} else {
	Array_shift = function(_this) {
		return _this.shift();
	};
};
// this splice works differently than the one provided with browsers
// because it doesn't change the original array
if (!Array.prototype.splice) {
  Array_splice = function(_this, start, deleteCount) {
    var len = parseInt(_this.length);

    start = start ? parseInt(start) : 0;
    start = (start < 0) ? Math.max(start+len,0) : Math.min(len,start);

    deleteCount = deleteCount ? parseInt(deleteCount) : 0;
    deleteCount = Math.min(Math.max(parseInt(deleteCount),0), len);

    var deleted = _this.slice(start, start+deleteCount);

    var insertCount = Math.max(arguments.length - 1,1);
    // new len, 1 more than last destination index
    var new_len = _this.length + insertCount - deleteCount;
    var start_slide = start + insertCount;
    var nslide = len - start_slide; // (_this.length - deleteCount) - start
    // slide up
    for(var i=new_len - 1;i>=start_slide;--i){
		_this[i] = _this[i - nslide];
	}
    // copy inserted elements
    for(i=start;i<start+insertCount;++i){
		_this[i] = arguments[i-start+3];
	}
    return deleted;
  };
} else {
	Array_splice = function(_this, start, deleteCount) {
		var args = [];
		var s = '';
		for(var i=3; i<arguments.length; i++) {
			args[i-3] = arguments[i];
			s += ', ' + 'args['+(i-3)+']';
		}
		s = 'var ret = _this.splice(start, deleteCount' + s + ')';
		eval(s);
		return ret;
	};
};

/* Object extensions */
// .toArray
Object_toArray = function(_this, delim) {
	var result;
	if (typeof(delim) == 'undefined') {
		delim = ',';
	}
	switch(typeof(_this)) {
		case 'array':
			result = _this;
			break;
		case 'string':
			if (_this.indexOf(delim)) {
				result = _this.split(delim);
			} else {
				result.push(_this);
			}
			break;
		default:
			result.push(_this);
			break;
	}
};

Object_weave = function(_this, source) {
	for (property in source) {
		_this[property] = source[property];
	}
	return _this;
};

Object_weave_safe = function(_this, source) {
	for (property in source) {
		if (typeof _this[property] == 'undefined') {
			_this[property] = source[property];
		}
	}
	return _this;
};


/* Array extensions */
// .indexOf : behaves exactly as String#indexOf
Array_indexOf = function(_this, x) {
	for (var i=0; i<_this.length; i++) {
		if (_this[i] == x) {
			return i;
		}
	}
	return -1;
};
// .lastIndexOf : behaves exactly as String#lastIndexOf
Array_lastIndexOf = function(_this, x) {
	for (var i=_this.length-1; i>=0; i--) {
		if (_this[i] == x) {
			return i;
		}
	}
	return -1;
};

Array_last = function(_this) {
	if (_this.length > 0) {
		return _this[_this.length - 1];
	}
};


/* String extensions */
// .trim : trim whitespace at beginning and end of a string
String_trim = function(_this, str) {
	if (!str) str = _this;
	return str.replace(/^\s*/, "").replace(/\s*$/, "");
};

// .normalize_space : return string with extra whitespace removed
String_normalize_space = function(_this, str) {
	if (!str) str = _this;
	return String_trim(str).replace(/\s+/g, " ");
};

String_htmlencode = function(_this, str) {
	if (!str) str = _this;
	return str.replace(/\&/g, "&amp;").replace(/\</g, "&lt;").replace(/\>/g, "&gt;").replace(/\"/g, "&quot;");
};

String_htmldecode = function(_this, str) {
	if (!str) str = _this;
	return str.replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, "\"").replace(/&amp;/g, "&");
};
/* from ruby.js */
Array_each = function(_this, block) {
	for (var index = 0; index < _this.length; ++index) {
		var item = _this[index];
		block(item, index)
	}
	return _this;
};

Number_times = function(_this, block) {
	for (var i = 0; i < _this; i++) block(i)
};
 
/* helpers */
// min for array, string and as function
//  [3, 2, 4].min() => 2
Array_min = function(_this) {
	if (_this.length == 0) return false;
	if (_this.length == 1) return _this[0];
	var min, me, val;
	min = 0;
	me = _this;
	Array_each(me, function(val, i) {
		if (val < me[min]) {
			min = i;
		}
	});
	return _this[min];
};

// "3,2,4".min() => 2
String_min = function(_this) {
	return Array_min(_this.split(','));
};

// min(3, 2, 4) => 2
function min() {
	//arguments.each = Array.prototype.each;
	var a = [];
	Array_each(arguments, function(val, i) {
		Array_push(a, val);
	});
	return Array_min(a);
};

// max for array, string and as function
//  [3, 2, 4].max() => 4
Array_max = function(_this) {
	if (_this.length == 0) return false;
	if (_this.length == 1) return _this[0];
	var max, me, val;
	max = 0;
	me = _this;
	Array_each(me, function(val, i) {
		if (val > me[max]) {
			max = i;
		}
	});
	return _this[max];
};

// "3,2,4".max() => 4
String_max = function(_this) {
	return Array_max(_this.split(','));
};

// max(3, 2, 4) => 4
function max() {
	//arguments.each = Array.prototype.each;
	var a = [];
	Array_each(arguments, function(val, i) {
		Array_push(a, val);
	});
	return Array_max(a);
};

