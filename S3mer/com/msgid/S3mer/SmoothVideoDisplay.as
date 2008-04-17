package com.msgid.S3mer
{
	import flash.media.Camera;
	
	import mx.controls.VideoDisplay;
	import mx.core.mx_internal;
	
	use namespace mx_internal;

	public class SmoothVideoDisplay extends VideoDisplay
	{
		
		private var _smoothing:Boolean = false;
		private var _pan:String = "C";
		public var cameraAttached:Boolean;
		
		public function SmoothVideoDisplay()
		{
			super();
//			this.smoothing = true;
			this.cameraAttached = false;
		}
		
		[Bindable]
		public function set smoothing(val:Boolean):void{
			if (val == _smoothing && videoPlayer == null) return;
			_smoothing = val;
			videoPlayer.smoothing = _smoothing;
		}
		
		public function get smoothing():Boolean{
			return _smoothing;
		}
		
		public function set pan(value:String):void {
			this._pan = value;
			
			doPan();
		}
		
		public function get pan():String {
			return this._pan;
		}
		
	    public override function play():void {
	    	super.play();
	    	
	    	doPan();
	    }
	    
	    public override function set source(value:String):void {
//	    	if( this.cameraAttached == true ) {
//	  			videoPlayer.clear();
//				this.close();
//	    	}
	    	
	    	super.source = value;
	    	
	    	this.cameraAttached = false;
	    } 
	    
	    public override function attachCamera(camera:Camera):void {
	    	super.attachCamera(camera);
  			videoPlayer.clear();
	    	
	    	this.cameraAttached = true;
	    	
	    }
	    
	    private function doPan():void {
			switch(this._pan) {
				case 'L':
					this.soundTransform.leftToRight = 0;
					this.soundTransform.leftToLeft = 1;
					this.soundTransform.rightToLeft = 1;
					this.soundTransform.rightToRight = 0;
					
					break;
				case 'R':
					this.soundTransform.leftToRight = 1;
					this.soundTransform.leftToLeft = 0;
					this.soundTransform.rightToLeft = 0;
					this.soundTransform.rightToRight = 1;

					break;
				case 'C':
					this.soundTransform.leftToRight = 0;
					this.soundTransform.leftToLeft = 1;
					this.soundTransform.rightToLeft = 0;
					this.soundTransform.rightToRight = 1;

					break;
			}
	    }
	}
}