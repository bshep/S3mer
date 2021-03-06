package com.s3mer.mediaObjects
{
	import com.s3mer.mediaObjects.customObjects.TimeDateObject;
	import com.s3mer.util.Scale;
	
	public class TimeObject extends GenericMediaObject
	{
		protected var internalObject:TimeDateObject = new TimeDateObject;

		public function TimeObject()
		{
			super();
			
			this.addChild(internalObject);
			this.internalObject.setStyle("left","0");
			this.internalObject.setStyle("right","0");
			this.internalObject.setStyle("top","0");
			this.internalObject.setStyle("bottom","0");
			
		}

		public override function play(_item:XML):void
		{
			super.play(_item);
			
			this.internalObject.play();
		}

		public override function resize(scale:Scale):void
		{
			super.resize(scale);
			
			this.internalObject.scaleX = scale.scaleX;
			this.internalObject.scaleY = scale.scaleY;
		}
	}
}