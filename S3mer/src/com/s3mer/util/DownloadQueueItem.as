package com.s3mer.util
{
	import flash.filesystem.File;
	
	import mx.collections.ArrayCollection;
	
	public class DownloadQueueItem
	{
		public var url:String;
		public var destinations:ArrayCollection;
//		public var completed:Boolean;
		
		public function get completed():Boolean {
			var ret:Boolean = true;
			var file:File;
			
			for each( var destination:String in destinations ) {
				file = new File(destination).resolvePath(FileIO.Url2Filename(this.url));
				if( file.exists == false ) {
					ret = false;
					break;
				}
			}
			
			return ret;
		}
		
		public function DownloadQueueItem(_url:String, _destination:String)
		{
			this.url = _url;
			
			this.destinations = new ArrayCollection();
			this.destinations.addItem(_destination);
//			this.completed = false;
		}
		
		public function addDestination(_destination:String):void {
			this.destinations.addItem(_destination);
		}

	}
}