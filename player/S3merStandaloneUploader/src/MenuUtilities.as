package
{
	import flash.display.NativeWindow;
	import flash.display.Screen;
	import flash.system.Capabilities;
	import flash.ui.Keyboard;

 	public  class MenuUtilities
	{
		public function MenuUtilities()
		{
		}
            
        private static function osType():String {
            if(Capabilities.os.indexOf("Windows") >= 0) {
	        	return "Windows";
            } else {
//                isMac = (Capabilities.os.indexOf("Mac OS") >= 0);
				return "Mac";
            }
        }
                
        public static function keyEquivalentModifiers(item:Object):Array
        {
            var result:Array = new Array();
            
            if (item.@keyEquivalent == null || item.@keyEquivField.length == 0)
            {
                return result;
            }
            
            if (item.@altKey != null && item.@altKey == true)
            {
                if (osType() == "Windows")
                {
                    result.push(Keyboard.ALTERNATE);
                }
            }
            
            if (item.@ctrlKey != null && item.@ctrlKey == true)
            {
                if (osType() == "Windows")
                {
                    result.push(Keyboard.CONTROL);
                }
                else if (osType() == "Mac")
                {
                    result.push(Keyboard.COMMAND);
                }
            }
            
            if (item.@shiftKey != null && item.@shiftKey == true)
            {
                result.push(Keyboard.SHIFT);
            }
            
            return result;
        }
        
        public static function centerWindow(objWindow:NativeWindow):void {
        	var width:int;
        	var height:int;
        	
        	width = Screen.mainScreen.visibleBounds.width;
        	height = Screen.mainScreen.visibleBounds.height;
        	
        	objWindow.x = ( width - objWindow.width ) / 2;
        	objWindow.y = ( height - objWindow.height ) / 2;
        }

	}
}