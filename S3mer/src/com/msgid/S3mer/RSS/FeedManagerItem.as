package com.msgid.S3mer.RSS
{
	import com.adobe.xml.syndication.generic.IItem;
	
	public class FeedManagerItem
	{
		public var item:IItem;
		public var timeShown:int;
		
		public function FeedManagerItem()
		{
			timeShown = 0;
		}

	}
}