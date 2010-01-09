package com.s3mer.mediaObjects
{
	import com.s3mer.mediaObjects.customObjects.SmoothVideoDisplay;
	import com.s3mer.util.FileIO;
	
	import flash.events.Event;
	
	import mx.events.VideoEvent;
	
	public class MovieObject extends GenericMediaObject
	{
		private var internalObject:SmoothVideoDisplay = new SmoothVideoDisplay();
		
		public function MovieObject() {
			super();
			
			this.addChild(internalObject);
			this.internalObject.setStyle("left","0");
			this.internalObject.setStyle("right","0");
			this.internalObject.setStyle("top","0");
			this.internalObject.setStyle("bottom","0");
			this.internalObject.maintainAspectRatio = false;
			this.internalObject.addEventListener(VideoEvent.COMPLETE, play_complete);
		}
		
		public override function play(_item:XML):void {
			super.play(_item);
			
			this.internalObject.source = this.mediaPath + "/" + FileIO.Url2Filename(_item.toString());
		}
		
		public override function stop():void {
			super.stop();
			
			this.internalObject.stop();
			
		}

		
	}
}