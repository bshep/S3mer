package com.msgid.S3mer.Utility
{
	import com.msgid.S3mer.LocalDatabase.LocalDatabase;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
		
	public class LoggerManager extends EventDispatcher
	{

		private static var _log:String = "";
		private static var _instanceLog:LoggerManager;
		
		public static function get log():String {
			return _log;
		}
		
		public static function get instance():LoggerManager {
			if (_instanceLog == null){
				_instanceLog = new LoggerManager();
			}
			
			return _instanceLog;			
		}
		
		public static function addEvent(str:String):void {
//			return;
//			if ( _instanceLog == null ) {
//				_instanceLog = new Logger();
//			}
//			
			LocalDatabase.insertStatusEvent(str);
			trace(str);
//			_log = str + "\n" + _log;
//			
//			if (_log.length > 5000) {
//				_log = _log.substr(0,5000);
//			}
//			
//			_instanceLog.updated();
		}
		
		public function updated():void {
			dispatchEvent(new Event(Event.ADDED));
		}
	}
	
}