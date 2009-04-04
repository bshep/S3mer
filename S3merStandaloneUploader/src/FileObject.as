package
{
	import flash.filesystem.File;
	
	public class FileObject
	{
		public var _file:File;
		public var _complete:Boolean;
		public var _uploading:Boolean;
		public var _error:Boolean;
		public var _status:String;

		public function FileObject(file:File)
		{
			this._file = file;
			this._complete = false;
		}
		
		public function get file():File {
			return this._file;
		}
		
		public function get complete():Boolean {
			return this._complete;
		}
		
		public function set complete(val:Boolean):void {
			this._complete = val;
		}
		
		public function get error():Boolean {
			return _error;
		}
		
		public function set error(val:Boolean):void {
			this._error = val;	
		}
		
		public function get uploading():Boolean {
			return _error;
		}
		
		public function set uploading(val:Boolean):void {
			this._uploading = val;	
		}
		
		public function get canUpload():Boolean {
			if( this._complete == false && this._error == false && this._uploading == false ) {
				return true;
			} else {
				return false;
			}
		}
		
		public function get status():String {
			if( this._complete ) {
				return "Complete";
			} else {
				if( this._error ) {
					return "Error";
				}
				
				if( this._uploading ) {
					return "Uploading";
				} else {
					return "Waiting";
				}
			}			
		}

	}
}