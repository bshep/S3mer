package com.msgid.S3mer
{
	import mx.collections.ArrayCollection;

	public class PlaylistCollection extends ArrayCollection
	{
		private var _currentPlaylist:Playlist;
		private var _currentItem:PlaylistObject;
		private var _EOL:Boolean; // Indicates if we are at the end of the playlists
		
		public function get EOL():Boolean {
			return _EOL;
		}
		
		public function rewind():void {
			this._EOL = false;
			this._currentPlaylist = null;
			this.nextPlaylist();
			this._currentPlaylist.rewind();
		}
		
		public function PlaylistCollection(source:Array=null)
		{
			super(source);
			
			this._EOL = false;
		}
		
		public function get item():PlaylistObject {
			return this._currentPlaylist.current;
		}
		
		public function get playlist():Playlist {
			return this._currentPlaylist;
		}
		
		
		// Moves to the next item in the lists
		public function nextItem():PlaylistObject {
			if(_currentPlaylist == null) {
				nextPlaylist();
			}
			
			// Check if we are at the end of the lists, if so dont change anything
			if(_EOL == false) {
				this._currentPlaylist.next(); // Go to the next item
				
				// If we moved to the last item, the move to the next playlist, this can set EOL...
				if(this._currentPlaylist.EOL) {
					nextPlaylist();
					this._currentPlaylist.rewind();
				}
			}
			
			return this._currentPlaylist.current;	
		}
		
		// Moves to the next list, return true is wrapped around
		public function nextPlaylist():Playlist {
			var ret:Playlist = null;
			var currIndex:int;
			
			//Find the current playlist in the array...
			if(_currentPlaylist != null) {
				currIndex = getItemIndex(_currentPlaylist);
			} else {
				currIndex = -1;
			}
			
			// This does one full loop through all the playlists, stopping when one is found to be available
			for ( var a:int = 0; a < this.length; a++ ) {
				
				if((a+currIndex) >= this.length - 1 ) {
					ret = _currentPlaylist;
					this._EOL = true;
					break;
				}

				//Starts at the next playlist index (a+currIndex+1), then do modular division to loop to the begining
				if ((this.getItemAt((a + currIndex + 1) % this.length) as Playlist).available) {
					this._currentPlaylist = this.getItemAt(a) as Playlist;
					this._currentPlaylist.rewind();
					break;
				}
				
			}
			return ret;
		}
	}
}