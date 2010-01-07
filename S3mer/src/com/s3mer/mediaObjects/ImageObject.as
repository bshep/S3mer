package com.s3mer.mediaObjects
{
	import com.s3mer.events.MediaEvent;
	import com.s3mer.util.FileIO;
	
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	
	import mx.controls.Image;

	public class ImageObject extends GenericMediaObject implements IMediaObject
	{
		private var internalObject:Image = new Image;
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
		
		public override function play(item:XML):void
		{
			super.play(item);
			
			this.internalObject.source = this.mediaPath + "/" + FileIO.Url2Filename(item.toString());

			if( item.@duration != '' && item.@duration != "0" ) {	
				this.imageTimer.delay = item.@duration*1000;
				this.imageTimer.start();
			}
			
		}
		
		private function play_complete(e:TimerEvent):void {
			this.dispatchEvent(new MediaEvent(MediaEvent.PLAY_COMPLETE));
		}
		
		public override function stop():void
		{
			super.stop();
		}

	}
}