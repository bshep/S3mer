package com.msgid.S3mer
{
	import flash.events.Event;
	import flash.events.ProgressEvent;
 
	public class DownloaderEvent extends ProgressEvent
	{
		public static const COMPLETE:String = "COMPLETE";
		public static const PROGRESS:String = "PROGRESS";
		public static const PARTIAL_COMPLETE:String = "PARTIAL_COMPLETE";
		
		public var _type:String;
		public var _percent:int;
		public var _downloader:Object;
	
		public function DownloaderEvent(type:String, obj:Object, percent:int = 0) {
			switch(type) {
				case COMPLETE:
					this._percent = 100;
					break;
				case PROGRESS:
					break;
			}

			super(type);
			this._downloader = obj;
			this._type = type;
			this._percent = percent;
		}
		
	    public override function clone():Event
	    {
	        return new DownloaderEvent(this._type, this._downloader);
	    }
	    
        public override function toString():String
	    {
	        return formatToString("DownloaderEvent", "type", "bubbles", "cancelable", "eventPhase", "message");
	    }

	}
}