package com.s3mer.util
{
	public  class PlayerState
	{
		public static const TYPE_DEMO:String = "demo";
		public static const TYPE_FREE:String = "free";
		public static const TYPE_PRO:String = "pro";
		
		public static var playerType:String; //Types: 0 -> free, 1->pro
		public static var username:String;
		public static var password:String;
		
		public static var credentialsValid:Boolean;
		public static var internetConnected:Boolean;
		public static var multiScreen:Boolean;
		
//		public var 
		
		
		public function PlayerState()
		{
		}

	}
}