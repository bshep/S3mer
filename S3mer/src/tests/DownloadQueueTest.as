package tests
{
	
	import com.s3mer.events.DownloadEvent;
	import com.s3mer.util.DownloadQueue;
	import com.s3mer.util.FileIO;
	
	import flash.events.Event;
	import flash.filesystem.File;
	
	import flexunit.framework.TestCase;

	public class DownloadQueueTest extends TestCase
	{

		public function testQueueAddAndDownload():void {
			DownloadQueue.addItem("http://www.s3mer.com", FileIO.mediaPath(99));
			
			DownloadQueue.eventDispatcher.addEventListener(DownloadEvent.QUEUE_COMPLETE, addAsync(_QueueDownloadComplete,10000));
			
			DownloadQueue.start();
		}	
		
		public function _QueueDownloadComplete(e:Event):void {
			var _file:File = new File(FileIO.mediaPath(99)).resolvePath("www.s3mer.com");
			
			assertTrue("DownloadQueue: File exists at - " + _file.nativePath, _file.exists);
			
			_file = new File(FileIO.mediaPath(99))
			_file.deleteDirectory(true);
		}
		
	}
}