package com.msgid.S3mer.Events
{
	import com.msgid.S3mer.LocalDatabase.LocalDatabase;
	
	public class LoggerPlaybackEvent
	{
		public var file:String;
		public var file_id:String;
		public var file_type:String;
		public var time_start:Date;
		public var time_end:Date;
		public var show_id:String;
		public var screen_id:int;
		
		public function LoggerPlaybackEvent(file:String, file_id:String, file_type:String, time_start:Date, time_end:Date, show_id:String, screen_id:int) 
		{
			this.file = file;
			this.file_type = file_type;
			this.file_id = file_id;
			this.time_start = time_start;
			this.time_end = time_end;
			this.show_id = show_id;
			this.screen_id = screen_id;
			
			var db:LocalDatabase = new LocalDatabase;
		}

	}
}