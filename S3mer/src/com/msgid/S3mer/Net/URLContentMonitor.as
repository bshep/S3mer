package com.msgid.S3mer.Net
{
	import com.msgid.S3mer.Logger;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.events.TimerEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.utils.Timer;

	[Event(name="change",type="flash.events.Event")]
	[Event(name="complete",type="flash.events.Event")]
	[Event(name="io_error",type="flash.events.IOErrorEvent")]

	public class URLContentMonitor extends EventDispatcher
	{
		
		
		private var _url:String;
		private var _rate:int;
		
		private var _timer:Timer;
		
		private var _lastUpdate:Date;
		
		private var _urlContent:String;
		private var _urlLoader:URLLoader;
		
		private var _compareFunction:Function;
		
		// Initialize the monitor with:
		// url: The url to monitor
		// rate: The number of seconds to wait between checks
		public function URLContentMonitor(url:String, rate:int)
		{
			super();
			
			_url = url;
			_rate = rate;
			
			_timer = new Timer(_rate*1000);
			_timer.addEventListener(TimerEvent.TIMER, loadURL);
		}
		
		public function get data():String {
			return _urlContent;
		}
		
		public function get lastUpdate():Date {
			return _lastUpdate;
		}
		
		public function set compareFunction(val:Function):void {
			this._compareFunction = val;
		}
		
		public function start():void {
			loadURL();
		}
		
		public function stop():void {
			_timer.stop();
		}
		
		public function reload():void {
			_timer.stop();
			
			loadURL();
		}
		
		private function loadURL(e:TimerEvent = null):void {
			_timer.stop();
			
			_urlLoader = new URLLoader();
			_urlLoader.addEventListener(Event.COMPLETE, loadURL_complete);
			_urlLoader.addEventListener(IOErrorEvent.IO_ERROR, loadURL_error);
			_urlLoader.load(new URLRequest(_url));
		}
		
		private function loadURL_complete(e:Event):void {
			var _data:String;
			var _compareResult:Boolean;
			
			_data = (e.target as URLLoader).data;
			
			_lastUpdate = new Date();
			
			if(_compareFunction != null) {
				_compareResult = _compareFunction(_data,_urlContent);
				
				if(!_compareResult) {
					//The url has updated!
					_urlContent = _data;
					dispatchEvent(new Event(Event.CHANGE));
				} else {
					dispatchEvent(new Event(Event.COMPLETE));
				}
				
			} else {
				if(_data != _urlContent) {
					//The url has updated!
					_urlContent = _data;
					dispatchEvent(new Event(Event.CHANGE));
				} else {
					dispatchEvent(new Event(Event.COMPLETE));
				}
			}
			
			_timer.start();
			
		}
		
		private function loadURL_error(e:IOErrorEvent):void {
			Logger.addEvent("URLContentMonitor: Error loading URL" + e.text);
		}
	}
}