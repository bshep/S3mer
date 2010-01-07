package com.s3mer.mediaObjects
{
	import com.s3mer.util.FileIO;
	
	import mx.controls.VideoDisplay;
	
	public class MovieObject extends GenericMediaObject
	{
		private var internalObject:VideoDisplay = new VideoDisplay();
		
		public function MovieObject() {
			super();
			
			this.addChild(internalObject);
			this.internalObject.setStyle("left","0");
			this.internalObject.setStyle("right","0");
			this.internalObject.setStyle("top","0");
			this.internalObject.setStyle("bottom","0");
			this.internalObject.maintainAspectRatio = false;
		}
		
		public override function play(item:XML):void {
			super.play(item);
			
			this.internalObject.source = this.mediaPath + "/" + FileIO.Url2Filename(item.toString());
		}
		
		public override function stop():void {
			super.stop();
			
			this.internalObject.stop();
			
		}

		
	}
}