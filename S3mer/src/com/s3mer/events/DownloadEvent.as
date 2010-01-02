package com.s3mer.events
{
	import flash.events.Event;

	public class DownloadEvent extends Event
	{
		public static const DOWNLOAD_COMPLETE:String = "DOWNLOAD_COMPLETE";
		public static const DOWNLOAD_PROGRESS:String = "DOWNLOAD_PROGRESS";
		public static const QUEUE_COMPLETE:String = "QUEUE_COMPLETE";


		public function DownloadEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
	}
}