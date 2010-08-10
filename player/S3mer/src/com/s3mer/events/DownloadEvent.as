package com.s3mer.events
{
	import com.s3mer.util.DownloadQueueItem;
	
	import flash.events.Event;

	public class DownloadEvent extends Event
	{
		public static const DOWNLOAD_COMPLETE:String = "DOWNLOAD_COMPLETE";
		public static const DOWNLOAD_PROGRESS:String = "DOWNLOAD_PROGRESS";
		public static const DOWNLOAD_ERROR:String = "DOWNLOAD_ERROR";
		public static const QUEUE_COMPLETE:String = "QUEUE_COMPLETE";

		public var originalEvent:Event;
		public var queueItem:DownloadQueueItem;

		public function DownloadEvent(type:String, _queueItem:DownloadQueueItem, _originalEvent:Event, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
			
			this.queueItem = _queueItem;
			this.originalEvent = _originalEvent;
		}
		
		public override function clone():Event {
			return new DownloadEvent(this.type, this.queueItem, this.originalEvent, this.bubbles, this.cancelable);
		}
		
	}
}