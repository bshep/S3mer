package com.s3mer.util
{
	import com.s3mer.events.DownloadEvent;
	
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.events.ProgressEvent;
	
	import mx.collections.ArrayCollection;
	
	public class DownloadQueue extends EventDispatcher
	{
		private static var _downloadQueue:DownloadQueue;
		
		private static var _queue:ArrayCollection = new ArrayCollection();
		private static var _downloader:Downloader;
		
		public function DownloadQueue(target:IEventDispatcher=null)
		{
			super(target);
		}

		public static function get eventDispatcher():EventDispatcher {
			if( _downloadQueue == null ) {
				_downloadQueue = new DownloadQueue();
			}
			
			
			return _downloadQueue;
		}
		
		public static function get complete():Boolean {
			var _complete:Boolean = true;
			
			for each( var queueItem:DownloadQueueItem in _queue ) {
				if( queueItem.completed == false ) {
					_complete = false;
				}
			}
			
			return _complete;
		}
		
		
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
			
//			download_next();
		}
		
		public static function start():void {
			download_next();
		}
		
		
		// Returns the item if the url matches, null if it doesnt match
		private static function isItemInList(_url:String):DownloadQueueItem {
			var item:DownloadQueueItem = null;
			
			for each( var queueItem:DownloadQueueItem in _queue ) {
				if( queueItem.url == _url ) {
					item = queueItem;
				}
			}
			
			return item;
		}
		
		private static function download_next():void {
			for each( var queueItem:DownloadQueueItem in _queue ) {
				if( queueItem.completed == false ) {
					_downloader.download(queueItem);
					break; // Only start one download at a time;
				}
			}
			
			if( DownloadQueue.complete ) {
				_downloadQueue.dispatchEvent(new DownloadEvent(DownloadEvent.QUEUE_COMPLETE,null, null));
			}
			
		}
		
		private static function downloadComplete(e:DownloadEvent):void {			
			_downloadQueue.dispatchEvent(e.clone());
			
//			e.queueItem.completed = true;
			
			download_next();
		}
		
		private static function downloadProgress(e:DownloadEvent):void {
			LoggerManager.addEvent("DownloadQueue.as downloadProgress: Downloading at: " + ((e.originalEvent as ProgressEvent).bytesLoaded / 
					(e.originalEvent as ProgressEvent).bytesTotal ) *100 + " %");
			
			_downloadQueue.dispatchEvent(e.clone());
			
		}
		
		private static function downloadError(e:IOErrorEvent):void {
			_downloadQueue.dispatchEvent(e.clone());
			
		}
		
	}
}