package com.s3mer.mediaObjects
{
	import com.s3mer.events.MediaEvent;
	import com.s3mer.mediaObjects.customObjects.SmoothImage;
	import com.s3mer.util.FileIO;
	
	import flash.events.TimerEvent;
	import flash.utils.Timer;

	public class ImageObject extends GenericMediaObject
	{
		private var internalObject:SmoothImage = new SmoothImage;
		private var imageTimer:Timer;
		
		public function ImageObject()
		{
			super();
			
			this.addChild(internalObject);
			this.internalObject.setStyle("left","0");
			this.internalObject.setStyle("right","0");
			this.internalObject.setStyle("top","0");
			this.internalObject.setStyle("bottom","0");
			this.internalObject.maintainAspectRatio = false;
			
			this.imageTimer = new Timer(1000);
			this.imageTimer.addEventListener(TimerEvent.TIMER, play_complete);
		}
		
		public override function play(_item:XML):void
		{
			super.play(_item);
			
			this.internalObject.source = this.mediaPath + "/" + FileIO.Url2Filename(_item.toString());

			if( _item.@duration != '' && _item.@duration != "0" ) {	
				this.imageTimer.delay = _item.@duration*1000;
				this.imageTimer.start();
			}
			
		}
		
		public override function stop():void
		{
			super.stop();
		}

	}
}