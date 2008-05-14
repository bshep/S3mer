package com.msgid.S3mer
{
//	import com.adobe.crypto.SHA1;
	
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	import flash.system.Capabilities;
	import flash.utils.ByteArray;
	
	
	public class FileIO
	{
		private static const ENABLE_HASH:Boolean = false;
		private static var _log:Logger = new Logger;	
		
		public static function isMacOs():Boolean {
//			Logger.addEvent("OS is: " + Capabilities.os);
			if (Capabilities.os.search("Mac") >= 0) {
				return true;
			} else {
				return false;
			}
		}
		
		public static function isWindows():Boolean {
//			Logger.addEvent("OS is: " + Capabilities.os);
			if (Capabilities.os.search("Windows") >= 0) {
				return true;
			} else {
				return false;
			}
		}
		
		public static function isLinux():Boolean {
//			Logger.addEvent("OS is: " + Capabilities.os);
			return false;
		}
		
		public static function getOs():String {
			if (isMacOs()) {
				return "MAC";
			} else if (isWindows()) {
				return "WINDOWS";
			} else if (isLinux()) {
				return "LINUX";
			} else {
				return "UNKNOWN";
			}
		}
		
		public static function storePath():String {
			return File.applicationStorageDirectory.nativePath;
		}
		
		public static function appPath():String {
			Logger.addEvent("Resource Directory: " + File.applicationDirectory.nativePath);
			return File.applicationDirectory.nativePath;
		}
		
		public static function assetsPath():String {
			var _appPath:File = new File(appPath());
			
			if (_appPath.resolvePath("assets").exists) {
				return _appPath.resolvePath("assets").nativePath;
			}
			
			if (_appPath.resolvePath("..").resolvePath("assets").exists) {
				return _appPath.resolvePath("..").resolvePath("assets").nativePath;	
			}
			
			return null;
		}
		
		public static function mediaPath(fileName:String):String {
			var tmpFile:File = File.applicationStorageDirectory.resolvePath("media").resolvePath(fileName);
			
			var realPath:String = tmpFile.nativePath;
			
			if(isMacOs()) {
				realPath = "file://" + realPath;
			}
			
			return realPath;
		}
		
		public static function Url2Path(url:String):String {
			var tmpFile:File = File.applicationStorageDirectory.resolvePath("media").resolvePath(Url2Filename(url));
			return tmpFile.nativePath;
		}
		
		public static function Url2Filename(url:String):String {
			return url.substr(url.lastIndexOf("/")+1);
		}

		
		
		public  static function fileExists(fileName:String, md5:String = ""):Boolean {
			var myStorageDir:File = File.applicationStorageDirectory;
			
			if (!myStorageDir.exists) {
				return false;
			}
			
			if (!myStorageDir.resolvePath("media").exists) {
				return false;
			}
			
			if (!myStorageDir.resolvePath("media").resolvePath(fileName).exists) {
				return false;
			}
			
			
//			if (ENABLE_HASH) {
//				var myStream:FileStream = new FileStream()
//				var data:ByteArray;
//				myStream.open(myStorageDir.resolvePath("media").resolvePath(fileName),FileMode.READ);
//				
//				data = new ByteArray();
//				myStream.readBytes(data);
//				var myMD5Hash:String = com.adobe.crypto.SHA1.hashBytes(data);
//				
//				myStream.close();
//				
//				Logger.addEvent("FileName: " +  fileName);
//				Logger.addEvent("SHA1 Hash(args): " +  md5);
//				Logger.addEvent("SHA1 Hash(calc): " +  myMD5Hash);
//				
//				if (md5 != "") {
//					if (md5 == myMD5Hash) {
//						return true;
//					} else {
//						return false;
//					}
//				}
//			}
			
			return true;
		}
		
		public static function mutateKey( key:String, md5:String ):String {
			return simpleCrypt(key,md5);
		}
		
		public static function simpleCrypt( data:String, key:String ):String {
			var ret:String = "";
			
			for(var i:int = 0; i < data.length; i++) {
				ret += String.fromCharCode(data.charCodeAt(i) ^ key.charCodeAt(i % key.length));
			}
			
			return ret;
		}

	}
}