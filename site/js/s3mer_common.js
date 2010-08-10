
String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}
String.prototype.ltrim = function() {
	return this.replace(/^\s+/,"");
}
String.prototype.rtrim = function() {
	return this.replace(/\s+$/,"");
}




function toggleLanguageBar(event) {
    var clickElem = Event.element(event,'div');
    var userlang;
    
    if (clickElem.tagName != 'DIV') { clickElem = Event.findElement(event,'div'); }
    var targetElem = $(clickElem.getAttribute('targetDiv'));
    if (targetElem == null){
        return;
    }
    if ( targetElem.id == 'language-bubble') {
        $('login-bubble').hide();
    }
    
    if ( targetElem.id == 'login-bubble') {
        $('language-bubble').hide();
    }
    
    if ( targetElem == null ) { 
        clickElem = clickElem.parentNode;
        targetElem = $(clickElem.getAttribute('targetDiv'));
    }

    if( targetElem.visible() == true ) {
        Effect.Fade(targetElem, {duration:0.2});
    } else {
        Effect.Appear(targetElem, {duration:0.2});
    }
}



function onFieldEnter(event) {
  var elem = Event.element(event);
  
//  if (elem.getAttribute('filledField') != 'true') { return; }
  
  if (elem.hasEntered == null) { elem.hasEntered = false; }
  
  if (elem.hasEntered == false) {
  
    if (elem.getAttribute('ispassword') == 'true') {
      Try.these(
        function() { 
          elem.type = 'password';
          return true;
        },
        function() { 
          //IE SUCKS so we have to this nasty stuff...
          var identity = elem.identify();
              
          elem.oldValue = elem.value;
          elem.clear();
          var oldHtml = elem.parentNode.innerHTML;
          var elemParentNode = elem.parentNode;
          
          elem.focus();
          elemParentNode.innerHTML = oldHtml.gsub(/>/,'type="password">');
          elem = $(identity);
          elem.focus();
          elem.focus();
          return true;
        }
      
      );
    }
    
    
    elem.oldValue = elem.value;
    elem.className = 'form-items';
    elem.hasEntered = true;
    elem.clear();
  }
}



function serializedToComma(serialized_str,drag_box){
	try{
		
		
		var ns=serialized_str;
		ns=ns.replace(/\[/g,"");
		ns=ns.replace(/\]/g,"");
		ns=ns.replace(/\=/g,"");
		ns=ns.replace(/&/g,",");
		ns=ns.replace(new RegExp(drag_box,"g"),"");
		
		return ns;
		
	}
	catch(e){
		alert(e.toString());
	}
}



function onFieldLeave(event) {
  var elem = Event.element(event);
  
//  if (elem.getAttribute('filledField') != 'true') { return; }

  if (elem.hasEntered == null) { elem.hasEntered = false; }
  
  if (elem.hasEntered == true && elem.value == "") {
  
    if (elem.getAttribute('ispassword') == 'true') {
      elem.type = 'text';
    }
    
    elem.value = elem.oldValue
    elem.className = 'form-items-disabled';
    elem.hasEntered = false;
  }
}

//hay que mejorar esto de abajo !!!

