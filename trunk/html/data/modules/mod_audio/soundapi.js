// Register change sound messages
var active_sound = '';
callback.register('message', function(msg) {
	// ## Handle MUSIC messages ##
	if (msg[0] == 'MUSIC') {
		try {
			// Obdain the flash external interface object
			var flash;
			if(navigator.appName.indexOf("Microsoft") != -1) {
				flash = window.ambient;
			} else {
				flash = window.document.ambient;
		    }  
			
			// Call the change() function and point on the new sound file			
			if (active_sound != msg[1]) {
				active_sound = msg[1];
				flash.change(msg[1]);
			}
		} catch (e) {
			// Trap errors
		}
	}
});
