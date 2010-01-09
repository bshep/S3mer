package com.s3mer.util
{
	import com.s3mer.events.MediaEvent;
	import com.s3mer.mediaObjects.GenericMediaObject;
	import com.s3mer.mediaObjects.ImageObject;
	import com.s3mer.mediaObjects.MovieObject;
	import com.s3mer.mediaObjects.TimeObject;
	import com.s3mer.ui.S3merWindow;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;

	/**
	 *  Dispatched when the region finishes playing some media
	 * 
	 *  @eventType mx.events.FlexEvent.ADD
	 */
	[Event(name="play_complete", type="com.s3mer.events.MediaEvent")]

	public class RegionManager extends EventDispatcher
	{
		private var window:S3merWindow;
		private var configuration:XML;
		
		private var playlistItems:XMLList;
		private var playlistIndex:int;
		private var currentItem:XML;
		
		private var mediaObjects:Array = new Array();
		private var currentObject:GenericMediaObject;
		
		private var layoutWidth:int;
		private var layoutHeight:int;
		private var scale:Scale;
		
		public function get id():String {
			return configuration.@id;
		}
		
		public function RegionManager(_window:S3merWindow)
		{
			super();
			
			window = _window;
		}
		
		public function configure(_configuration:XML, _layoutWidth:int, _layoutHeight:int, _scale:Scale):void {
			
			configuration = _configuration;
			layoutHeight = _layoutHeight;
			layoutWidth = _layoutWidth;
			scale = _scale;
		}
		
		public function play():void {
			var windowConfig:XML = window.configuration;
			
			// Get the ID for the first playlist for this region
			var playlistID:String = configuration.playlist[0].@id;
			
			// Get the XML for the playslist
			var playlist:XML = windowConfig.playlist.(@id == playlistID)[0];
			
			playlistItems = playlist.playlistitem;		
			playlistIndex = 0;
			
			currentItem = playlistItems[playlistIndex];
			
			play_common();
		}
		
		public function play_complete(e:Event):void {
			var object:GenericMediaObject = e.target as GenericMediaObject;
			LoggerManager.addEvent("RegionManager.as / play_complete: " + "id = " + object.id);
			
			this.dispatchEvent(e.clone());
			
			play_next();
		}
		
		public function play_next():void {
			playlistIndex = (playlistIndex + 1 ) % playlistItems.length();
			
			currentItem = playlistItems[playlistIndex];
			
			play_common();			
		}
		
		private function play_common():void {
			switch( currentItem.@type.toString() ) {
				case 'image':
					currentObject = new ImageObject();
					break;
				case 'video':
					currentObject = new MovieObject();
					break;
				case 'timedate':
					currentObject = new TimeObject();
					break;
				default:
					break;
			}
			
			if( currentObject == null ) {
				LoggerManager.addEvent("RegionManager.as / play : item type = " + currentItem.@type.toString() + " cannot handle yet");
				return;
			}
			
			currentObject.mediaPath = FileIO.mediaPath(window.screenNumber);
			currentObject.configure(this.configuration, this.layoutWidth, this.layoutHeight, this.scale);
			
			window.addChild(currentObject);
			
			for each( var mediaObject:GenericMediaObject in mediaObjects ) {
				window.removeChild(mediaObject);	
			}
			this.mediaObjects.pop();
			
			this.mediaObjects.push(currentObject);
			currentObject.addEventListener(MediaEvent.PLAY_COMPLETE, play_complete);
			currentObject.play(currentItem);
		}

		public function stop():void {
			currentObject.stop();
		}
		
		public function resize(scale:Scale):void {
			for each( var mediaObject:GenericMediaObject in this.mediaObjects ) {
				mediaObject.resize(scale);
			}
		}
		
	}
}