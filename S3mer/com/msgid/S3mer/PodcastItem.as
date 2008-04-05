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
			this._feedRSS = new XML((e.target as URLLoader).data);
			
//			Logger.addEvent("PODCAST LOAD COMPLETE");
			
			this._feedRSS.setNamespace(new Namespace());
			
//			Logger.addEvent("first podcast url: " + _feedRSS.channel.item[0].enclosure.@url.toString() );
			
			this._currentItemURL = _feedRSS.channel.item[0].enclosure.@url.toString();
			
			this.dispatchEvent(new Event(Event.COMPLETE));
			
		}
		
		public function queueDownload():void {
			if(FileIO.fileExists(FileIO.Url2Filename(_currentItemURL))) {
				Logger.addEvent("Already Downloaded File:"+ FileIO.Url2Filename(_currentItemURL));
//				downloadComplete(null);
			} else {
				Logger.addEvent("Downloading File:"+ FileIO.Url2Filename(_currentItemURL));
				PodcastManager._queue.addItem(_currentItemURL,"",null,false,false);
//				PodcastManager._queue.addEventListener(DownloaderEvent.COMPLETE,downloadComplete);
			}
			
			_item.file = FileIO.Url2Filename(_currentItemURL);
		}
		
		private function OnIOError(e:IOErrorEvent):void {
			_errored = true;
			Logger.addEvent("LOADRSS FAILED: url may not be valid: " + this._item.url);
			dispatchEvent(new Event("ERROR"));
		}
		
//		public function downloadComplete(e:DownloaderEvent):void {
//			PodcastManager._queue.removeEventListener(DownloaderEvent.COMPLETE,downloadComplete);
//			
//			_item.file = FileIO.Url2Filename(_currentItemURL);
//			
//			dispatchEvent(new Event(Event.COMPLETE));
//		}
		public function loaded():Boolean {
			return (_feedRSS!=null);
		}
	
		public function available():Boolean {
			return FileIO.fileExists(FileIO.Url2Filename(_currentItemURL));
		}
	}
}