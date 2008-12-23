package com.msgid.S3mer
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;

	public class PodcastItem extends EventDispatcher
	{
		public var _item:PlaylistObject;
		public var _feedRSS:XML;
		public var _errored:Boolean;
		
		public var _currentItemURL:String;
		
		public function PodcastItem(item:PlaylistObject)
		{
			this._item = item;
			
			this._item.file = "";
			super();
		}
		
		public function reloadRSS():void {
			var _loader:URLLoader = new URLLoader();
			var _loaderReq:URLRequest;
			
			_loader.addEventListener(IOErrorEvent.IO_ERROR,OnIOError);
			_loader.addEventListener(Event.COMPLETE,reloadRSS_complete);
			_loader.dataFormat = URLLoaderDataFormat.TEXT;
			try {
				_loaderReq = new URLRequest(this._item.url);
				_loader.load(_loaderReq);
			} catch(e:Error) {
				Logger.addEvent("HEARTBEAT FAILED");
			}
		}
		
		
		private function reloadRSS_complete(e:Event):void {
			extractFeedData((e.target as URLLoader).data);
						
			this.dispatchEvent(new Event(Event.COMPLETE));
			
		}
		
		public function queueDownload():void {
			if(FileIO.fileExists(FileIO.Url2Filename(_currentItemURL))) {
				Logger.addEvent("Already Downloaded File:"+ FileIO.Url2Filename(_currentItemURL));
			} else {
				Logger.addEvent("Downloading File:"+ FileIO.Url2Filename(_currentItemURL));
				PodcastManager._queue.addItem(_currentItemURL,"",null,false,false);
			}
			
			_item.file = FileIO.Url2Filename(_currentItemURL);
		}
		
		private function OnIOError(e:IOErrorEvent):void {
			_errored = true;
			Logger.addEvent("LOADRSS FAILED: url may not be valid: " + this._item.url);
			dispatchEvent(new Event("ERROR"));
		}
		
		public function loaded():Boolean {
			return (_feedRSS!=null);
		}
	
		public function available():Boolean {
			if(_errored == true) {
				return true;
			}
			
			return FileIO.fileExists(FileIO.Url2Filename(_currentItemURL));
		}
		
		public function errored():Boolean {
			return this._errored;
		}
		
		public function checkRSS():void {
			var _loader:URLLoader = new URLLoader();
			var _loaderReq:URLRequest;
			
			_loader.addEventListener(IOErrorEvent.IO_ERROR,OnIOError);
			_loader.addEventListener(Event.COMPLETE,checkRSS_complete);
			_loader.dataFormat = URLLoaderDataFormat.TEXT;
			try {
				_loaderReq = new URLRequest(this._item.url);
				_loader.load(_loaderReq);
			} catch(e:Error) {
				Logger.addEvent("HEARTBEAT FAILED");
			}			
		}
		
		public function checkRSS_complete(e:Event):void {
			extractFeedData((e.target as URLLoader).data);
			
			if(FileIO.fileExists(FileIO.Url2Filename(_currentItemURL))) {
				Logger.addEvent("Already Downloaded File:"+ FileIO.Url2Filename(_currentItemURL));
				_item.file = FileIO.Url2Filename(_currentItemURL);
			} else {
				Logger.addEvent("Downloading File:"+ FileIO.Url2Filename(_currentItemURL));
				
				PodcastManager._queue.addEventListener(DownloaderEvent.PARTIAL_COMPLETE,checkRSS_loadmedia_complete);
				PodcastManager._queue.addItem(_currentItemURL);
			}
						
		}
		
		public function checkRSS_loadmedia_complete(e:DownloaderEvent):void {
			if(FileIO.fileExists(FileIO.Url2Filename(_currentItemURL))) {
				Logger.addEvent("Media for RSS updated: " + this._item.url);
				_item.file = FileIO.Url2Filename(_currentItemURL);
			}			
		}
		
		private function extractFeedData(data:String):void {
			this._feedRSS = new XML(data);
			this._currentItemURL = _feedRSS.channel.item[0].enclosure.@url.toString();
			
		}
	}
}