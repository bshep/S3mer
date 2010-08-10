package com.s3mer.util.rss
{
	import com.adobe.xml.syndication.generic.IFeed;
	import com.adobe.xml.syndication.generic.IItem;
	
	import mx.collections.ArrayList;
	
	public class FeedManager
	{
		private var _maxSize:int;
		private var _feedItems:ArrayList;
		
		public function FeedManager(maxSize:int)
		{
			_maxSize = maxSize;
			this._feedItems = new ArrayList();
		}

		public function get length():int {
			return this._feedItems.length;
		}
		
		public function addItems(feed:IFeed):void {
			var _topItem:FeedManagerItem;
			var _startIndex:int;
			var _a:int;
			var _shownTimes:int;
			
			if( _feedItems.length > 0 ) {
				_topItem = _feedItems.getItemAt(0) as FeedManagerItem;
			} else {
				_topItem = null;
			}
			
			_startIndex = feed.items.length-1;
			if(_topItem != null) {
				for( _a = 0; _a < feed.items.length; _a++) {
					if(_topItem.item.title == (feed.items[_a] as IItem).title) {
						_startIndex = _a - 1;
						break;
					}
				}
			}
			
			if(_startIndex == -1) {
				return;
			}
			
			_shownTimes = findLeastShownItemShowings();
			
			for( _a = _startIndex; _a >= 0; _a--) {
				var _tmpFeedItem:FeedManagerItem = new FeedManagerItem();
				
				_tmpFeedItem.item = feed.items[_a];
				
				this._feedItems.addItemAt(_tmpFeedItem,_shownTimes);
			}
			
			
			//Cleanup extra items
			while( this._feedItems.length > _maxSize ) {
				this._feedItems.removeItemAt(_maxSize);
			}
		}
		
		public function getNextItem():IItem {
			var _leastShown:FeedManagerItem = findLeastShownItem();
			
			_leastShown.timeShown++;
			
			return _leastShown.item;
		}
		
		private function findLeastShownItemShowings():int {
			var _leastShown:FeedManagerItem;
			
			_leastShown = findLeastShownItem();
			
			if(_leastShown != null) {
				return _leastShown.timeShown
			} else {
				return 0;
			}
		}
		
		private function findLeastShownItem():FeedManagerItem {
			var _leastShown:FeedManagerItem;
			
			if(this._feedItems.length == 0) {
				return null;
			}
			
			_leastShown = this._feedItems.getItemAt(0) as FeedManagerItem;
			
			for(var a:int = 1; a < this._feedItems.length; a++) {
				var tmpItem:FeedManagerItem = this._feedItems.getItemAt(a) as FeedManagerItem;
				if(tmpItem.timeShown < _leastShown.timeShown) {
					_leastShown = tmpItem;
				}
			}
			
			return _leastShown;
		}
	}
}