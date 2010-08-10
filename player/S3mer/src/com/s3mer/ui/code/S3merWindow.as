	import com.s3mer.events.ConfigurationEvent;
	import com.s3mer.events.DownloadEvent;
	import com.s3mer.ui.S3merWindow;
	import com.s3mer.util.ApplicationSettings;
	import com.s3mer.util.DownloadQueue;
	import com.s3mer.util.FileIO;
	import com.s3mer.util.PlayerState;
	import com.s3mer.util.managers.LoggerManager;
	import com.s3mer.util.managers.ShowManager;
	import com.s3mer.util.net.NetworkFunctions;
	
	import flash.display.StageDisplayState;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.net.URLLoader;
	import flash.ui.Mouse;
	
	private var _screenNumber:int;
	
	private var _showManager:ShowManager = new ShowManager(this as S3merWindow);
	
	public var applicationObject:Object;
	
	public function get screenNumber():int {
		return _screenNumber;
	}
	
	public function set screenNumber(number:int):void {
		_screenNumber=number;
	}
	
	public function get configuration():XML {
		return PlayerState.configurations[this.screenNumber];
	}
	
	public function showWindow():void {
		LoggerManager.addEvent("S3merWindow.as: showWindow()");
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
		
		Mouse.show();
		Mouse.hide();

		this.addEventListener(ConfigurationEvent.REGISTRATION_COMPLETE, registrationComplete);
	}
	
	private function registrationComplete(e:ConfigurationEvent):void {
		(this.applicationObject as S3mer).registation_complete();
	}
	
	public function start():void {
		LoggerManager.addEvent("S3merWindow.as: start()");
		var configId:String;
		
		configId = ApplicationSettings.getValue("screen" + this.screenNumber + ".channel.id","");
		
		
		NetworkFunctions.remoteCommand(NetworkFunctions.CMD_GETCONFIG, getConfig_success, getConfig_error, 
				configId);	
	}
	
	private function getConfig_success(e:Event):void {
		var data:String;
		
		data = (e.target as URLLoader).data;
		
		PlayerState.configurations[this.screenNumber] = FileIO.decryptConfig(data);
		
		beginDownloads();
	}
	
	private function getConfig_error(e:IOErrorEvent):void {
		// Retry after 5 mins
		new NetworkFunctions().CheckOnlineStatus(start,5*60);
	}
	
	private function getMediaDirectory():String {
		return FileIO.mediaPath(this.screenNumber);
	}
	
	private function beginDownloads():void {
		var config:XML = PlayerState.configurations[this.screenNumber];
		
		DownloadQueue.eventDispatcher.addEventListener(DownloadEvent.QUEUE_COMPLETE, downloadsComplete);
		
		for each( var playlist:XML in config.playlist ) {
			for each( var playlistItem:XML in playlist.playlistitem ) {
				switch( playlistItem.@type.toString() ) {
					case 'image':
					case 'video':
						LoggerManager.addEvent("S3merWindow.as beginDownloads: Download file: " + playlistItem.toString());
						DownloadQueue.addItem(playlistItem.toString(), getMediaDirectory());
						break;
					default:
						LoggerManager.addEvent("S3merWindow.as beginDownloads: File of unknown type '" + playlistItem.@type + "'");
						break;
				}
			}
		}
		
		DownloadQueue.start();
	}
	
	private function downloadsComplete(e:DownloadEvent):void {
		LoggerManager.addEvent("S3merWindow.as downloadsComplete: Downloads COMPLETE");
		
		_showManager.start();
	}
	
	