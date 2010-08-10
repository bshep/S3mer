Event.observe(window, 'load', function() {
	Event.observe('login-form', 'submit', processLogin);
});

function onPageLoad() {

	// Detect IE and give warning
	if (!!(window.attachEvent && !window.opera)) {
		Effect.BlindDown('ie-issues-wrapper', {duration:0.2});
	}


}


function processLogin(event) {
  
  var elem = Event.element(event);
  var username = elem.username.value;
  var password = elem.password.value;
  var lang = $('lang').innerHTML;
  
  var invalidemail;
  var wronginformation;
  
  switch(lang){
    
    case 'en':
      invalidemail = 'Please enter a valid email address';
      wronginformation = 'Please check your information';
      break;
    case 'es':
      invalidemail = 'Entre un email v&aacute;lido';
      wronginformation = 'Virifique su informaci&oacute;n';
      break;
    case 'pt':
      invalidemail = 'Digite um endere&ccedil;o de email v&aacute;lido';
      wronginformation = 'Confira suas informa&ccedil;&otilde;es';
      break;
  }
  
  var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  
  // Check email is valid
  if(filter.test(username)) {
    //Check password is not blank
    if(password==''){
      Event.stop(event);
      $('login-error-box').innerHTML='<img src="images/icons/error.png" class="error-icon"/>' + wronginformation;
      Effect.BlindDown('login-error-box', {duration:0.2});
      elem.password.className='form-items-error';
    // Check if user can login
    } else {      
      // Check username + password
      Event.stop(event);
      new Ajax.Request('process_command.php?infocmd=verpass&user=' + username + '&pwd=' + password,{
        method:'get',
        onSuccess:function(transport){
          var result = parseInt(transport.responseText);
          if(result==1){
            // correct password + username combination
            elem.submit();
          } else {
            // wrong password + username combination
            Event.stop(event);
            $('login-error-box').innerHTML='<img src="images/icons/error.png" class="error-icon"/>' + wronginformation;
            Effect.BlindDown('login-error-box', {duration:0.2});
            elem.password.className='form-items-error';
            elem.username.className='form-items-error';
          }
          
        }
      });
      
    }
  //Email is not valid
  }else{
    Event.stop(event);
    $('login-error-box').innerHTML='<img src="images/icons/error.png" class="error-icon"/>' + invalidemail;
    Effect.BlindDown('login-error-box', {duration:0.2});
    elem.username.className='form-items-error';
  }
}