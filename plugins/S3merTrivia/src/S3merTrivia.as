// ActionScript file
import flash.events.Event;

import mx.controls.Text;

private function onAppLoad(e:Event):void {
	nextQuestionTimer.addEventListener(TimerEvent.TIMER,nextQuestion);
	nextQuestionTimer.start();
	nextQuestion(null);
}

private static var questionsXML:XML = 
	<questions>
		<question id="1">
			<text>Which actor plays John McClane in the "Die Hard" movies?</text>
			<correct>Bruce Willis</correct>
			<wa1>Tom Cruise</wa1>
			<wa2>Billy Bob Thornton</wa2>
			<wa3>Harrison Ford</wa3>
		</question>
		<question id="2">
			<text>What is the name of Jerry Springer's head security guy?</text>
			<correct>Steve</correct>
			<wa1>Rob</wa1>
			<wa2>Al</wa2>
			<wa3>Jim</wa3>
		</question>
	</questions>;


private var nextQuestionTimer:Timer = new Timer(10*1000);

private function getRandomQuestion(questionList:XML):XML {
	var list:XMLList = new XMLList(questionList.question);
	var randomIndex:int = Math.ceil(Math.random()*list.length()) - 1
	
	trace("Random Index = " + randomIndex);
	return list[randomIndex];
}


private var correctAnswer:int;

private function onStateTransitionComplete(e:Event):void {
	if(this.currentState == 'step_1') {
		
		var question:XML = this.getRandomQuestion(questionsXML);
		
		this.txtQuestion.text = question.text;
				
		this.shuffleAnswers(question);
		
		this.currentState = 'step_2';
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
				this.txtLabel0.text = getAnswer(index,question);
				break;
			case 1:
				this.txtLabel1.text = getAnswer(index,question);
				break;
			case 2:
				this.txtLabel2.text = getAnswer(index,question);
				break;
			case 3:
				this.txtLabel3.text = getAnswer(index,question);
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
