package com.s3mer.mediaObjects
{
	import com.s3mer.events.MediaEvent;
	import com.s3mer.util.LoggerManager;
	import com.s3mer.util.Scale;
	
	import flash.events.Event;
	
	import mx.containers.Canvas;
	import mx.core.ScrollPolicy;

	/**
	 *  Dispatched when object is finished playing the current media
	 * 
	 *  @eventType mx.events.FlexEvent.ADD
	 */
	[Event(name="play_complete", type="com.s3mer.events.MediaEvent")]

	public class GenericMediaObject extends Canvas implements IMediaObject
	{
		private var configuration:XML;
		private var item:XML;

		public var mediaPath:String;
		
		public function GenericMediaObject()
		{
			super();
			
			this.horizontalScrollPolicy = ScrollPolicy.OFF;
			this.verticalScrollPolicy = ScrollPolicy.OFF;
		}
		
		public function play(_item:XML):void
		{
			item = _item;
		}
		
		public function play_complete(e:Event):void {
			this.dispatchEvent(new MediaEvent(MediaEvent.PLAY_COMPLETE));
		}

		
		public function stop():void
		{
		}
		
		public function configure(_configuration:XML, layoutWidth:int, layoutHeight:int, scale:Scale):void
		{
			var left:Number, width:Number, top:Number, height:Number;
			this.configuration = _configuration;
			this.id	= _configuration.@id;
			
			left = _configuration.@left;
			top = _configuration.@top;
			width = _configuration.@width;
			height = _configuration.@height;
			
			_configuration.@right = layoutWidth - (left + width);

			_configuration.@bottom = layoutHeight - (top + height);

			resize(scale);
		}
		
		public function resize(scale:Scale):void
		{
			var left:Number, right:Number, top:Number, bottom:Number;
			
			top  = this.configuration.@top.toString();
			top *= scale.scaleY;
			
			left = this.configuration.@left.toString();
			left *= scale.scaleX;
			
			right = this.configuration.@right.toString();
			right *= scale.scaleX;
			
			bottom = this.configuration.@bottom.toString();
			bottom *= scale.scaleY;
			
			this.setStyle("top",top);
			this.setStyle("left",left);
			this.setStyle("right",right);
			this.setStyle("bottom",bottom);
			LoggerManager.addEvent("GenericMediaObject.as resize: " + " top = " + top + 
							" left = " + left +
							" right = " + right +
							" bottom = " + bottom );
			
		}
		
	}
}