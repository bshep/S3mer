package com.s3mer.mediaObjects.customObjects
{
	import com.s3mer.util.managers.LoggerManager;
	
	import flash.media.Camera;
	import flash.media.SoundTransform;
	
	import mx.controls.VideoDisplay;
	import mx.core.mx_internal;
	import mx.events.VideoEvent;
	
	use namespace mx_internal;

	public class SmoothVideoDisplay extends VideoDisplay
	{
		
		private var _smoothing:Boolean = false;
		private var _pan:Number = 0;
		public var cameraAttached:Boolean;
		
		public function SmoothVideoDisplay()
		{
			super();
			this.cameraAttached = false;
			this.addEventListener(VideoEvent.STATE_CHANGE, videoStateChanged);
		}
		
		[Bindable]
		[Inspectable(defaultValue="false")]
		public function set smoothing(val:Boolean):void{
			if (val == _smoothing || videoPlayer == null) return;
			_smoothing = val;
			videoPlayer.smoothing = _smoothing;
		}
		
		public function get smoothing():Boolean{
			return _smoothing;
		}
		
		public function set pan(value:Number):void {
			if(_pan != value) {
				_pan = value;
			}			
			
			doPan();
		}
		
		public function get pan():Number {
			return this._pan;
		}
		
	    public override function play():void {
	    	super.play();
	    	
	    	doPan();
	    }
	    
	    public override function set source(value:String):void {
	    	super.source = value;
	    	
	    	this.cameraAttached = false;
	    } 
	    
	    public override function attachCamera(camera:Camera):void {
	    	super.attachCamera(camera);
  			videoPlayer.clear();
	    	
	    	this.cameraAttached = true;
	    }
	    
	    public function detachCamera():void {
	    	videoPlayer.clear();
	    	videoPlayer.close();
	    	
	    	this.cameraAttached = false;
	    }
	    
	    private function doPan():void {
			if(videoPlayer) {
				var newTransform:SoundTransform = new SoundTransform();
				
				newTransform.pan = this._pan;
				if( videoPlayer.soundTransform != null ) {
					newTransform.volume = videoPlayer.volume;
				}
				
				try {
					videoPlayer.soundTransform = newTransform;
				} catch(e:Error) {
					LoggerManager.addEvent("SmoothVideoDisplay.as doPan:" + e.errorID + " could not set sound transform, probably a null ns object");
				}
			}
	    }
	    
		private function videoStateChanged(e:VideoEvent):void {
			if(e.state == "buffering") {
				doPan();
			}
		}
	}
}