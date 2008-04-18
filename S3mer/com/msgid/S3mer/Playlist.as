package com.msgid.S3mer
{
	import mx.collections.ArrayCollection;
	
	public class Playlist
	{
		private var _items:ArrayCollection;
		private var _position:int;
		
		public var id:String;
		
		
		private var _EOL:Boolean = false; //End of list reached during playback	
		private var _BOL:Boolean = false; //Begining of list reached during playback	
		public function get EOL():Boolean {
			return this._EOL;
		}
		
		public function get BOL():Boolean {
			return this._BOL;
		}
		
		public function Playlist() {
			this._items = new ArrayCollection();
			this._position = 0;
		}
		
/* 		public function add(url:String):void {
			this._items.addItem(new PlaylistObject(url));
		}
 */		
		public function addObj(obj:PlaylistObject):void {
			this._items.addItem(obj);
		}
		
		public function get length():int {
			return this._items.length;
		}
		
		public function first():void {
			if(this._items.length == 0) {
				return;// null;
			}
			
			this._EOL = false;
			this._BOL = true;
			this._position = 0;
			//return PlaylistObject(this._items.getItemAt(0));
		}
		
		public function next():void {
			//var tmpObj:PlaylistObject;
			
			if(this._items.length == 0) {
				return;
			}
			
			//tmpObj = PlaylistObject(this._items.getItemAt(this._position));
			
			this._position++;
			this._EOL = false;
			this._BOL = false;

			if(this._position > this._items.length - 1) {
				this._position = 0;
				this._EOL = true;
			}
			
			//return tmpObj;
		}
		
		public function previous():void {
			if(this._items.length == 0) {
				return;
			}
			
			this._position--;

			if(this._position < 0) {
				this._position = this._items.length - 1;
			}
			
			if(this._position == 0) {
				this._BOL = true;
			}
			
			//return PlaylistObject(this._items.getItemAt(this._position));
		}

		public function get current():PlaylistObject {
			if ( this._items.length == 0 ) {
				return null;
			} else {
				return this._items.getItemAt(this._position) as PlaylistObject;
			}
		}

		public function random():PlaylistObject {
			if(this._items.length == 0) {
				return null;
			}
		
			this._position = Math.round(Math.random()*(this._items.length-1));

			if(this._items.length < this._position+1) {
				this._position = 0;
			}
			
			return PlaylistObject(this._items.getItemAt(this._position));
		}

		public function get avaiable():Boolean {
			var _itemType:String
			
			for each (var item:PlaylistObject in this._items) {
				_itemType = (item.configXML.@type).toString();
				
				if( _itemType != "podcast" &&  _itemType != "rss" ) {
					if(!item.avaiable) {
						return false;
					}
				}
			}
			return true;
		}
		
		public function get pendingFiles():ArrayCollection {
			var pending:ArrayCollection = new ArrayCollection();
			
			for each (var item:PlaylistObject in this._items) {
				Logger.addEvent("Item Type: " + item.configXML.@type);
				switch((item.configXML.@type).toString()) {
					case "video":
					case "image":
					case "swf":
						if(!item.avaiable) {
							pending.addItem(item);
						}
						break;
					case "podcast":
						PodcastManager.addPodcast(item)
						break;
					
				}
			}
			return pending;
		}
	}
	

}