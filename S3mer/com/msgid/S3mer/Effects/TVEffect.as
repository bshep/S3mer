package com.msgid.S3mer.Effects
{
	import flash.events.EventDispatcher;
	
	import mx.effects.Fade;
	import mx.effects.Resize;
	import mx.effects.Sequence;
	import mx.events.EffectEvent;

	public class TVEffect extends EventDispatcher
	{
		public function TVEffect()
		{
			_sequence = new Sequence();
			
			_fadeEffect = new Fade();
			_sequence.addChild(_fadeEffect);
			_widthEffect = new Resize();
			_sequence.addChild(_widthEffect);			
			_heightEffect = new Resize();
			_sequence.addChild(_heightEffect);
			
			_sequence.addEventListener(EffectEvent.EFFECT_START,OnEffectStart,false,0,true);
			_sequence.addEventListener(EffectEvent.EFFECT_END,OnEffectEnd,false,0,true);
		}
		
		private function OnEffectEnd(e:EffectEvent):void {
			this.dispatchEvent(e);
		}
		
		private function OnEffectStart(e:EffectEvent):void {
			this.dispatchEvent(e);
		}
		
		public function play(targets:Array = null,
                         playReversedFromEnd:Boolean = false):
                         Array 
     	{
			_fadeEffect.alphaFrom = alphaFrom;
			_fadeEffect.alphaTo = alphaTo;
			_fadeEffect.duration = alphaDuration;
			
			_widthEffect.widthFrom = widthFrom;
			_widthEffect.widthTo = widthTo;
			
			
			_heightEffect.heightFrom = heightFrom;
			_heightEffect.heightTo = heightTo;
			
			return _sequence.play(targets,playReversedFromEnd);
		}
		
		private var _sequence:Sequence;
		private var _widthEffect:Resize;
		private var _heightEffect:Resize;
		private var _fadeEffect:Fade;
		
		public var widthFrom:Number;
		public var widthTo:Number;
		public var heightFrom:Number;
		public var heightTo:Number;
		public var alphaFrom:Number;
		public var alphaTo:Number;
		public var alphaDuration:Number;
		
		
	}
}