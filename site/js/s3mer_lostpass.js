function onPageLoad() {
  $('lost-pass-email').observe('focus', onFieldEnter);
  $('lost-pass-email').observe('blur', onFieldLeave);
}


function requestLostPassword(form,lang){
	try{
		
		
		var username = form.email.value;
		
		var invalidemail;
		
		if(lang=='en'){
			invalidemail='Invalid username';
		}
		else if(lang=='es'){
			invalidemail='usuario inválido';
		}
		else if(lang=='pt'){
			invalidemail='usuario inválido';
		}
		
		var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if(filter.test(username)){
			document.forms.sendpassword.submit();
		}
		else{
			alert(invalidemail);
		}
		
		
	}
	catch(e){
		alert(e.toString());
	}
}