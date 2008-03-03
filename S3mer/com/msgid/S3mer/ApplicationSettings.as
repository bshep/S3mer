package com.msgid.S3mer
{
	import flash.desktop.NativeApplication;
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	
	public class ApplicationSettings
	{
		private static var _AppSettingsInstance:ApplicationSettings;	
		private var _settings:XML;
		private var _settingsFile:File;
		
		private var _loaded:Boolean;
		
		private static function setupInstance():void {
			if (_AppSettingsInstance == null) {
				_AppSettingsInstance = new ApplicationSettings();
			}
		}
		
		public function ApplicationSettings() {
			this._settingsFile = new File(FileIO.mediaPath("settings.xml"));
			this._loaded = false;
		}
		
		public function _save():Boolean {
			var settingsRW:FileStream = new FileStream;

			try {
				settingsRW.open(this._settingsFile,FileMode.WRITE);
				settingsRW.writeUTFBytes(this._settings.toXMLString());
				settingsRW.close();
			} catch(e:Error) {
				Logger.addEvent("_AppSettingsInstance._save: " + e.message);
				return false;
			}
			
			return true;
		}
		
		public function _load(reload:Boolean):Boolean {
			var settingsRead:FileStream = new FileStream;
			
			if (this._loaded == true && reload == false ) {
				return true;
			}

			try {
				settingsRead.open(this._settingsFile,FileMode.UPDATE);
				this._settings = new XML(settingsRead.readUTFBytes(settingsRead.bytesAvailable));
				settingsRead.close();
			} catch(e:Error) {
				Logger.addEvent("_AppSettingsInstance._load: " + e.message);
				return false;
			}
			
			if (this._settings.toXMLString() == "") {
				this._settings = <settings><application><user><username/><password/></user></application></settings>;
			}

			
			return true;
		}
		
		public function _setValue(name:String, value:String):Boolean {
			var namePath:Array = name.split(".");
			var elem:XMLList;
			
			elem = this._settings.elements();
			for( var a:int = 0; a < namePath.length; a++ ) {
				elem = elem.elements(namePath[a]);	
			}
			elem[0] = value;
			
			return true;
		}

		public function _getValue(name:String, defValue:String = ""):String {
			var namePath:Array = name.split(".");
			var elem:XMLList;
			
			elem = this._settings.elements();
			for( var a:int = 0; a < namePath.length; a++ ) {
				elem = elem.child(namePath[a]);	
			}

			return elem[0];
		}
		
		//Static Vars
		public static const URL_LOGIN:String = "http://www.s3mer.com/loginplayer.php";
		public static const URL_LOGOUT:String = "http://www.s3mer.com/logout.php";
		public static const URL_PLAYER_LIST:String = "http://www.s3mer.com/playergetdata.php";
		public static const URL_USER_INFO:String = "http://www.s3mer.com/playergetdata.php";
		public static const URL_STATUS:String = "http://www.s3mer.com/checklogged.php";


		public static const URL_MEDIA:String = "http://www.s3mer.com/";
		public static const URL_HEARTBEAT:String = "http://www.s3mer.com/heartbeat.php";
		public static const URL_CONFIG:String = "http://www.s3mer.com/getxml.php";
		public static const URL_UPDATE:String = "http://www.s3mer.com/media/app/checkversion.php";
		
		

		
		//Static Functions
		public static function save():Boolean {
			setupInstance();
			return _AppSettingsInstance._save();
		}
		
		public static function load(reload:Boolean = false):Boolean {
			setupInstance();
			
			return _AppSettingsInstance._load(reload);
		}
		
		public static function setValue(name:String, value:String):Boolean {
			setupInstance();
			
			return _AppSettingsInstance._setValue(name, value);
		} 

		public static function getValue(name:String, defValue:String = ""):String {
			setupInstance();
			
			return _AppSettingsInstance._getValue(name, defValue);
		}
		
		public static function getVersion():String {
			var appXML:XML = NativeApplication.nativeApplication.applicationDescriptor;
			var air:Namespace = appXML.namespaceDeclarations()[0];
			return appXML.air::version;
		}
		
	}
}