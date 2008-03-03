package com.msgid.S3mer
{
	import flash.events.Event;
 
	public class ShowEvent extends Event
	{
		public static const STOPPED:String = "STOPPED";
		public static const STARTED:String = "STARTED";
		public static const NEXT_ITEM:String = "NEXT_ITEM";
		public static const INVALID_PLAYLIST_ITEM:String = "INVALID_PLAYLIST_ITEM";
		
		public var _type:String;
	
		public function ShowEvent(type:String) {
			super(type);
			this._type = type;
		}
		
	    public override function clone():Event
	    {
	        return new ShowEvent(this._type);
	    }
	    
        public override function toString():String
	    {
	        return formatToString("ShowEvent", "type", "bubbles", "cancelable", "eventPhase", "message");
	    }

	}
}