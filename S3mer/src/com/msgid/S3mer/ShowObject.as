package com.msgid.S3mer
{
	import com.msgid.S3mer.LocalDatabase.LocalDatabase;
	
	import flash.display.DisplayObject;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	
	import mx.collections.ArrayCollection;
	import mx.controls.HTML;
	import mx.controls.Label;
	import mx.controls.videoClasses.VideoError;
	import mx.effects.Fade;
	import mx.events.EffectEvent;
	import mx.events.VideoEvent;
	
	public class ShowObject extends EventDispatcher
	{
		public var id:String;
		public var resizeX:Number;
		public var resizeY:Number;
		
		private var _configXML:XML;
		private var _realObject:DisplayObject;
		private var _prevObject:DisplayObject;
		
		private var _playlists:ArrayCollection;
		private var _timers:ArrayCollection;
		
		private var _currentPlaylist:Playlist;
		private var _stopped:Boolean;	
		private var _errorplaying:Boolean;
		
		private var _videoLastTimeCode:Number;
		private var _videoRepeatTimeCode:int;
		private var _videoRepeatTimeCode2:int;
		
		private var _parent:Show;
		
		private var _item_start_time:Date = null;
		private var _item_end_time:Date = null;
		private var _item_file_id:String;
		private var _item_file:String;
		private var _item_type:String;
		
		private var _mainMediaRegion:String;
		private var _atShowEnd:Boolean = false;
		
		
		public function ShowObject() {
			this._playlists = new ArrayCollection();
			this._timers = new ArrayCollection();
			
			this._stopped = true;
			this._errorplaying = false;
		}
		
		public function resize():void {
			if( this._realObject != null ) {
				this._realObject.y = this._configXML.@top * this.resizeY;
				this._realObject.x = this._configXML.@left * this.resizeX;
				this._realObject.width = this._configXML.@width * this.resizeX;
				this._realObject.height = this._configXML.@height * this.resizeY;
			}
		}
		
		public function get object():DisplayObject {
			return this._realObject;
		}
		
		public function get stopped():Boolean {
			return this._stopped;
		}
		
		public function get currentPlaylist():Playlist {
			return _currentPlaylist;
		}
		
		public function get parent():Show {
			return _parent;
		}
		

		
		public function set parent(val:Show):void {
			this._parent = val;
		}
		
		public function nextPlaylist():void {
			//Find the current playlist in the array...
			var currIndex:int = this._playlists.getItemIndex(this._currentPlaylist);
			
			// This does one full loop through all the playlists, stopping when one is found to be available
			for ( var a:int = 0; a < this._playlists.length; a++ ) {
				
				if( this._mainMediaRegion == "1" && (a+currIndex) >= this._playlists.length - 1 ) {
					// This is the main media region, when we reach the end of the playlist, throw an event to move to the next show.
					
					// show_play_next finds the next show, if it returns false, we only have one show so dont need to set endofshow
					this._atShowEnd = this._parent._configuration.show_play_next();
//					this._parent.dispatchEvent(new ShowEvent(ShowEvent.NEXT_SHOW));
//					return;
				}

				//Starts at the next playlist index (a+currIndex+1), then do modular division to loop to the begining
				if ((this._playlists.getItemAt((a + currIndex + 1) % this._playlists.length) as Playlist).available) {
					this._currentPlaylist = this._playlists.getItemAt(a) as Playlist;
					return;
				}
				
			}
			

		}
		
		public function configure(objectXML:XML):void {
			this._configXML = objectXML;
			configure_image(objectXML);
			
			this._mainMediaRegion = this._configXML.@mainmedia;
		}
		
		private function configure_text(objectXML:XML):void {
			var newLabel:Label = new Label();
			
			newLabel.text = objectXML;
			newLabel.id	= objectXML.@id;
			
			this._realObject = newLabel;
			this.resize();
		}

		private function configure_rss(objectXML:XML):void {
			var newRSS:RSSFeedPanel = new RSSFeedPanel();
			
			newRSS.id	= objectXML.@id;
			
			
			this._realObject = newRSS;
			this.resize();
		}

		private var currImage:SmoothImage = new SmoothImage();
		private var prevImage:SmoothImage = new SmoothImage();


 		private function configure_image(objectXML:XML):void {
 			
 			
			var newImage:SmoothImage = prevImage;
			prevImage = currImage;
			currImage = newImage;
 
			
			newImage.scaleContent = true;
			newImage.opaqueBackground = null;
			
			newImage.maintainAspectRatio = false;
//			newImage.filters = [new BlurFilter(1.5,1.5,1)];
//			newImage.source = FileIO.Url2Path(objectXML.url.toString());
//			Logger.addEvent("IMG SRC: " + newImage.source);
			newImage.id	= objectXML.@id;
			
			this._realObject = newImage;
			this.resize();
		}
		
		private var _currVideoDisplay:SmoothVideoDisplay;
		private var _lastVideoDisplay:SmoothVideoDisplay;
		
		private function configure_video(objectXML:XML):void {
			if (_currVideoDisplay == null) {
				_currVideoDisplay = new SmoothVideoDisplay();
			}
			if (_lastVideoDisplay == null) {
				_lastVideoDisplay = new SmoothVideoDisplay();
			}
			
			var newVideo:SmoothVideoDisplay = _lastVideoDisplay;
			
			newVideo.maintainAspectRatio = false;
			newVideo.autoPlay = false;
			newVideo.autoRewind = false;
			newVideo.id	= objectXML.@id;
			newVideo.name = objectXML.@id;
			
			if (this._parent.hasAudio==true) {
				newVideo.volume = 1;			
				newVideo.pan = this._parent.audioPan;
			} else {
				newVideo.volume = 0;				
			}
			
			if (ApplicationSettings.getValue("video.mute","false") == "true") {
				newVideo.volume = 0;
			}
			
			newVideo.addEventListener(VideoEvent.COMPLETE,videoComplete,false,0,true);
			
			this._realObject = newVideo;
			this.resize();
		}
		
		private function configure_live(objectXML:XML):void {
			var newVideo:LiveVideoDisplay = new LiveVideoDisplay();
			
			newVideo.visible = false;
			
			this._parent.addChild(newVideo);
			this._parent.removeChild(newVideo);
			
			newVideo.visible = true;
			
			newVideo.maintainAspectRatio = false;
			newVideo.id	= objectXML.@id;
			newVideo.name = objectXML.@id;
			
			
			if (this._parent.hasAudio==true) {
				newVideo.volume = 1;			
			} else {
				newVideo.volume = 0;				
			}
			
			this._realObject = newVideo;
			this.resize();
			
		}
		
		private function configure_timedate(objectXML:XML):void {
			var newTimedate:TimeDateObject = new TimeDateObject();
			
			if(objectXML.@format == "24hr") {
				newTimedate.setFormat("24hr");
			} else {
				newTimedate.setFormat("12hr");
			}
			
			if(objectXML.@timeColor.toString() != "" ) {
				newTimedate.setTimeColor(objectXML.@timeColor);
			}
			
			if(objectXML.@dateColor.toString() != "" ) {
				newTimedate.setDateColor(objectXML.@dateColor);
			}
			
			this._realObject = newTimedate;
			this.resize();
		}
		
		public function play():void {
			this._stopped = false;
			this._atShowEnd = false;
			
			this._currentPlaylist = null;

			play_next();
		}
		
		public function play_next():void {
			var _playlist:Playlist = this.currentPlaylist;
			var currType:String;
			var nextType:String;

			//If the stop flag is set, then make sure the video is stopped and return since we wont need to play the next item.
			if ( this._stopped == true ) {
				this.stop_stage2();
				
				return;
			}
			
			
			// There is no current playlist... so go to the next one...
			if ( _playlist == null ) {
				if ( this._playlists.length == 0 ) {
					Logger.addEvent(this.toString() + ": No playlists defined for " + this.id);
					return;
				}
				
				this.nextPlaylist();
				_playlist = this.currentPlaylist;
				if ( _playlist != null ) {
					_playlist.first();
				} else {
					Logger.addEvent(this.toString() + ": ERROR, could not go to the next playlist");
					return;
				}
		
				currType = (_playlist.current as PlaylistObject).type;
			} else {
				currType = (_playlist.current as PlaylistObject).type;
				_playlist.next();
			}
			
			if(currType == "livevideo") {
				var tmpVD:LiveVideoDisplay;
				
				tmpVD = (this._realObject as LiveVideoDisplay);
				
				if(tmpVD) {
					tmpVD.stop();
				}
			}
			

			//Check if we have reached the end of the current playlist, if so then move to the next playlist
			if ( _playlist.EOL == true ) {
				_playlist.first();
				this.nextPlaylist();
				_playlist = this.currentPlaylist;
				
				_playlist.first();
				
				if(this._atShowEnd) {
					return;
				}
			}
			
			// we read the current value because the playlist item was moved to the next item
			currType = (_playlist.current as PlaylistObject).type; 
			
			this._item_end_time = new Date();
			
			if(this._item_start_time != null ) {
				try {
					LocalDatabase.insertPlaybackEvent(
						new LoggerPlaybackEvent(this._item_file,
							this._item_file_id,
							this._item_type,
							this._item_start_time,
							this._item_end_time,
							this._parent.id.slice(2),
							(this._parent.parent as S3merWindow).screenId));
				} catch(e:Error) {
					Logger.addEvent("ShowObject/play_next(): Could not store item in as run log due to and error, probably null pointer exception");
				}
			}
			
			this._item_start_time = new Date();
			this._item_type = (_playlist.current as PlaylistObject).type;

			
			this._item_file_id = (_playlist.current as PlaylistObject).id;

//			if(this._mainMediaRegion == "1"){
//				trace("here");
//			}

			switch(currType) {
				case "video":
					this._item_file = (_playlist.current as PlaylistObject).file;
					play_next_video();
					break;
				case "swf":
				case "image":
					this._item_file = (_playlist.current as PlaylistObject).file;
					play_next_image();
					break;	
				case "rss":
					this._item_file = (_playlist.current as PlaylistObject).url;
					play_next_rss();
					break;	
				case "podcast":
					this._item_file = (_playlist.current as PlaylistObject).url;
					play_next_podcast();
					break;	
				case "livevideo":
					this._item_file = "live";
					play_next_live();
					break;	
				case "timedate":
					play_next_timedate();
					break;
				case "url":
					this._item_file = (_playlist.current as PlaylistObject).url;
					play_next_url();
					break;
				default:
					Logger.addEvent(this.toString() + ": bad playlist item type. Value = " + (this._currentPlaylist.current as PlaylistObject).type);
					
					this.dispatchEvent(new ShowEvent("INVALID_PLAYLIST_ITEM"));
					return;
					
					
//					DONT DO THIS: this is a bad idea, could be an infinite loop
//					_playlist.next();
//					this.play_next();
//					return;
			}
		}
		
		private var cleancutFade:Fade ;
		
		private function isImage2Image(obj1:DisplayObject, obj2:DisplayObject):Boolean {
			if (obj1 is SmoothImage && obj2 is SmoothImage) {
				return true;
			} else {
				return false;
			}
		}
		
		private static function getAncestor(startObj:DisplayObject):S3merWindow {
			var _currAncestor:DisplayObject = startObj;
			
			while( _currAncestor != null && !(_currAncestor is S3merWindow) ) {
				_currAncestor = _currAncestor.parent;
			}
			
			return (_currAncestor as S3merWindow);
		}
		
		private function cleancut(currentObj:DisplayObject, nextObj:DisplayObject):void	{
			
			if ( this._parent != null ) {
				
				if (nextObj.parent != null) {
					nextObj.parent.removeChild(nextObj);
				}
				
				//If nextObj is an Image  and  currentObj is an Image then Fade
				if (isImage2Image(currentObj,nextObj)) {
					nextObj.alpha = 0;
				} else {
					nextObj.alpha = 1;
				}
				
				if ( currentObj is HTML && !(nextObj is HTML) ) {
					getAncestor(this._parent).enableKeyHandler();
				} 
					
				if (currentObj.parent == this._parent) {
//					if (isImage2Image(currentObj,nextObj)) {
//						this._parent.addChild(nextObj);
//					} else {
					this._parent.addChildAt(nextObj,this._parent.getChildIndex(currentObj));						
//					}
				} else {
					try {
						this._parent.addChild(nextObj);
					} catch(e:Error) {
						Logger.addEvent(e.message);
					}
				}
				
				//Start Fade for images
				if (nextObj is SmoothImage && currentObj is SmoothImage) {
					if (cleancutFade == null) {
						cleancutFade = new Fade();
					}
					
					cleancutFade.addEventListener(EffectEvent.EFFECT_END,Image2ImageFade);
					cleancutFade.alphaFrom = 0;
					cleancutFade.alphaTo = 1;
					cleancutFade.duration = 500;
					cleancutFade.play([nextObj]);
				}
				
				this.resize();
				
				
//				if ( currentObj is Canvas ) {
//					trace("objcanvas here");
//				}
				
				if (nextObj is SmoothVideoDisplay) {
					if ((nextObj as SmoothVideoDisplay).source != null ) {
						(nextObj as SmoothVideoDisplay).addEventListener(VideoEvent.READY,cleancut_stage2,false,0,true);
						(nextObj as SmoothVideoDisplay).play();
					} else {
						cleancut_stage2(null);
					}
				} else {
					if (nextObj is SmoothImage && currentObj is SmoothImage) {
						// This will be done after the fade...
					} else {
						if (currentObj.parent == this._parent) {
							this._parent.removeChild(currentObj);
						}
					}
				}
			}
		} 

		private function Image2ImageFade(e:EffectEvent):void {
			if (this._prevObject.parent == this._parent) {
				this._parent.removeChild(_prevObject);
			}			
		}

		private function cleancut_stage2(e:Event):void {
			if (this._prevObject.parent == this._parent) {
				this._parent.setChildIndex(this._realObject,this._parent.getChildIndex(this._prevObject));
				this._parent.removeChild(this._prevObject);
			}
			
			if (this._realObject is SmoothVideoDisplay) {
				var tmpVideoDisplay:SmoothVideoDisplay;
				
				tmpVideoDisplay = this._currVideoDisplay;
				this._currVideoDisplay = this._realObject as SmoothVideoDisplay;
				this._lastVideoDisplay = tmpVideoDisplay;
				if( this._lastVideoDisplay != null && this._lastVideoDisplay.cameraAttached != true ) {
					try {
						this._lastVideoDisplay.stop();
						this._lastVideoDisplay.close();
						this._lastVideoDisplay.source = null;
					} catch(e:Error) {
						this._lastVideoDisplay.source = null;
					}
				}
			}

		}
		
		private function play_next_live():void {
			var _playlist:Playlist = this.currentPlaylist;
			var liveTimer:TimerId = getTimerById("live_video_done");
			var videocheckTimer:TimerId = getTimerById("video_check");
			var tmpDuration:String = _playlist.current.configXML.@duration;
			
			var videoObj:LiveVideoDisplay;
			// For live video we need to setup the live_video_done timer 
			// which fires when we are done playing the video.
			
			if (!parseInt(tmpDuration)) {
				tmpDuration = "0";
			}
			
			if ( liveTimer == null ) {
				
				if (tmpDuration != "0") {				
					liveTimer = new TimerId("live_video_done",parseInt(tmpDuration)*1000);
					this._timers.addItem(liveTimer);
					
					liveTimer.addEventListener(TimerEvent.TIMER,live_video_done,false,0,true);
				}
			}
			
			if ( videocheckTimer == null ) {
				videocheckTimer = new TimerId("video_check",200);
				this._timers.addItem(videocheckTimer);
				
				videocheckTimer.addEventListener(TimerEvent.TIMER,video_check,false,0,true);
				videocheckTimer.stop();
			}

			if ( getTimerById("image_check") != null ) {
				getTimerById("image_check").stop();
			}

			this._prevObject = this._realObject;
			this.configure_live(this._configXML);
			
			videoObj = this._realObject as LiveVideoDisplay;
			
			if (videoObj.start() == false) {
				_errorplaying = true;
				videocheckTimer.start();
			}

//			SmoothVideoDisplay(this._realObject).play();
			cleancut(this._prevObject, this._realObject);
			
			if (ApplicationSettings.getValue("video.smoothing") == "false") {
				videoObj.smoothing = false;
			} else {
				videoObj.smoothing = true;
			}
			
			Logger.addEvent("Play live video");
			
			if ((tmpDuration != "0") && videoObj.cameraAttached == true) {
				liveTimer.start();
			}		
		}
		
		private function live_video_done(e:Event):void {
			(e.target as Timer).stop();
			this.play_next();
		}
		
		private function play_next_url():void {
			var liveTimer:TimerId = getTimerById("live_video_done");
			var _playlist:Playlist = this.currentPlaylist;
			var tmpDuration:String = _playlist.current.configXML.@duration;
			// For live video we need to setup the live_video_done timer 
			// which fires when we are done playing the video.
			
			if (!parseInt(tmpDuration)) {
				tmpDuration = "0";
			}
			
			if ( liveTimer == null ) {
				
				if (tmpDuration != "0") {				
					liveTimer = new TimerId("live_video_done",parseInt(tmpDuration)*1000);
					this._timers.addItem(liveTimer);
					
					liveTimer.addEventListener(TimerEvent.TIMER,live_video_done,false,0,true);
				}
			}

			this._prevObject = this._realObject;
			
			this._realObject = new HTML();
//			(this._realObject as HTML)
			(this._realObject as HTML).location = _playlist.current.url;
			(this._realObject as HTML).reload();
			(this._realObject as HTML).paintsDefaultBackground = true;
			(this._realObject as HTML).setStyle("backgroundColor","#000000");
//			(this._realObject as HTML).alpha = .5;
//			(this._realObject as HTML).scaleX = .5;
//			(this._realObject as HTML).scaleY = .5;
			(this._realObject as HTML).horizontalScrollPolicy = "false";
			(this._realObject as HTML).verticalScrollPolicy = "false";
//			(this._realObject as HTML).filters = [new BlurFilter(8)];
			

//			this.resize();
			cleancut(this._prevObject, this._realObject);
			
			(this.parent.parent as S3merWindow).disableKeyHandler();

			if ((tmpDuration != "0")) {
				liveTimer.start();
			}		
		}
		
		
		private function play_next_video():void {
			var _playlist:Playlist = this.currentPlaylist;
			var tmpTimer:TimerId = getTimerById("video_check");
			// For videos we need to setup the video_check timer which detects stuck video and when the video is done.
			if ( tmpTimer == null ) {
				tmpTimer = new TimerId("video_check",200);
				this._timers.addItem(tmpTimer);
				
				tmpTimer.addEventListener(TimerEvent.TIMER,video_check,false,0,true);
			}

			tmpTimer = getTimerById("live_video_done");
			
			if (tmpTimer != null) {
				tmpTimer.stop();
			}

			this._prevObject = this._realObject;
			this.configure_video(this._configXML);
			
			SmoothVideoDisplay(this._realObject).source = FileIO.mediaPath(getScreenId(),_playlist.current.file);
//			SmoothVideoDisplay(this._realObject).play();
			cleancut(this._prevObject, this._realObject);
			
			if (ApplicationSettings.getValue("video.smoothing") == "false") {
				SmoothVideoDisplay(this._realObject).smoothing = false;
			} else {
				SmoothVideoDisplay(this._realObject).smoothing = true;
			}
			
			Logger.addEvent("Play next: " + FileIO.mediaPath(getScreenId(),_playlist.current.file));
			
			getTimerById("video_check").start();
		}
		
		private function play_next_rss():void {
			var _playlist:Playlist = this.currentPlaylist;

			this._prevObject = this._realObject;
			this.configure_rss(this._configXML);
			
			RSSFeedPanel(this._realObject).source = _playlist.current.url;
			RSSFeedPanel(this._realObject).color = _playlist.current.configXML.@rsscolor;
			var tmpDuration:String;
			
			tmpDuration = _playlist.current.configXML.@duration;
			
			
			if (tmpDuration == "" ) {
				RSSFeedPanel(this._realObject).delay = 15 * 1000;
			} else {
				RSSFeedPanel(this._realObject).delay = parseInt(tmpDuration) * 1000;
			}
			
			RSSFeedPanel(this._realObject).logoUrl = _playlist.current.configXML.@logoUrl;
			
			cleancut(this._prevObject, this._realObject);
			
			Logger.addEvent("Play next: " + _playlist.current.url);
		
			RSSFeedPanel(this._realObject).play();
		}
		
		private function play_next_timedate():void {
			this._prevObject = this._realObject;
			this.configure_timedate(this.currentPlaylist.current.configXML);
			cleancut(this._prevObject, this._realObject);
			TimeDateObject(this._realObject).play();
		}
		
		private function play_next_podcast():void {
			if(this.currentPlaylist.current.file != "") {
				play_next_video();
			} else {
				play_next();
			}
			
		}
		
		
		
		private function play_next_image():void {
			var _playlist:Playlist = this.currentPlaylist;
			var tmpTimer:TimerId = getTimerById("image_check");

			if ( tmpTimer == null ) {
				tmpTimer = new TimerId("image_check", 15 * 1000);
				this._timers.addItem(tmpTimer);
				
				tmpTimer.addEventListener(TimerEvent.TIMER,image_check,false,0,true);
			}
			
			var tmpDuration:String;
			
			tmpDuration = _playlist.current.configXML.@duration;
			
			if (tmpDuration == "" ) {
				tmpTimer.delay = 15 * 1000;
			} else {
				if (tmpDuration != "0") {
					tmpTimer.delay = parseInt(tmpDuration) * 1000;
				}
			}
			
			this._prevObject = this._realObject;
			this.configure_image(this._configXML);
			SmoothImage(this._realObject).load(FileIO.mediaPath(getScreenId(),_playlist.current.file));
			
			cleancut(this._prevObject,this._realObject);

			Logger.addEvent("New timer duration: " +tmpTimer.delay);

			Logger.addEvent("Play next: " + FileIO.mediaPath(getScreenId(),_playlist.current.file));
		
			if (tmpDuration != "0") {
				tmpTimer.start();
			}
		}
		
		private function getScreenId():String {
			return (this._parent.parent as S3merWindow).screenId.toString();
		}

		public function image_check(e:TimerEvent):void {
			Timer(e.target).stop();
			
			if(!this._stopped) {
				this.play_next();
			} else {
				stop_stage2();
			}
		}
		
		public function videoComplete(e:VideoEvent):void {
			if(!this._stopped) {
				isVideoPlayingOK();
			} else {
				stop_stage2();
			}
		}

		public function video_check(e:TimerEvent):void {
			Timer(e.target).stop();
			if ( isVideoPlayingOK() ) { // Only restart the timer if we didnt move to another video
				Timer(e.target).start();			
			} else {
				if(!this._stopped) {
					this.play_next();
				} else {
					stop_stage2();
				}
			}
		}
		
		private var handlingVideoEvent:Boolean = false;
		
		private function isVideoPlayingOK():Boolean {
			var _isVideoPlayingOK:Boolean = true;
			
			if (handlingVideoEvent == false) {
				handlingVideoEvent = true; 
				try {
					
					if(this._realObject is SmoothVideoDisplay) {
						if (SmoothVideoDisplay(this._realObject).state == VideoEvent.STOPPED ) {
							_isVideoPlayingOK = false;
						}
						
						if (this._errorplaying == true) {
							_isVideoPlayingOK = false;
							this._errorplaying = false;
						}
						
						if (SmoothVideoDisplay(this._realObject).videoHeight == 0 ||
							SmoothVideoDisplay(this._realObject).videoWidth == 0 ) {
							
							if(_videoRepeatTimeCode2 >= 10) {				
								_isVideoPlayingOK = false;
							} else {
								_videoRepeatTimeCode2++;
							}
						} else {
							_videoRepeatTimeCode2 = 0;
						}
						
						
						// If the timecode repeats 10 times then video has been stopped for 2 secs
						if (SmoothVideoDisplay(this._realObject).playheadTime == this._videoLastTimeCode) {
							_videoRepeatTimeCode++;
						} else {
							_videoRepeatTimeCode = 0;
						}
						
						if (_videoRepeatTimeCode >= 10) {
							_videoRepeatTimeCode = 0;
							_isVideoPlayingOK = false;
						}
						
						_videoLastTimeCode = SmoothVideoDisplay(this._realObject).playheadTime;
					}
				} catch(e:TypeError) {
					Logger.addEvent(e.message + e.getStackTrace());
				}
				
				handlingVideoEvent = false; 
					
			}
			
			return _isVideoPlayingOK;
		}
		
		//This sets the stopped flag to true.  When the current item ends, then stop_stage2 will be called to cleanup
		public function stop(force:Boolean = true):void {
			if (force == true) {
				this._atShowEnd = false;
				stop_stage2();
			}
			this._stopped = true;
		}
		
		//Called when stop has been called and the current item has finished.
		private function stop_stage2():void {
			this.stopTimers();

			if (this._realObject is SmoothVideoDisplay) {
				//VideoDisplay(this._realObject).removeEventListener(VideoEvent.COMPLETE, stop_stage2);
				try {
					SmoothVideoDisplay(this._realObject).stop();
				} catch(e:VideoError) {
					
				}
			} 
			
			if (this._realObject is RSSFeedPanel) {
				RSSFeedPanel(this._realObject).stop();
			}
			
			if(this._realObject.parent) {
				this._realObject.parent.removeChild(this._realObject);
			}

			this.dispatchEvent(new ShowEvent(ShowEvent.STOPPED));
		}
		
		public function addPlaylist(list:Playlist):void {
			this._playlists.addItem(list);
		}
		
		public function removePlaylist(list:Playlist):void {
			var index:int = this._playlists.getItemIndex(list);
			
			if ( index >= 0 ) {
				this._playlists.removeItemAt(index);		
			}
		}
		
		public function clearPlaylist(list:Playlist):void {
			this._playlists.removeAll();
		}



		private function getTimerById(id:String):TimerId {
			for each (var _each:TimerId in this._timers) {
				if( _each.id == id ) {
					return _each;
				}
			}
			
			return null;
		}
		
		private function stopTimers():void {
			for each (var _timer:Timer in this._timers ) {
				_timer.stop();
			}
			
			this._timers.removeAll();			
		}
	}
}