package com.msgid.S3mer.LocalDatabase
{
	import com.msgid.S3mer.ApplicationSettings;
	import com.msgid.S3mer.Events.LoggerPlaybackEvent;
	import com.msgid.S3mer.Logger;
	
	import flash.data.SQLConnection;
	import flash.data.SQLResult;
	import flash.data.SQLStatement;
	import flash.errors.SQLError;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.SQLErrorEvent;
	import flash.events.SQLEvent;
	import flash.events.TimerEvent;
	import flash.filesystem.File;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import flash.utils.Timer; 

	public class LocalDatabase
	{
		private static var instance:LocalDatabase;
		
//		private var conn:SQLConnection = null;
		private var tmrPostData:Timer;
		
		public function LocalDatabase()
		{
//			conn = LocalDatabase.getConnection();
			tmrPostData = new Timer(1000*60*60);// Fire every hour
//			tmrPostData.delay = 1000*5*1; 
			tmrPostData.addEventListener(TimerEvent.TIMER, postData_timer);
			tmrPostData.start();
		}
		
		public static function init():void {
			if(instance == null) {
				instance = new LocalDatabase();
			}
		}
		
		public static function getConnection():SQLConnection {
			init();
			
			var ret:SQLConnection;
			var dbFile:File;
						
			ret = new SQLConnection(); 
			ret.addEventListener(SQLEvent.OPEN, openHandler); 
			ret.addEventListener(SQLErrorEvent.ERROR, errorHandler); 
			dbFile = File.applicationStorageDirectory.resolvePath("PlayLog.db"); 
			ret.open(dbFile); 
			
			return ret;
		}
		
		private static function openHandler(event:SQLEvent):void 
		{ 			
			var sql:String;
			
			sql = "CREATE TABLE IF NOT EXISTS playback_log(" +
						"	id INTEGER PRIMARY KEY AUTOINCREMENT, " +
						"	file_id INTEGER, " +
						"	show_id INTEGER, " +
						"	file_type TEXT, " +
						"	file TEXT, " +
						"	time_start INTEGER, " +
						"	time_end INTEGER," +
						"	screen_id INTEGER," +
						"   uploaded INTEGER" +
						")";
						
			execute(sql, event.currentTarget as SQLConnection);
			
			sql = "CREATE TABLE IF NOT EXISTS event_log(" +
						"	id INTEGER PRIMARY KEY AUTOINCREMENT, " +
						"	data TEXT " +
						")";
						
			execute(sql, event.currentTarget as SQLConnection);

//		    trace("the database was created successfully");
		    
		    
		}
		
		private static function errorHandler(event:SQLErrorEvent):void 
		{ 
		    trace("Error message:", event.error.message); 
		    trace("Details:", event.error.details); 
		}
		
		private static function execute(sql:String, conn:SQLConnection):void {
			
			var sqlStmt:SQLStatement = new SQLStatement();
			sqlStmt.sqlConnection = conn;		
			sqlStmt.text = sql;
			try {
				sqlStmt.execute();
			} catch(error:SQLError) {
			    trace("Error message:", error.message); 
			    trace("Details:", error.details); 
			}
		}
		
		public static function insertPlaybackEvent(event:LoggerPlaybackEvent):void {
			var sql:String;
			
			sql = "INSERT INTO playback_log(" +
						"	file_id, " +
						"	show_id, " +
						"	file, " +
						"	file_type, " +
						"	time_start, " +
						"	time_end," +
						"	screen_id," +
						"	uploaded" +
						") VALUES ( " + 
						"	" + event.file_id + ", " +
						"	" + event.show_id + ", " +
						"	'" + event.file + "', " +
						"	'" + event.file_type + "', " +
						"	'" + event.time_start.valueOf() + "', " +
						"	'" + event.time_end.valueOf() + "', " +
						"	" + event.screen_id + ", " +
						"	0" +
						")";
						
			execute(sql, getConnection());
			
			postDataToServer(50); // Make sure at least 50 items are in the list before uploading
		}
		
		public static function insertStatusEvent(data:String):void {
			var sql:String;
			
			sql = "INSERT INTO event_log(" + 
				"	data" + 
				") VALUES (" + 
				"	'" 
			sql = sql.concat(data);
			sql = sql.concat("')");
				
			execute(sql, getConnection());
		}
		
		private function postData_timer(e:TimerEvent):void {
			this.tmrPostData.stop();
			
			try {
				LocalDatabase.postDataToServer(0);
			} finally {
			}
		}
		
		public static function postDataToServer(minItems:int):void {
			var sql:String = "SELECT * FROM playback_log WHERE uploaded = '0' LIMIT 1000";
			var sqlStmt:SQLStatement = new SQLStatement();
			var sqlResult:SQLResult;
			var urlReq:URLRequest = new URLRequest();
			var urlLoad:URLLoader;
			var urlVars:URLVariables = new URLVariables();
			
			var ret:String;
			
			sqlStmt.sqlConnection = getConnection();
			sqlStmt.text = sql;
			
			sqlStmt.execute();
			sqlResult = sqlStmt.getResult();
			
			if(sqlResult.data == null ) {
				return;
			}
			
			if (sqlResult.data.length < minItems) {
				return;
			}
			
			for each( var row:Object in sqlResult.data ) {
				sql = "UPDATE playback_log SET uploaded = '1' WHERE id = " + row.id;
				sqlStmt.text = sql;
				sqlStmt.execute();
				
			}		
			
			ret = phpSerialize_runlog(sqlResult);
			
			urlReq.url = ApplicationSettings.URL_RUNLOG + "?playerid=" + ApplicationSettings.getValue("screen"+ sqlResult.data[0].screen_id +".channel.id","");
			urlVars.data = ret;
			urlReq.data = urlVars;
			
			urlReq.method = URLRequestMethod.POST;
			
			urlLoad = new URLLoader();
			urlLoad.addEventListener(Event.COMPLETE, postDataToServer_complete);
			urlLoad.addEventListener(IOErrorEvent.IO_ERROR, postDataToServer_ioError);
			
			urlLoad.load(urlReq);
			
			
			//trace(ret);
			
		}
		
		private static function postDataToServer_ioError(e:IOErrorEvent):void {
			Logger.addEvent("LocalDatabase: Error posting data back to server. Details: " + e.text);
		}
		
		private static function postDataToServer_complete(e:Event):void {
			var sql:String;
			var sqlStmt:SQLStatement = new SQLStatement();

			if((e.target as URLLoader).data == "OK") {
				sql = "DELETE FROM playback_log WHERE uploaded = 1"
			} else {
				sql = "UPDATE playback_log SET uploaded = 0 WHERE uploaded = 1";
			}
			sqlStmt.sqlConnection = getConnection();
			sqlStmt.text = sql;
			sqlStmt.execute();
			
			instance.tmrPostData.start();
		}
		
		public static function phpSerialize_runlog(res:SQLResult):String {
			var ret:String;
			var rownum:int = 0;
			ret = "a:" + res.data.length + ":{";
			
			for each( var row:Object in res.data ) {
				ret = ret + "i:" + rownum + ";";
				
				ret = ret + "a:6:{";
				
				ret = ret + "s:7:\"file_id\";s:" + row.file_id.toString().length + ":\"" + row.file_id + "\";";
				ret = ret + "s:7:\"show_id\";s:" + row.show_id.toString().length + ":\"" + row.show_id + "\";";
				ret = ret + "s:9:\"file_type\";s:" + row.file_type.toString().length + ":\"" + row.file_type + "\";";
				ret = ret + "s:4:\"file\";s:" + row.file.toString().length + ":\"" + row.file + "\";";
				ret = ret + "s:10:\"time_start\";s:" + row.time_start.toString().length + ":\"" + row.time_start + "\";";
				ret = ret + "s:8:\"time_end\";s:" + row.time_end.toString().length + ":\"" + row.time_end + "\"";
				
				ret = ret + ";}";
				
				//trace(row.toString());
				
				rownum += 1;
			}
			
			ret = ret + "}";
			
			return ret;
		}
		
	}
}