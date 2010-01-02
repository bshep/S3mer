package com.s3mer.util
{
	import mx.collections.ArrayCollection;
	
	public class DownloadQueueItem
	{
		public var url:String;
		public var destination:ArrayCollection;
		public var completed:Boolean;
		
		
		public function DownloadQueueItem(_url:String, _destination:String)
		{
			this.url = _url;
			
			this.destination = new ArrayCollection();
			this.destination.addItem(_destination);
			this.completed = false;
		}
		
		public function addDestination(_destination:String):void {
			this.destination.addItem(_destination);
		}

	}
}