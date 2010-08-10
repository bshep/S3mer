Event.observe(window, 'load', function() {
    goTurbo();
});

function gearsInstalled() {
  if (!window.google || !google.gears) {
    return false;
  } else {
    return true;
  }
}

function installGears() {
  if (gearsInstalled() == false) {
      var installMessage = 's3mer.com now supports google gears. Please install to take advantage';
      var iconUrl = 'http://media1.s3mer.com/app/s3merIcon48.png';

      location.href = 'http://gears.google.com/?action=install&message=' + installMessage + '&icon_src=' + iconUrl + '';
  } else {
      goTurbo();
  }
}

function goTurbo() {
  if (gearsInstalled() ==  false) {
    // console.log('Gears not installed');
  } else {

    // console.log('going turbo');

    var siteName = 's3mer';
    var imgUrl = 'http://media1.s3mer.com/app/s3merIcon48.png';
    var message = 'Click on Allow to enable gears for s3mer.com. This will make the site run a lot faster.';

    if(google.gears.factory.getPermission(siteName, imgUrl, message)) {
      try {
        var localServer = google.gears.factory.create('beta.localserver');
        // console.log('localserver created');
      } catch (ex) {
        // console.log('Could not create local server: ' + ex.message);
        return;
      }
      var store = localServer.createManagedStore('s3mer-store');
      store.manifestUrl = 'gears/gears_manifest.php';
      // console.log(store.manifestUrl);

      store.onprogress = function(details) {
        if($('gearSpinner').visible() == false){
          $('gearSpinner').appear({duration: 0.3});
        }
        
        var percent = Math.round((details.filesComplete / details.filesTotal)*100);
        
        $('gPercent').innerHTML = percent + '%';
        $('gBar').setStyle({width:percent+'%'});
        
        // console.log('progress: ' + details.filesComplete + ' of ' + details.filesTotal);
      };

      store.oncomplete = function() {
        if($('gearSpinner').visible() == true){
          $('gearSpinner').fade({duration: 0.3});
        }
        // console.log('complete');
      };
      
      store.onerror = function(error) {
        // console.log(error);
      }
      
      store.checkForUpdate();

    }
  }
}