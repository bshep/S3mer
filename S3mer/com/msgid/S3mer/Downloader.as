package com.msgid.S3mer
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.events.ProgressEvent;
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	
	public class Downloader extends EventDispatcher
	{
		private var _loader:URLLoader;
		public var _url:String;
		private var _filename:String;
		private var _lastPercentage:int;
		private var _complete:Boolean;
		private var _started:Boolean;
		private var _hash:String;
		private var _forceUpdate:Boolean;
		private var _screenId:String;
		

		public function get percent():int {
			return this._lastPercentage;		
		}
		
		public function get complete():Boolean {
			return this._complete;
		}
		
		public function get bytesTotal():int {
			return this._loader.bytesTotal;
		}
		
		public function get bytesLoaded():int {
			return this._loader.bytesLoaded;
		}
		
		public function get started():Boolean {
			return this._started;
		}
		
		public function get url():String {
			return this._url;
		}
		
		public function Downloader(url:String, hash:String, destination:String, forceUpdate:Boolean, screenId:String) {
			this._lastPercentage = 0;
			this._url = url;
			this._complete = false;
			this._started = false;
			this._hash = hash;
			this._forceUpdate = forceUpdate;
			this._screenId = screenId;
			
			if (destination == null) {
				this._filename = FileIO.Url2Filename(this._url);
			} else {
				this._filename = destination;
			}
		}
		
		public function download():void {
			var _loaderReq:URLRequest;

			this._started = true;
			if (isAlreadyDownloaded() && ( this._forceUpdate == false )) {
				Logger.addEvent("File: " + this._filename + " has already been downloaded.");
				this._complete = true;
				this.dispatchEvent(new DownloaderEvent(DownloaderEvent.COMPLETE, this));
				return;
			} else {
				Logger.addEvent("File: " + this._filename + " will be downloaded.");	
			}
	
			try {
				_loader = new URLLoader();
				_loader.addEventListener(Event.COMPLETE,OnDownloadComplete,false,0,true);			
				_loader.addEventListener(ProgressEvent.PROGRESS,OnProgress,false,0,true);
				_loader.addEventListener(IOErrorEvent.IO_ERROR,OnIOError,false,0,true);
				_loader.dataFormat = URLLoaderDataFormat.BINARY;


				_loaderReq = new URLRequest(this._url);

				_loader.load(_loaderReq);
			} catch(e:Error) {
				Logger.addEvent("Net connection error");
				this.dispatchEvent(new DownloaderEvent(DownloaderEvent.ERROR, this));			
			}
		}
		
		private function OnIOError(e:IOErrorEvent):void {
			Logger.addEvent("Net connection error");
			this._complete = true;
			this.dispatchEvent(new DownloaderEvent(DownloaderEvent.ERROR, this));			
		}
		
		private function OnProgress(e:ProgressEvent):void {
			var newPercentage:int = Math.floor((e.bytesLoaded/e.bytesTotal)*100);
			
			if (newPercentage > this._lastPercentage ) {
				Logger.addEvent("Downloaded " + newPercentage + "% of file");
				this._lastPercentage = newPercentage;
			}
			this.dispatchEvent(new DownloaderEvent(DownloaderEvent.PROGRESS, this, newPercentage));
		}
		
		private function isAlreadyDownloaded():Boolean {
			return FileIO.fileExists(this._filename, this._screenId);
		}
		
		public function stop():void {
			this._loader.close();
		}
		
		private function OnDownloadComplete(e:Event):void {
			var myStorageDir:File;
			var myStorageFile:File;
			var myStorageStream:FileStream = new FileStream();

			this._complete = true;
			this._lastPercentage = 100;

			myStorageDir = new File(FileIO.mediaPath(this._screenId,""));
			myStorageDir.createDirectory();
			
			myStorageFile = myStorageDir.resolvePath(this._filename);
			
			myStorageStream.open(myStorageFile,FileMode.WRITE);
			
			myStorageStream.writeBytes(_loader.data,0,_loader.bytesLoaded);
			
			myStorageStream.close();
			
			if( !isAlreadyDownloaded() ) {
				Logger.addEvent("Hash failed!!! File: " + this._filename + " URL: " + this._url)
			}
			
			this.dispatchEvent(new DownloaderEvent(DownloaderEvent.COMPLETE, this));
		}
		
	}
}