package com.msgid.S3mer
{
	import flash.events.Event;
 
	public class ConfigurationEvent extends Event
	{
		public static const UPDATED:String = "UPDATED";
		
		public var _type:String;
	
		public function ConfigurationEvent(type:String) {
			super(type);
			this._type = type;
		}
		
	    public override function clone():Event
	    {
	        return new ConfigurationEvent(this._type);
	    }
	    
        public override function toString():String
	    {
	        return formatToString("ConfigurationEvent", "type", "bubbles", "cancelable", "eventPhase", "message");
	    }

	}
}