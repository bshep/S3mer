package com.s3mer.mediaObjects
{
	import com.s3mer.mediaObjects.customObjects.RSSFeedPanel;
	import com.s3mer.util.Scale;
	
	public class RSSObject extends GenericMediaObject
	{
		protected var internalObject:RSSFeedPanel = new RSSFeedPanel;

		public function RSSObject()
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
			
			this.internalObject.rssURL = _item.toString();
			this.internalObject.logoURL = _item.@logoUrl;
			
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