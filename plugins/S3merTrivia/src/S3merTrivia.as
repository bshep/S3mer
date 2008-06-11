// ActionScript file
import flash.display.StageScaleMode;
import flash.events.Event;
import flash.events.IOErrorEvent;
import flash.net.URLLoader;
import flash.net.URLRequest;
import flash.utils.Timer;

private function onAppLoad(e:Event):void {
	nextQuestionTimer.addEventListener(TimerEvent.TIMER,nextQuestion);
	showAnswerTimer.addEventListener(TimerEvent.TIMER,showAnswer);

	this.dataUrl = this.parameters.dataUrl;
	
	if( this.dataUrl == null ) {
		this.dataUrl = "getTriviaDataStatic.php";
	}
	
	this.dataLoad();
	this.systemManager.stage.scaleMode = StageScaleMode.EXACT_FIT;
}


private function dataLoadComplete(e:Event):void {
	this.questionsXML = new XML(e.target.data);

	nextQuestion(null);
}

private function dataLoadError(e:IOErrorEvent):void {
	this.currentState = "step_1_error";
	this.txtErrorDetail.text = e.text;
	
}

private function dataLoad():void {
	var loader:URLLoader = new URLLoader();
	
	loader.addEventListener(Event.COMPLETE, dataLoadComplete);
	loader.addEventListener(IOErrorEvent.IO_ERROR, dataLoadError);
	
	loader.load(new URLRequest(this.dataUrl));
}

private var nextQuestionTimer:Timer = new Timer(4*1000,1);
private var showAnswerTimer:Timer = new Timer(5*1000,1);
private var correctAnswer:int;

private var dataUrl:String;

private var questionsXML:XML;



private function getRandomQuestion(questionList:XML):XML {
	var list:XMLList = new XMLList(questionList.question);
	var randomIndex:int = Math.ceil(Math.random()*list.length()) - 1
	
	trace("Random Index = " + randomIndex);
	return list[randomIndex];
}


private function onStateTransitionComplete(e:Event):void {
	if(this.currentState == 'step_1') {
		
		var question:XML = this.getRandomQuestion(questionsXML);
		
		this.txtQuestion.text = question.text;
				
		this.shuffleAnswers(question);
		
		this.currentState = 'step_2';
	} else {
		showAnswerTimer.start();
	}
}

private function shuffleAnswers(question:XML):void {
	var newOrder:Array = new Array;
	
	
	
	while(newOrder.length < 4) {
		var newNum:int = getRandomInt(0,4);
		
		if (newOrder.indexOf(newNum) == -1 ) {
			newOrder.push(newNum);
		}
		
	}
	
	for(var a:int=0;a <= 3;a++) {
		var index:int = newOrder.pop();
		
		
		switch(a) {
			case 0:
				this.txtLabel0.text = "A) " + getAnswer(index,question);
				break;
			case 1:
				this.txtLabel1.text = "B) " + getAnswer(index,question);
				break;
			case 2:
				this.txtLabel2.text = "C) " + getAnswer(index,question);
				break;
			case 3:
				this.txtLabel3.text = "D) " + getAnswer(index,question);
				break;
		}
		
		if(index == 3) {
			this.correctAnswer = a;
		}

		
	}
	
	
}

private function getAnswer(num:int,question:XML):String {
	var answer:String;

	switch(num) {
		case 0:
			answer = question.wa1;		
			break;
		case 1:
			answer = question.wa2;		
			break;
		case 2:
			answer = question.wa3;		
			break;
		case 3:
			answer = question.correct;		
			break;
	}
	
	return answer;
}

private function showAnswer(e:Event):void {
	switch(this.correctAnswer) {
		case 0:
			this.currentState = "step_2a";
			break;
		case 1:
			this.currentState = "step_2b";
			break;
		case 2:
			this.currentState = "step_2c";
			break;
		case 3:
			this.currentState = "step_2d";
			break;
	}
	
	this.nextQuestionTimer.start();
}

private function getRandomInt(min:int = 0, max:int = 100):int {
	return Math.ceil(min + Math.random()*(max-min)) - 1;
}


private function nextQuestion(e:Event):void {
	if(this.currentState == 'step_1') {
		onStateTransitionComplete(null);
	} else {
		this.currentState = 'step_1';
	}

}
