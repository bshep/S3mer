package com.s3mer.events
{
	import flash.events.Event;

	public class MediaEvent extends Event
	{
		public static const PLAY_COMPLETE:String = "PLAY_COMPLETE";
		
		public function MediaEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		public override function clone():Event {
			return new MediaEvent(this.type, this.bubbles, this.cancelable);
		}

		
	}
}