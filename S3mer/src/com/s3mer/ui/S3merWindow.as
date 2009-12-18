import mx.containers.Canvas;

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
	if(_ScreenNumber==0){
		
//		var myLogin:LoginWindow;
//		myLogin = new LoginWindow();
//		this.addChild(myLogin);
//		myLogin.setStyle("horizontalCenter","0");
//		myLogin.setStyle("verticalCenter","0");
//		myLogin.show();	
	}
}
