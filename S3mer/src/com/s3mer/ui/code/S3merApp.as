			import com.s3mer.events.ConfigurationEvent;
			import com.s3mer.ui.S3merWindow;
			import com.s3mer.util.ApplicationSettings;
			import com.s3mer.util.LoggerManager;
			import com.s3mer.util.NetUtils;
			import com.s3mer.util.PlayerState;
			
			import flash.display.Screen;
			
			import mx.collections.ArrayCollection;
			import mx.events.FlexEvent;
			
			private var _playerWindows:ArrayCollection = new ArrayCollection;
			private var _isPro:Boolean = false;
			private var _isDemo:Boolean = false;
			private var _networkStatus:Boolean = false;
			
			
			private function isMultiscreen():Boolean {
				var multiScreen:Boolean;
				
				if(this._playerWindows.length > 1) {
					multiScreen = true;
				} else {
					multiScreen = false;
				}
				
				return multiScreen;
			}
			
			public function get networkStatus():Boolean {
				return _networkStatus;
			}
			
			private function networkStatusChanged(event:Event):void {
				this.removeEventListener(Event.NETWORK_CHANGE,networkStatusChanged);					
				new NetUtils().CheckOnlineStatus(onAppLoad_stage2);
			}
						
			private function getScreenById(screenId:int):S3merWindow {
				for each( var _screen:S3merWindow in this._playerWindows ) {
					if( screenId == _screen.screenNumber ) {
						return _screen;
					}
				}
				
				return null;			
			}
			
			private function onAppLoad(e:FlexEvent):void {
				this.frameRate = 30;
				
				ApplicationSettings.load();
				if( ApplicationSettings.getValue("ui.lang","") != "" ) {
					resourceManager.localeChain = [ ApplicationSettings.getValue("ui.lang","en_US") ];
				}
				
				new NetUtils().CheckOnlineStatus(onAppLoad_stage2);
				
			}
			
			

			private function onAppLoad_stage2(online:Boolean):void {
				if(!online) {
					LoggerManager.addEvent("S3merApp.as onAppLoad_stage2: network is down... waiting for it to come up");
					this.addEventListener(Event.NETWORK_CHANGE,networkStatusChanged);
					return;
				} else {
					LoggerManager.addEvent("S3merApp.as onAppLoad_stage2: network is up! Here we go...");
					
				}
				
				this.addEventListener(ConfigurationEvent.CREDENTIALS_CHECKED, createPlayerWindows);
				checkCredentials();
			}
			
			private function checkCredentials():void {
				
				// Check the config file
				if( (ApplicationSettings.getValue("user.username","") == "") ||  (ApplicationSettings.getValue("user.password","") == "")){
					PlayerState.credentialsValid = false;
					LoggerManager.addEvent("S3merApp.as checkCredentials: Username and/or Password not found");
					this.dispatchEvent(new ConfigurationEvent(ConfigurationEvent.CREDENTIALS_CHECKED));	
				} else {
					PlayerState.username = ApplicationSettings.getValue("user.username","");
					PlayerState.password = ApplicationSettings.getValue("user.password","");
					// Try to login
					NetUtils.remoteCommand(NetUtils.CMD_LOGIN, checkCredentials_complete, checkCredentials_error);
					
				}
				
				
				
				// Decide wether we are logged in or not and what type of player (pro, free)
				
//				this.dispatchEvent(new ConfigurationEvent(ConfigurationEvent.CREDENTIALS_CHECKED));	
			}
			
			private function checkCredentials_complete(e:Event):void {
				var _data:String;
				
				_data = (e.target as URLLoader).data;
				
				if(_data == "OK") {
					PlayerState.credentialsValid = true;
				} else {
					PlayerState.credentialsValid = false;
				}
				
				LoggerManager.addEvent("S3merApp.as checkCredentials_complete: Login Successful");
				
				
				this.dispatchEvent(new ConfigurationEvent(ConfigurationEvent.CREDENTIALS_CHECKED));	
			}
			
			private function checkCredentials_error(e:IOErrorEvent):void {
				LoggerManager.addEvent("S3merApp.as checkCredentials_error: Login Command Failed");
				
				PlayerState.internetConnected = false;
				
				// Retry after a while...
				new NetUtils().CheckOnlineStatus(onAppLoad_stage2, 60);
			}
			
			public function registation_complete():void {
				LoggerManager.addEvent("S3merApp.as registration_complete: Registration Complete");
				
				for each( var _window:S3merWindow in this._playerWindows ) {
					_window.start();
				}
			}
			
		
			private function createPlayerWindows(e:ConfigurationEvent):void {
				var screenNumber:int = 0;
				var _window:S3merWindow;
				for each( var _screen:Screen in Screen.screens ) {
					LoggerManager.addEvent("S3merApp.as createPlayerWindows:Creating window for screen #" + screenNumber);
					_window = new S3merWindow();
					_window.screenNumber = screenNumber;
					_window.applicationObject = this;
					_window.open(false);
					
					_window.showWindow();
					this._playerWindows.addItem(_window);
					screenNumber++;
				}
				
				if( PlayerState.credentialsValid == true ) {
					registation_complete();
				}
			}
	
