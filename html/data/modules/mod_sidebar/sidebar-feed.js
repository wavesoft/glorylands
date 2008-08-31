
callback.register('message', function(msg) {
	// ## Handle SIDEBAR messages ##
	if (msg[0] == 'SIDEBAR') {
		
		var vIcon = $('sidebar_icon');
		var vHead = $('sidebar_head');
		var vText = $('sidebar_text');
		
		
		vIcon.src = 'images/'+msg[1];
		vHead.setHTML(msg[2]);
		vText.setHTML(msg[3]);
		
	}
});

// Convert hour/min/sec into sec
function sb_hms2sec(hms) {
	var hmss = new String(hms);
	var parts = hmss.split(':');
	
	if (parts.length < 3) {
		return 0;	
	}
	
	var h = new Number(parts[0]);
	var m = new Number(parts[1]);
	var s = new Number(parts[2]);
	
	s += (m * 60) + (h * 3600);
	return s;
}

// Convert seconds into hour/min/sec
function sb_sec2hms(sec) {
	var h = new Number(0);
	var m = new Number(0);
	var s = new Number(sec);
	if (s >= 60) {
		m = Math.floor(s / 60);
		s = s % 60;
		if (m >= 60) {
			h = Math.floor(m / 60);
			m = m % 60;
		}
	}
	
	h = new String(h);
	m = new String(m);
	s = new String(s);
	if (h.length == 1) h='0'+h;
	if (m.length == 1) m='0'+m;
	if (s.length == 1) s='0'+s;
	return h+':'+m+':'+s;
}

// Countdown all countdownable entries every second
setInterval(function() {
	$$('.sidebar_timedown').each(function(e) {
		var time_hms = new String(e.getText());
		var time = sb_hms2sec(time_hms);
		if (time > 0) {
			time--;
			if ((time <= 0) || (time == 'NaN')) {
				e.innerHTML = '<font color="red">00:00:00</font>';
			} else {
				e.innerHTML = sb_sec2hms(time);
			}
		}

	});
}, 1000);