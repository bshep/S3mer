package com.s3mer.mediaObjects
{
	public class SWFObject extends ImageObject
	{
		public function SWFObject()
		{
			super();
		}
		
		public override function play(_item:XML):void
		{
			this.item = _item;
			
			this.internalObject.source = _item.toString();

			if( _item.@duration != '' && _item.@duration != "0" ) {	
				this.imageTimer.delay = _item.@duration*1000;
				this.imageTimer.start();
			}
			
		}

		
	}
}