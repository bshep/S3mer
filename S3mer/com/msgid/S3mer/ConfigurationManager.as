package com.msgid.S3mer
{
	import flash.display.DisplayObject;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.events.TimerEvent;
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	import flash.utils.ByteArray;
	import flash.utils.Timer;
	
	import mx.collections.ArrayCollection;
	import mx.core.Container;
	import mx.events.EffectEvent;
	import mx.utils.Base64Decoder;
    
	public class ConfigurationManager extends EventDispatcher
	{
		private var _showsCur:ArrayCollection;
		private var _schedulesCur:ArrayCollection;
		private var _playlistsCur:ArrayCollection;
		
		private var _showsNew:ArrayCollection;
		private var _schedulesNew:ArrayCollection;
		private var _playlistsNew:ArrayCollection;
		
		private var _container:Container;

		private var _reloadConfigTimer:Timer;
		private var _heartbeatTimer:Timer;
		private var _expirationDate:Date;
		private var _expired:Boolean;
		private var _updatingConfigguration:Boolean;
		
		
		private var _config:XML;
		
		private static var _downloadQueue:DownloadQueue;
		
		// BaseURL for media, can be overwritten by config file
		private var _mediaURL:String = "";//ApplicationSettings.URL_MEDIA;
		
		private var _hearbeatURL:String = ApplicationSettings.URL_HEARTBEAT + "?playerid=";
		
		// Configuration URL
		private var _configURL:String = ApplicationSettings.URL_CONFIG;
	
		private var _stopped:Boolean;
			
		public function ConfigurationManager(container:Container) {
			this._showsNew = new ArrayCollection();
			this._schedulesNew = new ArrayCollection();
			this._playlistsNew = new ArrayCollection();
			
			this._showsCur = new ArrayCollection();
			this._schedulesCur = new ArrayCollection();
			this._playlistsCur = new ArrayCollection();
			
			this._container = container;
			this._expired = false;
			this._updatingConfigguration = false;
			
			if (_downloadQueue == null) {
				_downloadQueue = new DownloadQueue();
				
				PodcastManager.setQueue(_downloadQueue);
			}
			
			_downloadQueue.addEventListener(DownloaderEvent.PROGRESS,OnDownloadProgress,false,0,true);
			_downloadQueue.addEventListener(DownloaderEvent.PARTIAL_COMPLETE,OnDownloadFileComplete,false,0,true);
			_downloadQueue.addEventListener(DownloaderEvent.COMPLETE,OnDownloadComplete,false,0,true);

			if (_heartbeatTimer == null && S3merWindow(this._container).screenId == 0) {
				_heartbeatTimer = new Timer(1000);
				_heartbeatTimer.addEventListener(TimerEvent.TIMER, OnHeartbeatTimer,false,0,true);
				_heartbeatTimer.start();
			}
			
			if (_reloadConfigTimer == null ) {
				_reloadConfigTimer = new Timer(5*60*1000);
				_reloadConfigTimer.addEventListener(TimerEvent.TIMER, OnReloadTimer);
			}
			
			this._configURL = ApplicationSettings.getValue("config.configurl",this._configURL);
			this._mediaURL = ApplicationSettings.getValue("config.mediaurl",this._mediaURL);
			this._expirationDate = new Date(ApplicationSettings.getValue("config.expiration","0"));
			
			if (isExpired()) {
				this._expired = true;
			}
			
			
			this._stopped = true;
		}
		
		private function isExpired():Boolean {
			var expirationDate:Date;
			var dateNow:Date = new Date();
			var gracePeriod:Number = new Number(3); //3 days grace period... will read this from config eventually.
			
			expirationDate = new Date(this._expirationDate.valueOf() + 1000*60*60*24*gracePeriod);
			
			if( dateNow.valueOf() > expirationDate.valueOf() ) {
				return true;
			} else {
				return false;
			}
			
		}
		
		private function OnHeartbeatTimer(e:TimerEvent):void {
			var _loader:URLLoader = new URLLoader();
			var _loaderReq:URLRequest;
			_heartbeatTimer.stop();
			
			
			
			_loader.addEventListener(Event.COMPLETE,OnHeartbeat_stage2,false,0,true);
			_loader.addEventListener(IOErrorEvent.IO_ERROR,OnIOError,false,0,true);
			_loader.dataFormat = URLLoaderDataFormat.TEXT;
			try {
				var screenId:int = S3merWindow(this._container).screenId;
				
				_loaderReq = new URLRequest(_hearbeatURL + ApplicationSettings.getValue("screen"+ screenId +".channel.id",""));
//				_loaderReq.setLoginCredentials("development","mils0ft");
				
				_loader.load(_loaderReq);
			} catch(e:Error) {
				Logger.addEvent("HEARTBEAT FAILED");
			}
		}
		
		private function OnReloadTimer(e:TimerEvent):void {
			this._reloadConfigTimer.stop();
			
			this.updateConfiguration();
		}
		
		private function OnIOError(e:IOErrorEvent):void {
			Logger.addEvent("HEARTBEAT FAILED: Probably not connected");
			if(this._heartbeatTimer != null) {
				this._heartbeatTimer.start();
			}
		}
		 
		
		private function OnHeartbeat_stage2(e:Event):void {
			var response:String;
			
			response = e.target.data;
			
//			Logger.addEvent("Heartbeat Response: " + response);
			
			switch(response) {
				case 'U':
					//Unknown
					break;
				case 'R':
					//Refresh
					this._expired = true;
					this.updateConfiguration();
					break;
				case 'O':
					//OK
//					this._lastSuccessfulHeartbeat = new Date();
					ApplicationSettings.setValue("config.heartbeat.ticks", (new Date()).valueOf().toString());
					break;
				case '?':
					//Other Error
					break;
			}
			
			
			if (isExpired() && this._expired == false) {
				this._expired = true;
				this.updateConfiguration();
			}
			
			_heartbeatTimer.start();
		}

		private function OnDownloadProgress(e:DownloaderEvent):void {
			this.dispatchEvent(e);
		}

		private function OnDownloadFileComplete(e:DownloaderEvent):void {
			// A file completed, check any layouts that are now 
			// accessible and determine if we should switch
			
			this.dispatchEvent(e);
		}
		private function OnDownloadComplete(e:DownloaderEvent):void {
			// All downloads complete
			// Sanity check, all media should be available?
		}
		
		// Called whenever the configuration file was updated
		public function updateConfiguration():void {
			
			if (this._updatingConfigguration) {
				return;
			}
			
			this._updatingConfigguration = true;
			
			var screenId:int = S3merWindow(this._container).screenId;

			_downloadQueue.addEventListener(DownloaderEvent.COMPLETE,updateConfiguration_step2,false,0,true)
			_downloadQueue.addEventListener(DownloaderEvent.ERROR,onDownloadError,false,0,true)

			Logger.addEvent("ConfigurationManager::updateConfiguration: screenId = " + ApplicationSettings.getValue("screen"+ screenId +".channel.id",""));
//			_downloadQueue.addItem(getChannelUrl(ApplicationSettings.getValue("screen"+ screenId +".channel.id","")), "", "config" + screenId + ".xml", false,true);
			_downloadQueue.addItem(getChannelUrl(ApplicationSettings.getValue("screen"+ screenId +".channel.id","")), "", "config" + screenId + ".xml", false,true);

			_downloadQueue.start();
		}
		
		private function getChannelUrl(channelNumber:String):String {
			return this._configURL + "?playerid=" + channelNumber;
		}
		
		private function onDownloadError(e:Event):void {
			Logger.addEvent("Error with download" + (e.target).toString());
			
			if( this._expired == true || isExpired()) {
				this.stop();
				
				showNotConnected();
				
				// Set the reload timer to 1 min intervals
				this._reloadConfigTimer.delay = 60*1000;
				this._reloadConfigTimer.start();
				this._updatingConfigguration = false;
				return;				
			}
			
			//Check if we already have a config file, if so then do what it says
			// otherwise, display not comm-error message
			
			if (new File(FileIO.mediaPath("config" + S3merWindow(this._container).screenId + ".xml")).exists) {
				updateConfiguration_step2_5();
			} else {
				this.stop();
				
				showNotConnected();
				
				// Set the reload timer to 1 min intervals
				this._reloadConfigTimer.delay = 60*1000;
				this._reloadConfigTimer.start();
				this._updatingConfigguration = false;
			}
			
			
		}
		
		private var showNotConnnected:SmoothImage;
		
		private function showNotConnected():void {
			var myAppObject:Container = this._container;
			
			if( showNotConnnected == null ) {
				showNotConnnected = new SmoothImage();
				showNotConnnected.source = new File(FileIO.assetsPath()).resolvePath("internetConnection.swf").nativePath;
				showNotConnnected.x = 0;
				showNotConnnected.y = 0;
				showNotConnnected.width = myAppObject.width;
				showNotConnnected.height = myAppObject.height;
			}

			if (showNotConnnected.parent != myAppObject) {
				myAppObject.addChild(showNotConnnected);
			}
		}
		
		private function hideNotConnected():void {
			var myAppObject:Container = this._container;
			
			if( showNotConnnected == null ) {
				showNotConnnected = new SmoothImage();
				showNotConnnected.source = new File(FileIO.assetsPath()).resolvePath("internetConnection.swf").nativePath;
				showNotConnnected.x = 0;
				showNotConnnected.y = 0;
				showNotConnnected.width = myAppObject.width;
				showNotConnnected.height = myAppObject.height;
			}

			if (showNotConnnected.parent == myAppObject) {
				myAppObject.removeChild(showNotConnnected);
			}
		}

		private function updateConfiguration_step2(e:DownloaderEvent):void {
			updateConfiguration_step2_5();
		}

		
		private function updateConfiguration_step2_5():void {
			var configFile:File = new File(FileIO.mediaPath("config" + S3merWindow(this._container).screenId + ".xml"));
			var configReader:FileStream;
			var config:XML;
			
			_downloadQueue.removeEventListener(DownloaderEvent.ERROR,onDownloadError);
			_downloadQueue.removeEventListener(DownloaderEvent.COMPLETE,updateConfiguration_step2);
			
			configReader = new FileStream;
			
			try {
				
				configReader.open(configFile,FileMode.READ);
			} catch(e:Error) {
				Logger.addEvent("ConfigurationManager: " + e.message);
			}
			
			try {
				// Ensure the new configuration file is valid before we replace the copy in memory
				config = new XML(configReader.readUTFBytes(configReader.bytesAvailable));
				
				config = decryptConfig(config);
				
				var newConfigUrl:String = config.config.configurl;
				if (newConfigUrl != "" && newConfigUrl != this._configURL) {
					Logger.addEvent("config.channel.config.configurl: " + config.config.configurl)
					this._configURL = newConfigUrl;
					
					ApplicationSettings.setValue("config.configurl",this._configURL);
					ApplicationSettings.save();
					this._updatingConfigguration = false;
					this.updateConfiguration();
					return;
				}
				
				var newMediaUrl:String = config.config.mediaurl;
				if (newMediaUrl != "" && newMediaUrl != this._mediaURL) {
					Logger.addEvent("config.channel.config.mediaurl: " + config.config.mediaurl)
					this._mediaURL = newMediaUrl;
					ApplicationSettings.setValue("config.mediaurl",this._mediaURL);
				}
				
				
				var expirationDate:String = config.config.expirationDate;
				if (expirationDate != "") {
					this._expirationDate = new Date(Number(expirationDate)*1000);
					ApplicationSettings.setValue("config.expiration",this._expirationDate.valueOf().toString());
				}
				
				ApplicationSettings.save();
				
				this._config = config;
				
				this.hideNotConnected();
				this._expired = false;
				
				// Clear previous config
				this._showsNew = new ArrayCollection;
				this._schedulesNew = new ArrayCollection;
				this._playlistsNew = new ArrayCollection;

				parseSchedules(); // No dependencies so do this first
				
				parsePlaylists(); // Playlists may contain Schedules so do this after Schedules
				
				parseShows(); // Shows may contain Playlists and Schedules so this is last
				
				_downloadQueue.addEventListener(DownloaderEvent.COMPLETE,updateConfiguration_step3,false,0,true);
				initiateDownloads();
				
				//TODO: show some loading graphics while updating...
			} catch(e:Error) {
				Logger.addEvent("ConfigurationManager: " + e.message);
			}
		}
		
		private function decryptConfig(_config:XML):XML {
			var b64dec:Base64Decoder = new Base64Decoder();
			var decoded_ba:ByteArray;
			var decoded:String;
			var md5:String;
			var key:String = "disuri301293rfbc,nWou1309rjfbckvjh085-4cnkn091()*&*%&%$()";

			b64dec.decode(_config.content.toString());
			
			decoded_ba = b64dec.toByteArray();
			decoded = decoded_ba.toString();
			
			b64dec.decode(_config.timestamp);
			decoded_ba = b64dec.toByteArray();
			
			md5 = decoded_ba.toString();
			
			key = FileIO.mutateKey(key,md5);
			
			decoded = FileIO.simpleCrypt(decoded,key);		
			
			return new XML(decoded);
		}
		
		
		private function updateConfiguration_step3(e:DownloaderEvent):void {
			_downloadQueue.removeEventListener(DownloaderEvent.COMPLETE,updateConfiguration_step3);
			
			this.dispatchEvent(new ConfigurationEvent(ConfigurationEvent.UPDATED));
			cleanupMedia();
			
			//Set config reload to 1 hr
			this._updatingConfigguration = false;
			this._reloadConfigTimer.delay = 60*60*1000;
			this._reloadConfigTimer.start();
		}
		
		public function play():void {
			this._stopped = false;
			this.switchShow("");		
		}
		
		public function stop():void {
			var myShowObject:Show;

			myShowObject = Show(this._container.getChildByName("currentShow"));
			
			for each( var _showObj:DisplayObject in this._container.getChildren()) {
				this._container.removeChild(_showObj);
			}
			
			this._stopped = true;
			
			if (myShowObject != null) {
				myShowObject.stop(true);
			}
		}
		
		public function reset():void {
			var myShowObject:Show 

			
			for each( var _screen:Container in this._container) {
				myShowObject = Show(_screen.getChildByName("currentShow"));
			
				this.stop();
				
				if (myShowObject != null) {
					_screen.removeChild(myShowObject);
				}
			}

		}
		
		public function stopDownloads():void {
			_downloadQueue.stop();
		}
		
		public function setSmoothing(active:Boolean):void {
			var myShowObject:Show;
			
			for each( var _screen:Container in this._container) {
				myShowObject = Show(_screen.getChildByName("currentShow"));
				
				if (myShowObject != null) {
					myShowObject.setSmoothing(active);
				}
			}
		}
		
		private function initiateDownloads():void {
			for each (var _playlist:Playlist in this._playlistsNew) {
				//Run through all playlist items and determine which ones we need to download,
				//this also creates all podcast items.
				for each( var _playlistObj:PlaylistObject in _playlist.pendingFiles ) {
					_downloadQueue.addItem(_playlistObj.url, _playlistObj.hash);
				}
			}
			

			//Setup an event listener to get notified when the PodcastManager is done loading podcasts
			PodcastManager.podcastManager.addEventListener(Event.COMPLETE,initiateDownloads_step2);
			//Tell all podcast items to queue downloads at the end of the queue
			PodcastManager.setupDownloads();
			
		}
		
		private function cleanupMedia():void {
			var mediaFolder:File = new File(FileIO.storePath()).resolvePath("media");
			var configReg:RegExp = /config[0-9]\.xml/;
			
			for each( var _file:File in mediaFolder.getDirectoryListing() ) {
				if (_file.name != "settings.xml" && _file.name.search(configReg) == -1) {
					if (!fileExistsInPlaylist(_file)) {
						_file.deleteFileAsync();
					}
				}
			}

		}
		
		private function fileExistsInPlaylist(myFile:File):Boolean {
			
			for each ( var _pl:Playlist in this._playlistsNew ) {
				for each (var _plo:PlaylistObject in _pl._items) {
					if ( _plo.file == myFile.name ) {
						return true;
					}
				}
			}
			
			return false;
		}
		
		private function initiateDownloads_step2(e:Event):void {
			//Start the queue
			_downloadQueue.start();
			
		}
		
		private function parsePlaylists():void {
			var newPlaylist:Playlist;
			var newPlaylistObj:PlaylistObject;
			
			for each (var playlistXML:XML in _config.playlist) {
				newPlaylist = new Playlist();
				
				Logger.addEvent("Playlist id: " + playlistXML.@id);
				newPlaylist.id = playlistXML.@id;
								
				for each (var playlistitemXML:XML in playlistXML.playlistitem) {
					Logger.addEvent("- element url: " + playlistitemXML);
					
					newPlaylistObj = new PlaylistObject(playlistitemXML);
					
					if (playlistitemXML.@conditionmatch.toString() == "all") {
						newPlaylistObj.conditionMatchAll = true;
					} else {
						newPlaylistObj.conditionMatchAll = false;
					}
					
					newPlaylistObj.hash = playlistitemXML.@hash;
					
					for each (var conditionXML:XML in playlistitemXML.@condition) {
						newPlaylistObj.conditions.addItem(conditionXML);
					}
					
					newPlaylist.addObj(newPlaylistObj);
				}
				if(newPlaylist.length > 0 ) {
					this._playlistsNew.addItem(newPlaylist);
				}
			}
		}
	
		private function parseShows():void {
			for each (var showXML:XML in _config.show) {
				var newShow:Show = new Show();
				
				Logger.addEvent("Show id: " + showXML.@id);
				newShow.id = showXML.@id;
				newShow.visible = true;
				newShow.configuredWidth = showXML.@width;
				newShow.configuredHeight = showXML.@height;	

				newShow.x = 0;
				newShow.y = 0;
				newShow.width = this._container.width;
				newShow.height = this._container.height;
							
				newShow.resizeX = newShow.width/newShow.configuredWidth;
				newShow.resizeY = newShow.height/newShow.configuredHeight;

				try {
					for each (var regionXML:XML in showXML.region) {
						var hasAudio:String;
						var audioPan:String;
						
						hasAudio = ApplicationSettings.getValue("screen"+ S3merWindow(this._container).screenId +".audio.enabled","");
						audioPan = ApplicationSettings.getValue("screen"+ S3merWindow(this._container).screenId +".audio.pan","C");
						
						Logger.addEvent("- region id: " + regionXML.@id + " type: " + regionXML.@type);
						
						newShow.addObject(regionXML, hasAudio, audioPan);
						
						parseShow_addPlaylists(regionXML, newShow);
						parseShow_addSchedules(regionXML, newShow);
					}
				} catch (e:Error) {
					Logger.addEvent("Error @ " + e.message + e.getStackTrace());
				}		
				
				this._showsNew.addItem(newShow);
			}
		}

		private function parseShow_addSchedules(regionXML:XML, show:Show):void {
			// TODO: Do something useful
		}

		private function parseShow_addPlaylists(regionXML:XML, show:Show):void {
			var tmpShowObject:ShowObject;
			
			tmpShowObject = show.getObjectById(regionXML.@id);
			
			if (tmpShowObject == null) {
				Logger.addEvent("ERROR: could not find object with id = " + regionXML.@id);
				return;
			}
			
			for each (var playlistXML:XML in regionXML.playlist) {
				var tmpPlaylist:Playlist = this.getPlaylistById(this._playlistsNew, playlistXML.@id);
				
				if (tmpPlaylist == null) {
					Logger.addEvent("ERROR: no playlist defined with id = " + playlistXML.@id);
					continue;
				}
				
				Logger.addEvent("- adding reference to playlist id = " + playlistXML.@id);
				tmpShowObject.addPlaylist(tmpPlaylist);
			}

		}
	
		
		public function switchShow(newShowId:String):void {
			//TODO: Check this and make sure it works properly
			
			var newShowObject:Show
			var oldShowObject:Show;
			
			if( this._stopped == true ) {
				return;
			}
			
			if( this._showsNew.length == 0 ) {
				oldShowObject = Show(this._container.getChildByName("currentShow"));
				
				if( oldShowObject != null ) {
					oldShowObject.stop(true);
					this._container.removeChild(oldShowObject);
				}
				return;
			}
			
			if(newShowId == "") {
				newShowObject = Show(this._showsNew.getItemAt(0));
			} else {
		 		newShowObject = getShowById(this._showsNew, newShowId);
			}

			if (newShowObject != null) {
				oldShowObject = Show(this._container.getChildByName("currentShow"));
				newShowObject.fadeOut(0);
				newShowObject.name = "currentShow";
				this._container.addChildAt(newShowObject,0);
				S3merWindow(this._container).show = newShowObject;
				
				if (oldShowObject != null) {
					oldShowObject.name = "oldShow";
					oldShowObject.addEventListener(EffectEvent.EFFECT_END,switchShow_stage2,false,0,true);
					oldShowObject.fadeOut(1000);
				} else {
					newShowObject.addEventListener(EffectEvent.EFFECT_END,switchShow_stage3,false,0,true);
					newShowObject.play();			
					newShowObject.fadeIn(0);
					
					
				}
			}
			
		}
		
		//Remove old Show, begin fadein of new layout
		private function switchShow_stage2(e:EffectEvent):void {
			var oldShowObject:Show = Show(this._container.getChildByName("oldShow"));
			var currShowObject:Show = Show(this._container.getChildByName("currentShow"));
			oldShowObject.stop(true);
			oldShowObject.removeEventListener(EffectEvent.EFFECT_END,switchShow_stage2);
			currShowObject.addEventListener(EffectEvent.EFFECT_END,switchShow_stage3,false,0,true);
			this._container.removeChild(oldShowObject);
			
						
			if( this._stopped == true ) {
				this._container.removeChild(currShowObject);
			} else {
				currShowObject.play();			
				currShowObject.fadeIn();
				
			}

		}

		//Begin processing new Show
		private function switchShow_stage3(e:EffectEvent):void {
			var currShowObject:Show = Show(this._container.getChildByName("currentShow"));
			
			if( currShowObject == null ) {
				return;
			}
			
			currShowObject.removeEventListener(EffectEvent.EFFECT_END,switchShow_stage3);
	
			if( this._stopped == true ) {
				currShowObject.stop(true);
				this._container.removeChild(currShowObject);
			}
		
			
			this._showsCur = this._showsNew;
			this._playlistsCur = this._playlistsNew;
			this._schedulesCur = this._schedulesNew;
		}

		
		private function getShowById(obj:ArrayCollection,id:String):Show {
			for each (var _each:Show in obj) {
				if( _each.id == id ) {
					return _each;
				}
			}
			
			return null;
		}

		private function getPlaylistById(obj:ArrayCollection,id:String):Playlist {
			for each (var _each:Playlist in obj) {
				if( _each.id == id ) {
					return _each;
				}
			}
			
			return null;
		}

		private function getScheduleById(obj:ArrayCollection,id:String):Schedule {
			for each (var _each:Schedule in obj) {
				if( _each.id == id ) {
					return _each;
				}
			}
			
			return null;
		}

		private function parseSchedules():void {
			
		}

		public function resize( newHeight:int, newWidth:int):void {
			
			for each ( var show:Show in this._showsNew ) {
				show.width = newWidth;
				show.height = newHeight;
				
				show.resize();
			}
		}

		public function muteAudio(mute:Boolean):void {
			for each( var _showObj:DisplayObject in this._container.getChildren()) {
				if ( _showObj is Show) {
					(_showObj as Show).muteAudio(mute);
				}

			}
		}


	}
}