function onLoginFieldEnter(event) {
  var elem = Event.element(event);
	var passwd_elem = $('login_password');
	var login_elem = $('login_email');
	
	
  
//  if (elem.getAttribute('filledField') != 'true') { return; }
  
  if (elem.hasEntered == null) { elem.hasEntered = false; }

	if (elem == passwd_elem) {
		if(login_elem.className == 'login-bubble-fields-enabled') {
			login_elem.className = 'login-bubble-fields-enabled-small';
		}
		
		if(login_elem.className == 'login-bubble-fields-disabled-big') {
			login_elem.className = 'login-bubble-fields-disabled';
		}
	}
	
	if (elem == login_elem) {
		if(passwd_elem.className == 'login-bubble-fields-enabled') {
			passwd_elem.className = 'login-bubble-fields-enabled-small';
		}
		
		if(passwd_elem.className == 'login-bubble-fields-disabled-big') {
			passwd_elem.className = 'login-bubble-fields-disabled';
		}
	}
  
  if (elem.hasEntered == false) {
  
    if (elem.getAttribute('ispassword') == 'true') {
      Try.these(
        function() { 
          elem.type = 'password';
          return true;
        },
        function() { 
          //IE SUCKS so we have to this nasty stuff...
          var identity = elem.identify();    
          elem.oldValue = elem.value;
          elem.clear();
          var oldHtml = elem.parentNode.innerHTML;
          var elemParentNode = elem.parentNode;
          
          
          elemParentNode.innerHTML = oldHtml.gsub(/>/,'type="password">');
          elem = $(identity);
          return true;
        }
      
      );
    }
    
    
    elem.oldValue = elem.value;
    elem.className = 'login-bubble-fields-enabled';
    elem.hasEntered = true;
    elem.clear();
  } else {
	elem.className = 'login-bubble-fields-enabled';
  }
}

function onLoginFieldLeave(event) {
  var elem = Event.element(event);
	var passwd_elem = $('login_password');
	var login_elem = $('login_email');
  
//  if (elem.getAttribute('filledField') != 'true') { return; }

  if (elem.hasEntered == null) { elem.hasEntered = false; }

  if (login_elem.className == 'login-bubble-fields-disabled' || login_elem.className == 'login-bubble-fields-enabled-small') {
		if (login_elem.hasEntered == false) {
			login_elem.className = 'login-bubble-fields-disabled-big';
		} else {
			login_elem.className = 'login-bubble-fields-enabled';
		}
	}

  if (elem.hasEntered == true && elem.value == "") {
  
    if (elem.getAttribute('ispassword') == 'true') {
      elem.type = 'text';
    }
    
    elem.value = elem.oldValue
		
		if(passwd_elem == elem) {
			elem.className = 'login-bubble-fields-disabled';
		} else {
	    elem.className = 'login-bubble-fields-disabled-big';			
		}
    elem.hasEntered = false;
  } else {
		if(passwd_elem == elem) {
			elem.className = 'login-bubble-fields-enabled-small';
		} else {
			elem.className = 'login-bubble-fields-enabled';
		}
	
	
  }
}

function SaveSessionVariable(event){
	
	var elem = Event.element(event);
	var vname = elem.identify();
	var vvalue = elem.value;
	
	new Ajax.Request("putsessionvariable.php?vname=" + vname + "&vvalue=" + vvalue);

}



function IsNumeric(sText){
	
   var ValidChars = "0123456789.";
   var IsNumber=true;
   var Char;

 
   for (i = 0; i < sText.length && IsNumber == true; i++) 
      { 
      Char = sText.charAt(i); 
      if (ValidChars.indexOf(Char) == -1) 
         {
         IsNumber = false;
         }
      }
   return IsNumber;	
   
}


