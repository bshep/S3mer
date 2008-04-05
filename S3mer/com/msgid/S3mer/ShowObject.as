package com.msgid.S3mer
{
	import flash.display.DisplayObject;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.TimerEvent;
	import flash.filters.BlurFilter;
	import flash.media.SoundTransform;
	import flash.utils.Timer;
	
	import mx.collections.ArrayCollection;
	import mx.controls.Image;
	import mx.controls.Label;
	import mx.effects.Fade;
	import mx.events.VideoEvent;
	
	public class ShowObject extends EventDispatcher
	{
		public var id:String;
		public var resizeX:Number;
		public var resizeY:Number;
		
		private var _configXML:XML;
		private var _realObject:DisplayObject;
		private var _prevObject:DisplayObject;
		
		private var _schedules:ArrayCollection;
		private var _playlists:ArrayCollection;
		private var _timers:ArrayCollection;
		
		private var _currentPlaylist:Playlist;
		private var _stopped:Boolean;	
		
		private var _videoLastTimeCode:Number;
		private var _videoRepeatTimeCode:int;
		
		private var _parent:Show;
		
		public function ShowObject() {
			this._schedules = new ArrayCollection();
			this._playlists = new ArrayCollection();
			this._timers = new ArrayCollection();
			
			this._stopped = true;
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
			//TODO: Go through conditions to see which playlist can be played
			
			//Find the current playlist in the array...
			var currIndex:int = this._playlists.getItemIndex(this._currentPlaylist);
			
			// This does one full loop through all the playlists, stopping when one is found to be available
			for ( var a:int = 0; a < this._playlists.length; a++ ) {
				
				//Starts at the next playlist index (a+currIndex+1), then do modular division to loop to the begining
				if (this._playlists.getItemAt((a + currIndex + 1) % this._playlists.length).avaiable) {
					this._currentPlaylist = this._playlists.getItemAt(a) as Playlist;
					return;
				}
			}
			

		}
		
		public function configure(objectXML:XML):void {
			this._configXML = objectXML;
			
			switch(objectXML.@type.toString()) {
				case 'text':
					configure_text(objectXML);
					break;
  				case 'image':
					configure_image(objectXML);
					break;
  				case 'video':
					configure_video(objectXML);
					break;
  				case 'rss':
					configure_rss(objectXML);
					break;
				case 'condition':
					break;
				default:
					configure_image(objectXML);
					break;
			}
			
//			this.parent.addChild(this._realObject);
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



 		private function configure_image(objectXML:XML):void {
			var newImage:Image = new Image();
			
			newImage.scaleContent = true;

			
			newImage.maintainAspectRatio = false;
			newImage.filters = [new BlurFilter(1.5,1.5,1)];
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
				var newSoundTransform:SoundTransform = newVideo.soundTransform;
				
				newVideo.volume = 1;			
			
				newVideo.pan = this._parent.audioPan;
//				newVideo.soundTransform = newSoundTransform;
									
			} else {
				newVideo.volume = 0;				
			}
			
			
			newVideo.addEventListener(VideoEvent.COMPLETE,videoComplete,false,0,true);
			
			this._realObject = newVideo;
			this.resize();
		}
		
		private function configure_timedate(objectXML:XML):void {
			var newTimedate:TimeDateObject = new TimeDateObject();
			
			this._realObject = newTimedate;
			this.resize();
		}
		
		public function play():void {
			this._stopped = false;

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
			

			//Check if we have reached the end of the current playlist, if so then move to the next playlist
			if ( _playlist.EOL == true ) {
				this.nextPlaylist();
				_playlist = this.currentPlaylist;
				_playlist.first();
			}
			
			// we read the current value because the playlist item was moved to the next item
			currType = (_playlist.current as PlaylistObject).type; 
			

//			if (currType != nextType ) { //|| _playlist.BOL != true
//				var parentShow:Show = this._realObject.parent as Show;
//				var tmpObject:DisplayObject = this._realObject;
//				
//				switch(nextType) {
//					case "video":
//						this.configure_video(this._configXML);
//						break;
//					case "image":
//						this.configure_image(this._configXML);
//						break;
//					default:
//						Logger.addEvent(this.toString() + ": bad playlist item type. Value = " + (this._currentPlaylist.current as PlaylistObject).type);
//						
//						this.dispatchEvent(new ShowEvent("INVALID_PLAYLIST_ITEM"));
//						return;
//				}
//				
////				parentShow.addChild(this._realObject);
////				parentShow.removeChild(tmpObject);
//				
//				currType = nextType;
//			}
//			

			switch(currType) {
				case "video":
					play_next_video();
					break;
				case "image":
					play_next_image();
					break;	
				case "rss":
					play_next_rss();
					break;	
				case "podcast":
					play_next_podcast();
					break;	
				case "timedate":
					play_next_timedate();
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
		
		private function cleancut(currentObj:DisplayObject, nextObj:DisplayObject):void	{
			
			if ( this._parent != null ) {
				
				if (currentObj.parent == this._parent) {
					this._parent.addChildAt(nextObj,this._parent.getChildIndex(currentObj));
					nextObj.alpha = 1;
				} else {
					this._parent.addChild(nextObj);
				}
				

				
				if (nextObj is SmoothVideoDisplay) {
					(nextObj as SmoothVideoDisplay).addEventListener(VideoEvent.READY,cleancut_stage2,false,0,true);
					(nextObj as SmoothVideoDisplay).play();
				} else {
					if (currentObj.parent == this._parent) {
						this._parent.removeChild(currentObj);
					}
				}
				
//				if (cleancutFade == null ) {
//					cleancutFade = new Fade();
//				
//					cleancutFade.duration = 100;
//					cleancutFade.alphaFrom = 0.0;
//					cleancutFade.alphaTo = 1.0;
//					
//					cleancutFade.addEventListener(EffectEvent.EFFECT_END,cleancut_COMPLETE,false,0,true);
//									
//				} else {
//					if (cleancutFade.isPlaying) {
//						cleancutFade.end();
//					}
//				}
				
//				if (cleancutFadeR == null ) {
//					cleancutFadeR = new Fade();
//				
//					cleancutFadeR.duration = 100;
//					cleancutFadeR.alphaFrom = 0.0;
//					cleancutFadeR.alphaTo = 1.0;
//					
//									
//				} else {
//					if (cleancutFade.isPlaying) {
//						cleancutFadeR.end();
//					}
//				}
				
//				cleancutFade.play([nextObj]);
//				cleancutFadeR.play([nextObj]);

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
				this._lastVideoDisplay.stop();
				this._lastVideoDisplay.close();
				this._lastVideoDisplay.source = null;
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

			this._prevObject = this._realObject;
			this.configure_video(this._configXML);
			SmoothVideoDisplay(this._realObject).source = FileIO.mediaPath(_playlist.current.file);
//			SmoothVideoDisplay(this._realObject).play();
			cleancut(this._prevObject, this._realObject);
			
			if (ApplicationSettings.getValue("video.smoothing") == "false") {
				SmoothVideoDisplay(this._realObject).smoothing = false;
			} else {
				SmoothVideoDisplay(this._realObject).smoothing = true;
			}
			
			Logger.addEvent("Play next: " + FileIO.mediaPath(_playlist.current.file));
			
			tmpTimer.start();
		}
		
		private function play_next_rss():void {
			var _playlist:Playlist = this.currentPlaylist;

			this._prevObject = this._realObject;
			this.configure_rss(this._configXML);
			
			RSSFeedPanel(this._realObject).source = _playlist.current.url;

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
			this.configure_timedate(this._configXML);
			cleancut(this._prevObject, this._realObject);
			TimeDateObject(this._realObject).play();
		}
		
		private function play_next_podcast():void {
			if(this.currentPlaylist.current.file != "") {
				play_next_video();
			} else {
//				play_next();
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
			Image(this._realObject).load(FileIO.mediaPath(_playlist.current.file));
			
			cleancut(this._prevObject,this._realObject);

			Logger.addEvent("New timer duration: " +tmpTimer.delay);

			Logger.addEvent("Play next: " + FileIO.mediaPath(_playlist.current.file));
		
			if (tmpDuration != "0") {
				tmpTimer.start();
			}
		}
		

		public function image_check(e:TimerEvent):void {
			Timer(e.target).stop();

			this.play_next();
		}
		
		public function videoComplete(e:VideoEvent):void {
			videoNext();
		}

		public function video_check(e:TimerEvent):void {
			Timer(e.target).stop();
			videoNext();
			Timer(e.target).start();			
		}
		
		private var handlingVideoEvent:Boolean = false;
		
		private function videoNext():void {
			
			if (handlingVideoEvent == false) {
				handlingVideoEvent = true; 
				try {
					
					if(this._realObject is SmoothVideoDisplay) {
						if (SmoothVideoDisplay(this._realObject).state == VideoEvent.STOPPED ) {
							this.play_next();
							handlingVideoEvent = false; 
							return;					
						}
						
						
						// If the timecode repeats 10 times then video has been stopped for 2 secs
						if (SmoothVideoDisplay(this._realObject).playheadTime == this._videoLastTimeCode) {
							_videoRepeatTimeCode++;
						} else {
							_videoRepeatTimeCode = 0;
						}
						
						if (_videoRepeatTimeCode >= 10) {
							_videoRepeatTimeCode = 0;
							this.play_next();
							handlingVideoEvent = false; 
							
							return;					
						}
						
						_videoLastTimeCode = SmoothVideoDisplay(this._realObject).playheadTime;
					}
				} catch(e:Error) {
					Logger.addEvent(e.message + e.getStackTrace());
				}
				handlingVideoEvent = false; 
					
			}
		}
		
		//This sets the stopped flag to true.  When the current item ends, then stop_stage2 will be called to cleanup
		public function stop(force:Boolean = true):void {
			if (force == true) {
				stop_stage2();
			}
			this._stopped = true;
		}
		
		//Called when stop has been called and the current item has finished.
		private function stop_stage2():void {
			if (this._realObject is SmoothVideoDisplay) {
				//VideoDisplay(this._realObject).removeEventListener(VideoEvent.COMPLETE, stop_stage2);
				SmoothVideoDisplay(this._realObject).stop();
			} 
			
			if (this._realObject is RSSFeedPanel) {
				RSSFeedPanel(this._realObject).stop();
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

		public function addSchedule(list:Schedule):void {
			this._schedules.addItem(list);
		}
		
		public function removeSchedule(list:Schedule):void {
			var index:int = this._schedules.getItemIndex(list);
			
			if ( index >= 0 ) {
				this._schedules.removeItemAt(index);		
			}
		}

		public function clearSchedule(list:Playlist):void {
			this._schedules.removeAll();
		}
		
		private function getTimerById(id:String):TimerId {
			for each (var _each:TimerId in this._timers) {
				if( _each.id == id ) {
					return _each;
				}
			}
			
			return null;
		}

	}
}