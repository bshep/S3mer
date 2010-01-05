package com.s3mer.util
{
	import com.s3mer.mediaObjects.ImageObject;
	import com.s3mer.ui.S3merWindow;
	
	public class ShowManager
	{
		private var window:S3merWindow;

		private var _layoutSizeX:int;
		private var _layoutSizeY:int;
		
		public function ShowManager(_window:S3merWindow)
		{
			window = _window;
		}
		
		public function start():void {
			LoggerManager.addEvent("ShowManager.as: Started");
			
			var imageObject:ImageObject = new ImageObject();
			var config:XML = PlayerState.configurations[window.screenNumber];
			
			_layoutSizeX = config.show.@width;
			_layoutSizeY = config.show.@height;
			
			
			var configRegion:XML = config.show.region.(@id == "rg952")[0];
			var configPlaylist:XML = (config.playlist.(@id == "pl900")[0]).playlistitem[0];
			
			imageObject.mediaPath = FileIO.mediaPath(window.screenNumber);
			imageObject.configure(configRegion,_layoutSizeX,_layoutSizeY);
			
			window.addChild(imageObject);
			
			imageObject.play(configPlaylist);
		}

	}
}