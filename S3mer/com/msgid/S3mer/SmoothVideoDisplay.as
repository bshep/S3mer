package com.msgid.S3mer
{
	import mx.controls.VideoDisplay;
	import mx.core.mx_internal;
	
	use namespace mx_internal;

	public class SmoothVideoDisplay extends VideoDisplay
	{
		
		private var _smoothing:Boolean = false;
		
		public function SmoothVideoDisplay()
		{
			super();
//			this.smoothing = true;
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
		
	}
}