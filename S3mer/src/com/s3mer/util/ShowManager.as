package com.s3mer.util
{
	import com.s3mer.events.MediaEvent;
	import com.s3mer.ui.S3merWindow;
	
	import flash.display.Screen;
	import flash.events.Event;
	
	import mx.events.ResizeEvent;

	public class ShowManager
	{
		private var window:S3merWindow;

		private var layoutWidth:int;
		private var layoutHeight:int;
		
		private var regionObjects:Array = new Array();
		
		public function ShowManager(_window:S3merWindow)
		{
			window = _window;
			
			window.addEventListener(ResizeEvent.RESIZE, showResize);
		}
		
		private function showResize(e:ResizeEvent):void {
			var _scale:Scale = this.scale;
			
			
			for each( var regionObject:RegionManager in regionObjects ) {
				regionObject.resize(_scale);
			}
		}
		
		public function get scale():Scale {
			var scaleX:Number;
			var scaleY:Number;
			var screen:Screen;
			
			screen = Screen.screens[window.screenNumber];
			
			scaleX = window.width / layoutWidth;
			scaleY = window.height / layoutHeight;
			
			LoggerManager.addEvent("ShowManager.as scale: " + " scaleX = " + scaleX + " scaleY = " + scaleY );
			
			return new Scale(scaleX, scaleY);			
		}
		
		public function start():void {
			LoggerManager.addEvent("ShowManager.as start: Started");
			
			var config:XML = window.configuration;
			
			// Load the layoutsize into the showmanager so we can calculate the scale
			layoutWidth = config.show.@width;
			layoutHeight = config.show.@height;

			var _scale:Scale = this.scale;
			var _region:RegionManager;
			
			for each( var configRegion:XML in config.show.region ) {
				LoggerManager.addEvent("ShowManager.as / start : " + "region.id = " + configRegion.@id);
				_region = new RegionManager(window);
				
				_region.configure(configRegion, this.layoutWidth, this.layoutHeight, _scale);
				_region.addEventListener(MediaEvent.PLAY_COMPLETE, play_complete);
				
				regionObjects.push(_region);
				
				_region.play();
			}
		}
		
		private function play_complete(e:Event):void {
			var object:RegionManager = e.target as RegionManager;
			LoggerManager.addEvent("ShowManager.as / play_complete: " + "id = " + object.id);
		}

	}
}