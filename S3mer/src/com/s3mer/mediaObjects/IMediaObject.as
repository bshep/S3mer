package com.s3mer.mediaObjects
{
	import com.s3mer.util.Scale;
	
	public interface IMediaObject 
	{
		function play(item:XML):void;
		function stop():void;
		function configure(configuration:XML, x:int, y:int, scale:Scale):void;
		function resize(scale:Scale):void;
	}
}