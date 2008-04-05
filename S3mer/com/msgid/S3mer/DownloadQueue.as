package com.msgid.S3mer
{
	import flash.events.EventDispatcher;
	import flash.events.ProgressEvent;
	
	import mx.collections.ArrayCollection;

	public class DownloadQueue extends EventDispatcher
	{
		private var _downloaders:ArrayCollection;
		private var _percent:int;
		private var _maxdownloads:int;
		private var _started:Boolean;
		//private var _complete:Boolean;
		
		public function DownloadQueue(maxdownloads:int = 1) {
			this._downloaders = new ArrayCollection();
			this._maxdownloads = maxdownloads;
			this._started = false;
			//this._complete = false;
		}
		
		public function get percent():int {
			var percent:Number = 0;
			var count:int = 0;
			
			for each (var mydownloader:Downloader in this._downloaders) {
				percent += mydownloader.percent;
				count++;
			}
			
			percent = Math.ceil(percent/count);
			
			return percent;
		}
		
		public function get complete():Boolean {
			for each (var mydownloader:Downloader in this._downloaders) {
				if (!mydownloader.complete) {
					return false;
				}
			}
			
			return true;
		}
		
		
		public function addItem(url:String, hash:String ="", filename:String = null, autostart:Boolean = true, forceUpdate:Boolean = false):void {
			var myDL:Downloader

			myDL = new Downloader(url, hash, filename, forceUpdate);
			this._downloaders.addItem(myDL);
		
			myDL.addEventListener(DownloaderEvent.PROGRESS,OnProgress);
			myDL.addEventListener(DownloaderEvent.COMPLETE,OnComplete);
			myDL.addEventListener(DownloaderEvent.ERROR,OnError);
			if (this._started) {
				this.startNext();			
			}
		}
		
		public function start():void {
			if(!this.complete) {
				this._started = true;
				this.startNext();
			} else {
				this.dispatchEvent(new DownloaderEvent(DownloaderEvent.COMPLETE, this));
			}
		}
		
		private function get active():int {
			var count:int = 0;
			
			for each (var mydownloader:Downloader in this._downloaders) {
				if (!mydownloader.complete && mydownloader.started) {
					count++;
				}
			}
			
			return count;
		}
		
		private function startNext():void {
			if (this.active < this._maxdownloads) {
				for each (var mydownloader:Downloader in this._downloaders) {
					if (!mydownloader.complete && !mydownloader.started) {
						mydownloader.download();
						
						if (this.active == this._maxdownloads) {
							return;
						}
					}
				}
			}
		}
		
		private function OnProgress(e:ProgressEvent):void {
			this.dispatchEvent(new DownloaderEvent(DownloaderEvent.PROGRESS,this));
		}
		
		private function OnComplete(e:ProgressEvent):void {
			if (this.complete) {
				this.dispatchEvent(new DownloaderEvent(DownloaderEvent.COMPLETE,this));
			} else {
				this.dispatchEvent(new DownloaderEvent(DownloaderEvent.PARTIAL_COMPLETE,this));
				this.startNext();
			}
		}

		private function OnError(e:DownloaderEvent):void {
			dispatchEvent(e);
		}
	}
}