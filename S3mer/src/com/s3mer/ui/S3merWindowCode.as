import com.s3mer.events.ConfigurationEvent;
import com.s3mer.util.PlayerState;

import flash.events.Event;

private var _ScreenNumber:int;
public var applicationObject:Object;



public function getScreen():int{
	return _ScreenNumber;
}
public function setScreen(scrnmbr:int):void{
	_ScreenNumber=scrnmbr;
}
public function showWindow():void{
	var _screen:Screen=Screen.screens[this._ScreenNumber];
	this.visible=true;
	this.move(_screen.bounds.x,_screen.bounds.y);
	this.height=_screen.bounds.height;
	this.width=_screen.bounds.width;
	this.title = "Screen #"+this._ScreenNumber;
	this.stage.displayState=StageDisplayState.FULL_SCREEN_INTERACTIVE;
	if( _ScreenNumber == 0 && PlayerState.credentialsValid == false ){
		var myLogin:LoginWindow;
		myLogin = new LoginWindow();
		this.addChild(myLogin);
		myLogin.setStyle("horizontalCenter","0");
		myLogin.setStyle("verticalCenter","0");
		myLogin.show();	
	}
	
	this.addEventListener(ConfigurationEvent.REGISTRATION_COMPLETE, registrationComplete);
}

public function registrationComplete(e:ConfigurationEvent):void {
	(this.applicationObject as S3mer).registation_complete(e);
}
