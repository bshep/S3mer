import com.s3mer.events.ConfigurationEvent;
import com.s3mer.util.ApplicationSettings;
import com.s3mer.util.PlayerState;
import com.s3mer.util.managers.LoggerManager;
import com.s3mer.util.net.NetworkFunctions;

import flash.events.Event;
import flash.events.IOErrorEvent;
import flash.net.URLLoader;

import mx.collections.ListCollectionView;
import mx.events.EffectEvent;
import mx.events.ListEvent;

[Embed(source="assets/swf/spinner.swf")]
[Bindable]
public static var SpinnerSwf:Class;


public function show():void {
	LoggerManager.addEvent("LoginhWindow.as: show()")
	this.cmbLanguage.selectedIndex = localeListIndex(resourceManager.localeChain[0]);

	updateLocaleStrings();
	
//	if (0) {
//		var tmpTextField:TextInput = new TextInput();
//		var tmpLabel:Label = new Label();
//		
//		tmpTextField.addEventListener(FlexEvent.ENTER, function(e:Event):void {
//			ApplicationSettings.load();
//								
//			ApplicationSettings.setValue("user.username","");
//			ApplicationSettings.setValue("user.password","");
//			ApplicationSettings.setValue("screen0.channel.id",(e.target as TextInput).text);
//			ApplicationSettings.save();
//			
//			(e.target as TextInput).parent.parent.parent.visible = false;
//			
//		});
//		
//		
//		tmpLabel.text = "PlayerID:";
//		tmpTextField.width = 125;
//		ctlLoginWindowBarBottom.addChildAt(tmpTextField,0);
//		ctlLoginWindowBarBottom.addChildAt(tmpLabel,0);
//	}
}

private function localeListIndex(locale:String):int {
	for( var a:int = 0; a < localesList.length; a++ ) {
		if( localesList[a].data == locale ) {
			return a;
		}
	}
	
	return 0;
}

private function updateLocaleStrings():void {
	pnlMode.title = resourceManager.getString('application', 'GREETING');
	btnDemo.label = resourceManager.getString('application', 'START_DEMO');
	btnLogin1.label = resourceManager.getString('application', 'LOGIN');
	lblLanguage.text = resourceManager.getString('application', 'LANGUAGE');
	
}

private function OnLoginClick(e:Event):void {
	doLogin();
}

private function OnTuneInClick(e:MouseEvent):void {
	if(this.saveInfo()) {
		this.visible = false;
		
		this.dispatchEvent(new ConfigurationEvent(ConfigurationEvent.REGISTRATION_COMPLETE, true));					
	} else {
		this.cmbPlayers0.setStyle("borderColor","#FF0000");
		this.cmbPlayers0.setStyle("borderThickness","5");
	}
}

private function doLogin():void {
	this.txtUsername.enabled = false;
	this.txtPassword.enabled = false;
	this.spinner.source = SpinnerSwf;
	this.spinner.visible = true;
	
	PlayerState.username = this.txtUsername.text;
	PlayerState.password = this.txtPassword.text;
	
	NetworkFunctions.remoteCommand(NetworkFunctions.CMD_LOGIN, doLogin_stage2, doLogin_error);
}

private function doLogin_error(e:IOErrorEvent):void {
	LoggerManager.addEvent("LoginWindow.as doLogin_error: Error during login");
}

private function doLogin_stage2(e:Event):void {
	var result:String;
	
	result = (e.target as URLLoader).data;
	
	LoggerManager.addEvent("LoginWindow.as doLogin_stage2: LOGIN RESULT: " + result);
	
	if( result == "OK" ) {
		highlightTextbox(this.txtUsername,"",false);
		highlightTextbox(this.txtPassword,"",false);

		NetworkFunctions.remoteCommand(NetworkFunctions.CMD_PLAYERLIST,playerList_loaded, playerList_error);
	} else {
		this.txtUsername.enabled = true;
		this.txtPassword.enabled = true;
		highlightTextbox(this.txtUsername);
		highlightTextbox(this.txtPassword);
		this.spinner.visible = false;
	}
}

private function playerList_error(e:IOErrorEvent):void {
	LoggerManager.addEvent("LoginWindow.as playerList_error: Error during loading player list");
}

private function playerList_loaded(e:Event):void {
	var result:XML;
					
	this.spinner.visible = false;
	
	result = new XML(URLLoader(e.target).data);
	
	if(!result) {
		LoggerManager.addEvent("LoginWindow.as playerList_loaded: Error loading player list, Invalid XML returned");
	}
	
	if( PlayerState.playerType != PlayerState.TYPE_DEMO ) {
		if( result.user.@isPro == "true" ) {
			PlayerState.playerType = PlayerState.TYPE_PRO;
		} else {
			PlayerState.playerType = PlayerState.TYPE_FREE;						
		}
		
		if( PlayerState.multiScreen == true ) {
			this.currentState = "register_multiPlayer";
		} else {
			this.currentState = "register_choosePlayer";
		}
		
	}
	

	populatePlayerList(result, this.cmbPlayers0);
	populatePlayerList(result, this.cmbPlayers1);

}			

