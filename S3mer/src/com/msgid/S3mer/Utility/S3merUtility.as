package com.msgid.S3mer.Utility
{
	public class S3merUtility
	{
        private static var hasDeterminedDebugStatus:Boolean = false;
 
        public static function get isDebug():Boolean
        {
            if(!hasDeterminedDebugStatus)
            {
                try
                {
                    throw new Error();
                }
                catch(e:Error)
                {
                    var stackTrace:String = e.getStackTrace();
                    _isDebug = stackTrace != null && stackTrace.indexOf("[") != -1;
                    hasDeterminedDebugStatus = true;
                    return _isDebug;
                }
            }
            return _isDebug;
        }
        private static var _isDebug:Boolean;

	}
}