package com.msgid.S3mer.Effects
{
	import mx.effects.IEffectInstance;
	import mx.events.EffectEvent;

	public class ExtendedEffectEvent extends EffectEvent
	{
		public function ExtendedEffectEvent(eventType:String, bubbles:Boolean=false, cancelable:Boolean=false, effectInstance:IEffectInstance=null)
		{
			super(eventType, bubbles, cancelable, effectInstance);
		}
		
	    public override function clone():Event
	    {
	        return new ExtendedEffectEvent(this._type);
	    }
	    
        public override function toString():String
	    {
	        return formatToString("ExtendedEffectEvent", "type", "bubbles", "cancelable", "eventPhase", "message");
	    }

		
	}
}