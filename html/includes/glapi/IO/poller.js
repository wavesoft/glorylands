var feeder_interval=5000;
var feeder_timer=0;
var feeder_enabled=true;
var iD = 0;

// Called when a gloryIO event occured. This is used so we don't
// overload the server by concurrent requests.
function reset_feeder() {
	if (feeder_timer) clearTimeout(feeder_timer);
	if (feeder_enabled) {
		feeder_timer=setTimeout(feeder, feeder_interval);
	}
}

function feeder() {
	// Every then and now, dump the messages currently
	// stacked and waitting for me to get them

	iD++;
	//$('prompt').set('html', 'Feeded: '+iD);
	gloryIO('msgfeed.php',false,true,function(e) {
		reset_feeder();
	});
}
