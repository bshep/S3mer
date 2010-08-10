package com.s3mer.mediaObjects
{
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.media.Camera;
	import flash.media.Microphone;
	import flash.utils.Timer;
	
	import mx.controls.VideoDisplay;
	
	public class LiveVideoObject extends GenericMediaObject
	{
		protected var internalObject:VideoDisplay = new VideoDisplay();
		protected var displayTimer:Timer;
		
		protected var camera:Camera;
		protected var microphone:Microphone;
		
		public function LiveVideoObject()
		{
			super();
			
			this.addChild(internalObject);
			this.internalObject.setStyle("left","0");
			this.internalObject.setStyle("right","0");
			this.internalObject.setStyle("top","0");
			this.internalObject.setStyle("bottom","0");
			this.internalObject.maintainAspectRatio = false;

			this.displayTimer = new Timer(1000, 1);
			this.displayTimer.addEventListener(TimerEvent.TIMER, play_complete);
		}
	
		public override function play(_item:XML):void {
			this.item = _item;
			
			this.camera = Camera.getCamera();
			this.microphone = this.getPreferedMicrophone();
			
			if(camera) {			
				camera.setMode(720,480,30);
				this.internalObject.attachCamera(camera);
			}
			
			if(microphone) {
				microphone.setLoopBack(true);
				microphone.setUseEchoSuppression(true);
			}

			if( _item.@duration != '' && _item.@duration != "0" ) {	
				this.displayTimer.delay = _item.@duration*1000;
				this.displayTimer.reset();
				this.displayTimer.start();
			}
		}
		
		public override function play_complete(e:Event):void {
			this.internalObject.attachCamera(null);
			
			this.microphone.setLoopBack(false);
			
			super.play_complete(e);
		}

		private function getPreferedMicrophone():Microphone {
			var miclist:Array = Microphone.names;
			var a:int;
			
			
			for( a = 0; a< miclist.length; a++ ){
				if( miclist[a] == "Built-in Input" ) {
					return Microphone.getMicrophone(a)
				}
			}
			
			for( a = 0; a< miclist.length; a++ ){
				if( miclist[a] == "Built-in Microphone" ) {
					return Microphone.getMicrophone(a)
				}
			}
			
			return Microphone.getMicrophone();
		}
		
	}
}