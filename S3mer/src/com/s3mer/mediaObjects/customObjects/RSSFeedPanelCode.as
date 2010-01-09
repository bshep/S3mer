	import com.adobe.xml.syndication.generic.FeedFactory;
	import com.adobe.xml.syndication.generic.IFeed;
	import com.adobe.xml.syndication.generic.IItem;
	import com.s3mer.util.net.URLContentMonitor;
	import com.s3mer.util.rss.FeedManager;
	
	import mx.controls.Label;
	import mx.events.EffectEvent;
	import mx.events.FlexEvent;
	
	public function set color(val:String):void {
		this._color = val;
		
		if (headtext1 != null ) { 
			this.headtext.setStyle("color","#" + val);
			this.bodytext.setStyle("color","#" + val);
		}
	}
	
	private function set mode(val:int):void {
		if( this._stopped == true ) {
			this._mode = val;
		}
	}
	
	public function get color():String {
		return this._color;
	}
	
	public var delay:int;
	
	public var rssURL:String;
	public var logoURL:String;
	
	private var _playNextTimer:Timer;
	
	private var _feedItem:IItem;
	private var _feedManager:FeedManager = new FeedManager(50);

	private var _urlMonitor:URLContentMonitor;
	
	private var _color:String;
	
	private var _stopped:Boolean = false;
	private var _mode:int = MODE_SCROLL;
	
	public static const MODE_SLIDE:int = 0;
	public static const MODE_SCROLL:int = 1;
	
	public var SCROLL_TIMER_DELTA:Number = -3.5;
	
	private function get headlineDescription():String {
		var ret:String;
		
		if(_feedItem != null) {
			ret = _feedItem.excerpt.value;
		}
		
		return ret;
	}
	
	private function get headlineTitle():String {
		var ret:String;
		
		if(_feedItem != null) {
			ret = _feedItem.title.toString();
		}
		
		return ret;
	}
	
	private function get numHeadlines():int {
		return this._feedManager.length;
	}
	
	private function OnRSSLoad_complete(e:Event):void {
		var _feed:IFeed;
//		this.visible = true;
		
		try {
			var tmpRSS:XML = new XML(_urlMonitor.data); // Load data into a temp variable
		} catch (e:Error) {
			return; //TODO: Detect the loading error...
		}
		
		_feed = FeedFactory.getFeedByXML(tmpRSS);
		
		this._feedManager.addItems(_feed);

		if(this._feedItem == null) {
			this._feedItem = this._feedManager.getNextItem();
		}
		
//		this.logoUrl = _feed.metadata.image.url;
//		updateLogoURL();
		
		if( this._playNextTimer != null && this._playNextTimer.running ) {
			this._playNextTimer.stop();
		}
		
		switch(this._mode) {
			case MODE_SCROLL:
				break;
			case MODE_SLIDE:
				if ( !this._playNextTimer.running ) {
					this._playNextTimer.start();
				}
				break;
		}
		
		this.play_next();
	}
	
	private function OnRSSLoad_error(e:IOErrorEvent):void {
		// What to do?
	}
	
	private function updateLogoURL():void {
		switch(this._mode) {
			case MODE_SLIDE:
				this.logoSWF.source = this.logoURL;
				
				break;
			case MODE_SCROLL:
				if(spacer1 == null || spacer2 == null || spacer3 == null) {
					return;
				}
				this.spacer1.source = this.logoURL;
				this.spacer2.source = this.logoURL;
				this.spacer3.source = this.logoURL;
				
				break;
		}
		
	}
	
	public function play():void {
		trace("RSS URL: " + this.rssURL);
		trace("Logo URL: " + this.logoURL);
		
		if( this._urlMonitor == null ) {
			this._urlMonitor = new URLContentMonitor(this.rssURL,60);
			this._urlMonitor.addEventListener(Event.CHANGE, OnRSSLoad_complete);
			this._urlMonitor.addEventListener(IOErrorEvent.IO_ERROR, OnRSSLoad_error);
		}
		
		switch(this._mode) {
			case MODE_SCROLL:
				this.currentState = "sidescroll";
				break;
			case MODE_SLIDE:
				this.currentState = "";
				play_step2();
				break;
		}
	}
	
	private function play_step2():void {
		
		if (_playNextTimer == null ) {
			_playNextTimer = new Timer(this.delay);
			_playNextTimer.addEventListener(TimerEvent.TIMER, OnPlayNextTimer,false,0,true);
		} else {
			_playNextTimer.delay = this.delay;
		}
		
		if(!this.willTrigger(Event.EXIT_FRAME)) {
			this.addEventListener(Event.EXIT_FRAME, OnScrollTimer);
		}

		this.logoSWF.source = this.logoURL;

		if (headtext != null ) { 
			this.headtext.setStyle("color","#" + this._color);
		}
		
		if (bodytext != null ) {
			this.bodytext.setStyle("color","#" + this._color);
		}
		
		this._stopped = false;
		this._urlMonitor.start();
	}
	
	public function changeToSideScroll(e:FlexEvent):void {
		this.headtext1.x = this.width;
		this.headtext2.x = this.width+50;
		this.headtext3.x = this.width+100;
		
		this.headtext1.setStyle("color","#" + this._color);
		this.headtext2.setStyle("color","#" + this._color);
		this.headtext3.setStyle("color","#" + this._color);
		this.spacer1.source = this.logoURL;
		this.spacer2.source = this.logoURL;
		this.spacer3.source = this.logoURL;
		
		this.headline.mask = this.headline_mask;
		
		repositionSideScrollElements();
		play_step2();
	}
	
	public function repositionSideScrollElements():void {
		this.headtext1.validateNow();
		this.headtext2.validateNow();
		this.headtext3.validateNow();
		
		var currState:int = calculateState();
		
		switch(currState) {
			case 1:
				this.spacer1.x = this.headtext1.x + this.headtext1.textWidth + 20;
				this.headtext2.x = this.spacer1.x + this.spacer1.width + 20;
				this.spacer2.x = this.headtext2.x + this.headtext2.textWidth + 20;
				this.headtext3.x = this.spacer2.x + this.spacer2.width + 20;
				this.spacer3.x = this.headtext3.x + this.headtext3.textWidth + 20;
				
				break;
			case 2:
				this.spacer2.x = this.headtext2.x + this.headtext2.textWidth + 20;
				this.headtext3.x = this.spacer2.x + this.spacer2.width + 20;
				this.spacer3.x = this.headtext3.x + this.headtext3.textWidth + 20;
				this.headtext1.x = this.spacer3.x + this.spacer3.width + 20;
				this.spacer1.x = this.headtext1.x + this.headtext1.textWidth + 20;
				
				break;
			case 3:
				this.spacer3.x = this.headtext3.x + this.headtext3.textWidth + 20;
				this.headtext1.x = this.spacer3.x + this.spacer3.width + 20;
				this.spacer1.x = this.headtext1.x + this.headtext1.textWidth + 20;
				this.headtext2.x = this.spacer1.x + this.spacer1.width + 20;
				this.spacer2.x = this.headtext2.x + this.headtext2.textWidth + 20;
				
				break;
		}
		

	}
	
	private function calculateState():int {
		if( headtext2.x > 0 && headtext3.x > 0 ) {
			return 1;
		}
		
		if( headtext2.x <= 0 && headtext3.x > 0 ) {
			return 2;
		}
		
		if( headtext3.x <= 0 && headtext1.x > 0 ) {
			return 3;
		}
		
		return 1;
	}
	
	public function stop():void {
		if(this._playNextTimer)
			this._playNextTimer.stop();
		if(this._urlMonitor) 
			this._urlMonitor.stop();
			
		if(this.willTrigger(Event.EXIT_FRAME)) 
			this.removeEventListener(Event.EXIT_FRAME, OnScrollTimer);
			
		this._stopped = true;
	}
	
	public function resume():void {
		this._stopped = false;
		this._playNextTimer.start();
		this._urlMonitor.start();
	}
	
	private function scrollChangeFontSize(newSize:String):void {
		this.headtext1.setStyle("fontSize", newSize);
		this.headtext2.setStyle("fontSize", newSize);
		this.headtext3.setStyle("fontSize", newSize);
		this.spacer1.setStyle("fontSize", newSize);
		this.spacer2.setStyle("fontSize", newSize);
		this.spacer3.setStyle("fontSize", newSize);
		
		repositionSideScrollElements();
	}
	
	private function OnScrollTimer(e:Event):void {
		var currState:int = calculateState();
		
		switch(currState) {
			case 1:
				this.headtext1.x += SCROLL_TIMER_DELTA;
				break;
			case 2:
				this.headtext2.x += SCROLL_TIMER_DELTA;
				break;
			case 3:
				this.headtext3.x += SCROLL_TIMER_DELTA;
				break;
		}
		
		
		repositionSideScrollElements();
		
		if( currState != calculateState() ) {
			switch(currState) {
				case 1:
					updateHeadline(headtext1);
					break;
				case 2:
					updateHeadline(headtext2);
					break;
				case 3:
					updateHeadline(headtext3);
					break;
			}
		}
	}

	
	private function OnPlayNextTimer(e:TimerEvent):void {
		if ( this._stopped == true ) {
			return;
		}

		this._playNextTimer.stop();
		
		play_next();
	}
	
	private function play_next():void {
		switch(this._mode) {
			case MODE_SCROLL:
					play_next_scroll();
				break;
			case MODE_SLIDE:				
				if(this.y > this.screen.height/2) { 
					this.currentState = "hidden_bottom";
				} else {
					this.currentState = "hidden_top";
				}
				break;
		}
	}
	
	private function next_headline():void {
		if ( this._stopped == true ) {
			return;
		}
		
		if(this._feedManager.length == 0) {
			return;
		}
		
		this._feedItem = this._feedManager.getNextItem();
	}
	
	private function isHeadlineEmpty():Boolean {
		if(this.headlineDescription == "") {
			return true;
		} else {
			return false;
		}
		
	}
	
	private function play_next_stage2(e:EffectEvent):void {
		if(isHeadlineEmpty()) {
			next_headline();
		}
										
		if (this.numHeadlines == 0) {
			headtext.htmlText = this.rssURL;
			bodytext.htmlText = "Actualizando..."
		} else {
			headtext.htmlText = this.cleanupHTML(this.headlineTitle);
			bodytext.htmlText = this.cleanupHTML(this.headlineDescription);
		}
		
		this.truncateText(headtext, this.headline.width);
		this.truncateText(bodytext, this.headline.width);
		
		this.currentState = "";
		next_headline();
	}
	
	private function updateHeadline(obj:Label):void {
		if(isHeadlineEmpty()) {
			next_headline();
		}

		obj.htmlText = this.cleanupHTML(this.headlineTitle);
		obj.validateNow();
		obj.width = obj.textWidth;
		
		next_headline();
		
	}
	
	private function play_next_scroll():void {
		if(isHeadlineEmpty()) {
			next_headline();
		}

		if (this.numHeadlines == 0) {
			headtext1.htmlText = this.rssURL;
			headtext2.htmlText = "Actualizando..."
		} else {
			headtext1.htmlText = this.cleanupHTML(this.headlineTitle);
			next_headline();
			headtext2.htmlText = this.cleanupHTML(this.headlineTitle);
			next_headline();
			headtext3.htmlText = this.cleanupHTML(this.headlineTitle);
		}
		
		this.headtext1.validateNow();
		
		this.headtext2.validateNow();
		
		this.headtext3.validateNow();
		
		this.headtext1.width = this.headtext1.textWidth + 10;
		this.headtext2.width = this.headtext2.textWidth + 10;
		this.headtext3.width = this.headtext3.textWidth + 10;
		
		repositionSideScrollElements();
						
		next_headline();
	}
	
	
	private function cleanupHTML(htmlToClean:String):String {
		var ret:String;
		
		if( htmlToClean != null ) { 
			ret = htmlToClean;
			ret = keepFirstStringBeforeTag(ret,/<p>/i);
			ret = keepFirstStringBeforeTag(ret,/<br>/i);
			ret = ret.replace(/^\s/,"");
		}
		
		return ret;
	}
	
	private function keepFirstStringBeforeTag(htmlStr:String,tag:RegExp):String {
		var ret:String = "";
		var retArr:Array;
		var index:int = 0;
		
		retArr = htmlStr.split(tag);
		
		for(index = 0; index < retArr.length; index++ ) {
			if(retArr[index].toString().replace(" ","") != "") {
				ret = retArr[index];
				break;
			}
		}
		
		
		return ret;
	}
	
	// This function truncates text in bodytext and headertext
	private function truncateText(obj:Label, width:Number):void {
		var txtWidth:Number;
		var txt:String;
		var txtArr:Array;
		
		obj.validateNow();
		txt = obj.text;
//		Logger.addEvent("Truncate Text In: " + txt);
		txtWidth = obj.measureText(txt).width;
		
		while ( txtWidth > width && txt.length > 5 ) {
			txtArr = txt.split(" ");
			txtArr.pop();
			
			txt = txtArr.join(" ") + "...";


			txtWidth = obj.measureText(txt).width;
//			Logger.addEvent("Truncate Text Out");
		}
//		Logger.addEvent("Truncate Text Out: " + txt);
		
		obj.text = txt;
	}
	
