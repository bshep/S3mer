package com.s3mer.mediaObjects
{
	public interface IMediaObject 
	{
		function play(item:XML):void;
		function stop():void;
		function configure(configuration:XML, x:int, y:int):void;
	}
}