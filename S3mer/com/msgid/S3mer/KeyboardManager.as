package com.msgid.S3mer
{
	import flash.desktop.NativeApplication;
	import flash.display.Stage;
	import flash.display.StageDisplayState;
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	import flash.events.KeyboardEvent;
	import flash.system.Capabilities;
	
	import mx.core.Application;
	import mx.effects.Move;
	import mx.events.TweenEvent;

	public class KeyboardManager extends EventDispatcher
	{
		public function KeyboardManager(target:IEventDispatcher=null)
		{
			super(target);
		}
		
		
		private static function statusDisplay(text:String, target:Object):void {
			Logger.addEvent("HOTKEY: " + text);
			if (target == null || target is Stage) {
//				this._notificationPanel.showNotification(text);
			} else {
				if (target is S3merWindow) {
					S3merWindow(target)._notificationPanel.showNotification(text);
				}
			}
		}
		
		public static function HandleKeyUp(e:KeyboardEvent, target:Object):void {
			switch(String.fromCharCode(e.charCode).toUpperCase()){
//				case "D":
//					//this.appLog.alpha = 0
//					var myTween:Move = new Move();
//					myTween.duration = 1000;
//						
//					
//					if (!target.appLog.visible == true) {
//						myTween.yFrom = -target.appLog.height;
//						myTween.yTo = target.appLog.y;
//						
//					} else {
//						myTween.yTo = -target.appLog.height;
//						myTween.yFrom = target.appLog.y;
//					}
//					myTween.play([target.appLog]);
//					
//					if (!target.appLog.visible == true) {
//						statusDisplay("Debug ON", target);
//						target.appLog.visible = true;
//					} else {
//						statusDisplay("Debug Off", target);
//						myTween.addEventListener(TweenEvent.TWEEN_END,target.OnHideLogComplete,false,0,true);
//					}
//					
//					break;
				case "F":
					if ( target.systemManager.stage.displayState == StageDisplayState.FULL_SCREEN_INTERACTIVE ) {
						statusDisplay("Fullscreen OFF", target);
						target.systemManager.stage.displayState = StageDisplayState.NORMAL;
						target.width += 1;
						target.width -= 1;
						target.height += 1;
						target.height -= 1;
						
						target.move((Capabilities.screenResolutionX-target.width)/2,(Capabilities.screenResolutionY-target.height)/2);
					} else {
						statusDisplay("Fullscreen ON", target);
						target.systemManager.stage.displayState = StageDisplayState.FULL_SCREEN_INTERACTIVE;
						target.systemManager.stage.nativeWindow.alwaysInFront = true;
					}
					
					break;
				case "X": // Resets config and reloads
					statusDisplay("Reset Config", target);
					(target as S3merWindow).disableKeyHandler(); 
					(target as S3merWindow).configuration.reset();
					ApplicationSettings.setValue("user.username","");
					ApplicationSettings.setValue("user.password","");
					ApplicationSettings.save();
					
					(target as S3merWindow).resetApp();
					break;
				case "R": //Reloads config from disk
					statusDisplay("Reload Config", target);
					(target as S3merWindow).disableKeyHandler(); 
					(target as S3merWindow).configuration.reset();
					target.reloadApp();
					(target as S3merWindow).stopped = true;
					break;
				case "S":
					var pan:String;
					
					pan = ApplicationSettings.getValue("video.pan","0");
					
					if (pan == "0") {
						pan = "-1";
						target.configuration.pan = new Number(pan);
						ApplicationSettings.setValue("video.pan",pan);
						statusDisplay("Pan Left", target);
					} else if(pan == "-1") {
						pan = "1";
						target.configuration.pan = new Number(pan);
						ApplicationSettings.setValue("video.pan",pan);
						statusDisplay("Pan Right", target);
					} else {
						pan = "0";
						target.configuration.pan = new Number(pan);
						ApplicationSettings.setValue("video.pan",pan);
						statusDisplay("Pan Center", target);
					}
					
					ApplicationSettings.save();
				
					break;
//					//Toggle smoothing
//					var smoothing:String;
//					
//					smoothing = ApplicationSettings.getValue("video.smoothing");
//					
//					if (smoothing == "false") {
//						target._configuration.setSmoothing(true);
//						ApplicationSettings.setValue("video.smoothing","true");
//						statusDisplay("Smoothing ON", target);
//					} else {
//						target._configuration.setSmoothing(false);
//						ApplicationSettings.setValue("video.smoothing","false");
//						statusDisplay("Smoothing OFF", target);
//					}
//					
//					ApplicationSettings.save();
//					break;
				case "H":
					var showcursor:String;
					
					showcursor = ApplicationSettings.getValue("ui.showcursor");
					if (showcursor == "true") {
						target.cursorManager.setBusyCursor();
						target.cursorManager.hideCursor();
						ApplicationSettings.setValue("ui.showcursor","false");
						statusDisplay("Cursor OFF", target);
					} else {
						target.cursorManager.removeBusyCursor();
						ApplicationSettings.setValue("ui.showcursor","true");
						statusDisplay("Cursor ON", target);
					}
					ApplicationSettings.save();
					break;
//				case "J": //Previous
//					statusDisplay("Previous", target);
//					break;
//				case "K": //Pause/Resume
//					statusDisplay("Play/Pause", target);
//					break;
//				case "L": //Next
//					statusDisplay("Next", target);
//					break;
				case "U": //Enable/Disable Updates
					var updatesEnabled:String;
					
					updatesEnabled = ApplicationSettings.getValue("updatesEnabled");
					if (updatesEnabled == "true") {
						ApplicationSettings.setValue("updatesEnabled","false");
						statusDisplay("Updates OFF", target);
					} else {
						ApplicationSettings.setValue("updatesEnabled","true");
						statusDisplay("Updates ON", target);
					}
					ApplicationSettings.save();
					break;
				case "M": //Enable/Disable Autostartup
					var autoStart:String;
					
					autoStart = ApplicationSettings.getValue("ui.autoStartEnabled","true");
					if (autoStart == "true") {
						ApplicationSettings.setValue("ui.autoStartEnabled","false");
						statusDisplay("AutoStart OFF", target);
					} else {
						ApplicationSettings.setValue("ui.autoStartEnabled","true");
						statusDisplay("AutoStart ON", target);
					}
					ApplicationSettings.save();
					
//					try {
						if( ApplicationSettings.getValue("ui.autoStartEnabled","true") == "true" ) {
							NativeApplication.nativeApplication.startAtLogin = true;
						} else {
							NativeApplication.nativeApplication.startAtLogin = false;
						}
											
//					} catch( e:IllegalOperationError) {
//						Logger.addEvent("could not set to startup");
//					}
					break;
				case "Q": //Quit
					statusDisplay("Quit", target);
					Application.application.exit();
					break;
//				case "G": //Quit
//					statusDisplay("GCollect", target);
//					
//					for each ( var _show:Object in target.getChildren() ) {
//						if (_show is Show) {
//							target.removeChild(_show as DisplayObject);
//							target._configuration = null;
//							target._updater = null;
//						}
//					}
//					
//					break;
				case "*": //Redo First Run Config and QUIT
					(target as S3merWindow).redoInitialSetup();
					
					Application.application.exit();
					
					break;
					
				case "#":
					
					(target as S3merWindow).disableKeyHandler(); 
					(target as S3merWindow).configuration.reset();
					(target as S3merWindow).cleanMediaDirectory();
					(target as S3merWindow).stopped = true;
				
					break;
				default:
//					Logger.addEvent("KEY_UP: charcode = " + e.charCode + " str: " + String.fromCharCode(e.charCode).toUpperCase());
					break;
			}				
		}


	}
}