package com.msgid.S3mer.LocalDatabase
{
	import com.msgid.S3mer.ApplicationSettings;
	import com.msgid.S3mer.LoggerPlaybackEvent;
	
	import flash.data.SQLConnection;
	import flash.data.SQLResult;
	import flash.data.SQLStatement;
	import flash.errors.SQLError;
	import flash.events.Event;
	import flash.events.SQLErrorEvent;
	import flash.events.SQLEvent;
	import flash.filesystem.File;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables; 

	public class LocalDatabase
	{
		private var conn:SQLConnection = null;
		private static var dbFile:File;
//		private static var inited:Boolean = false;
		
		public function LocalDatabase()
		{
			conn = LocalDatabase.getConnection();
		}
		
		public static function getConnection():SQLConnection {
			var ret:SQLConnection;
						
			ret = new SQLConnection(); 
			ret.addEventListener(SQLEvent.OPEN, openHandler); 
			ret.addEventListener(SQLErrorEvent.ERROR, errorHandler); 
			dbFile = File.applicationStorageDirectory.resolvePath("PlayLog.db"); 
			ret.open(dbFile); 
			
			return ret;
		}
		
		public static function openHandler(event:SQLEvent):void 
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
						"   uploaded INTEGER" +
						")";
						
			execute(sql, event.currentTarget as SQLConnection);

//		    trace("the database was created successfully");
		    
		    
		}
		
		public static function errorHandler(event:SQLErrorEvent):void 
		{ 
		    trace("Error message:", event.error.message); 
		    trace("Details:", event.error.details); 
		}
		
		public static function execute(sql:String, conn:SQLConnection):void {
			
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
						"	uploaded" +
						") VALUES ( " + 
						"	" + event.file_id + ", " +
						"	" + event.show_id + ", " +
						"	'" + event.file + "', " +
						"	'" + event.file_type + "', " +
						"	'" + event.time_start.valueOf() + "', " +
						"	'" + event.time_end.valueOf() + "', " +
						"	0" +
						")";
						
			execute(sql, getConnection());
			
			postDataToServer();
		}
		
		public static function postDataToServer():void {
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
			
			if (sqlResult.data.length < 20) {
				return;
			}
			
			for each( var row:Object in sqlResult.data ) {
				sql = "UPDATE playback_log SET uploaded = '1' WHERE id = " + row.id;
				sqlStmt.text = sql;
				sqlStmt.execute();
				
			}		
			
			ret = phpSerialize(sqlResult);
			
			urlReq.url = ApplicationSettings.URL_RUNLOG + "?playerid=" + ApplicationSettings.getValue("screen"+ 0 +".channel.id","");
			urlVars.data = ret;
			urlReq.data = urlVars;
			
			urlReq.method = URLRequestMethod.POST;
			
			urlLoad = new URLLoader();
			urlLoad.addEventListener(Event.COMPLETE, postDataToServer_complete);
			
			urlLoad.load(urlReq);
			
			
			//trace(ret);
			
		}
		
		public static function postDataToServer_complete(e:Event):void {
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
		}
		
		public static function phpSerialize(res:SQLResult):String {
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