private function highlightTextbox(obj:TextInput,color:String = "#FF0000", enable:Boolean = true):void {
	if ( enable ) {
		obj.setStyle("borderColor",color);
		obj.setStyle("borderThickness","3");
	} else {
		obj.setStyle("borderColor","#000000");
		obj.setStyle("borderThickness","1");
	}
 }

private function populatePlayerList(playersXML:XML, cmbTarget:ComboBox):void {
	var dataProvider:ListCollectionView;
	
	if(cmbTarget && playersXML) {
		dataProvider = (cmbTarget.dataProvider as ListCollectionView)
		dataProvider.removeAll();
		
		if(PlayerState.multiScreen && PlayerState.playerType == PlayerState.TYPE_PRO) {
			dataProvider.addItem({label:"<none>", data:new XML("<id>-1</id>")});
		}
		
		for each( var _player:XML in playersXML.player ) {
			dataProvider.addItem({label:_player.@name, data:_player.id});
			LoggerManager.addEvent("LoginWindow.as populatedPlayerList: Player ID: " + _player.@name );
		}
		
		if (dataProvider.length > 0) {
			cmbTarget.selectedIndex = 0;
		}				
	}
}
		
public function hide_complete(e:EffectEvent):void {
	this.dispatchEvent(new Event("WINDOW_CLOSED"));
}

private function resetFields():void {
	if( this.currentState == "register") {
		this.txtUsername.enabled = true;
		this.txtPassword.enabled = true;
		this.txtUsername.text = "e-mail"; 
		this.txtPassword.text = "password";
		this.txtPassword.displayAsPassword  = false;
	}
}

public function saveInfo():Boolean {
	if(this.cmbPlayers0.selectedItem) {
	
		ApplicationSettings.load();
							
		ApplicationSettings.setValue("user.username",PlayerState.username);
		ApplicationSettings.setValue("user.password",PlayerState.password);
		ApplicationSettings.setValue("screen0.channel.id",this.cmbPlayers0.selectedItem.data[0]);
		
		if(this.currentState == "register_multiPlayer") {
			ApplicationSettings.setValue("screen1.channel.id",this.cmbPlayers1.selectedItem.data[0]);
		} else {
			ApplicationSettings.setValue("screen1.channel.id","-1");					
		}
		
		ApplicationSettings.save();
		
		return true;
	} else {
		return false;
	}
}

private function onDemoMode(e:Event):void {
	this.visible = false;
	
	PlayerState.username = "demo@s3mer.com";
	PlayerState.password = "thisisthedemoacctpassword";
	
	// Login & Choose player & start playing....
	NetworkFunctions.remoteCommand(NetworkFunctions.CMD_LOGIN, demoMode_step2, demoMode_error);
}

private function demoMode_step2(e:Event):void {
	var result:String;
	
	result = (e.target as URLLoader).data;
	
	LoggerManager.addEvent("LoginWindow.as demoMode_Step2: LOGIN RESULT: " + result);
	
	if( result == "OK" ) {
		NetworkFunctions.remoteCommand(NetworkFunctions.CMD_PLAYERLIST,demoMode_step3, demoMode_error);
	} else {
		
	}
	
}

private function demoMode_step3(e:Event):void {
	var result:XML;
					
	result = new XML(URLLoader(e.target).data);
	
	if(result) {
		ApplicationSettings.load();
							
		ApplicationSettings.setValue("user.username",PlayerState.username);
		ApplicationSettings.setValue("user.password",PlayerState.password);
		ApplicationSettings.setValue("screen0.channel.id",result.player.id);
		
		ApplicationSettings.save()

		this.dispatchEvent(new ConfigurationEvent(ConfigurationEvent.REGISTRATION_COMPLETE, true));					
	}				
}

private function demoMode_error(e:IOErrorEvent):void {
	
}

private function onRegisteredMode(e:Event):void {
	this.currentState = "register";
}

private function cmbLanguageChange(e:ListEvent):void {
	resourceManager.localeChain = [ cmbLanguage.selectedItem.data ];
	ApplicationSettings.setValue("ui.lang",cmbLanguage.selectedItem.data);
	ApplicationSettings.save();
	
	updateLocaleStrings();
}

[Bindable]
private var localesList:Array = [ {label:"English", data:"en_US"},
								  {label:"Español", data:"es_ES"},
								  {label:"Italiano", data:"it_IT"},
								  {label:"Portuguese", data:"pt_BR"}];