//			private function HandleKeyUp(e:KeyboardEvent):void {
//				KeyboardManager.HandleKeyUp(e,this);
//			}
//			
//			private function onAppClose(e:Event):void {
//				exit();
//			}
//			
//			public function resetApp():void {
//				var _window:S3merWindow;
//				
//
//				for each( _window in this._playerWindows ) {
//					_window.reload();
//					_window.close();
//				}				
//				this._playerWindows.removeAll();
//				
//				createPlayerWindows();
//				createLoginWindow();
//				
//				if( this._loginWindow ) {
//					this._loginWindow.checkCredentials(onAppLoad_postLogin);
//				}
//			}
//			
//			public function reloadApp():void {
//				for each( var _window:S3merWindow in this._playerWindows ) {
//					_window.doReload();
//				}				
//			}
//			
//			private function onAppLoad_postLogin(e:Event):void {
//				if(e != null && (e.target as URLLoader) == null) {
//					onAppLoad_stage2(e);
//				} else {
//				
//					if (e == null || (e.target as URLLoader).data != "OK") {
//						this.doLogin();
//					} else {
//						onAppLoad_stage2(e);
//					}	
//				}
//
//			}
//			
//			private function doLogin():void {
//				this._loginWindow.addEventListener("WINDOW_CLOSED",onAppLoad_stage2,false,0,true);
//				this.setStyle("backgroundColor","#FFFFFF");
//
////				if ( stage.nativeWindow.closed != true ) { 
////					stage.nativeWindow.visible = true;
////					this.visible = true;
////				}
//					
//				cursorManager.removeBusyCursor();
//				
//				this._loginWindow.visible = true;	
//			}
//			
//			private function onAppLoad_stage2(e:Event):void {
//				if(this._loginWindow.willTrigger("WINDOW_CLOSED")) {
//					this._loginWindow.removeEventListener("WINDOW_CLOSED", onAppLoad_stage2);
//				}
//				
//				this._isPro = this._loginWindow.isPro;
//				this._isDemo = this._loginWindow.isDemoMode;
//				
//				this._loginWindow.visible = false;
//
//				systemManager.stage.addEventListener(KeyboardEvent.KEY_UP,HandleKeyUp,false,0,true);
//				if (ApplicationSettings.getValue("ui.showcursor") != "true") {
//					this.cursorManager.setBusyCursor();
//					this.cursorManager.hideCursor();
//				}
//
//				if( doInitialSetup() == false ) {
//					return;
//				}
//				
//				for each( var _window:S3merWindow in this._playerWindows) {
//					if( this._isPro || _window.screenId == 0) {
//						_window.loadConfiguration(isMultiscreen());
//						if(_window.channelId == "-1") {
//							_window.close();
////							this._playerWindows.removeItemAt(this._playerWindows.getItemIndex(_window));
//						} else {
//							_window.enableKeyHandler();
//						}
//					}
//					
//					if(this._isDemo && _window.screenId != 0) {
//						_window.close();
////						this._playerWindows.removeItemAt(this._playerWindows.getItemIndex(_window));
//					}
//				}
//				this.stage.removeEventListener(Event.CLOSE,onAppClose);
//				
//				for each(  _window in this._playerWindows) {
//					if( _window.closed == true ) {
//						this._playerWindows.removeItemAt(this._playerWindows.getItemIndex(_window));
//						break;
//					}
//				}
//
//				
//				for each(  _window in this._playerWindows) {
//					if( _window.visible == true ) {
//						_window.activate();
//						_window.orderToFront();
//						break;
//					}
//				}
//				
//				if(this._playerWindows.length == 0) {
//					ApplicationSettings.setValue("user.username","");
//					ApplicationSettings.setValue("user.password","");
//					ApplicationSettings.save();
//					this.resetApp()
//				}
//				
//				this.close();
//			}
//			
//			public function doInitialSetup(ignoreFlags:Boolean = false):Boolean {
//				if (FileIO.assetsPath() == null) {
//					LoggerManager.addEvent("S3merApp.as doInitialSetup: Could not find assets path, cannot do initial setup");
//					return false;
//				}
//				
//				var storePath:File = new File(FileIO.storePath());
//				var assetsPath:File = new File(FileIO.assetsPath());
//				var appPath:File = new File(FileIO.appPath());
//				
//				storePath.resolvePath("Utilities").createDirectory();
//				
//				if ( !S3merUtility.isDebug ) {
//					try {
//						if( ApplicationSettings.getValue("ui.autoStartEnabled","true") == "true" ) {
//							NativeApplication.nativeApplication.startAtLogin = true;
//						} else {
//							NativeApplication.nativeApplication.startAtLogin = false;
//						}
//											
//					} catch( e:IllegalOperationError) {
//						LoggerManager.addEvent("S3merApp.as doInitialSetup could not set to startup");
//					}
//				}
//				
//				return true;
//				
//				// We have eliminated the initial setup stuff since it was annoying
//				switch( FileIO.getOs() ) {
//					case 'WINDOWS':
//						try {
//							var firstimeFile:File = File.userDirectory.resolvePath("firstrun.bat");
//							var templateFile:File;
//							var firstimeStream:FileStream = new FileStream();
//							var templateStream:FileStream = new FileStream();
//							
//							if (ignoreFlags == false && File.userDirectory.resolvePath("S3merSetupDone").exists) {
//								return true;
//							}
//							
//							assetsPath = assetsPath.resolvePath("Windows");
//							
//							templateFile = assetsPath.resolvePath("firstrun.bat");
//							
//							templateStream.open(templateFile,FileMode.READ);
//							firstimeStream.open(firstimeFile,FileMode.WRITE);
//							
//							firstimeStream.writeMultiByte("set APPPATH=" + appPath.nativePath + "\r\n", File.systemCharset);
//							firstimeStream.writeMultiByte("@echo off\r\n", File.systemCharset);
//							
//							var contents:String = templateStream.readMultiByte(templateStream.bytesAvailable, File.systemCharset);
//							
//							firstimeStream.writeMultiByte(contents,File.systemCharset);
//							
//							templateStream.close();
//							firstimeStream.close();
//							
//							assetsPath.resolvePath("s3merConfig.ico").copyTo(File.userDirectory.resolvePath("s3merConfig.ico"),true);
//							assetsPath.resolvePath("LAUNCH.SHK").copyTo(File.userDirectory.resolvePath("Desktop").resolvePath("S3mer Config.lnk"),true);
//						} catch(e:Error) {
//							trace(e.message);
//						}
//						break;
//					case 'MAC':
//						if (ignoreFlags == false && File.userDirectory.resolvePath("Library").resolvePath("Preferences").resolvePath("com.s3mer.playerConfig").resolvePath("configDone").exists) {
//							return true;
//						}
//						
//						assetsPath = assetsPath.resolvePath("Mac");
//						
//						if(!File.userDirectory.resolvePath("Desktop").resolvePath("S3merConfig.app").exists) {
//							assetsPath.resolvePath("S3merConfig.app").copyTo(File.userDirectory.resolvePath("Desktop").resolvePath("S3merConfig.app"));						
//						}
//																		
//						break;
//					case 'LINUX':
//						break;
//					default:
//						break;
//				}
//				
//				for each( var _win:S3merWindow in this._playerWindows) {
//					_win.displayImage(assetsPath.resolvePath("firstrun.swf"));
//					_win.enableKeyHandler();
//				}
//				
//				return false;
//			}
//			



			private function onInvoke(e:InvokeEvent):void {
//		        var now:String = new Date().toTimeString();
//		        LoggerManager.addEvent("S3merApp.as inInvoke: Invoke event received: " + now);
//				this._updater = new S3merApplicationUpdater();
			}
			
			
			
