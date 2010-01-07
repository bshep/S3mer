	import mx.formatters.DateFormatter;
	
	private var _timer:Timer;
	private const _origWidth:int = 251;
	private const _origHeight:int = 81;
	
	private var resizing:Boolean = false;
	private var _timeFormat:String = "12hr";
				
	public function play():void {
		setupTimer();
		update();
		_timer.start();
	}
	
	public function setFormat(val:String):void {
		this._timeFormat = val;
	}
	
	public function setTimeColor(timeColor:String):void {
		this.txtTime.setStyle("color",timeColor);	
	}
	
	public function setDateColor(dateColor:String):void {
		this.txtDate.setStyle("color",dateColor);	
	}
	
	public function resize(scaleX:Number, scaleY:Number):void {
		if ( this.resizing == false ) {
			this.resizing = true;
			
			var scale:Number;
			
	//		if (scaleX >scaleY ) {
	//			scale = scaleY;
	//		} else {
	//			scale = scaleX;
	//		}
			
			scale = scaleY;
			
//			this.txtDate.setStyle("top",8*scale);
			
			this.txtTime.setStyle("fontSize", (Math.floor(43*scale)).toString());
			this.txtDate.setStyle("fontSize", (Math.floor(20*scale)).toString());
			
			this.txtDate.y = 8*scale + 5;
			this.txtTime.y = this.txtDate.y + this.txtDate.measureText(this.txtDate.text).height - 8;
			
			var adjustingFonts:Boolean = true;
			do {					
				if ( this.txtTime.y + this.txtTime.measureText(this.txtTime.text).height > this.height ) {
					var fontSize:Number = Number(this.txtDate.getStyle("fontSize"));
					
					fontSize -= 1;
					this.txtDate.setStyle("fontSize", fontSize.toString());
					this.txtTime.y = this.txtDate.y + this.txtDate.measureText(this.txtDate.text).height - 8;
	//				Logger.addEvent("re-adjusting fonts");
				} else {
					adjustingFonts = false;
				}
			} while( adjustingFonts && fontSize > 2 )
	
	
			this.txtTime.width = this.width;
			this.txtDate.width = this.width;
			
			this.resizing = false;
		}
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
	
