package com.s3mer.util.net
{
	import com.s3mer.util.ApplicationSettings;
	import com.s3mer.util.PlayerState;
	
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.TimerEvent;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	import flash.utils.Timer;
	
	public class NetworkFunctions
	{
		private var _callback:Function;
		private var _timer:Timer
		
		public function CheckOnlineStatus(callback:Function, delay:int = 0):void {
			this._callback = callback;
			
			if( delay == 0 ) {
				doRequest();
			} else {
				_timer = new Timer(delay*1000, 1);
				_timer.addEventListener(TimerEvent.TIMER_COMPLETE, doRequest);
				_timer.start();
			}
		}
		
		private function doRequest(e:Event = null):void {
			var request:URLRequest = new URLRequest("http://www.s3mer.com/");
			var requestLoader:URLLoader = new URLLoader();
			
            requestLoader.addEventListener(Event.COMPLETE, requestCompleteHandler);
            requestLoader.addEventListener(IOErrorEvent.IO_ERROR, requestErrorHandler);
            requestLoader.load(request);
		}
		
		private function requestCompleteHandler(event:Event):void {
			this._callback(true);
			this._callback = null;
			this._timer = null;
		}
		
		private function requestErrorHandler(event:IOErrorEvent):void {
			this._callback(false);			
			this._callback = null;
			this._timer = null;
		}
		
		public static const CMD_LOGIN:String = "login";
		public static const CMD_LOGOUT:String = "logout";
		public static const CMD_PLAYERLIST:String = "playerlist";
		public static const CMD_USERINFO:String = "userinfo";
		public static const CMD_STATUS:String = "checkstatus";
		public static const CMD_GETCONFIG:String = "getconfig";
		
		public static function remoteCommand(command:String, successFn:Function, failureFn:Function, args:String = ""):void {
			var _loader:URLLoader = new URLLoader;
			var _loaderReq:URLRequest;
			var _url:String;
			var _variables:URLVariables = new URLVariables();
			
			_loader.dataFormat = URLLoaderDataFormat.TEXT;
			_loader.addEventListener(Event.COMPLETE, successFn);
			_loader.addEventListener(IOErrorEvent.IO_ERROR, failureFn);
			
			switch(command) {
				case CMD_LOGIN:
					_url = ApplicationSettings.URL_LOGIN;
					_variables.username = PlayerState.username;
					_variables.password = PlayerState.password;	
					break;
				case CMD_LOGOUT:
					_url = ApplicationSettings.URL_LOGOUT;
					break;
				case CMD_PLAYERLIST:
					_url = ApplicationSettings.URL_PLAYER_LIST;
					_variables.data= "1";
					break;
				case CMD_USERINFO:
					_url = ApplicationSettings.URL_USER_INFO;
					_variables.data= "2";
					break;
				case CMD_STATUS:
					_url = ApplicationSettings.URL_STATUS;
					break;
				case CMD_GETCONFIG:
					_url = ApplicationSettings.URL_CONFIG;
					_variables.playerid = args;
					break;
			}

			_loaderReq = new URLRequest(_url);
			_loaderReq.data = _variables;
			
			_loader.load(_loaderReq);
		}
		
		
		
	}
}