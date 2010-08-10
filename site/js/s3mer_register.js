


function onPageLoad() {

  $('email1').observe('focus', onFieldEnter);
  $('email2').observe('focus', onFieldEnter);
  $('pass1').observe('focus', onFieldEnter);
  $('pass2').observe('focus', onFieldEnter);
  $('first').observe('focus', onFieldEnter);
  $('last').observe('focus', onFieldEnter);
  $('address1').observe('focus', onFieldEnter);
  $('address2').observe('focus', onFieldEnter);
  $('city').observe('focus', onFieldEnter);
  $('state').observe('focus', onFieldEnter);
  $('zip').observe('focus', onFieldEnter);  
  $('explain').observe('focus', onFieldEnter);  
    
  $('email1').observe('blur', onFieldLeave);
  $('email2').observe('blur', onFieldLeave);
  $('pass1').observe('blur', onFieldLeave);
  $('pass2').observe('blur', onFieldLeave);
  $('first').observe('blur', onFieldLeave);
  $('last').observe('blur', onFieldLeave);
  $('address1').observe('blur', onFieldLeave);
  $('address2').observe('blur', onFieldLeave);
  $('city').observe('blur', onFieldLeave);
  $('state').observe('blur', onFieldLeave);
  $('zip').observe('blur', onFieldLeave);  
  $('explain').observe('blur', onFieldLeave);  
}

function processRegister(form,lang){
	try{
		
		if(lang=='en'){
			invalidemail='Invalid username';
			mailmustmatch='Mail address and confirmation must match';
			passwordempty='Password cannot be empty';
			passwordmustmatch='Password and confirmation must match';
			firstname='First Name';
			firstnameempty='You have not entered your name';
			lastname='Last Name';
			lastnameempty='You have not entered your last name';
			selectcountry='Please select your country';
			selectvenue='Please select your industry';
			mustagreeterms='You must agree with terms and conditions of service';
			accountexists='An account for this email already exists';
		}
		else if(lang=='es'){
			invalidemail='usuario inválido';
			mailmustmatch='El email y su confirmación tiene que concordar';
			passwordempty='La contraseña no puede estar vacía';
			passwordmustmatch='La contraseña y su confirmación tiene que concordar';
			firstname='Nombre';
			firstnameempty='Tiene que entrar su nombre';
			lastname='Apellido';
			lastnameempty='Tiene que entrar su apellido';
			selectcountry='Seleccione su país';
			selectvenue='Seleccione su industria';
			mustagreeterms='Usted tiene que estar de acuerdo con los términos y condiciones del servicio';
			accountexists='Existe una cuenta para este email en nuestro sistema';
		}
		else if(lang=='pt'){
			invalidemail='usuario inválido';
			mailmustmatch='O endereço electrónico e seu confirmação deve concordar';
			passwordempty='A senha não pode estar vazia';
			passwordmustmatch='A senha e seu confirmação devem concordar';
			firstname='Nome';
			firstnameempty='Você deve entrar como o seu nome';
			lastname='Sobrenome';
			lastnameempty='Você deve entrar com o seu sobrenome';
			selectcountry='Selecione seu pais';
			selectvenue='Selecione seu indústria';
			mustagreeterms='Você tem que concordar com os termos e condições do serviço';
			accountexists='Existe uma conta para este endereço no nosso sistema';
		}
	
		var errorcount=0;
		var errormessage=new Array();
		var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		
		
		if(!filter.test(form.email1.value)){	
			errorcount++;
			errormessage.push(invalidemail);
			$('email1').className='form-items-error';
		}
		else{
			$('email1').className='form-items';
		}
		
		
		if(form.email1.value!=form.email2.value){
			errorcount++;
			errormessage.push(mailmustmatch);
			$('email2').className='form-items-error';
		}
		else{
			$('email2').className='form-items';
		}
		
			
		if(form.password1.value=='' || form.password1.value=='Password'){
			errorcount++;
			errormessage.push(passwordempty);
			$('pass1').className='form-items-error';
		}
		else{
			$('pass1').className='form-items';
		}
						
				
		if(form.password1.value!=form.password2.value){
			errorcount++;
			errormessage.push(passwordmustmatch);
			$('pass2').className='form-items-error';
		}
		else{
			$('pass2').className='form-items-disabled';
		}
					
		
		if(form.firstname.value=='' || form.firstname.value==firstname){
			errorcount++;
			errormessage.push(firstnameempty);
			
			$('first').className='form-items-error';
			
		}
		else{
			$('first').className='form-items';
		}
				
			
		if(form.lastname.value=='' || form.lastname.value==lastname){
			errorcount++;
			errormessage.push(lastnameempty);
			$('last').className='form-items-error';
		}
		else{
			$('last').className='form-items';
		}
		
		
		if(form.country.value==0){
			errorcount++;
			errormessage.push(selectcountry);
			$('country').className='form-items-error';
		}
		else{
			$('country').className='form-items';
		}
		
	
		if(form.venue.value==0 && ($F('explain').length==0 || $('explain').className=='form-items-disabled')){
			errorcount++;
			errormessage.push(selectvenue);
			$('venue').className='form-items-error';
		}
		else{
			$('venue').className='form-items';
		}
		
		
		if(!form.agreeterms.checked){
			errorcount++;
			errormessage.push(mustagreeterms);
		}
		
		var ac=0;
		try{
		
			new Ajax.Request('process_command.php?infocmd=acc-count&uname=' + form.email1.value,{
							method:'get',
							asynchronous: false,
							onSuccess: function(transport){
								ac=parseInt(transport.responseText);	
							}
						})
	
		}
		catch(e){
			alert(e.toString());
		}


		if (ac>0){
			errorcount++;
			$('email1').className='form-items-error';
			$('email2').className='form-items-error';
			errormessage.push(accountexists);
		}
		
				
		if(errorcount==0){
			document.forms.registration.submit();
		}
		else{
			
			var inHTML='';
			var x;
			
			for (x=0;x<errormessage.length;x++){
				inHTML+='<img src="images/icons/error.png" class="error-icon"/>' + errormessage[x] + '<br>';
			
			}
			
			$('register-error-box').innerHTML=inHTML;
			
			new Effect.ScrollTo('pageBodyid',{offset: -1000})
			
		
			if(!$('register-error-box').visible()){
				try{
					Effect.BlindDown('register-error-box', {duration:0.2});
				}
				catch(e){
					alert(e.toString());
				}
			}
		}
	}
	catch(e){
		alert('Error validating');
	}
}


function otherIndustryShow(myform){
	if(myform.venue.value==0){
		$('venue_explain').show();
	}
	else{	
		$('venue_explain').hide();	
	}
}
