package com.s3mer.util

{
	import com.s3mer.util.managers.LoggerManager;
	
	import flash.desktop.NativeApplication;
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	
	import mx.utils.Base64Decoder;
	import mx.utils.Base64Encoder;
	
	public class ApplicationSettings
	{
		private static var _AppSettingsInstance:ApplicationSettings;	
//		public static var SERVER:String = "localhost/s3mer/app";
		public static var SERVER:String = "www.s3mer.com/app";
		//Static Vars
		public static var URL_LOGIN:String = "https://" + SERVER + "/loginplayer.php";
		public static var URL_LOGOUT:String = "http://" + SERVER + "/logout.php";
		public static var URL_PLAYER_LIST:String = "https://" + SERVER + "/playergetdata.php";
		public static var URL_USER_INFO:String = "https://" + SERVER + "/playergetdata.php";
		public static var URL_STATUS:String = "https://" + SERVER + "/checklogged.php";

		public static var URL_MEDIA:String = "http://media1.s3mer.com.s3.amazonaws.com/";
//		public static var URL_MEDIA:String = "http://dhr770hhem8f8.cloudfront.net/";
		public static var URL_HEARTBEAT:String = "http://" + SERVER + "/heartbeat.php";
		public static var URL_CONFIG:String = "https://" + SERVER + "/getxmltest.php";
//		public static var URL_CONFIG_DEBUG:String = "https://" + SERVER + "/getxmltest_debug.php";
		public static var URL_RUNLOG:String = "http://" + SERVER + "/asrunlog.php";
		public static var URL_UPDATE:String = "http://" + SERVER + "/checkversion.php?version=1";

		private static const SETTINGS_KEY:String = "oewiur0830nf,mnv098-39n kchj098-932n,mcn-09";
		public static const CONFIG_KEY:String = "disuri301293rfbc,nWou1309rjfbckvjh085-4cnkn091()*&*%&%$()";

		private var _settings:XML;
		private var _settingsFile:File;
		
		private var _loaded:Boolean;
		
		private static function setupInstance():void {
			if (_AppSettingsInstance == null) {
				_AppSettingsInstance = new ApplicationSettings();
			}
		}
		
		public function ApplicationSettings() {
			this._settingsFile = File.applicationStorageDirectory.resolvePath("settings.xml");
			this._loaded = false;
		}
		
		public function _save():Boolean {
			var settingsRW:FileStream = new FileStream;
			var settings:String;
			var b64enc:Base64Encoder = new Base64Encoder();
			
			settings = this._settings.toXMLString();
			settings = FileIO.simpleCrypt(settings,ApplicationSettings.SETTINGS_KEY);
			b64enc.encode(settings);
			settings = b64enc.toString();
			
			try {
				settingsRW.open(this._settingsFile,FileMode.WRITE);
				settingsRW.writeUTFBytes(settings);
				settingsRW.close();
			} catch(e:Error) {
				LoggerManager.addEvent("ApplicationSettings.as _save: _AppSettingsInstance._save: " + e.message);
				return false;
			}
			
			return true;
		}
		
		public function _load(reload:Boolean):Boolean {
			var settingsRead:FileStream = new FileStream;
			var settings:String;
			var b64dec:Base64Decoder = new Base64Decoder();
			
			if (this._loaded == true && reload == false ) {
				return true;
			}

			try {
				settingsRead.open(this._settingsFile,FileMode.UPDATE);
				
				settings = settingsRead.readUTFBytes(settingsRead.bytesAvailable);
				settingsRead.close();
				
				if (settings.charAt(0) != "<" ) {
					b64dec.decode(settings);
					settings = b64dec.toByteArray().toString();
					
					settings = FileIO.simpleCrypt(settings,ApplicationSettings.SETTINGS_KEY);					
					this._settings = new XML(settings);
				} else {
					this._settings = new XML(settings);
					this._save();
				}
								
			} catch(e:Error) {
				LoggerManager.addEvent("ApplicationSettings.as _load: _AppSettingsInstance._load: " + e.message);
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

			if (elem[0] == null) {
				return defValue;
			}			
			return elem[0];
		}
					
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