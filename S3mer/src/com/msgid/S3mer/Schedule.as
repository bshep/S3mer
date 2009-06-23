package com.msgid.S3mer
{
	import com.msgid.S3mer.Utility.LoggerManager;

	public class Schedule
	{
		public var id:String;

		private var dateStart:Date;
		private var dateEnd:Date;
		private var	timeStart:Date;
		private var timeEnd:Date;
		private var days:Array; // index into array is the day, sunday = 0, the value indicated wether it is active that day
		private var ampm:Array; // indexes -> 0:AM 1:PM
		

		public function get valid():Boolean {
			return true;
		}
		
		public function get isPlayable():Boolean {
			var nowDate:Date = new Date();
			var tmpDate:Date = new Date();
			
			if(nowDate < dateStart) {
				return false;				
			}
			
			if(nowDate > dateEnd) {
				return false;
			}
			
			tmpDate.time = 0;
			tmpDate.setHours(nowDate.hours, nowDate.minutes, nowDate.seconds,0);
			
			if( getTimePart(timeStart.toTimeString()) != "00:00:00" || getTimePart(timeEnd.toTimeString()) != "00:00:00" ) {
				if(tmpDate < timeStart && getTimePart(timeStart.toTimeString()) != "00:00:00") {
					return false;
				}
				
				if(tmpDate > timeEnd && getTimePart(timeEnd.toTimeString()) != "00:00:00") {
					return false;
				}
			}
			
			if( this.ampm[0] != this.ampm[1] ) {
				if( this.ampm[0] == "0" && nowDate.getHours() < 12 ) {
					return false;
				}
				
				if( this.ampm[1] == "0" && nowDate.getHours() >= 12 ) {
					return false;
				}
			}
			
			if( this.days[nowDate.getDay()] == "0" ) {
				return false;
			}
			
			return true;
		}
		
		public function Schedule(scheduleXML:XML):void {
			LoggerManager.addEvent("Schedule id: " + scheduleXML.@id);
			this.id = scheduleXML.@id;
			
			this.days = new Array(7);
			this.ampm = new Array(2);
							
			for each (var conditionXML:XML in scheduleXML.condition) {
				
				switch(conditionXML.@type.toString()) {
					case "daterange":
						this.dateStart = new Date();
						this.dateEnd = new Date();
						
						this.setDateFromString(this.dateStart, conditionXML.@startdate);
						this.setDateFromString(this.dateEnd, conditionXML.@enddate);

						LoggerManager.addEvent("- Start: " + dateStart.toDateString() + " End: " + dateEnd.toDateString());
						break;
					case "timerange":
						this.timeStart = new Date();
						this.timeEnd = new Date();
						
						this.setTimeFromString(this.timeStart, conditionXML.@starttime);
						this.setTimeFromString(this.timeEnd, conditionXML.@endtime);
					
						LoggerManager.addEvent("- Start: " + timeStart.toTimeString() + " End: " + timeEnd.toTimeString());
						break;							
					case "dayweek":
						this.days[getDayIndex(conditionXML.@day)] = conditionXML.@value.toString();
						
						LoggerManager.addEvent(" - Day: " + getDayIndex(conditionXML.@day) + " Value: " + conditionXML.@value);
						break;
					case "ampm":
					
						this.ampm[0] = conditionXML.@AM.toString()
						this.ampm[1] = conditionXML.@PM.toString()
						LoggerManager.addEvent(" - AM: " + conditionXML.@AM + " PM: " + conditionXML.@PM);
						break;
					default:
						LoggerManager.addEvent("- INVALID CONDITION DATA: " + conditionXML);
						break;
				}
			}
		}
		
		private function setTimeFromString(dateObj:Date, timeStr:String):void {
			var sections:Array = timeStr.split(":");
			
			dateObj.time = 0;
			
			if(sections.length != 3) {
				LoggerManager.addEvent("- Invalid time specification: " + timeStr );
				return;
			}
			
			dateObj.setHours(sections[0],sections[1],sections[2],0);
			
		}
		
		private function setDateFromString(dateObj:Date, dateStr:String):void {
			var sections:Array = dateStr.split("-");
			
			dateObj.time = 0;
			
			if(sections.length != 3) {
				LoggerManager.addEvent("- Invalid date specification: " + dateStr );
				return;
			}
			
			dateObj.setFullYear(sections[0],sections[1]-1,sections[2]);
			
		}
		
		private function getTimePart(timeStr:String):String {
			//We dont care about time zones since times are always given in localtime
			
			return timeStr.split(" ")[0]; // Time will be in this format: 00:00:00 GMT-0400
		}
		
		private function getDayIndex(dayStr:String):int {
			var ret:int;
			
			switch(dayStr) {
				case "Sun":
					ret = 0;
					break;
				case "Mon":
					ret = 1;
					break;
				case "Tue":
					ret = 2;
					break;
				case "Wed":
					ret = 3;
					break;
				case "Thu":
					ret = 4;
					break;
				case "Fri":
					ret = 5;
					break;
				case "Sat":
					ret = 6;
					break;				
				default:
					LoggerManager.addEvent("Invalid day specification: '" + dayStr + "'");
					ret  = 0;
					break;
			}

			return ret;
		}
	}
}