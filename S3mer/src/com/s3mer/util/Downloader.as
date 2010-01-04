package com.s3mer.util
{
	import com.s3mer.events.DownloadEvent;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.events.ProgressEvent;
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	
	public class Downloader extends EventDispatcher
	{
		private var _queueItem:DownloadQueueItem;
		private var _downloadInProgress:Boolean;
		
		public function Downloader()
		{
		}
		
		public function download(_item:DownloadQueueItem):Boolean {
			if( this._downloadInProgress == false ) {
				this._downloadInProgress = true;
			} else {
				return false;
			}
			
//			if( _item.completed == true ) {
//				this._downloadInProgress = false;
//				return true;
//			}
			
			var loader:URLLoader;
			var request:URLRequest = new URLRequest();
			
			_queueItem = _item;
			
			request.url = _item.url;
			request.method = URLRequestMethod.GET;
			
			loader = new URLLoader();
			
			loader.dataFormat = URLLoaderDataFormat.BINARY;
			loader.addEventListener(ProgressEvent.PROGRESS, progressListener);
			loader.addEventListener(Event.COMPLETE,completeListener);
			loader.addEventListener(IOErrorEvent.IO_ERROR,errorListener);
			
			loader.load(request);
			
			return true;
		}
		
//		public function checkIfAlreadyDownloaded(_item:DownloadQueueItem):Boolean {
//			var ret:Boolean = false;
//			var file:File;
////			var existFile:File;
//			
//			for each(var dest:String in _item.destinations) {
//				file = new File(dest).resolvePath(FileIO.Url2Filename(_item.url));
//				if( file.exists ) {
//					ret = true;
////					existFile = file;
//				} else {
////					if (ret == true && existFile != null) {
////						existFile.copyTo(file);
////					}
//				}
//			}
//			
//			
//			return ret;
//		}
		
		public function progressListener(e:ProgressEvent):void {
			var event:DownloadEvent = new DownloadEvent(DownloadEvent.DOWNLOAD_PROGRESS, this._queueItem, e);
			
			this.dispatchEvent(event);			
		}
		
		public function completeListener(e:Event):void {
			this._downloadInProgress = false;

			var outputFile:File;
			var outputFileStream:FileStream = new FileStream();
			
			for each( var destination:String in _queueItem.destinations ) {
				outputFile = new File(destination).resolvePath(FileIO.Url2Filename(_queueItem.url));
				outputFileStream.open(outputFile,FileMode.WRITE);
				outputFileStream.writeBytes((e.target as URLLoader).data);	
			}
			
			var event:DownloadEvent = new DownloadEvent(DownloadEvent.DOWNLOAD_COMPLETE, this._queueItem, e);
			
			this.dispatchEvent(event);
		}
		
		public function errorListener(e:IOErrorEvent):void {
			this._downloadInProgress = false;
			var event:DownloadEvent = new DownloadEvent(DownloadEvent.DOWNLOAD_ERROR, this._queueItem, e);
			
			this.dispatchEvent(event);			
		}

	}
}