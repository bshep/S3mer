package com.msgid.S3mer
{
	import com.msgid.S3mer.Utility.LoggerManager;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	
	import mx.collections.ArrayCollection;
	
	public class PodcastManager extends EventDispatcher
	{
		//Static class declarations
		private static var _podcastManager:PodcastManager;
		public static var _queue:DownloadQueue;
		private static var _podcasts:ArrayCollection;
		private static var _reloadTimer:Timer;
		
		//Instance class declarations
		
		public function PodcastManager()
		{
			super();
		}
		
		public static function get podcastManager():PodcastManager {
			init();
			
			return _podcastManager;
		}
		
		public static function init():void {
			if (_podcastManager == null) {
				_podcastManager = new PodcastManager();
			}
			
			if (_podcasts == null) {
				_podcasts = new ArrayCollection();
			}
			
			if (_reloadTimer == null) {
				_reloadTimer = new Timer(1*60*1000);
				_reloadTimer.addEventListener(TimerEvent.TIMER, onReloadTick);
			}
		}
		
		public static function setQueue(queue:DownloadQueue):void {
			if( _queue == null ) {
				_queue = queue;
			}
		}
		
		public static function addPodcast(playlistItem:PlaylistObject, screenId:String):void {
			var newPodcast:PodcastItem = new PodcastItem(playlistItem, screenId);
			init();
			
			LoggerManager.addEvent("New Podcast:"+ playlistItem.url);
			
			
			newPodcast.addEventListener(Event.COMPLETE, loadComplete);
			newPodcast.addEventListener("ERROR", loadError);
			_podcasts.addItem(newPodcast);
		}
		
		public static function setupDownloads():void {
			if (_podcasts.length == 0) {
				_podcastManager.dispatchEvent(new Event(Event.COMPLETE));
				return;
			}
			
			
			for each( var item:PodcastItem in _podcasts ) {
				item.reloadRSS();
			}
			
		}
		
		public static function loadError(e:Event):void {
			//TODO: log the error to ASRun Log
			
			loadComplete(e);
			
		}
		
		public static function loadComplete(e:Event):void {
			if((e.target as PodcastItem)._errored == false) {
				(e.target as PodcastItem).queueDownload();
			}
					
			if( loaded() == true ) {		
				_podcastManager.dispatchEvent(new Event(Event.COMPLETE));
				_reloadTimer.start();
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
		
		public static function onReloadTick(e:TimerEvent):void {
			for each( var item:PodcastItem in _podcasts ) {
				item.checkRSS();
			}
		}
		
	}
	

}