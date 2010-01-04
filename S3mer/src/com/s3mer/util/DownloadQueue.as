package com.s3mer.util
{
	import com.s3mer.events.DownloadEvent;
	
	import flash.events.EventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.events.ProgressEvent;
	
	import mx.collections.ArrayCollection;
	
	public class DownloadQueue extends EventDispatcher
	{
		private static var _downloadQueue:DownloadQueue;
		
		private static var _queue:ArrayCollection = new ArrayCollection();
		private static var _downloader:Downloader;
		
		public static function addItem(_url:String, _destination:String):void {
			var queueItem:DownloadQueueItem = isItemInList(_url);
			
			if( _downloadQueue == null ) {
				_downloadQueue = new DownloadQueue();
			}
			
			if( _downloader == null ) {
				_downloader = new Downloader();
				_downloader.addEventListener(DownloadEvent.DOWNLOAD_COMPLETE, downloadComplete);
				_downloader.addEventListener(DownloadEvent.DOWNLOAD_PROGRESS, downloadProgress);
			}
			
			if (queueItem == null) { // Item is not on the list, then add it to the queue
				_queue.addItem(new DownloadQueueItem(_url,_destination));
			} else { // Item is in the list, add a new destination to it.
				queueItem.addDestination(_destination);
			}
			
			download_next();
		}
		
		
		// Returns the item if the url matches, null if it doesnt match
		public static function isItemInList(_url:String):DownloadQueueItem {
			var item:DownloadQueueItem = null;
			
			for each( var queueItem:DownloadQueueItem in _queue ) {
				if( queueItem.url == _url ) {
					item = queueItem;
				}
			}
			
			return item;
		}
		
		public static function download_next():void {
			for each( var queueItem:DownloadQueueItem in _queue ) {
				if( queueItem.completed == false ) {
					_downloader.download(queueItem);
					break; // Only start one download at a time;
				}
			}
		}
		
		public static function downloadComplete(e:DownloadEvent):void {			
			_downloadQueue.dispatchEvent(e.clone());
			
			e.queueItem.completed = true;
			
			download_next();
		}
		
		public static function downloadProgress(e:DownloadEvent):void {
			LoggerManager.addEvent("Downloading at: " + ((e.originalEvent as ProgressEvent).bytesLoaded / 
					(e.originalEvent as ProgressEvent).bytesTotal ) *100 + " %");
			
			_downloadQueue.dispatchEvent(e.clone());
			
		}
		
		public static function downloadError(e:IOErrorEvent):void {
			_downloadQueue.dispatchEvent(e.clone());
			
		}
		
	}
}