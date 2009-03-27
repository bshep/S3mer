package com.msgid.S3mer
{
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	
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
	}
}