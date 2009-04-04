package com.msgid.S3mer
{
	import air.update.ApplicationUpdater;
	import air.update.events.DownloadErrorEvent;
	import air.update.events.StatusFileUpdateErrorEvent;
	import air.update.events.StatusUpdateErrorEvent;
	import air.update.events.UpdateEvent;
	
	import flash.events.ErrorEvent;
	import flash.events.Event;
	
	import mx.controls.Alert;
	
	
	public class S3merApplicationUpdater
	{
		private var appUpdater:ApplicationUpdater = new ApplicationUpdater();

		public function S3merApplicationUpdater() {
			// updater stuff
		    appUpdater.updateURL = "http://www.s3mer.com/app/checkversion.php?version=2";
		    appUpdater.delay = 15*60*1000; // Check-every 15 minutes.
		    appUpdater.addEventListener(UpdateEvent.INITIALIZED, onUpdateInitialized);
		    appUpdater.addEventListener(ErrorEvent.ERROR, onUpdateError);
		    appUpdater.addEventListener(DownloadErrorEvent.DOWNLOAD_ERROR, onUpdateError);
		    appUpdater.addEventListener(StatusFileUpdateErrorEvent.FILE_UPDATE_ERROR, onUpdateError);
		    appUpdater.addEventListener(StatusUpdateErrorEvent.UPDATE_ERROR, onUpdateError);

		    appUpdater.initialize();
		}

		private function onUpdateInitialized(event:UpdateEvent):void
		{
			appUpdater.checkNow();
		}
		
		private function onUpdateError(event:Event):void
		{
			(event.target as ApplicationUpdater).delay = 0;
			(event.target as ApplicationUpdater).cancelUpdate();
        	Alert.show('Contact us s3mer.team@s3mer.com and report this\n\n' + event.toString(), 'Updater Error');
		}

	}
}