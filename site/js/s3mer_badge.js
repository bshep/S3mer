// -----------------------------------------------------------------------------
// Globals
// Major version of Flash required
var requiredMajorVersion = 9;
// Minor version of Flash required
var requiredMinorVersion = 0;
// Minor version of Flash required
var requiredRevision = 115;		// This is Flash Player 9 Update 3
// -----------------------------------------------------------------------------
// Version check based upon the values entered above in "Globals"
var hasReqestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);

// Check to see if the version meets the requirements for playback
if (hasReqestedVersion) {
	// if we've detected an acceptable version
	// embed the Flash Content SWF when all tests are passed

	AC_FL_RunContent(
		'codebase','http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab',
		'width','217',
		'height','180',
		'id','badge',
		'align','middle',
		'src','badge',
		'quality','high',
		'bgcolor','#FFFFFF',
		'name','badge',
		'allowscriptaccess','all',
		'pluginspage','http://www.macromedia.com/go/getflashplayer',
		'flashvars','appname=S3mer&appurl=http://media1.s3mer.com.s3.amazonaws.com/app/S3mer87.air&airversion=1.0&imageurl=images/badge.png',
		'movie','js/badge' ); //end AC code

} else {  // Flash Player is too old or we can't detect the plugin
	document.write('<table id="AIRDownloadMessageTable"><tr><td>Download <a href="http://media1.s3mer.com.s3.amazonaws.com/app/S3mer87.air">s3mer PlayerApp</a> now.<br /><br /><span id="AIRDownloadMessageRuntime">This application requires the <a href="');
	
	var platform = 'unknown';
	if (typeof(window.navigator.platform) != undefined)
	{
		platform = window.navigator.platform.toLowerCase();
		if (platform.indexOf('win') != -1)
			platform = 'win';
		else if (platform.indexOf('mac') != -1)
			platform = 'mac';
	}
	
	if (platform == 'win')
		document.write('http://airdownload.adobe.com/air/win/download/1.0/AdobeAIRInstaller.exe');
	else if (platform == 'mac')
		document.write('http://airdownload.adobe.com/air/mac/download/1.0/AdobeAIR.dmg');
	else
	document.write('http://www.adobe.com/go/getair/');

		
	document.write('">Adobe&#174;&nbsp;AIR&#8482; runtime</a>.</span></td></tr></table>');
}
// -->
