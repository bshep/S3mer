package com.s3mer.mediaObjects
{
	public interface IMediaObject 
	{
		function play(item:XML):void;
		function stop():void;
		function configure(configuration:XML, x:int, y:int, scaleX:Number, scaleY:Number):void;
		function resize(scaleX:Number, scaleY:Number):void;
	}
}