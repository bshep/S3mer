package com.s3mer.util
{
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.ProgressEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	
	public class Downloader
	{
		private var errorListener:Function;
		private var completeListener:Function;
		private var progressListener:Function;
		
		
		public function Downloader(_progressListener:Function, _completeListener:Function, _errorListener:Function)
		{
			this.progressListener = _progressListener;
			this.completeListener = _completeListener;
			this.errorListener = _errorListener;
		}
		
		public function download(_url:String):void {
			var loader:URLLoader;
			var request:URLRequest = new URLRequest();
			
			request.url = _url;
			request.method = URLRequestMethod.GET;
			
			loader = new URLLoader();
			loader.addEventListener(ProgressEvent.PROGRESS, progressListener);
			loader.addEventListener(Event.COMPLETE,completeListener);
			loader.addEventListener(IOErrorEvent.IO_ERROR,errorListener);
			
			loader.load(request);
		}

	}
}