	import mx.formatters.DateFormatter;
	
	private var _timer:Timer;
	private var _timeFormat:String = "12hr";
				
	public function play():void {
		setupTimer();
		update();
		_timer.start();
	}
	
	public function set format(val:String):void {
		this._timeFormat = val;
	}
	
	public function set timeColor(timeColor:String):void {
		this.txtTime.setStyle("color",timeColor);	
	}
	
	public function set dateColor(dateColor:String):void {
		this.txtDate.setStyle("color",dateColor);	
	}
	
	private function setupTimer():void {
		if (_timer == null) {
			_timer = new Timer(60*1000);
			_timer.addEventListener(TimerEvent.TIMER, OnTimer,false,0,true);
		}
	}
	
	private function OnTimer(e:TimerEvent):void {
		this._timer.stop();
		this.update();
		this._timer.start();
	}
	
	private function update():void {
		var datenow:Date = new Date();
		var df:DateFormatter = new DateFormatter();
		
		switch ( resourceManager.localeChain[0] ) {
			case "en_US":
			default:
				df.formatString = "MMMM D";
				break;
			case "pt_BR":
				df.formatString = "D de MMMM";
				break;
			case "es_ES":
				df.formatString = "D de MMMM";
				break;
			case "it_IT":
				df.formatString = "D MMMM";
				break;
		}
		
		this.txtDate.text = df.format(datenow).toUpperCase();
		
		if( this._timeFormat == "24hr" ) { 
			df.formatString = "J:NN";
		} else {
			df.formatString = "L:NN A";
		}				
		
		this.txtTime.text = df.format(datenow).toUpperCase();
		
		this._timer.delay = (60 - datenow.getSeconds())*1000;
	}
	
	public function stop():void {
		if(this._timer)
			this._timer.stop();
	}
	
