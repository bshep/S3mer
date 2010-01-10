package com.s3mer.mediaObjects
{
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.ui.Mouse;
	import flash.utils.Timer;
	
	import mx.controls.HTML;
	
	public class HTMLObject extends GenericMediaObject
	{
		protected var internalObject:HTML = new HTML;
		protected var displayTimer:Timer;
		
		public function HTMLObject()
		{
			super();

			this.addChild(internalObject);
			this.internalObject.setStyle("left","0");
			this.internalObject.setStyle("right","0");
			this.internalObject.setStyle("top","0");
			this.internalObject.setStyle("bottom","0");

			this.displayTimer = new Timer(1000, 1);
			this.displayTimer.addEventListener(TimerEvent.TIMER, play_complete);
		}
		
		public override function play_complete(e:Event):void {
			Mouse.hide();
			
			this.internalObject.location = "about:blank";
			
			super.play_complete(e);
		}
		
		public override function play(_item:XML):void
		{
			super.play(_item);
			
			this.internalObject.location = _item.toString();

			if( _item.@duration != '' && _item.@duration != "0" ) {	
				this.displayTimer.delay = _item.@duration*1000;
				this.displayTimer.reset();
				this.displayTimer.start();
			}
			
			Mouse.show();
		}

		
	}
}