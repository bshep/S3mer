package com.s3mer.util
{
	import mx.collections.ArrayCollection;
	
	public class DownloadQueueItem
	{
		public var url:String;
		public var destinations:ArrayCollection;
		public var completed:Boolean;
		
		
		public function DownloadQueueItem(_url:String, _destination:String)
		{
			this.url = _url;
			
			this.destinations = new ArrayCollection();
			this.destinations.addItem(_destination);
			this.completed = false;
		}
		
		public function addDestination(_destination:String):void {
			this.destinations.addItem(_destination);
		}

	}
}