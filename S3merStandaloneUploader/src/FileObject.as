package
{
	import flash.filesystem.File;
	import flash.net.URLRequest;
	
	public class FileObject
	{
		private var _file:File;
		private var _complete:Boolean;
		private var _uploading:Boolean;
		private var _error:Boolean;
		private var _status:String;
		private var _urlRequest:URLRequest;
		

		public function FileObject(file:File)
		{
			this._file = file;
			this._complete = false;
		}
		
		[Bindable]
		public function get file():File {
			return this._file;
		}
		
		public function set file(val:File):void {
			this._file = val;
		}
		
		[Bindable]
		public function get complete():Boolean {
			return this._complete;
		}
		
		public function set complete(val:Boolean):void {
			this._complete = val;
		}
		
		[Bindable]
		public function get error():Boolean {
			return this._error;
		}
		
		public function set error(val:Boolean):void {
			this._error = val;	
		}
		
		[Bindable]
		public function get uploading():Boolean {
			return this._uploading;
		}
		
		public function set uploading(val:Boolean):void {
			this._uploading = val;	
		}
		
		[Bindable]
		public function get urlRequest():URLRequest {
			return this._urlRequest;
		}
		
		public function set urlRequest(val:URLRequest):void {
			this._urlRequest = val;	
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