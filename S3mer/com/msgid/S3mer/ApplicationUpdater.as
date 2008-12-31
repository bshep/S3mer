package com.msgid.S3mer
{
	import flash.desktop.Updater;
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	import flash.utils.Timer;
	
	public class ApplicationUpdater
	{
		private var _remoteVersion:Number;
		private var _timer:Timer;
		
		private var _newVersion:String;
		
		public function ApplicationUpdater()
			{
			super();
			
			this._timer = new Timer(60*60*1000);
			this._timer.addEventListener(TimerEvent.TIMER,OnTimer,false,0,true);
		}

		public function start():void {
			checkupdate();
			
			this._timer.start();
		}
		
		private function OnTimer(e:TimerEvent):void {
			checkupdate();
		}
		
		public function checkupdate():void {
			var _loader:URLLoader = new URLLoader();
			
			_loader.addEventListener(Event.COMPLETE,checkupdate_stage2,false,0,true);
			_loader.dataFormat = URLLoaderDataFormat.TEXT;
			
			_loader.load(new URLRequest(ApplicationSettings.URL_UPDATE));
		}
		
		private function checkupdate_stage2(e:Event):void {
			var versionXML:XML;
			var localVersion:Number;
			
//			Logger.addEvent("UPDATER: local = " + ApplicationSettings.getVersion() + " remote = " + versionXML.version);
			
			try {
				versionXML  = new XML(URLLoader(e.target).data);
				
				localVersion =  parseFloat(ApplicationSettings.getVersion());
				_remoteVersion = parseFloat(versionXML.version);
				
				if ( localVersion < _remoteVersion ) {
					this._newVersion = versionXML.version.toString();
					
					Logger.addEvent("UPDATER: New version found local = " + localVersion + " remote = " + _remoteVersion);
					if (ApplicationSettings.getValue("updatesEnabled") != "false") {
						Logger.addEvent("UPDATER: Updating...");
						performUpdate(ApplicationSettings.URL_MEDIA + versionXML.url);
					} else {
						if (versionXML.force == "true") {
							Logger.addEvent("UPDATER: Updates Disabled. Update forced. Updating...");							
							performUpdate(ApplicationSettings.URL_MEDIA + versionXML.url);
						} else {
							Logger.addEvent("UPDATER: Updates Disabled");
						}						
					}
				} else {
					Logger.addEvent("UPDATER: Latest Version Installed");
				}
				
			} catch (e:Error) {
				Logger.addEvent("UPDATER: Error while parsing UPDATE XML");
			}
		}
		
		private function performUpdate(updateURL:String):void {
			var _loader:URLLoader = new URLLoader();
			
			_loader.addEventListener(Event.COMPLETE,performUpdate_stage2,false,0,true);
			_loader.dataFormat = URLLoaderDataFormat.BINARY;
			
			_loader.load(new URLRequest(updateURL));
		}

		private function performUpdate_stage2(e:Event):void {
			var myStorageStream:FileStream = new FileStream();
			var myStorageFile:File = new File(FileIO.storePath("update.air"));

			myStorageStream.open(myStorageFile,FileMode.WRITE);
			
			myStorageStream.writeBytes(URLLoader(e.target).data,0,URLLoader(e.target).bytesLoaded);
			
			myStorageStream.close();
			
			try {
				(new Updater).update(myStorageFile,this._newVersion);
			} catch(e:Error) {
				
			}
		}


	}
}