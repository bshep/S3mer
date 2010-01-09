package com.s3mer.mediaObjects
{
	import com.s3mer.util.Scale;
	
	import flash.events.Event;
	
	public interface IMediaObject 
	{
		/**
		 *	Function called when the item should play something
		 *  
		 * @param item XML describing what should be played in the container.
		 * 
		 */
		function play(item:XML):void;
		function play_complete(e:Event):void;

		function stop():void;
		function configure(configuration:XML, x:int, y:int, scale:Scale):void;
		function resize(scale:Scale):void;
	}
}