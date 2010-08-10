// activateOnUnload();
// 
// function activateOnUnload() {
//  onbeforeunload = function() {
//    return 'If you leave this page you will lose any unsaved changes.';
//  } 
// }

function deactivateOnUnload() {
	onbeforeunload = function() {
		return;
	}
}

function onPageLoad() {

	if($('venue').value==0){
		$('venue_explain').show();
	}
	else{
		$('venue_explain').hide();
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


function saveSettings(lang){
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
			
			


			if($('pass1').value!=$('pass2').value){
				errorcount++;
				errormessage.push(passwordmustmatch);
				$('pass2').className='form-items-error';
			}
			else{
				$('pass2').className='form-items-disabled';
			}
			
			if($('first').value=='' || $('first').value==firstname){
				errorcount++;
				errormessage.push(firstnameempty);

				$('first').className='form-items-error';

			}
			else{
				$('first').className='form-items';
			}


			if($('last').value=='' || $('last').value==lastname){
				errorcount++;
				errormessage.push(lastnameempty);
				$('last').className='form-items-error';
			}
			else{
				$('last').className='form-items';
			}


			if($('country').value==0){
				errorcount++;
				errormessage.push(selectcountry);
				$('country').className='form-items-error';
			}
			else{
				$('country').className='form-items';
			}


			if($('venue').value==0 && ($F('explain').length==0 || $('explain').className=='form-items-disabled')){
				errorcount++;
				errormessage.push(selectvenue);
				$('venue').className='form-items-error';
			}
			else{
				$('venue').className='form-items';
			}
			
			
			if(errorcount==0){
				document.forms.settings.submit();
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
		alert(e.toString());
	}
}

function cancel(){
	
}
function turnWizardOn(){
	try{
		
		new Ajax.Request('process_command.php?commandnr=turnwizardon');
		
	}
	catch(e){
		alert(e.toString());
	}
}