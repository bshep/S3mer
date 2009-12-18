package com.s3mer.util
{
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	
	public class NetUtils
	{
		private var _callback:Function;
		
		public function CheckOnlineStatus(callback:Function):void {
			this._callback = callback;
			
			var request:URLRequest = new URLRequest("http://www.s3mer.com/");
			var requestLoader:URLLoader = new URLLoader();
			
            requestLoader.addEventListener(Event.COMPLETE, requestCompleteHandler);
            requestLoader.addEventListener(IOErrorEvent.IO_ERROR, requestErrorHandler);
            requestLoader.load(request);

		}
		
		private function requestCompleteHandler(event:Event):void {
			this._callback(true);
		}
		
		private function requestErrorHandler(event:IOErrorEvent):void {
			this._callback(false);			
		}
		
		public static var CMD_LOGIN:String = "login";
		public static var CMD_LOGOUT:String = "logout";
		public static var CMD_PLAYERLIST:String = "playerlist";
		public static var CMD_USERINFO:String = "userinfo";
		public static var CMD_STATUS:String = "checkstatus";
		
		public static function remoteCommand(command:String, successFn:Function, failureFn:Function):void {
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
			}

			_loaderReq = new URLRequest(_url);
			_loaderReq.data = _variables;
			
			_loader.load(_loaderReq);
		}
		
		
		
	}
}