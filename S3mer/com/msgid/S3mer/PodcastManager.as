package com.msgid.S3mer
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	import mx.collections.ArrayCollection;
	
	public class PodcastManager extends EventDispatcher
	{
		//Static class declarations
		public static var _podcastManager:PodcastManager;
		public static var _queue:DownloadQueue;
		private static var _podcasts:ArrayCollection;
		
		//Instance class declarations
		
		public function PodcastManager()
		{
			super();
		}
		
		public static function init():void {
			if (_podcastManager == null) {
				_podcastManager = new PodcastManager();
			}
			
			if (_podcasts == null) {
				_podcasts = new ArrayCollection();
			}
			
		}
		
		public static function setQueue(queue:DownloadQueue):void {
			if( _queue == null ) {
				_queue = queue;
			}
		}
		
		public static function addPodcast(playlistItem:PlaylistObject):void {
			var newPodcast:PodcastItem = new PodcastItem(playlistItem);
			init();
			
			Logger.addEvent("New Podcast:"+ playlistItem.url);
			
			
			newPodcast.addEventListener(Event.COMPLETE, loadComplete);
			newPodcast.addEventListener("ERROR", loadError);
			_podcasts.addItem(newPodcast);
		}
		
		public static function setupDownloads():void {
			
			for each( var item:PodcastItem in _podcasts ) {
				item.reloadRSS();
			}
			
		}
		
		public static function loadError(e:Event):void {
			
			
		}
		
		public static function loadComplete(e:Event):void {
			if((e.target as PodcastItem)._errored == false) {
				(e.target as PodcastItem).queueDownload();
			}
					
			if( loaded() == true ) {		
				_podcastManager.dispatchEvent(new Event(Event.COMPLETE));
			}			
		}
		
		public static function loaded():Boolean {
			for each( var item:PodcastItem in _podcasts ) {
				//Only return false for those podcasts which are not in error state and havent been loaded.
				if( item.loaded() == false && item._errored == false ) { 
					return false; //If any podcast hasnt been loaded then exit...
				}
			}
			return true;
		}
	}
	

}