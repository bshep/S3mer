package com.s3mer.mediaObjects.customObjects
{
    import flash.display.Bitmap;
    import flash.display.Loader;
    import flash.events.Event;
    
    import mx.controls.Image;
    import mx.core.mx_internal;
    
    use namespace mx_internal;
    
    /**
     * SmoothImage
     *
     * Automatically turns smoothing on after image has loaded
     *
     * @author Ben Longoria
     */
    public class SmoothImage extends Image {
        
        public function SmoothImage():void {
            super();
        }
        
        /**
         * @private
         */
        override mx_internal function contentLoaderInfo_completeEventHandler(event:Event):void {
            var smoothLoader:Loader = event.target.loader as Loader;
            var smoothImage:Bitmap = smoothLoader.content as Bitmap;
            
            if(smoothImage != null) {
	            smoothImage.smoothing = true;
           	}
	                   
            super.contentLoaderInfo_completeEventHandler(event);
        }
        
    }
}