//			
//			
//			
//			private function play():void {
//				for each( var _window:S3merWindow in this._playerWindows) {
//					if(_window.visible) {
//						_window.play();
//					}
//				}
//			}
//			
//			public function saveWindowPositions():void {
//				for each( var _window:S3merWindow in this._playerWindows) {
//					if( _window.stage.displayState == StageDisplayState.FULL_SCREEN_INTERACTIVE ) {
//						ApplicationSettings.setValue("screen" + _window.screenId + ".location.x","0");
//						ApplicationSettings.setValue("screen" + _window.screenId + ".location.y","0");
//						ApplicationSettings.setValue("screen" + _window.screenId + ".location.width","0");
//						ApplicationSettings.setValue("screen" + _window.screenId + ".location.height","0");
//					} else {
//						ApplicationSettings.setValue("screen" + _window.screenId + ".location.x",_window.nativeWindow.bounds.x.toString());
//						ApplicationSettings.setValue("screen" + _window.screenId + ".location.y",_window.nativeWindow.bounds.y.toString());
//						ApplicationSettings.setValue("screen" + _window.screenId + ".location.width",_window.width.toString());
//						ApplicationSettings.setValue("screen" + _window.screenId + ".location.height",_window.height.toString());
//					}
//					ApplicationSettings.save();
//				}
//			}
			