function IsValidTime(timeStr,lang) {
		
	if(lang=='en'){
		notvalidformat="Time is not in a valid format.";
		hourbetween1and12="Hour must be between 1 and 12. (or 0 and 23 for military time)";
		inidicateformat="Please indicate which time format you are using.  OK = Standard Time, CANCEL = Military Time";
		mustspecifyampm="You must specify AM or PM.";
		cannotspecifyampm="You can't specify AM or PM for military time.";
		minutebetween0and59="Minute must be between 0 and 59.";
		secondbetween0and59="Second must be between 0 and 59.";
	}
	else if(lang=='es'){
		notvalidformat="La hora no está en un formato válido";
		hourbetween1and12="La hora tiene que estar entre 1 y 12. (o 0 y 23 para 24H)";
		inidicateformat="Indique el formato de hora que está utilizando.  OK = 12H, CANCEL = 24H";
		mustspecifyampm="Tiene que especificar AM o PM.";
		cannotspecifyampm="No puede especificar AM o PM para formato 24H.";
		minutebetween0and59="El minuto tiene que ser entre 0 y 59.";
		secondbetween0and59="El segundo tiene que ser entre 0 y 59.";
	}
	else if(lang=='pt'){
		notvalidformat="A hora não está num formato válido";
		hourbetween1and12="A hora deve ser entre 1 y 12 para formato 12H. (ou 0 e 23 para formato 24H)";
		inidicateformat="Selecione o formato de hora.  OK = 12H, CANCEL = 24H";
		mustspecifyampm="Deve selecionar entre AM e PM.";
		cannotspecifyampm="Não pode selecionar entre AM ou PM para formato 24H.";
		minutebetween0and59="O minuto deve ser entre 0 e 59.";
		secondbetween0and59="O segundo deve ser entre 0 e 59.";
	}
	
	
	// Checks if time is in HH:MM:SS AM/PM format.
	// The seconds and AM/PM are optional.

	var timePat = /^(\d{1,2}):(\d{2})(:(\d{2}))?(\s?(AM|am|PM|pm))?$/;

	var matchArray = timeStr.match(timePat);
	if (matchArray == null) {
		alert(notvalidformat);
		return false;
	}
	hour = matchArray[1];
	minute = matchArray[2];
	second = matchArray[4];
	ampm = matchArray[6];

	if (second=="") { second = null; }
	if (ampm=="") { ampm = null }

	if (hour < 0  || hour > 23) {
		alert(hourbetween1and12);
		return false;
	}
	if (hour <= 12 && ampm == null) {
	if (confirm(indicateformat)) {
		alert(mustspecifyampm);
		return false;
	   }
	}
	if  (hour > 12 && ampm != null) {
		alert(cannotspecifyampm);
		return false;
	}
	if (minute<0 || minute > 59) {
		alert (minutebetween0and59);
		return false;
	}
	if (second != null && (second < 0 || second > 59)) {
		alert ();
		return false;
	}
	return true;
}


function MySQLTime(timeStr,lang){
	
	if(lang=='en'){
		inidicateformat="Please indicate which time format you are using.  OK = Standard Time, CANCEL = Military Time";
	}
	else if(lang=='es'){
		inidicateformat="Please indicate which time format you are using.  OK = Standard Time, CANCEL = Military Time";
	}
	else if(lang=='pt'){
		inidicateformat="Please indicate which time format you are using.  OK = Standard Time, CANCEL = Military Time";
	}
	
	
	// Checks if time is in HH:MM:SS AM/PM format.
	// The seconds and AM/PM are optional.

	var timePat = /^(\d{1,2}):(\d{2})(:(\d{2}))?(\s?(AM|am|PM|pm))?$/;

	var matchArray = timeStr.match(timePat);
	if (matchArray == null) {
		return false;
	}
	hour = matchArray[1];
	minute = matchArray[2];
	second = matchArray[4];
	ampm = matchArray[6];

	if (second=="") { second = null; }
	if (ampm=="") { ampm = null }

	if (hour < 0  || hour > 23) {
		return false;
	}
	if (hour <= 12 && ampm == null) {
	if (confirm(indicateformat)) {
		return false;
	   }
	}
	if  (hour > 12 && ampm != null) {
		return false;
	}
	if (minute<0 || minute > 59) {
		return false;
	}
	if (second != null && (second < 0 || second > 59)) {
		return false;
	}
	
	if(ampm.toLowerCase() == 'am' && parseInt(hour) == 12 ){
		hour=0;
	}
	else{	
		if(ampm.toLowerCase() == 'pm' && parseInt(hour)!=12){
			hour= parseInt(hour) + 12;
		}
	}
	
	var returntime = hour + ":" + minute;	
	return returntime;
	
}


function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}







