package com.msgid.S3mer
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	
	public class Logger extends EventDispatcher
	{
		private static var _log:String = "";
		private static var _instanceLog:Logger;
		
		public static function get log():String {
			return _log;
		}
		
		public static function get instance():Logger {
			if (_instanceLog == null){
				_instanceLog = new Logger();
			}
			
			return _instanceLog;			
		}
		
		public static function addEvent(str:String):void {
			if ( _instanceLog == null ) {
				_instanceLog = new Logger();
			}
			
			trace(str);
			_log = str + "\n" + _log;
			
			if (_log.length > 5000) {
				_log = _log.substr(0,5000);
			}
			
			_instanceLog.updated();
		}
		
		public function updated():void {
			dispatchEvent(new Event(Event.ADDED));
		}
	}
	
}