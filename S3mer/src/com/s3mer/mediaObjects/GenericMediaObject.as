package com.s3mer.mediaObjects
{
	import com.s3mer.util.LoggerManager;
	
	import mx.containers.Canvas;
	import mx.core.ScrollPolicy;

	public class GenericMediaObject extends Canvas implements IMediaObject
	{
		private var configuration:XML;

		public var mediaPath:String;

		public function GenericMediaObject()
		{
			super();
			
			this.horizontalScrollPolicy = ScrollPolicy.OFF;
			this.verticalScrollPolicy = ScrollPolicy.OFF;
		}
		
		public function play(item:XML):void
		{
		}
		
		public function stop():void
		{
		}
		
		public function configure(_configuration:XML, layoutWidth:int, layoutHeight:int, scaleX:Number, scaleY:Number):void
		{
			
			this.configuration = _configuration;
			
			_configuration.@right = (layoutWidth - _configuration.@width.toString());;
			_configuration.@bottom =  (layoutHeight - _configuration.@height.toString());;

			resize(scaleX, scaleY);
		}
		
		public function resize(scaleX:Number, scaleY:Number):void
		{
			var left:Number, right:Number, top:Number, bottom:Number;
			
			top  = this.configuration.@top.toString();
			top *= scaleY;
			
			left = this.configuration.@left.toString();
			left *= scaleX;
			
			right = this.configuration.@right.toString();
			right *= scaleX;
			
			bottom = this.configuration.@bottom.toString();
			bottom *= scaleY;
			
			this.setStyle("top",top);
			this.setStyle("left",left);
			this.setStyle("right",right);
			this.setStyle("bottom",bottom);
			LoggerManager.addEvent("GenericMediaObject.as: " + " top = " + top + 
							" left = " + left +
							" right = " + right +
							" bottom = " + bottom );
			
		}
		
	}
}