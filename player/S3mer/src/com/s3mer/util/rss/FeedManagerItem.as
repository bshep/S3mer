package com.s3mer.util.rss	
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