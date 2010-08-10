// Define CSS Styles
var disabledStyle = 'disabled';
var enabledStyle = 'enabled';


function magicFieldThingy(){

	// Detect Internet Explorer and do crazy stuff
	if (!!(window.attachEvent && !window.opera)) {


		var allInputItems = $A(document.getElementsByTagName('input'));
		var allSelectItems = $A(document.getElementsByTagName('select'));

		for (i=0; i<allInputItems.length; i++) {
			allInputItems[i].oldValue = allInputItems[i].value;
			allInputItems[i].className=disabledStyle;
			allInputItems[i].onfocus=function(){
				if (this.value==this.oldValue) {
					this.className=enabledStyle;
					this.value='';
				}
			};
			allInputItems[i].onblur=function(){
				if (this.value=='') {
					this.className=disabledStyle;
					this.value=this.oldValue;
				}
			};
		}

		for (i=0; i<allSelectItems.length; i++) {
			allSelectItems[i].oldValue = allSelectItems[i].value;
			allSelectItems[i].className=disabledStyle;
			allSelectItems[i].onfocus=function(){
				if (this.value==this.oldValue) {
					this.className=enabledStyle;
				}
			};
			allSelectItems[i].onblur=function(){
				if (this.value=='') {

				}
			};

		}

	} else {

		// Detect INPUT and SELECT items
		var allInputItems = $A(document.getElementsByTagName('input'));
		var allSelectItems = $A(document.getElementsByTagName('select'));

		// Ignore items with magicField="false" & Do things to INPUT items
		for (i=0; i<allInputItems.length; i++) {
			if (allInputItems[i].readAttribute('magicField')!='false') {
				if (allInputItems[i].type=='text' || allInputItems[i].type=='password') {
					allInputItems[i].observe('focus', onMagicFocus);
					allInputItems[i].observe('blur', onMagicBlur);
					allInputItems[i].disabledStyle = disabledStyle;
					allInputItems[i].enabledStyle = enabledStyle;
					if (allInputItems[i].value=='') {
						allInputItems[i].magicLabel = allInputItems[i].readAttribute('magicLabel');
						allInputItems[i].value = allInputItems[i].magicLabel;
						allInputItems[i].className=disabledStyle;
					} else {
						allInputItems[i].className=enabledStyle;
					}
					if (allInputItems[i].type=='password') {
						allInputItems[i].type='text';
						allInputItems[i].isPassword = true;
					}
				}
			}
		}
	}

	// Ignore items with magicField="false" & Do things to SELECT items
	for (i=0; i<allSelectItems.length; i++) {
		if (allSelectItems[i].readAttribute('magicField')!='false') {
			allSelectItems[i].observe('focus', onMagicSelectFocus);
			allSelectItems[i].observe('blur', onMagicSelectBlur);
			allSelectItems[i].magicLabel = allSelectItems[i].readAttribute('magicLabel');
			allSelectItems[i].magicDeleteFirst = allSelectItems[i].readAttribute('magicDeleteFirst');
			allSelectItems[i].disabledStyle = disabledStyle;
			allSelectItems[i].enabledStyle = enabledStyle;
			allSelectItems[i].className=disabledStyle;
		}
	}
}

function onMagicSelectFocus(event) {
	var element = Event.element(event);
	element.className=element.enabledStyle;
	
	if (element.magicDeleteFirst!='false') {
		var firstOption = element.firstDescendant();
		firstOption.hide();
	}
}
function onMagicSelectBlur(event) {
	var element = Event.element(event);
	if (element.value==element.magicLabel){
		element.className=element.disabledStyle;
	}
}
function onMagicFocus(event) {
	var element = Event.element(event);

	if (element.isPassword){
		element.type='password';
		element.focus();
	}
	if (element.value!=element.magicLabel){
		element.className=element.enabledStyle;
	} else {
		element.value='';
		element.className=element.enabledStyle;	
	}
}
function onMagicBlur(event){
	var element = Event.element(event);

	if (element.value=='') {
		if (element.isPassword) {
			element.type='text';
		}
		element.value=element.magicLabel;
		element.className=element.disabledStyle;
	}
}

window.onload = magicFieldThingy;