	import com.s3mer.events.ConfigurationEvent;
	import com.s3mer.util.ApplicationSettings;
	import com.s3mer.util.FileIO;
	import com.s3mer.util.NetUtils;
	import com.s3mer.util.PlayerState;
	
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.net.URLLoader;
	
	private var _screenNumber:int;
	public var applicationObject:Object;
	
	
	public function get screenNumber():int {
		return _screenNumber;
	}
	
	public function set screenNumber(number:int):void {
		_screenNumber=number;
	}
	
	public function showWindow():void {
		this.visible = true;
	
		var _screen:Screen = Screen.screens[this._screenNumber];
		this.move(_screen.bounds.x,_screen.bounds.y);
		this.height = _screen.bounds.height;
		this.width = _screen.bounds.width;
		
		this.title = "Screen #"+this._screenNumber;
		this.stage.displayState = StageDisplayState.FULL_SCREEN_INTERACTIVE;
		if( _screenNumber == 0 ){
			if( PlayerState.credentialsValid == false ) {
			var myLogin:LoginWindow;
			myLogin = new LoginWindow();
			this.addChild(myLogin);
			myLogin.setStyle("horizontalCenter","0");
			myLogin.setStyle("verticalCenter","0");
			myLogin.show();	
			}
		}
		
		this.addEventListener(ConfigurationEvent.REGISTRATION_COMPLETE, registrationComplete);
	}
	
	public function registrationComplete(e:ConfigurationEvent):void {
		(this.applicationObject as S3mer).registation_complete();
	}
	
	public function start():void {
		var configId:String;
		
		configId = ApplicationSettings.getValue("screen" + this.screenNumber + ".channel.id","");
		
		
		NetUtils.remoteCommand(NetUtils.CMD_GETCONFIG, getConfig_success, getConfig_error, 
				configId);	
	}
	
	public function getConfig_success(e:Event):void {
		var data:String;
		
		data = (e.target as URLLoader).data;
		
		PlayerState.configurations[this.screenNumber] = FileIO.decryptConfig(data);
	}
	
	public function getConfig_error(e:IOErrorEvent):void {
		
	}