package com.s3mer.util
{
	import com.s3mer.mediaObjects.GenericMediaObject;
	import com.s3mer.mediaObjects.ImageObject;
	import com.s3mer.mediaObjects.MovieObject;
	import com.s3mer.mediaObjects.TimeObject;
	import com.s3mer.ui.S3merWindow;
	
	import flash.display.Screen;
	
	import mx.events.ResizeEvent;
	
	public class ShowManager
	{
		private var window:S3merWindow;

		private var _layoutSizeX:int;
		private var _layoutSizeY:int;
		
		private var mediaObjects:Array = new Array();
		
		public function ShowManager(_window:S3merWindow)
		{
			window = _window;
			
			window.addEventListener(ResizeEvent.RESIZE, showResize);
		}
		
		private function showResize(e:ResizeEvent):void {
			var _scale:Scale = this.scale;
			
			
			for each( var showObject:GenericMediaObject in mediaObjects ) {
				showObject.resize(_scale);
			}
		}
		
		public function get scale():Scale {
			var scaleX:Number;
			var scaleY:Number;
			var screen:Screen;
			
			screen = Screen.screens[window.screenNumber];
			
			scaleX = window.width / _layoutSizeX;
			scaleY = window.height / _layoutSizeY;
			
			LoggerManager.addEvent("ShowManager.as: " + " scaleX = " + scaleX + " scaleY = " + scaleY );
			
			return new Scale(scaleX, scaleY);			
		}
		
		public function start():void {
			LoggerManager.addEvent("ShowManager.as: Started");
			
			var config:XML = PlayerState.configurations[window.screenNumber];
			_layoutSizeX = config.show.@width;
			_layoutSizeY = config.show.@height;

			var _scale:Scale = this.scale;
			var imageObject:ImageObject = new ImageObject();
			var movieObject:MovieObject = new MovieObject();
			var timeObject:TimeObject = new TimeObject();
			
			
			
			
			var configRegion:XML = config.show.region.(@id == "rg952")[0];
			var configPlaylist:XML = (config.playlist.(@id == "pl900")[0]).playlistitem[0];
			
			imageObject.mediaPath = FileIO.mediaPath(window.screenNumber);
			imageObject.configure(configRegion,_layoutSizeX,_layoutSizeY, _scale);
			
			window.addChild(imageObject);
			this.mediaObjects.push(imageObject);
			
			imageObject.play(configPlaylist);
			
			//******************
			configRegion = config.show.region.(@id == "rg948")[0];
			configPlaylist = (config.playlist.(@id == "pl896")[0]).playlistitem[0];
			
			movieObject.mediaPath = FileIO.mediaPath(window.screenNumber);
			movieObject.configure(configRegion,_layoutSizeX,_layoutSizeY, _scale);
			
			window.addChild(movieObject);
			this.mediaObjects.push(movieObject);
			
			movieObject.play(configPlaylist);
			//******************
			configRegion = config.show.region.(@id == "rg950")[0];
//			configPlaylist = (config.playlist.(@id == "pl896")[0]).playlistitem[0];
			
//			timeObject.mediaPath = FileIO.mediaPath(window.screenNumber);
			timeObject.configure(configRegion,_layoutSizeX,_layoutSizeY, _scale);
			
			window.addChild(timeObject);
			this.mediaObjects.push(timeObject);
			
			timeObject.play(configPlaylist);
			
			
		}

	}
}