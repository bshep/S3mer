package com.s3mer.events
{
	import flash.events.Event;

	public class ConfigurationEvent extends Event
	{
		public static const CREDENTIALS_CHECKED:String = "CREDENTIALS_CHECKED";
		public static const REGISTRATION_COMPLETE:String = "REGISTRATION_COMPLETE";
		
		public var _type:String;
	

		public function ConfigurationEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
			_type = type;
		}
		
	}
}