package com.msgid.S3mer
{
	import flash.utils.Timer;

	public class TimerId extends Timer
	{
		public var id:String;
		
		public function TimerId(_id:String, delay:Number, repeatCount:int=0.0)
		{
			super(delay, repeatCount);
		
			this.id	= _id;
		}
		
	}
}