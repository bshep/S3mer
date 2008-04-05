package com.msgid.S3mer
{
	import flash.events.Event;
	
	import mx.collections.ArrayCollection;
	
	public class PodcastManager extends Object
	{
		//Static class declarations
		private static var _podcastManager:PodcastManager;
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
			newPodcast.reloadRSS();
			_podcasts.addItem(newPodcast);
		}
		
		public static function loadComplete(e:Event):void {
			

		}
		

		
		
	}
	

}