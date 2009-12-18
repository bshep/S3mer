			import com.s3mer.util.LoggerManager;
			import com.s3mer.util.ApplicationSettings;
			import mx.events.FlexEvent;
			import mx.events.IndexChangedEvent;
			import mx.resources.ResourceBundle;
			import mx.events.EffectEvent;
			import mx.collections.ListCollectionView;
			import mx.events.ListEvent;
			

			[Embed(source="assets/spinner.swf")]
			[Bindable]
			public static var SpinnerSwf:Class;
			
			
			private var _isDemoMode:Boolean = false;
			private var _isMultiScreen:Boolean = false;
			private var _isPro:Boolean = false;
			
			
			private var _username:String;
			private var _password:String;
			
			public function set isMultiScreen(val:Boolean):void {
				this._isMultiScreen = val;
			}
			
			public function get isPro():Boolean {
				return this._isPro;
			}

			public function get isDemoMode():Boolean {
				return this._isDemoMode;
			}
			
			public function show():void {
				this.alpha = 1.0;
				this.cmbLanguage.selectedIndex = localeListIndex(resourceManager.localeChain[0]);


				updateLocaleStrings();
				
				if (0) {
					var tmpTextField:TextInput = new TextInput();
					var tmpLabel:Label = new Label();
					
					tmpTextField.addEventListener(FlexEvent.ENTER, function(e:Event):void {
						ApplicationSettings.load();
											
						ApplicationSettings.setValue("user.username","");
						ApplicationSettings.setValue("user.password","");
						ApplicationSettings.setValue("screen0.channel.id",(e.target as TextInput).text);
						ApplicationSettings.save();
						
						(e.target as TextInput).parent.parent.parent.visible = false;
						
					});
					
					
					tmpLabel.text = "PlayerID:";
					tmpTextField.width = 125;
					ctlLoginWindowBarBottom.addChildAt(tmpTextField,0);
					ctlLoginWindowBarBottom.addChildAt(tmpLabel,0);
				}
//				this.visible = true;
//				this.TVEffect_Show.play([this]);
			}
			
			private function localeListIndex(locale:String):int {
				for( var a:int = 0; a < localesList.length; a++ ) {
					if( localesList[a].data == locale ) {
						return a;
					}
				}
				
				return 0;
			}
			
			private function updateLocaleStrings():void {
				pnlMode.title = resourceManager.getString('application', 'GREETING');
				btnDemo.label = resourceManager.getString('application', 'START_DEMO');
				btnLogin1.label = resourceManager.getString('application', 'LOGIN');
				lblLanguage.text = resourceManager.getString('application', 'LANGUAGE');
				
			}
			
			private function doRemoteCommand(command:String, listener:Function):void {
				var _loader:URLLoader = new URLLoader;
				var _loaderReq:URLRequest;
				var _url:String;
				
				_loader.dataFormat = URLLoaderDataFormat.TEXT;
				_loader.addEventListener(Event.COMPLETE, listener);
				_loader.addEventListener(IOErrorEvent.IO_ERROR, RemoteCommand_error);
				this.addEventListener("CONNECTION_ERROR", listener);
				
				switch(command) {
					case "login":
						_url = ApplicationSettings.URL_LOGIN + "?username=" + 
							this._username + "&password=" + this._password;
						break;
					case "logout":
						_url = ApplicationSettings.URL_LOGOUT;
						break;
					case "playerlist":
						_url = ApplicationSettings.URL_PLAYER_LIST+ "?data=1";
						break;
					case "userinfo":
						_url = ApplicationSettings.URL_USER_INFO + "?data=2";
						break;
					case "checkstatus":
						_url = ApplicationSettings.URL_STATUS;
						break;
				}

				_loaderReq = new URLRequest(_url);
				
				_loader.load(_loaderReq);
				
			}
			
			private function RemoteCommand_error(e:IOErrorEvent):void {
				resetFields();
				
				if( this._isDemoMode == true ) {
					this.btnDemo.enabled = true;
					this.btnLogin1.enabled = true;
				} else {
					this.currentState = "register";
				}
				
				dispatchEvent(new Event("CONNECTION_ERROR"));
			}
			
			private function OnLoginClick(e:Event):void {
				doLogin();
			}
			
			private function OnTuneInClick(e:MouseEvent):void {
				if(this.saveInfo()) {
					this.visible = false;	
				} else {
					this.cmbPlayers0.setStyle("borderColor","#FF0000");
					this.cmbPlayers0.setStyle("borderThickness","5");
				}
			}
			
			private function doLogin():void {
				this.txtUsername.enabled = false;
				this.txtPassword.enabled = false;
				this.spinner.source = SpinnerSwf;
				this.spinner.visible = true;
				
				this._username = this.txtUsername.text;
				this._password = this.txtPassword.text;
				
				doRemoteCommand("login", doLogin_stage2);
			}
			
			private function doLogin_stage2(e:Event):void {
				var result:String;
				
				result = URLLoader(e.target).data;
				
				LoggerManager.addEvent("LOGIN RESULT: " + result);
				
				if( result == "OK" ) {
					highlightTextbox(this.txtUsername,"",false);
					highlightTextbox(this.txtPassword,"",false);
					doRemoteCommand("playerlist",playerListLoaded);
					
					doRemoteCommand("checkstatus",doLogin_stage3);
				} else {
					this.txtUsername.enabled = true;
					this.txtPassword.enabled = true;
					highlightTextbox(this.txtUsername);
					highlightTextbox(this.txtPassword);
					this.spinner.visible = false;
					
					if( this._isDemoMode == true ) {
						this.btnDemo.enabled = true;
						this.btnLogin1.enabled = true;
					}
				}
				
			}
			
			
			private function highlightTextbox(obj:TextInput,color:String = "#FF0000", enable:Boolean = true):void {
				if ( enable ) {
					obj.setStyle("borderColor",color);
					obj.setStyle("borderThickness","3");
				} else {
					obj.setStyle("borderColor","#000000");
					obj.setStyle("borderThickness","1");
				}
 			}
			
			private function doLogin_stage3(e:Event):void {
				var result:String;
				
				result = URLLoader(e.target).data;
				
				LoggerManager.addEvent("LOGIN RESULT: " + result);
			}
			
			private function playerListLoaded(e:Event):void {
				var result:XML;
								
				this.spinner.visible = false;
				
				result = new XML(URLLoader(e.target).data);
				
				if(!result) {
					LoggerManager.addEvent("Error loading player list, Invalid XML returned");
				}
				
				if (!_isDemoMode) {
//					if(1) {
					if(result.user.@isPro == "true") {
						this._isPro	= true;
					} else {
						this._isPro = false;
					}

					if(this._isMultiScreen && this._isPro) {
						this.currentState = "register_multiPlayer";
					} else {
						this.currentState = "register_choosePlayer";
					}
				}

				
				populatePlayerList(result, this.cmbPlayers0);
				populatePlayerList(result, this.cmbPlayers1);
				
				
				if (this._isDemoMode) {
					this.onDemoMode_stage2();
				}
				
				
			}
			
			private function populatePlayerList(playersXML:XML, cmbTarget:ComboBox):void {
				var dataProvider:ListCollectionView;
				
				if(cmbTarget && playersXML) {
					
					dataProvider = (cmbTarget.dataProvider as ListCollectionView)
					dataProvider.removeAll();
					
//					if(1) {
					if(this._isMultiScreen && this._isPro && !_isDemoMode) {
						dataProvider.addItem({label:"<none>", data:new XML("<id>-1</id>")});
					}
					
					for each( var _player:XML in playersXML.player ) {
						dataProvider.addItem({label:_player.@name, data:_player.id});
						LoggerManager.addEvent("Player ID: " + _player.@name );
					}
					
					if (dataProvider.length > 0) {
						cmbTarget.selectedIndex = 0;
					}				
				}
			}
					
			public function hide_complete(e:EffectEvent):void {
				this.OnClosed(e);
			}
			
			private function resetFields():void {
				if( this.currentState == "register") {
					this.txtUsername.enabled = true;
					this.txtPassword.enabled = true;
					this.txtUsername.text = "e-mail"; 
					this.txtPassword.text = "password"; 
				}
			}
			
			private function doLogout():void {
				doRemoteCommand("logout",noop);
				resetFields();
				this.currentState = "register";				
			}
			
			
			private function noop(e:Event):void {
				doRemoteCommand("checkstatus",noop_stage2);
			}

			private function noop_stage2(e:Event):void {
				var result:String;
				
				result = URLLoader(e.target).data;
				
				LoggerManager.addEvent("LOGIN RESULT: " + result);
			}

		
			private function OnClosed(e:Event):void {
				this.dispatchEvent(new Event("WINDOW_CLOSED"));
			}
			
			public function saveInfo():Boolean {
				if(this.cmbPlayers0.selectedItem) {
				
					ApplicationSettings.load();
										
					ApplicationSettings.setValue("user.username",this._username);
					ApplicationSettings.setValue("user.password",this._password);
					ApplicationSettings.setValue("screen0.channel.id",this.cmbPlayers0.selectedItem.data[0]);
					
					if(this.currentState == "register_multiPlayer" && !_isDemoMode) {
						ApplicationSettings.setValue("screen1.channel.id",this.cmbPlayers1.selectedItem.data[0]);
					} else {
						ApplicationSettings.setValue("screen1.channel.id","-1");					
					}
					
					ApplicationSettings.save();
					
					return true;
				} else {
					return false;
				}
			}
			
			public function checkCredentials(listener:Function):void {
				
				ApplicationSettings.load();
					
				this._username = ApplicationSettings.getValue("user.username","");
				this._password = ApplicationSettings.getValue("user.password","");
				
				if( this._username == "demo@s3mer.com" ) {
					this._username = "";
					this._password = "";
				}
				
				if (this._username == "" || this._password == "") {
					listener(null);
				} else {
					this.doRemoteCommand("login",listener);
				}
				
				this.resetFields();				
			}
			
			private function OnComboChanged(e:ListEvent):void {
				trace("Selected ID: " + this.cmbPlayers0.selectedItem.data[0]);
			}
			
			private function onDemoMode(e:Event):void {

				this.height = 0;
				this.width = 0;
				this.visible = false;
				
				this.currentState = "register_choosePlayer";
				
				this.txtPassword.displayAsPassword = true;
				this.txtUsername.displayAsPassword = true;
				this.txtUsername.text = "demo@s3mer.com";
				this.txtPassword.text = "thisisthedemoacctpassword";
				
				this.btnDemo.enabled = false;
				this.btnLogin1.enabled = false;
				this.btnTune0.enabled = false;
				this.linkbutton2.enabled = false;
				this.cmbPlayers0.enabled = false;

				this._isDemoMode = true;
				
				OnLoginClick(e);			
			}
			
			private function onDemoMode_stage2():void {
				this.saveInfo();
				this.visible = false;	
			}
			
			private function onRegisteredMode(e:Event):void {
				this.currentState = "register";
			}
			
			private function cmbLanguageChange(e:ListEvent):void {
				resourceManager.localeChain = [ cmbLanguage.selectedItem.data ];
				ApplicationSettings.setValue("ui.lang",cmbLanguage.selectedItem.data);
				ApplicationSettings.save();
				
				updateLocaleStrings();
			}
			
			[Bindable]
			private var localesList:Array = [ {label:"English", data:"en_US"},
											  {label:"Español", data:"es_ES"},
											  {label:"Italiano", data:"it_IT"},
											  {label:"Portuguese", data:"pt_BR"}];
			