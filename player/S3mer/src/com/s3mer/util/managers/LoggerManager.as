package com.s3mer.util.managers
{
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	import flash.ui.Keyboard;
	
	public class LoggerManager
	{

//		private static var _log:String = "";
		private static var _LMInstance:LoggerManager = new LoggerManager();
		
//		public static function get log():String {
//			return _log;
//		}
//		
		public static function get instance():LoggerManager {
			if (_LMInstance == null){
				_LMInstance = new LoggerManager();
			}
			
			return _LMInstance;			
		}
		
		public static function addEvent(str:String):void {

			if ( _LMInstance == null ) {
				trace("LoggerManager.as: LOGGER NOT INITTED: This is BAD and should never happen!");
				return;
			}
//			
//			LocalDatabase.insertStatusEvent(str);
			trace(str);
			_LMInstance.addEventInst(str);
//			_log = str + "\n" + _log;
//			
//			if (_log.length > 5000) {
//				_log = _log.substr(0,5000);
//			}
//			
//			_LMInstance.updated();
		}
		
		public function addEventInst(str:String):void {
			var logFile:File = File.applicationStorageDirectory.resolvePath("logfile.txt");
			var logFileStream:FileStream = new FileStream();
			
			logFileStream.open(logFile, FileMode.APPEND);
			
			logFileStream.writeUTFBytes(str + "\n");
			logFileStream.close();
		}
		
//		public function updated():void {
//			dispatchEvent(new Event(Event.ADDED));
//		}
	}
	
}