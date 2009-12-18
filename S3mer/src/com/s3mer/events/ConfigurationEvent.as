package com.s3mer.events
{
	import flash.events.Event;

	public class ConfigurationEvent extends Event
	{
		public static const UPDATED:String = "UPDATED";
		
		public var _type:String;
	

		public function ConfigurationEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
	}
}