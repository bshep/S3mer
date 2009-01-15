package com.msgid.S3mer
{
	import mx.collections.ArrayCollection;
	
	public class PlaylistObject
	{
		private var _url:String;
		private var _file:String;
		private var _file_id:String;
		private var _type:String;
		private var _screenId:String;
		private var _configXML:XML;
		
//		public var conditions:ArrayCollection;
//		public var conditionMatchAll:Boolean;
		public var hash:String;
		

		public function get configXML():XML {
			return this._configXML;
		}

		public function get url():String {
			return this._url;
		}
		
		public function get file():String {
			return this._file;
		}
		
		public function get id():String {
			if(this._file_id == "") {
				return "0";
			}
			
			return this._file_id;
		}
		
		public function get type():String {
			return this._type;
		}

		public function set file(val:String):void {
			this._file = val;
		}

		public function PlaylistObject(playlistItemXML:XML, screenId:String):void {
			this._configXML = playlistItemXML;
			this._url = playlistItemXML.toString();
			this._type = playlistItemXML.@type;
			this._file_id = playlistItemXML.@mediaid;
			this._file = this._url.substr(this._url.lastIndexOf("/")+1);
			this._screenId = screenId;
//			this.conditions = new ArrayCollection;
//			this.conditionMatchAll = false;
			this.hash = "";
		}

		public function get available():Boolean {
			return FileIO.fileExists(this._file, this._screenId);
		}
	}
}