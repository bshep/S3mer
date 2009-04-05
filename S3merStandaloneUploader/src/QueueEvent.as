package
{
	import flash.events.Event;

	public class QueueEvent extends Event
	{
		private var _fileObj:FileObject;
		
		public static const DELETE:String = "delete";
			
		public function get fileObject():FileObject {
			return this._fileObj;
		}
		
		public function QueueEvent(type:String, fileObj:FileObject, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			this._fileObj = fileObj;
			super(type, bubbles, cancelable);
		}
		
	}
}