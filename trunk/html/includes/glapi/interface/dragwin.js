// =====================================================
//  Create a draggable, popup window with the specified
//  header and content
// =====================================================
var winCache = [];
var winStack = [];
var lastZ=250100;

function rect_collide(r1, r2) {
	if ((r1.l >= r2.l) && (r1.l <= r2.r) && (r1.t >= r2.t) && (r1.t <= r2.b)) {
		return true;
	}
	if ((r2.l >= r1.l) && (r2.l <= r1.r) && (r2.t >= r1.t) && (r2.t <= r1.b)) {
		return true;
	}
	return false
}

function draggable_win_align(win) {
	// If this window collides with another, move it in a position that it doesn't
	var collides = false;
	var w_pos = win.getPosition();
	var w_siz = win.getSize();
	var w_rect = {
		l: w_pos.x,
		t: w_pos.y,
		r: w_pos.x+w_siz.x,
		b: w_pos.y+w_siz.y
	};
	var limits = $(document.body).getSize();
	var i;
	winStack.each(function(win) {
		try{
			var pos = win.getPosition();
			var siz = win.getSize();
			var rect = {
				l: pos.x,
				t: pos.y,
				r: pos.x+siz.x,
				b: pos.y+siz.y
			};
			if (rect_collide(rect, w_rect)) {
				collides = {x: pos.x+16, y: pos.y+16};
				return;
			}
		} catch(e) {
		}
	});
	if (collides != false) {
		win.setStyle('top', collides.y);	
		win.setStyle('left', collides.x);	
		//draggable_win_align(win);
	}
}

function createWindow(header, content, x, y, w, h, guid) {
	
	// If window is not already visible, create a new
	if (!winCache[header]) {
	
		// Create elements
		var eBody = document.createElement('div');
		var eHead = document.createElement('div');
		var eContent = document.createElement('span');
		var linkToggle = document.createElement('a');
		var linkDispose = document.createElement('a');
		
		// Initiate elements
		if (!isInternetExplorer) {
			eBody.setAttribute('class', 'container');
			eHead.setAttribute('class', 'dragger');
			eContent.setAttribute('class', 'content');
			linkToggle.setAttribute('class', 'toggle');
			linkDispose.setAttribute('class', 'dispose');
		} else { /* Internet explorer requires explicit definition */
			eBody.setAttribute('className', 'container');
			eHead.setAttribute('className', 'dragger');
			eContent.setAttribute('className', 'content');
			linkToggle.setAttribute('className', 'toggle');
			linkDispose.setAttribute('className', 'dispose');
		}
		linkToggle.setAttribute('title', 'Minimize');
		linkDispose.setAttribute('title', 'Close');
		eHead.innerHTML = "<span align=\"top\" class=\"left\">&nbsp;</span><span class=\"center\">"+header+"</span><span class=\"right\">&nbsp;</span>";
		
		// Nest elements
		eBody.appendChild(eHead);
		eBody.appendChild(eContent);
		eContent.innerHTML = content;
	
		// Initiate slider
		if (!isMozilla) { /* Slide effect works with bugs on mozilla */
			var slider = new Fx.Slide(eContent);
		}
		linkToggle.setAttribute('href', 'javascript:;');
		linkToggle.innerHTML = '&nbsp;';
		eHead.appendChild(linkToggle);
		$(linkToggle).addEvent('click', function(e){
			e = new Event(e);
			if (!isMozilla) { 
				slider.toggle();
			} else { /* Slide effect works with bugs on mozilla */
				if (eContent.style.display == 'none') {
					eContent.style.display = '';
				} else {
					eContent.style.display = 'none';
				}
			}
			e.stop();
		});
	
		// Initiate disposer
		linkDispose.setAttribute('href', 'javascript:void(null)');
		linkDispose.innerHTML = '&nbsp;';
		eHead.appendChild(linkDispose);
		$(linkDispose).addEvent('click', function(e){
			e = new Event(e);

			// Notify server that the window is closed
			gloryIO('?a=window.closed&guid='+guid,false,true);

			// Cleanup window variables
			delete winCache[header];
			winStack.erase(eBody);

			// Remove body element
			eBody.dispose();

			e.stop();
		});
	
		// Prepare Fade Effects	
		$(eHead).addEvent('mousedown', function(e) {
			eBody.setStyles({'opacity': 0.7, 'z-index': lastZ++});
		});
		$(eHead).addEvent('mouseup', function(e) {
			eBody.setStyles({'opacity': 1});
		});
		$(eBody).addEvent('mousedown', function(e) {
			eBody.setStyles({'z-index': lastZ++});
		});
		$(eBody).addEvent('click', function(e) {
			new Event(e).stop();
		});
		
		// Move to a specific location (if set)
		if (x) eBody.setStyles({'left':x});
		if (y) eBody.setStyles({'top':y});
		if (w) eBody.setStyles({'width':w});
		if (h) eBody.setStyles({'height':h});
				
		// Create object
		eBody.setStyles({'z-index': lastZ++});
		document.body.appendChild(eBody);

		// Align the window
		draggable_win_align(eBody);

		// Bind to dragger
		var d = new Drag.Move(eBody, {handle: eHead});
				
		// Save usefull variables
		winCache[header] = [eBody, eContent, slider];
		winStack.push(eBody);
		
		// Return content object
		return eBody;
	
	// Else, If it is already visible, show it and update content
	} else {
		
		var eBody = winCache[header][0];
		var eContent = winCache[header][1];
		var slider = winCache[header][2];

		// Display window
		if (!isMozilla) { 
			slider.slideIn();
		} else { /* Slide effect works with bugs on mozilla */
			eContent.style.display = '';
		}
		
		// Update content
		eContent.innerHTML = content;

		// Update sizes (if set)
		if (w) eBody.setStyles({'width':w});
		if (h) eBody.setStyles({'height':h});

	}
}