package com.s3mer.util
{
	public final class Scale
	{
		private var _scaleX:Number;
		private var _scaleY:Number;
		
		public function get scaleX():Number {
			return _scaleX;
		}
		
		public function get scaleY():Number {
			return _scaleY;
		}
		
		public function Scale(_x:Number, _y:Number)
		{
			_scaleX = _x;
			_scaleY = _y;
		}
	}
}