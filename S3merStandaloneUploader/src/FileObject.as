package
{
	import flash.filesystem.File;
	
	public class FileObject
	{
		public var _file:File;
		public var _complete:Boolean;

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

	}
}