function onPageLoad() {
  $('password1').observe('focus', onFieldEnter);
  $('password1').observe('blur', onFieldLeave);
  $('password2').observe('focus', onFieldEnter);
  $('password2').observe('blur', onFieldLeave);
}


function requestLostPassword(form,lang){
	try{
		
		
		var password1 = form.password1.value;
		var password2 = form.password2.value;
		
		if(password1 == password2){
			document.forms.resetpassword.submit();
		}
		else{
			$('password1').className = "form-items-error";
			$('password2').className = "form-items-error";
		}
		
		
	}
	catch(e){
		alert(e.toString());
	}
}